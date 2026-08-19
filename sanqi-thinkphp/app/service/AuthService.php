<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\service;

use app\model\User as UserModel;

/**
 * 认证服务 — Token/Cookie/Session 统一管理
 */
class AuthService
{
    // ============================================================
    // Token 操作
    // ============================================================

    /**
     * 验证 auth_token，返回 User 模型或 null
     */
    public static function verifyAuthToken(string $token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        [$dataB64, $signature] = $parts;
        $expectedSig = hash_hmac('sha256', $dataB64, self::getAuthKey());
        if (!hash_equals($expectedSig, $signature)) {
            return null;
        }

        $decoded = base64_decode($dataB64, true);
        if ($decoded === false) {
            return null;
        }

        $colonPos = strpos($decoded, ':');
        if ($colonPos === false) {
            return null;
        }

        $userId = (int)substr($decoded, 0, $colonPos);
        $passid = substr($decoded, $colonPos + 1);
        if ($userId <= 0 || $passid === '') {
            return null;
        }

        return UserModel::where('id', $userId)->where('passid', $passid)->find();
    }

    /**
     * 生成 auth_token 字符串
     */
    public static function makeAuthToken(int $userId, string $passid): string
    {
        $dataB64 = base64_encode($userId . ':' . $passid);
        $signature = hash_hmac('sha256', $dataB64, self::getAuthKey());
        return $dataB64 . '.' . $signature;
    }

    // ============================================================
    // Cookie 操作
    // ============================================================

    /**
     * 设置认证 Cookie
     */
    public static function setAuthCookie(int $userId, string $passid): void
    {
        setcookie('auth_token', self::makeAuthToken($userId, $passid), [
            'expires'  => time() + 604800,
            'path'     => '/',
            'httponly'  => true,
            'samesite' => 'Lax',
            'secure'   => self::isSecure(),
        ]);
    }

    /**
     * 判断当前请求是否为 HTTPS（支持反向代理场景）
     */
    private static function isSecure(): bool
    {
        // 显式配置覆盖（config/app.php 中设置 'app_secure' => true）
        $configured = config('app.app_secure');
        if ($configured !== null) {
            return (bool) $configured;
        }

        // 标准 PHP 环境
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        if (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
            return true;
        }

        // 反向代理头（需确保 web 服务器已配置信任这些头）
        $proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
        if (strtolower($proto) === 'https') {
            return true;
        }

        return false;
    }

    /**
     * 清除认证 Cookie
     */
    public static function clearAuthCookie(): void
    {
        setcookie('auth_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly'  => true,
            'samesite' => 'Lax',
            'secure'   => self::isSecure(),
        ]);
        unset($_COOKIE['auth_token']);
    }

    // ============================================================
    // Session 操作
    // ============================================================

    /**
     * 将用户信息写入 Session（仅安全字段）
     */
    public static function writeUserSession(array $user): void
    {
        session('user', [
            'id'       => $user['id'],
            'username' => $user['username'],
            'name'     => $user['name'],
            'img'      => $user['img'],
            'sign'     => $user['sign'],
            'email'    => $user['email'],
            'essqx'    => $user['essqx'],
            'esseam'   => $user['esseam'],
            'passid'   => $user['passid'] ?? '',
        ]);
    }

    /**
     * 登录后重新生成 Session ID，防止 Session 固定攻击。
     * 成功返回 true；如果 Session 驱动不支持则返回 false。
     */
    public static function regenerateSession(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        $oldId = session_id();
        try {
            session_regenerate_id(true);
        } catch (\Throwable $e) {
            return false;
        }

        return session_id() !== $oldId;
    }

    /**
     * 清除登录状态（Cookie + Session）
     */
    public static function clearLogin(): void
    {
        self::clearAuthCookie();
        session('user', null);
        session('admin_2fa_pending', null);
        session('admin_2fa_verified', null);
    }

    // ============================================================
    // 辅助方法
    // ============================================================

    /**
     * 获取 HMAC 签名密钥
     */
    public static function getAuthKey(): string
    {
        static $key = null;
        if ($key !== null) {
            return $key;
        }

        $key = config('app.app_key');
        if (empty($key)) {
            $key = env('APP_KEY', '');
        }
        if (empty($key)) {
            throw new \RuntimeException('应用密钥 APP_KEY 未配置，请在 .env 文件中设置 APP_KEY');
        }

        return $key;
    }

    /**
     * 将模型或数组统一转为数组
     */
    public static function toArray($user): array
    {
        if ($user instanceof \think\Model) {
            return $user->toArray();
        }
        return (array)$user;
    }
}
