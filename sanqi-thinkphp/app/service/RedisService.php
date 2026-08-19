<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

class RedisService
{
    protected static $client = false;

    public static function client()
    {
        if (self::$client !== false) {
            return self::$client;
        }

        self::$client = null;
        $config = config('cache.stores.redis');
        if (empty($config) || !class_exists('\Redis')) {
            return null;
        }

        try {
            $redis = new \Redis();
            $timeout = (float)($config['timeout'] ?? 1.0);
            $host = (string)($config['host'] ?? '127.0.0.1');
            $port = (int)($config['port'] ?? 6379);
            if (!$redis->connect($host, $port, $timeout)) {
                return null;
            }

            if (!empty($config['password'])) {
                $redis->auth((string)$config['password']);
            }

            if (isset($config['select'])) {
                $redis->select((int)$config['select']);
            }

            self::$client = $redis;
        } catch (\Throwable $e) {
            self::$client = null;
        }

        return self::$client;
    }

    public static function prefix()
    {
        return (string)config('cache.stores.redis.prefix', 'sanqi:');
    }

    public static function key($key)
    {
        return self::prefix() . (string)$key;
    }

    public static function available()
    {
        return self::client() !== null;
    }
}
