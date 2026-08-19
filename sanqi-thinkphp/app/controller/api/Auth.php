<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\api;

use app\controller\Base;
use app\model\User;
use app\service\AdminSecurityService;
use app\service\AuthService;
use app\service\EmailTemplateService;
use app\service\RateLimitService;
use app\validate\AuthValidate;

/**
 * 认证API控制器
 */
class Auth extends Base
{
    protected function rl(string $group, string $key): array
    {
        return config("ratelimit.{$group}.{$key}") ?: ['max' => 5, 'window' => 3600, 'lockout' => 3600];
    }
    /**
     * 登录
     */
    public function login()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $ip = request()->ip();
        $username = trim((string)$this->request->post('zh', ''));
        $password = $this->request->post('mm', '');
        $loginIpKey = 'front_login_ip:' . $ip;
        $loginUserKey = 'front_login_user:' . strtolower($username);

        $rlIp = $this->rl('login', 'ip');
        $rlUser = $this->rl('login', 'username');

        try {
            RateLimitService::assertAllowed($loginIpKey, $rlIp['max'], $rlIp['window'], '登录尝试过多，请{minutes}分钟后再试');
            if ($username !== '') {
                RateLimitService::assertAllowed($loginUserKey, $rlUser['max'], $rlUser['window'], '该账号登录失败次数过多，请{minutes}分钟后再试', $rlUser['lockout']);
            }
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }

        // 验证参数
        $validate = new AuthValidate();
        $validate->scene('login');
        if (!$validate->batch(true)->check(['zh' => $username, 'mm' => $password])) {
            return $this->error($validate->getError());
        }

        // 查询用户
        $user = User::where('username', $username)->find();
        if (!$user) {
            RateLimitService::hit($loginIpKey, $rlIp['lockout']);
            if ($username !== '') {
                RateLimitService::hit($loginUserKey, $rlUser['lockout'], $rlUser['window'], $rlUser['max']);
            }
            return $this->error('账号或密码错误');
        }

        // 验证密码（返回 true 或新哈希字符串表示验证成功）
        $verifyResult = verifyPassword($password, $user['password']);
        if ($verifyResult === false) {
            RateLimitService::hit($loginIpKey, $rlIp['lockout']);
            RateLimitService::hit($loginUserKey, $rlUser['lockout'], $rlUser['window'], $rlUser['max']);
            return $this->error('账号或密码错误');
        }

        // 旧 MD5 哈希自动升级
        if (is_string($verifyResult)) {
            User::where('id', $user['id'])->update(['password' => $verifyResult]);
        }

        // 检查封禁
        if (isFlag($user['ban'] ?? 0) !== 0) {
            if ($user['bantime'] === 'true' || $user['bantime'] === '' || $user['bantime'] === null) {
                return $this->error('您的账号已被永久封禁');
            } elseif (strtotime($user['bantime']) > time()) {
                return $this->error('您的账号已被封禁至' . $user['bantime']);
            }
        }

        RateLimitService::clear($loginIpKey);
        RateLimitService::clear($loginUserKey);

        $redirect = $this->safeLoginRedirect((string)$this->request->post('redirect', '/'));
        $userArray = AuthService::toArray($user);
        if ($this->isAdminRedirect($redirect) && AdminSecurityService::requiresTwoFactor($userArray)) {
            return $this->beginAdminTwoFactor($userArray);
        }

        return $this->finishLogin($userArray);
    }

    public function verify2fa()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $token = (string)$this->request->post('token', '');
        $code = (string)$this->request->post('code', '');
        $pending = AdminSecurityService::getPendingLogin($token);
        if (!$pending) {
            return $this->error('验证已过期，请重新登录');
        }

        $user = User::where('id', (int)$pending['user_id'])->find();
        if (!$user) {
            AdminSecurityService::clearPendingLogin();
            return $this->error('账号不存在，请重新登录');
        }

        $userArray = AuthService::toArray($user);
        if (($userArray['role'] ?? '') !== 'admin') {
            AdminSecurityService::clearPendingLogin();
            return $this->error('无管理员权限');
        }

        $config = AdminSecurityService::getTwoFactorConfig();
        $method = $config['method'] ?? 'email';
        $valid = false;
        if ($method === 'totp') {
            $valid = AdminSecurityService::verifyTotp((string)($config['totp_secret'] ?? ''), $code);
        } else {
            $valid = AdminSecurityService::verifyPendingEmailCode($code, $pending);
        }

        if (!$valid) {
            return $this->error('二步验证码错误或已过期');
        }

        AdminSecurityService::clearPendingLogin();
        return $this->finishLogin($userArray, true);
    }

    protected function safeLoginRedirect(string $redirect): string
    {
        $redirect = trim($redirect);
        if ($redirect === '' || $redirect[0] !== '/' || strpos($redirect, '//') === 0 || strpos($redirect, '\\') !== false) {
            return '/';
        }
        return $redirect;
    }

    protected function isAdminRedirect(string $redirect): bool
    {
        return $redirect === '/admin' || strpos($redirect, '/admin/') === 0;
    }

    protected function beginAdminTwoFactor(array $user)
    {
        $config = AdminSecurityService::getTwoFactorConfig();
        $method = ($config['method'] ?? 'email') === 'totp' ? 'totp' : 'email';
        $token = AdminSecurityService::startPendingLogin($user);

        if ($method === 'email') {
            $email = trim((string)($user['email'] ?? ''));
            if ($email === '') {
                AdminSecurityService::clearPendingLogin();
                return $this->error('管理员账号未绑定邮箱，无法发送二步验证码');
            }

            $code = (string)random_int(100000, 999999);
            AdminSecurityService::setPendingEmailCode($code);
            $siteConfig = $this->getSiteConfig();
            $template = EmailTemplateService::render('verify_code', [
                'site_name' => $siteConfig['name'] ?? '',
                'code' => $code,
                'minutes' => 5,
            ]);

            $sent = sendEmail($siteConfig, $email, $template['subject'], $template['body']);
            if (!$sent) {
                AdminSecurityService::clearPendingLogin();
                return $this->error('二步验证码发送失败，请检查邮件配置');
            }
        }

        return $this->success('需要二步验证', [
            'requires_2fa' => 1,
            'method' => $method,
            'token' => $token,
        ]);
    }

    protected function finishLogin(array $user, bool $markTwoFactor = false)
    {
        $passid = randomString(64);

        try {
            // 更新用户登录信息
            User::where('id', $user['id'])->update([
                'passid' => $passid,
                'logip' => request()->ip(),
                'logtime' => date('Y-m-d H:i:s')
            ]);

            // 设置签名认证Cookie（HttpOnly + SameSite）
            $this->setAuthCookie($user['id'], $passid);

            AuthService::regenerateSession();

            // 设置Session（仅安全字段）
            $sessionUser = [
                'id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['name'],
                'img' => $user['img'],
                'sign' => $user['sign'],
                'email' => $user['email'],
                'essqx' => $user['essqx'],
                'esseam' => $user['esseam'],
                'passid' => $passid,
                'role' => $user['role'] ?? 'user',
            ];
            session('user', $sessionUser);

            if ($markTwoFactor) {
                AdminSecurityService::markTwoFactorVerified($sessionUser);
            } else {
                AdminSecurityService::clearTwoFactorVerified();
            }

            return $this->success('登录成功');
        } catch (\Exception $e) {
            return $this->error('登录失败: ' . $e->getMessage());
        }
    }

    /**
     * 注册
     */
    public function register()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $ip = request()->ip();
        $rl = $this->rl('register', 'ip');

        try {
            RateLimitService::assertAllowed('register_ip:' . $ip, $rl['max'], $rl['window'], '注册太频繁，请{minutes}分钟后再试');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }

        // 检查注册开关
        $siteConfig = $this->getSiteConfig();
        if (isFlag($siteConfig['regqx'] ?? 0) !== 0) {
            return $this->error('注册已关闭');
        }

        $username = trim((string)$this->request->post('zh', ''));
        $password = $this->request->post('mm', '');
        // 兼容前端 em 和 email 两种参数名
        $email = trim((string)($this->request->post('email') ?? $this->request->post('em', '')));
        // 是否为发送验证码请求
        $sendCode = (int)$this->request->post('fsyzm', 0);
        // 注册验证码（用户填写的）
        $inputCode = trim((string)$this->request->post('yzm', ''));

        // 验证参数
        $validate = new AuthValidate();
        $validate->scene('register');
        if (!$validate->batch(true)->check(['zh' => $username, 'mm' => $password, 'email' => $email])) {
            return $this->error($validate->getError());
        }

        // 检查用户名是否已存在（使用模糊提示，防止用户枚举）
        if (User::where('username', $username)->count() > 0) {
            RateLimitService::hit('register_ip:' . $ip, $rl['lockout']);
            return $this->error('注册信息有误，请检查后重试');
        }

        // 检查邮箱是否已被使用
        if (User::where('email', $email)->count() > 0) {
            RateLimitService::hit('register_ip:' . $ip, $rl['lockout']);
            return $this->error('注册信息有误，请检查后重试');
        }

        $regverify = (string)($siteConfig['regverify'] ?? '0');

        // ===== 发送注册验证码 =====
        if ($sendCode === 1) {
            if ($regverify !== '1') {
                return $this->success('当前无需邮箱验证');
            }

            // 限流
            try {
                RateLimitService::assertAllowed('reg_code_email:' . strtolower($email), 5, 3600, '验证码发送太频繁，请{minutes}分钟后再试');
                RateLimitService::assertAllowed('reg_code_ip:' . $ip, 10, 3600, '验证码发送太频繁，请{minutes}分钟后再试');
            } catch (\RuntimeException $e) {
                return $this->error($e->getMessage());
            }

            $code = random_int(100000, 999999);

            $template = EmailTemplateService::render('register_code', [
                'code' => $code,
                'minutes' => '2',
                'site_name' => $siteConfig['name'] ?? ''
            ]);
            $mailResult = sendEmail(
                $siteConfig,
                $email,
                $template['subject'],
                $template['body']
            );

            if ($mailResult) {
                session('reg_verify', [
                    'code' => md5(md5((string)$code)),
                    'expire' => time() + 120,
                    'username' => $username,
                    'email' => $email
                ]);
                RateLimitService::hit('reg_code_email:' . strtolower($email), 3600);
                RateLimitService::hit('reg_code_ip:' . $ip, 3600);
                return $this->success('验证码已发送');
            } else {
                return $this->error('发送失败，请检查邮箱配置');
            }
        }

        // ===== 注册验证模式下，校验验证码 =====
        if ($regverify === '1') {
            if ($inputCode === '') {
                return $this->error('请输入邮箱验证码');
            }

            $saved = session('reg_verify');
            if (!$saved || !is_array($saved)) {
                return $this->error('验证码错误或已过期');
            }
            if ($saved['expire'] < time()) {
                \think\facade\Session::delete('reg_verify');
                return $this->error('验证码已过期');
            }
            if ($saved['code'] !== md5(md5($inputCode))) {
                return $this->error('验证码错误');
            }
            if ($saved['username'] !== $username || $saved['email'] !== $email) {
                return $this->error('验证码与注册信息不匹配');
            }

            \think\facade\Session::delete('reg_verify');
        }

        // 生成认证Token
        $passid = randomString(64);

        // 创建用户（捕获并发注册时的唯一约束冲突）
        try {
            $newUser = User::create([
                'username' => $username,
                'password' => md5Password($password),
                'name' => $username,
                'img' => '',
                'homeimg' => '',
                'sign' => '这个人很懒，什么都没留下',
                'url' => '',
                'email' => $email,
                'passid' => $passid,
                'essqx' => isFlag($siteConfig['readonly'] ?? 0, 0) !== 0 ? 0 : 1,
                'esseam' => 1,
                'ban' => 0,
                'bantime' => '',
                'regtime' => date('Y-m-d H:i:s'),
                'logip' => request()->ip(),
                'logtime' => date('Y-m-d H:i:s')
            ]);
        } catch (\think\exception\PDOException $e) {
            if ($e->getCode() == 23000) {
                RateLimitService::hit('register_ip:' . $ip, $rl['lockout']);
                return $this->error('注册信息有误，请检查后重试');
            }
            throw $e;
        }

        $userId = $newUser->id;

        // 设置签名认证Cookie（HttpOnly + SameSite）
        $this->setAuthCookie($userId, $passid);

        \app\service\AuthService::regenerateSession();

        // 设置Session（仅安全字段）
        $essqx = isFlag($siteConfig['readonly'] ?? 0, 0) !== 0 ? 0 : 1;
        session('user', [
            'id' => $userId,
            'username' => $username,
            'name' => $username,
            'img' => '',
            'sign' => '这个人很懒，什么都没留下',
            'email' => $email,
            'essqx' => $essqx,
            'esseam' => 1,
            'passid' => $passid
        ]);

        RateLimitService::hit('register_ip:' . $ip, $rl['lockout']);
        return $this->success('注册成功');
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        AuthService::clearLogin();
        return redirect('/');
    }

    /**
     * 找回密码
     */
    public function repass()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $tig = $this->request->post('tig', '0');
        $username = trim((string)$this->request->post('useke', ''));
        $email = trim((string)$this->request->post('useem', ''));

        if ($tig == '0') {
            $ip = request()->ip();
            $rlIp = $this->rl('repass_send', 'ip');
            $rlEmail = $this->rl('repass_send', 'email');
            $rlEmailHour = $this->rl('repass_send', 'email_hour');
            $rlUser = $this->rl('repass_send', 'username');

            try {
                RateLimitService::assertAllowed('repass_send_ip:' . $ip, $rlIp['max'], $rlIp['window'], '验证码发送太频繁，请{minutes}分钟后再试');
                if ($email !== '') {
                    RateLimitService::assertAllowed('repass_send_email:' . strtolower($email), $rlEmail['max'], $rlEmail['window'], '验证码发送太频繁，请{minutes}分钟后再试');
                    RateLimitService::assertAllowed('repass_send_email_hour:' . strtolower($email), $rlEmailHour['max'], $rlEmailHour['window'], '该邮箱验证码发送过多，请{minutes}分钟后再试');
                }
                if ($username !== '') {
                    RateLimitService::assertAllowed('repass_send_user:' . strtolower($username), $rlUser['max'], $rlUser['window'], '该账号验证码发送过多，请{minutes}分钟后再试');
                }
            } catch (\RuntimeException $e) {
                return $this->error($e->getMessage());
            }

            // 发送验证码
            $validate = new AuthValidate();
            $validate->scene('repass');
            if (!$validate->batch(true)->check(['useke' => $username, 'useem' => $email])) {
                return $this->error($validate->getError());
            }

            $user = User::where('username', $username)->where('email', $email)->find();
            if (!$user) {
                RateLimitService::hit('repass_send_ip:' . $ip, $rlIp['lockout']);
                if ($email !== '') {
                    RateLimitService::hit('repass_send_email_hour:' . strtolower($email), $rlEmailHour['lockout']);
                }
                if ($username !== '') {
                    RateLimitService::hit('repass_send_user:' . strtolower($username), $rlUser['lockout']);
                }
                return $this->error('账号或邮箱错误');
            }

            RateLimitService::hit('repass_send_ip:' . $ip, $rlIp['lockout']);
            RateLimitService::hit('repass_send_email:' . strtolower($email), $rlEmail['lockout']);
            RateLimitService::hit('repass_send_email_hour:' . strtolower($email), $rlEmailHour['lockout']);
            RateLimitService::hit('repass_send_user:' . strtolower($username), $rlUser['lockout']);

            // 生成验证码
            $code = random_int(100000, 999999);

            // 发送邮件
            $siteConfig = $this->getSiteConfig();
            $template = EmailTemplateService::render('repass_code', [
                'code' => $code,
                'minutes' => '2',
                'site_name' => $siteConfig['name'] ?? ''
            ]);
            $mailResult = sendEmail(
                $siteConfig,
                $email,
                $template['subject'],
                $template['body']
            );

            if ($mailResult) {
                // 存储验证码到Session
                session('safyzm', [
                    'code' => md5(md5((string)$code)),
                    'expire' => time() + 120,
                    'username' => $username,
                    'email' => $email
                ]);
                return $this->success('验证码已发送');
            } else {
                return $this->error('发送失败，请检查邮箱配置');
            }
        } else {
            // 验证码验证并重置密码
            $code = $this->request->post('safyzm', '');
            $newPassword = $this->request->post('safxmm', '');

            // 验证参数
            $validate = new AuthValidate();
            $validate->scene('reset');
            if (!$validate->batch(true)->check(['safyzm' => $code, 'safxmm' => $newPassword])) {
                return $this->error($validate->getError());
            }

            // 从Session读取验证码
            $saved = session('safyzm');
            if (!$saved || !is_array($saved)) {
                return $this->error('验证码错误或已过期');
            }

            // 验证码过期检查
            if ($saved['expire'] < time()) {
                \think\facade\Session::delete('safyzm');
                return $this->error('验证码已过期');
            }

            // 验证码匹配检查
            if ($saved['code'] !== md5(md5($code))) {
                return $this->error('验证码错误');
            }

            // 二次校验：确保username和email与发送验证码时一致
            if ($saved['username'] !== $username || $saved['email'] !== $email) {
                return $this->error('账号或邮箱与验证码不匹配');
            }

            // 查找用户
            $user = User::where('username', $username)->where('email', $email)->find();
            if (!$user) {
                return $this->error('用户不存在');
            }

            // 更新密码
            User::where('id', $user['id'])->update([
                'password' => md5Password($newPassword),
                'passid' => randomString(64)
            ]);

            // 清除验证码Session
            \think\facade\Session::delete('safyzm');

            return $this->success('密码重置成功');
        }
    }

    /**
     * 全站密码验证
     */
    public function sitePasswordVerify()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $pagepass = $this->request->post('pagepass', '');
        $siteConfig = $this->getSiteConfig();

        if (!isSitePasswordEnabled($siteConfig['pagepass'] ?? '')) {
            return $this->success('验证成功');
        }

        if (verifySitePasswordInput($pagepass, $siteConfig['pagepass'])) {
            setSitePasswordCookie($siteConfig['pagepass']);
            return $this->success('验证成功');
        } else {
            return $this->error('密码错误');
        }
    }
}
