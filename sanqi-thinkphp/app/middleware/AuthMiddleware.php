<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\middleware;

use app\model\User as UserModel;
use app\service\AuthService;

abstract class AuthMiddleware
{
    protected static $requestUser = null;

    protected function authenticate()
    {
        if (self::$requestUser !== null) {
            return ['status' => 'ok', 'user' => self::$requestUser];
        }

        $sessionUser = session('user');
        if ($sessionUser && isset($sessionUser['id'])) {
            $user = UserModel::find((int)$sessionUser['id']);
            if ($this->isSessionUserValid($user, $sessionUser)) {
                self::cacheAuthenticatedUser($user);
                return ['status' => 'ok', 'user' => self::$requestUser];
            }

            $this->clearLogin();
            return ['status' => 'invalid', 'user' => null];
        }

        $token = cookie('auth_token');
        if (!$token) {
            return ['status' => 'missing', 'user' => null];
        }

        $user = AuthService::verifyAuthToken($token);
        if (!$user) {
            $this->clearLogin();
            return ['status' => 'invalid', 'user' => null];
        }

        $userArray = AuthService::toArray($user);
        AuthService::writeUserSession($userArray);
        self::cacheAuthenticatedUser($userArray);
        return ['status' => 'ok', 'user' => self::$requestUser];
    }

    public static function currentAuthenticatedUser()
    {
        return self::$requestUser;
    }

    protected static function cacheAuthenticatedUser($user)
    {
        self::$requestUser = AuthService::toArray($user);
    }

    protected function isSessionUserValid($dbUser, array $sessionUser)
    {
        return $dbUser
            && !empty($sessionUser['passid'])
            && hash_equals((string)($dbUser['passid'] ?? ''), (string)$sessionUser['passid']);
    }

    protected function clearLogin()
    {
        self::$requestUser = null;
        AuthService::clearLogin();
    }

    protected function banState(array $user)
    {
        if ((int)($user['ban'] ?? 0) === 0) {
            return ['banned' => false, 'expired' => false, 'message' => ''];
        }

        $banTime = $user['bantime'] ?? '';
        if ($banTime === 'true' || $banTime === '' || $banTime === null) {
            return ['banned' => true, 'expired' => false, 'message' => '您的账号已被永久封禁'];
        }

        $ts = strtotime($banTime);
        if ($ts !== false && $ts > time()) {
            return ['banned' => true, 'expired' => false, 'message' => '您的账号已被封禁至' . $banTime];
        }

        return ['banned' => false, 'expired' => true, 'message' => ''];
    }

    protected function clearExpiredBan(array $user)
    {
        if (!empty($user['id'])) {
            UserModel::where('id', (int)$user['id'])->update(['ban' => 0, 'bantime' => '']);
        }
    }

    protected function enforceBan($request, array $user, array $options = [])
    {
        $ban = $this->banState($user);
        if ($ban['expired']) {
            $this->clearExpiredBan($user);
            return true;
        }

        if (!$ban['banned']) {
            return true;
        }

        if (!empty($options['clear_login'])) {
            $this->clearLogin();
        }

        $mode = $options['mode'] ?? 'auto';
        if ($mode === 'json' || ($mode === 'auto' && $request->isAjax())) {
            return $this->jsonError($ban['message'], 403);
        }

        return redirect($options['redirect'] ?? '/?banned=1');
    }

    protected function jsonError($msg, int $httpCode = 401)
    {
        return json(['code' => (string)$httpCode, 'msg' => $msg], $httpCode);
    }

    protected function rejectLogin($request, $status, $redirect)
    {
        if ($request->isAjax()) {
            return $this->jsonError($status === 'missing' ? '请先登录' : '登录已过期，请重新登录', 401);
        }

        return redirect($redirect);
    }

    // 委托给 AuthService（保留以兼容旧代码）
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
}
