<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\traits;

use app\service\AuthService;

trait AuthTrait
{
    protected function getUser()
    {
        static $resolved = false;
        static $cachedUser = null;

        if ($resolved) {
            return $cachedUser;
        }

        $resolved = true;

        // 优先检查中间件已认证的用户
        if (class_exists('\\app\\middleware\\AuthMiddleware') && method_exists('\\app\\middleware\\AuthMiddleware', 'currentAuthenticatedUser')) {
            $requestUser = \app\middleware\AuthMiddleware::currentAuthenticatedUser();
            if ($requestUser) {
                $cachedUser = $requestUser;
                return $cachedUser;
            }
        }

        // 检查 Session
        $user = session('user');
        if ($user) {
            if (empty($user['id']) || empty($user['passid'])) {
                AuthService::clearLogin();
                $cachedUser = null;
                return null;
            }

            $dbUser = \app\model\User::where('id', (int)$user['id'])->find();
            if (!$dbUser || !hash_equals((string)($dbUser['passid'] ?? ''), (string)$user['passid'])) {
                AuthService::clearLogin();
                $cachedUser = null;
                return null;
            }

            $cachedUser = AuthService::toArray($dbUser);
            return $cachedUser;
        }

        // 检查 Cookie 认证令牌
        $token = cookie('auth_token');
        if (!$token) {
            $cachedUser = null;
            return null;
        }

        $user = AuthService::verifyAuthToken($token);
        if (!$user) {
            $cachedUser = null;
            return null;
        }

        $userArray = AuthService::toArray($user);
        AuthService::writeUserSession($userArray);
        try { if (session_status() === PHP_SESSION_ACTIVE) session_regenerate_id(true); } catch (\Throwable $e) {}

        $cachedUser = $userArray;
        return $cachedUser;
    }

    public static function verifyAuthToken($token)
    {
        return AuthService::verifyAuthToken($token);
    }

    public static function makeAuthToken($userId, $passid)
    {
        return AuthService::makeAuthToken($userId, $passid);
    }

    public static function setAuthCookie($userId, $passid)
    {
        AuthService::setAuthCookie($userId, $passid);
    }

    public static function clearAuthCookie()
    {
        AuthService::clearAuthCookie();
    }

    protected static function getAuthKey()
    {
        return AuthService::getAuthKey();
    }

    protected function getUsername()
    {
        $user = $this->getUser();
        return $user ? $user['username'] : null;
    }

    protected function isLogin()
    {
        return $this->getUser() !== null;
    }

    protected function isAdmin()
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }

        return (string)($user['role'] ?? '') === 'admin';
    }

    protected function requireLogin()
    {
        if (!$this->isLogin()) {
            if (request()->isAjax()) {
                return $this->error('请先登录');
            } else {
                return redirect('/?login=1');
            }
        }

        return true;
    }

    protected function requireAdmin()
    {
        if (!$this->isAdmin()) {
            return $this->error('无权限访问');
        }

        return true;
    }
}
