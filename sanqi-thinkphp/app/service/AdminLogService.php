<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

class AdminLogService
{
    const OPERATION_PREFIX = 'admin_operation_';
    const LOGIN_PREFIX = 'admin_login_';

    public static function operation($action, $target = '', array $context = [])
    {
        self::write(self::OPERATION_PREFIX . date('Y-m-d') . '.log', [
            'time' => date('Y-m-d H:i:s'),
            'admin' => self::currentUsername(),
            'ip' => self::clientIp(),
            'action' => (string)$action,
            'target' => (string)$target,
            'context' => self::filterContext($context),
        ]);
    }

    public static function login($ip, $username, $status, $reason = '')
    {
        self::write(self::LOGIN_PREFIX . date('Y-m-d') . '.log', [
            'time' => date('Y-m-d H:i:s'),
            'status' => (string)$status,
            'ip' => (string)$ip,
            'username' => (string)$username,
            'reason' => (string)$reason,
        ]);
    }

    public static function read($type = 'operation', $limit = 200)
    {
        if ($type === 'login') {
            $files = array_merge(
                glob(app()->getRuntimePath() . 'log/' . self::LOGIN_PREFIX . '*.log') ?: [],
                glob(app()->getRuntimePath() . 'log/admin_????-??-??.log') ?: []
            );
        } else {
            $files = glob(app()->getRuntimePath() . 'log/' . self::OPERATION_PREFIX . '*.log') ?: [];
        }
        rsort($files, SORT_STRING);

        $rows = [];
        foreach ($files as $file) {
            $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            for ($i = count($lines) - 1; $i >= 0; $i--) {
                $row = json_decode($lines[$i], true);
                if (!is_array($row)) {
                    $row = self::parseLegacyLoginLine($lines[$i]);
                }
                if (is_array($row)) {
                    $rows[] = $row;
                }
                if (count($rows) >= $limit) {
                    return $rows;
                }
            }
        }

        return $rows;
    }

    protected static function write($filename, array $payload)
    {
        $logDir = app()->getRuntimePath() . 'log/';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        @file_put_contents(
            $logDir . $filename,
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }

    protected static function currentUsername()
    {
        $user = session('user');
        return $user['username'] ?? '';
    }

    protected static function clientIp()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    protected static function filterContext(array $context)
    {
        $secretKeys = ['password', 'pass', 'key', 'secret', 'token', 'operatorPassword', 'accessKeySecret', 'emkey', 'aliyun_secret'];
        foreach ($context as $key => $value) {
            foreach ($secretKeys as $secretKey) {
                if (stripos((string)$key, $secretKey) !== false) {
                    $context[$key] = '***';
                    continue 2;
                }
            }
            if (is_array($value)) {
                $context[$key] = self::filterContext($value);
            } elseif (is_string($value) && strlen($value) > 300) {
                $context[$key] = substr($value, 0, 300) . '...';
            }
        }
        return $context;
    }

    protected static function parseLegacyLoginLine($line)
    {
        if (!preg_match('/^([^|]+)\s+\|\s+([^|]+)\s+\|\s+IP:([^|]*)\s+\|\s+User:([^|]*)(?:\s+\|\s+Reason:(.*))?$/', $line, $m)) {
            return null;
        }

        return [
            'time' => trim($m[1]),
            'status' => trim($m[2]),
            'ip' => trim($m[3]),
            'username' => trim($m[4]),
            'reason' => trim($m[5] ?? ''),
        ];
    }
}
