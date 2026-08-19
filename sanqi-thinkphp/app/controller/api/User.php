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
use app\model\User as UserModel;
use think\facade\Cache;
use app\service\ExternalRequestGuard;
use app\service\UploadService;
use app\validate\UserValidate;

/**
 * 用户 API 控制器
 */
class User extends Base
{
    protected $uploadService;

    protected function initialize()
    {
        parent::initialize();
        $this->uploadService = new UploadService();
    }

    /**
     * 更新用户资料
     */
    public function update()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) {
            return;
        }

        $user = $this->getUser();
        $type = $this->request->post('lx', '');

        $validate = new UserValidate();
        $validate->scene('update');
        if (!$validate->batch(true)->check(['lx' => $type])) {
            return $this->error($validate->getError());
        }

        switch ($type) {
            case 'aq':
                return $this->updateSecurity($user);

            case 'zlnc':
                return $this->updateName($user);

            case 'zlqm':
                return $this->updateSign($user);

            case 'zlwz':
                return $this->updateWebsite($user);

            default:
                return $this->error('参数错误');
        }
    }

    protected function updateSecurity(array $user)
    {
        $oldPass = $this->request->post('oldmm', $this->request->post('usermm', ''));
        $newPass = $this->request->post('newmm', $this->request->post('userxmm', ''));
        $email = trim((string)$this->request->post('email', $this->request->post('userem', '')));
        $sessionUser = session('user') ?: [];
        $sessionChanged = false;

        if ($oldPass !== '' && $newPass !== '') {
            $validate = new UserValidate();
            $validate->scene('updateSecurity');
            if (!$validate->batch(true)->check(['oldmm' => $oldPass, 'newmm' => $newPass])) {
                return $this->error($validate->getError());
            }

            $verifyResult = verifyPassword($oldPass, $user['password']);
            if ($verifyResult === false) {
                return $this->error('原密码错误');
            }

            $newPassid = randomString(64);
            UserModel::where('id', $user['id'])->update([
                'password' => hashPassword($newPass),
                'passid' => $newPassid,
            ]);
            $this->setAuthCookie($user['id'], $newPassid);
            $sessionUser['passid'] = $newPassid;
            $sessionChanged = true;
        }

        if ($email !== '') {
            $emailValidate = new UserValidate();
            if (!$emailValidate->batch(true)->check(['email' => $email])) {
                return $this->error($emailValidate->getError());
            }
            UserModel::where('id', $user['id'])->update(['email' => $email]);
            $sessionUser['email'] = $email;
            $sessionChanged = true;
        }

        if ($sessionChanged) {
            session('user', $sessionUser);
        }

        return $this->success('更新成功');
    }

    protected function updateName(array $user)
    {
        $name = trim((string)$this->request->post('nc', $this->request->post('usernc', '')));

        $validate = new UserValidate();
        $validate->scene('updateName');
        if (!$validate->batch(true)->check(['nc' => $name])) {
            return $this->error($validate->getError());
        }

        UserModel::where('id', $user['id'])->update(['name' => $name]);
        $sessionUser = session('user') ?: [];
        $sessionUser['name'] = $name;
        session('user', $sessionUser);
        return $this->success('更新成功');
    }

    protected function updateSign(array $user)
    {
        $sign = (string)$this->request->post('qm', $this->request->post('userqm', ''));

        $validate = new UserValidate();
        $validate->scene('updateSign');
        if (!$validate->batch(true)->check(['qm' => $sign])) {
            return $this->error($validate->getError());
        }

        UserModel::where('id', $user['id'])->update(['sign' => $sign]);
        $sessionUser = session('user') ?: [];
        $sessionUser['sign'] = $sign;
        session('user', $sessionUser);
        return $this->success('更新成功');
    }

    protected function updateWebsite(array $user)
    {
        $url = (string)$this->request->post('wz', $this->request->post('userurl', ''));
        $url = str_replace('#$#', '&', $url);

        if ($url !== '') {
            $validate = new UserValidate();
            $validate->scene('updateWebsite');
            if (!$validate->batch(true)->check(['wz' => $url])) {
                return $this->error($validate->getError());
            }
        }

        UserModel::where('id', $user['id'])->update(['url' => $url]);
        $sessionUser = session('user') ?: [];
        $sessionUser['url'] = $url;
        session('user', $sessionUser);
        return $this->success('更新成功');
    }

    /**
     * 退出其他设备
     */
    public function logoutAll()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) {
            return;
        }

        $user = $this->getUser();
        $newPassid = randomString(64);
        UserModel::where('id', $user['id'])->update(['passid' => $newPassid]);
        $this->setAuthCookie($user['id'], $newPassid);

        $sessionUser = session('user') ?: [];
        $sessionUser['passid'] = $newPassid;
        session('user', $sessionUser);

        return $this->success('已退出其他设备');
    }

    public function avatar()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) {
            return;
        }

        $user = $this->getUser();
        $file = $this->fileToArray($this->request->file('file'));
        $result = $this->uploadService->uploadAvatar($file, $user['id']);

        if ($result['success']) {
            UserModel::where('id', $user['id'])->update(['img' => $result['url']]);
            $sessionUser = session('user') ?: [];
            $sessionUser['img'] = $result['url'];
            session('user', $sessionUser);
            if (!request()->isAjax()) {
                return redirect('/setup');
            }
            return $this->success('上传成功', ['url' => $result['url']]);
        } else {
            return $this->error($result['message']);
        }
    }

    public function cover()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) {
            return;
        }

        $user = $this->getUser();
        $file = $this->fileToArray($this->request->file('file'));
        $result = $this->uploadService->uploadCover($file, $user['id']);

        if ($result['success']) {
            UserModel::where('id', $user['id'])->update(['homeimg' => $result['url']]);
            $sessionUser = session('user') ?: [];
            $sessionUser['homeimg'] = $result['url'];
            session('user', $sessionUser);
            return $this->success('上传成功', ['url' => $result['url']]);
        } else {
            return $this->error($result['message']);
        }
    }

    public function emailNotify()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) {
            return;
        }

        $user = $this->getUser();
        $status = $this->request->post('ztm', '0');

        UserModel::where('id', $user['id'])->update(['esseam' => $status]);
        $sessionUser = session('user') ?: [];
        $sessionUser['esseam'] = $status;
        session('user', $sessionUser);

        return $this->success('更新成功');
    }

    /**
     * 上传收款码
     * POST /api/user/qr
     */
    public function qr()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) {
            return;
        }

        $user = $this->getUser();
        $type = $this->request->post('type', '');
        if (!in_array($type, ['alipay', 'wechat'])) {
            return $this->error('参数错误');
        }

        $file = $this->fileToArray($this->request->file('file'));
        $result = $this->uploadService->uploadImage($file);

        if ($result['success']) {
            $field = $type === 'alipay' ? 'alipay_qr' : 'wechat_qr';
            UserModel::where('id', $user['id'])->update([$field => $result['url']]);
            $sessionUser = session('user') ?: [];
            $sessionUser[$field] = $result['url'];
            session('user', $sessionUser);
            return $this->success('上传成功', ['url' => $result['url']]);
        } else {
            return $this->error($result['message']);
        }
    }

    /**
     * IP 地理位置查询
     */
    public function ipLocation()
    {
        try {
            ExternalRequestGuard::assertAllowed('ip_location', request()->ip());
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), '429');
        }

        $ip = trim((string)request()->get('ip', ''));
        if ($ip === '' || $ip === 'ok') {
            $ip = $this->clientPublicIp();
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return $this->error('未获取到有效IP');
        }

        // 拒绝内网和保留地址，防止 SSRF 探测内网拓扑
        if ($this->isReservedIp($ip)) {
            return $this->error('内网IP无法定位');
        }

        $cacheKey = 'ip_location_' . $ip;
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            if (!is_array($cached) || (trim((string)($cached['region'] ?? '')) === '' && trim((string)($cached['city'] ?? '')) === '' && trim((string)($cached['addr'] ?? '')) === '')) {
                return $this->error('IP位置查询失败');
            }
            return $this->success('获取成功', $cached);
        }

        // 硬编码 URL，禁止从配置读取，防止被篡改指向内网
        $baseUrl = 'https://whois.pconline.com.cn/ipJson.jsp';
        $timeout = 2;
        $ttl = 86400;
        $url = $baseUrl . '?ip=' . rawurlencode($ip) . '&json=true';

        $context = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'ignore_errors' => true,
                'header' => "User-Agent: sanqi/1.0\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);
        $response = @file_get_contents($url, false, $context);
        if (!$response && function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => $timeout,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_USERAGENT => 'sanqi/1.0',
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        if ($response) {
            $data = json_decode($response, true);
            if (!$data && function_exists('mb_convert_encoding')) {
                $data = json_decode(mb_convert_encoding($response, 'UTF-8', 'GBK,GB2312,UTF-8'), true);
            }
            if ($data && !empty($data['addr'])) {
                $result = [
                    'region' => $data['pro'] ?? $data['region'] ?? '',
                    'city' => $data['city'] ?? '',
                    'addr' => $data['addr'] ?? '',
                ];
                Cache::set($cacheKey, $result, $ttl);
                return $this->success('获取成功', $result);
            }
        }

        $empty = ['region' => '', 'city' => '', 'addr' => ''];
        Cache::set($cacheKey, $empty, 300);
        return $this->error('IP位置查询失败');
    }

    /**
     * 判断是否为内网或保留 IP 地址
     */
    private function clientPublicIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'HTTP_X_CLIENT_IP',
            'HTTP_X_CLUSTER_CLIENT_IP',
        ];

        foreach ($headers as $header) {
            $value = $_SERVER[$header] ?? '';
            if ($value === '') {
                continue;
            }

            foreach (explode(',', (string)$value) as $candidate) {
                $candidate = trim($candidate);
                if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_IP) && !$this->isReservedIp($candidate)) {
                    return $candidate;
                }
            }
        }

        return request()->ip();
    }

    private function isReservedIp(string $ip): bool
    {
        // FILTER_FLAG_NO_PRIV_RANGE + FILTER_FLAG_NO_RES_RANGE 只在 filter_var 不带 FILTER_VALIDATE_IP 时可用
        // 手动判断常见保留段
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return true;
        }
        // 额外拦截 169.254 (link-local)、IPv6 本地链路和环回
        if (preg_match('/^169\.254\./', $ip) || preg_match('/^fe80:/i', $ip) || $ip === '::1') {
            return true;
        }
        return false;
    }
}
