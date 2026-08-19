<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\server;

use app\controller\Base;
use app\middleware\AuthMiddleware;
use app\model\Installation;
use app\model\ServerVersion;
use app\model\User;
use app\service\AdminSecurityService;
use app\service\AuthService;

class Admin extends Base
{
    public function index()
    {
        $keyword = trim((string)request()->get('keyword', ''));

        $versions = ServerVersion::order('id', 'desc')->select()->toArray();

        $query = Installation::order('id', 'desc');
        if ($keyword !== '') {
            $query->where('domain|ip|admin_user', 'like', '%' . $keyword . '%');
        }
        $installations = $query->limit(100)->select()->toArray();

        return view('server/admin/index', [
            'pageTitle' => '服务端管理',
            'versions' => $versions,
            'installations' => $installations,
            'keyword' => $keyword,
            'installCount' => Installation::count(),
        ]);
    }

    public function login()
    {
        return redirect('/?login=1&redirect=' . rawurlencode('/server/admin'));
    }

    public function logout()
    {
        AuthService::clearLogin();
        AdminSecurityService::clearTwoFactorVerified();
        return redirect('/?login=1&redirect=' . rawurlencode('/server/admin'));
    }

    public function saveVersion()
    {
        if ($error = $this->requireCurrentAdminPassword()) {
            return $error;
        }

        $id = (int)request()->post('id', 0);
        $version = trim((string)request()->post('version', ''));
        $content = trim((string)request()->post('content', ''));

        if ($version === '' || $content === '') {
            return json(['code' => 400, 'msg' => '版本号和更新内容不能为空']);
        }

        if ($id > 0) {
            $existing = ServerVersion::find($id);
            if (!$existing) {
                return json(['code' => 400, 'msg' => '版本不存在']);
            }
            ServerVersion::where('id', $id)->update([
                'version' => $version,
                'content' => $content,
            ]);
        } else {
            $dup = ServerVersion::where('version', $version)->find();
            if ($dup) {
                return json(['code' => 400, 'msg' => '版本号已存在']);
            }
            ServerVersion::create([
                'version' => $version,
                'content' => $content,
                'is_active' => 1,
            ]);
        }

        return json(['code' => 200, 'msg' => '保存成功']);
    }

    public function deleteVersion()
    {
        if ($error = $this->requireCurrentAdminPassword()) {
            return $error;
        }

        $id = (int)request()->post('id', 0);
        if ($id <= 0) {
            return json(['code' => 400, 'msg' => '参数错误']);
        }

        ServerVersion::where('id', $id)->delete();
        return json(['code' => 200, 'msg' => '已删除']);
    }

    public function toggleVersion()
    {
        if ($error = $this->requireCurrentAdminPassword()) {
            return $error;
        }

        $id = (int)request()->post('id', 0);
        if ($id <= 0) {
            return json(['code' => 400, 'msg' => '参数错误']);
        }

        $version = ServerVersion::find($id);
        if (!$version) {
            return json(['code' => 400, 'msg' => '版本不存在']);
        }

        ServerVersion::where('id', $id)->update([
            'is_active' => $version['is_active'] ? 0 : 1,
        ]);
        return json(['code' => 200, 'msg' => $version['is_active'] ? '已禁用' : '已启用']);
    }

    private function requireCurrentAdminPassword()
    {
        $password = (string)request()->post('admin_password', '');
        if ($password === '') {
            return json(['code' => 403, 'msg' => '请输入管理员密码确认']);
        }

        $sessionUser = AuthMiddleware::currentAuthenticatedUser() ?: session('user');
        $userId = (int)($sessionUser['id'] ?? 0);
        if ($userId <= 0) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }

        $admin = User::find($userId);
        if (!$admin || (string)($admin['role'] ?? '') !== 'admin') {
            return json(['code' => 403, 'msg' => '无管理员权限']);
        }

        $verifyResult = verifyPassword($password, (string)$admin['password']);
        if ($verifyResult === false) {
            return json(['code' => 403, 'msg' => '管理员密码错误']);
        }

        if (is_string($verifyResult)) {
            User::where('id', $userId)->update(['password' => $verifyResult]);
        }

        return null;
    }
}
