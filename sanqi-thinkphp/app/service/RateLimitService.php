<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

class RateLimitService
{
    protected static $path = '';
    protected static $cleanupProbability = 100;

    public static function check($key, $maxAttempts, $windowSeconds, $lockSeconds = null)
    {
        $record = RedisService::available() ? self::readRedis($key) : self::readFileRecord($key);
        return self::checkRecord($key, $record, $maxAttempts, $windowSeconds, $lockSeconds);
    }

    public static function hit($key, $windowSeconds, $lockSeconds = null, $maxAttempts = null)
    {
        if (RedisService::available()) {
            return self::hitRedis($key, $windowSeconds, $lockSeconds, $maxAttempts);
        }

        self::maybeCleanup();
        return self::withFileLock($key, function ($file) use ($windowSeconds, $lockSeconds, $maxAttempts) {
            $record = self::nextRecord(self::readFile($file), $windowSeconds, $lockSeconds, $maxAttempts);
            self::writeFile($file, $record);
            return $record;
        });
    }

    public static function clear($key)
    {
        if (RedisService::available()) {
            RedisService::client()->del(self::redisKey($key));
            return;
        }

        $file = self::file($key);
        if (file_exists($file)) {
            @unlink($file);
        }
        $lockFile = $file . '.lock';
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }
    }

    public static function assertAllowed($key, $maxAttempts, $windowSeconds, $message, $lockSeconds = null)
    {
        $state = self::check($key, $maxAttempts, $windowSeconds, $lockSeconds);
        if ($state['allowed']) {
            return;
        }

        $minutes = max(1, (int)ceil($state['retry_after'] / 60));
        throw new \RuntimeException(str_replace('{minutes}', (string)$minutes, $message));
    }

    public static function cleanup($maxFiles = 1000)
    {
        self::ensurePath();
        self::withCleanupLock(function () use ($maxFiles) {
            self::cleanupExpired($maxFiles);
        });
    }

    protected static function cleanupExpired($maxFiles = 1000)
    {
        $now = time();
        $checked = 0;
        foreach (glob(self::$path . '*.json') ?: [] as $file) {
            if ($checked++ >= $maxFiles) {
                break;
            }

            $record = self::readFile($file);
            $expiresAt = intval($record['expires_at'] ?? 0);
            $fallbackExpired = filemtime($file) !== false && filemtime($file) < ($now - 86400);
            if (($expiresAt > 0 && $expiresAt <= $now) || ($expiresAt <= 0 && $fallbackExpired)) {
                @unlink($file);
                @unlink($file . '.lock');
            }
        }

        foreach (glob(self::$path . '*.json.lock') ?: [] as $lockFile) {
            $dataFile = substr($lockFile, 0, -5);
            if (!file_exists($dataFile) && filemtime($lockFile) !== false && filemtime($lockFile) < ($now - 3600)) {
                @unlink($lockFile);
            }
        }
    }

    protected static function checkRecord($key, array $record, $maxAttempts, $windowSeconds, $lockSeconds = null)
    {
        $now = time();

        if (!empty($record['locked_until']) && $record['locked_until'] > $now) {
            return [
                'allowed' => false,
                'remaining' => 0,
                'retry_after' => $record['locked_until'] - $now,
                'locked' => true,
            ];
        }

        if (empty($record['start']) || ($record['start'] + $windowSeconds) <= $now) {
            return [
                'allowed' => true,
                'remaining' => $maxAttempts,
                'retry_after' => 0,
                'locked' => false,
            ];
        }

        $count = intval($record['count'] ?? 0);
        if ($count >= $maxAttempts) {
            $retryAfter = max(1, ($record['start'] + $windowSeconds) - $now);
            if ($lockSeconds !== null && $lockSeconds > $retryAfter) {
                $record['locked_until'] = $now + $lockSeconds;
                self::writeRecord($key, $record, $windowSeconds, $lockSeconds);
                $retryAfter = $lockSeconds;
            }

            return [
                'allowed' => false,
                'remaining' => 0,
                'retry_after' => $retryAfter,
                'locked' => true,
            ];
        }

        return [
            'allowed' => true,
            'remaining' => $maxAttempts - $count,
            'retry_after' => 0,
            'locked' => false,
        ];
    }

    protected static function nextRecord(array $record, $windowSeconds, $lockSeconds = null, $maxAttempts = null)
    {
        $now = time();
        if (empty($record['start']) || ($record['start'] + $windowSeconds) <= $now) {
            $record = [
                'start' => $now,
                'count' => 1,
                'locked_until' => 0,
                'expires_at' => $now + self::recordTtl($windowSeconds, $lockSeconds),
            ];
        } else {
            $record['count'] = intval($record['count'] ?? 0) + 1;
            $record['expires_at'] = $now + self::recordTtl($windowSeconds, $lockSeconds);
        }

        if ($maxAttempts !== null && $lockSeconds !== null && $record['count'] >= $maxAttempts) {
            $record['locked_until'] = $now + $lockSeconds;
            $record['expires_at'] = max($record['expires_at'], $record['locked_until'] + 60);
        }

        return $record;
    }

    protected static function readFileRecord($key)
    {
        self::maybeCleanup();
        return self::readFile(self::file($key));
    }

    protected static function writeRecord($key, array $record, $windowSeconds, $lockSeconds = null)
    {
        if (RedisService::available()) {
            self::writeRedis($key, $record);
            return;
        }

        self::write($key, $record);
    }

    protected static function hitRedis($key, $windowSeconds, $lockSeconds = null, $maxAttempts = null)
    {
        $record = self::nextRecord(self::readRedis($key), $windowSeconds, $lockSeconds, $maxAttempts);
        self::writeRedis($key, $record);
        return $record;
    }

    protected static function readRedis($key)
    {
        $value = RedisService::client()->get(self::redisKey($key));
        if ($value === false || $value === null) {
            return [];
        }

        $data = json_decode((string)$value, true);
        return is_array($data) ? $data : [];
    }

    protected static function writeRedis($key, array $record)
    {
        $now = time();
        $ttl = max(1, intval($record['expires_at'] ?? ($now + 300)) - $now);
        RedisService::client()->setex(self::redisKey($key), $ttl, json_encode($record, JSON_UNESCAPED_UNICODE));
    }

    protected static function redisKey($key)
    {
        return RedisService::key('ratelimit:' . hash('sha256', (string)$key));
    }

    protected static function write($key, array $data)
    {
        self::withFileLock($key, function ($file) use ($data) {
            self::writeFile($file, $data);
        });
    }

    protected static function readFile($file)
    {
        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        $data = json_decode((string)$content, true);
        return is_array($data) ? $data : [];
    }

    protected static function writeFile($file, array $data)
    {
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    protected static function withFileLock($key, callable $callback)
    {
        $file = self::file($key);
        $lockFile = $file . '.lock';
        $handle = fopen($lockFile, 'c');
        if ($handle === false) {
            return $callback($file);
        }

        try {
            flock($handle, LOCK_EX);
            return $callback($file);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    protected static function file($key)
    {
        self::ensurePath();
        return self::$path . hash('sha256', (string)$key) . '.json';
    }

    protected static function ensurePath()
    {
        if (self::$path === '') {
            self::$path = app()->getRuntimePath() . 'ratelimit/';
            if (!is_dir(self::$path)) {
                mkdir(self::$path, 0755, true);
            }
        }
    }

    protected static function maybeCleanup()
    {
        self::ensurePath();
        if (random_int(1, self::$cleanupProbability) === 1) {
            self::withCleanupLock(function () {
                self::cleanupExpired(200);
            }, false);
        }
    }

    protected static function withCleanupLock(callable $callback, $blocking = true)
    {
        $lockFile = self::$path . '_cleanup.lock';
        $handle = fopen($lockFile, 'c');
        if ($handle === false) {
            return $callback();
        }

        $lockMode = $blocking ? LOCK_EX : (LOCK_EX | LOCK_NB);
        if (!flock($handle, $lockMode)) {
            fclose($handle);
            return null;
        }

        try {
            return $callback();
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    protected static function recordTtl($windowSeconds, $lockSeconds = null)
    {
        return max((int)$windowSeconds, (int)($lockSeconds ?? 0)) + 300;
    }
}
