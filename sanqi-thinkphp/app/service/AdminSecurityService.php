<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

class AdminSecurityService
{
    public static function getTwoFactorConfig()
    {
        $row = \app\model\Configx::where('title', 'admin_2fa')->find();
        $config = ($row && !empty($row['text'])) ? json_decode($row['text'], true) : [];
        if (!is_array($config)) {
            $config = [];
        }

        return array_merge([
            'enabled' => 0,
            'method' => 'email',
            'totp_secret' => '',
        ], $config);
    }

    public static function requiresTwoFactor(array $user): bool
    {
        if (($user['role'] ?? '') !== 'admin') {
            return false;
        }

        $config = self::getTwoFactorConfig();
        return !empty($config['enabled']);
    }

    public static function isTwoFactorVerified(array $user): bool
    {
        if (!self::requiresTwoFactor($user)) {
            return true;
        }

        $verified = session('admin_2fa_verified');
        if (!is_array($verified)) {
            return false;
        }

        return (int)($verified['user_id'] ?? 0) === (int)($user['id'] ?? 0)
            && hash_equals((string)($verified['passid'] ?? ''), (string)($user['passid'] ?? ''));
    }

    public static function markTwoFactorVerified(array $user): void
    {
        session('admin_2fa_verified', [
            'user_id' => (int)($user['id'] ?? 0),
            'passid' => (string)($user['passid'] ?? ''),
            'verified_at' => time(),
        ]);
    }

    public static function clearTwoFactorVerified(): void
    {
        session('admin_2fa_verified', null);
    }

    public static function saveTwoFactorConfig(array $config)
    {
        $current = self::getTwoFactorConfig();
        $config = array_merge($current, $config);
        $config['enabled'] = !empty($config['enabled']) ? 1 : 0;
        $config['method'] = in_array($config['method'] ?? 'email', ['email', 'totp'], true) ? $config['method'] : 'email';

        $json = json_encode($config, JSON_UNESCAPED_UNICODE);
        $exists = \app\model\Configx::where('title', 'admin_2fa')->find();
        if ($exists) {
            \app\model\Configx::where('title', 'admin_2fa')->update(['text' => $json]);
        } else {
            \app\model\Configx::create(['title' => 'admin_2fa', 'text' => $json]);
        }
    }

    public static function verifyAdminPassword($password)
    {
        $admin = \app\model\User::where('essqx', 2)->find();
        if (!$admin) {
            return false;
        }

        $verifyResult = verifyPassword((string)$password, (string)$admin['password']);
        if ($verifyResult === false) {
            return false;
        }

        // 旧 MD5 哈希自动升级
        if (is_string($verifyResult)) {
            \app\model\User::where('id', $admin['id'])->update(['password' => $verifyResult]);
        }

        return true;
    }

    public static function startPendingLogin(array $user)
    {
        $token = bin2hex(random_bytes(16));
        session('admin_2fa_pending', [
            'token' => $token,
            'user_id' => (int)$user['id'],
            'username' => $user['username'],
            'created_at' => time(),
            'email_code_hash' => '',
            'email_code_expires' => 0,
        ]);
        return $token;
    }

    public static function getPendingLogin($token)
    {
        $pending = session('admin_2fa_pending');
        if (empty($pending['token']) || !hash_equals((string)$pending['token'], (string)$token)) {
            return null;
        }
        if (empty($pending['created_at']) || (time() - (int)$pending['created_at']) > 600) {
            session('admin_2fa_pending', null);
            return null;
        }
        return $pending;
    }

    public static function setPendingEmailCode($code)
    {
        $pending = session('admin_2fa_pending');
        if (!$pending) {
            return;
        }
        $pending['email_code_hash'] = password_hash((string)$code, PASSWORD_DEFAULT);
        $pending['email_code_expires'] = time() + 300;
        session('admin_2fa_pending', $pending);
    }

    public static function verifyPendingEmailCode($code, array $pending)
    {
        if (empty($pending['email_code_hash']) || empty($pending['email_code_expires'])) {
            return false;
        }
        if ((int)$pending['email_code_expires'] < time()) {
            return false;
        }
        return password_verify((string)$code, (string)$pending['email_code_hash']);
    }

    public static function clearPendingLogin()
    {
        session('admin_2fa_pending', null);
    }

    public static function generateTotpSecret($length = 20)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $secret;
    }

    public static function verifyTotp($secret, $code, $window = 1)
    {
        $code = preg_replace('/\s+/', '', (string)$code);
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $timeSlice = (int)floor(time() / 30);
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::totpCode($secret, $timeSlice + $i), $code)) {
                return true;
            }
        }
        return false;
    }

    public static function totpCode($secret, $timeSlice)
    {
        $key = self::base32Decode($secret);
        if ($key === '') {
            return '000000';
        }

        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $key, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $value = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        return str_pad((string)(intval($value) % 1000000), 6, '0', STR_PAD_LEFT);
    }

    protected static function base32Decode($secret)
    {
        $secret = strtoupper(preg_replace('/[^A-Z2-7]/', '', (string)$secret));
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        $out = '';

        for ($i = 0; $i < strlen($secret); $i++) {
            $value = strpos($alphabet, $secret[$i]);
            if ($value === false) {
                continue;
            }
            $bits .= str_pad(decbin($value), 5, '0', STR_PAD_LEFT);
        }

        for ($i = 0; $i + 8 <= strlen($bits); $i += 8) {
            $out .= chr(bindec(substr($bits, $i, 8)));
        }

        return $out;
    }
}
