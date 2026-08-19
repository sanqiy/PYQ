<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

class CommentSecurityService
{
    const CONFIG_KEY = 'comment_security';
    protected static $configCache = null;

    public static function getConfig()
    {
        if (self::$configCache !== null) {
            return self::$configCache;
        }

        $row = \app\model\Configx::where('title', self::CONFIG_KEY)->find();
        $config = ($row && !empty($row['text'])) ? json_decode($row['text'], true) : [];
        if (!is_array($config)) {
            $config = [];
        }

        self::$configCache = array_merge([
            'audit_enabled' => 0,
            'keywords' => '',
            'blacklist' => '',
            'rate_limit_enabled' => 1,
            'rate_limit_max' => 10,
            'rate_limit_window' => 60,
            'rate_limit_lock' => 300,
        ], $config);

        return self::$configCache;
    }

    public static function saveConfig(array $config)
    {
        self::$configCache = null;
        $current = self::getConfig();
        $config = array_merge($current, [
            'audit_enabled' => !empty($config['audit_enabled']) ? 1 : 0,
            'keywords' => self::normalizeLines($config['keywords'] ?? ''),
            'blacklist' => self::normalizeLines($config['blacklist'] ?? ''),
            'rate_limit_enabled' => array_key_exists('rate_limit_enabled', $config) ? (!empty($config['rate_limit_enabled']) ? 1 : 0) : ($current['rate_limit_enabled'] ?? 1),
            'rate_limit_max' => self::positiveInt($config['rate_limit_max'] ?? ($current['rate_limit_max'] ?? 10), 10),
            'rate_limit_window' => self::positiveInt($config['rate_limit_window'] ?? ($current['rate_limit_window'] ?? 60), 60),
            'rate_limit_lock' => self::positiveInt($config['rate_limit_lock'] ?? ($current['rate_limit_lock'] ?? 300), 300),
        ]);

        $json = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $exists = \app\model\Configx::where('title', self::CONFIG_KEY)->find();
        if ($exists) {
            \app\model\Configx::where('title', self::CONFIG_KEY)->update(['text' => $json]);
        } else {
            \app\model\Configx::create(['title' => self::CONFIG_KEY, 'text' => $json]);
        }
    }

    public static function addBlacklistItems(array $items): array
    {
        self::$configCache = null;
        $config = self::getConfig();
        $current = self::lines($config['blacklist'] ?? '');
        $added = [];

        foreach ($items as $item) {
            $item = trim((string)$item);
            if ($item === '' || in_array($item, $current, true)) {
                continue;
            }
            $current[] = $item;
            $added[] = $item;
        }

        if ($added) {
            self::saveConfig(array_merge($config, [
                'blacklist' => implode("\n", $current),
            ]));
        }

        return $added;
    }

    public static function keywordHits(string $text): array
    {
        $config = self::getConfig();
        $hits = [];
        foreach (self::lines($config['keywords'] ?? '') as $keyword) {
            if ($keyword !== '' && mb_stripos($text, $keyword, 0, 'UTF-8') !== false) {
                $hits[] = $keyword;
            }
        }
        return $hits;
    }

    public static function evaluate($text, array $identity, $isAdmin = false)
    {
        if ($isAdmin) {
            return ['allowed' => true, 'audit' => false, 'reason' => ''];
        }

        $config = self::getConfig();
        $blacklistHit = self::firstMatch(self::lines($config['blacklist'] ?? ''), [
            $identity['username'] ?? '',
            $identity['name'] ?? '',
            $identity['email'] ?? '',
            $identity['url'] ?? '',
            $identity['ip'] ?? '',
        ]);

        if ($blacklistHit !== '') {
            return ['allowed' => false, 'audit' => false, 'reason' => '评论命中黑名单，已拦截'];
        }

        $keywordHit = self::firstMatch(self::lines($config['keywords'] ?? ''), [(string)$text]);
        $audit = !empty($config['audit_enabled']) || $keywordHit !== '';

        return ['allowed' => true, 'audit' => $audit, 'reason' => $keywordHit !== '' ? '命中关键词' : ''];
    }

    protected static function normalizeLines($value)
    {
        $lines = self::lines($value);
        return implode("\n", $lines);
    }

    public static function assertRateLimit($ip, $isAdmin = false)
    {
        if ($isAdmin) {
            return;
        }

        $config = self::getConfig();
        if (empty($config['rate_limit_enabled'])) {
            return;
        }

        $ip = self::normalizeIp($ip);
        RateLimitService::assertAllowed(
            'comment_ip:' . $ip,
            self::positiveInt($config['rate_limit_max'] ?? 10, 10),
            self::positiveInt($config['rate_limit_window'] ?? 60, 60),
            '评论太频繁，请{minutes}分钟后再试',
            self::positiveInt($config['rate_limit_lock'] ?? 300, 300)
        );
    }

    public static function hitRateLimit($ip, $isAdmin = false)
    {
        if ($isAdmin) {
            return;
        }

        $config = self::getConfig();
        if (empty($config['rate_limit_enabled'])) {
            return;
        }

        $ip = self::normalizeIp($ip);
        RateLimitService::hit(
            'comment_ip:' . $ip,
            self::positiveInt($config['rate_limit_window'] ?? 60, 60),
            self::positiveInt($config['rate_limit_lock'] ?? 300, 300),
            self::positiveInt($config['rate_limit_max'] ?? 10, 10)
        );
    }

    protected static function normalizeIp($ip)
    {
        $ip = trim((string)$ip);
        return $ip !== '' ? $ip : 'unknown';
    }

    protected static function positiveInt($value, $default)
    {
        $value = (int)$value;
        return $value > 0 ? $value : (int)$default;
    }

    protected static function lines($value)
    {
        $parts = preg_split('/\r\n|\r|\n|,|，/', (string)$value);
        $lines = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '' && !in_array($part, $lines, true)) {
                $lines[] = $part;
            }
        }
        return $lines;
    }

    protected static function firstMatch(array $rules, array $values)
    {
        foreach ($rules as $rule) {
            foreach ($values as $value) {
                if ($rule !== '' && $value !== '' && mb_stripos((string)$value, $rule, 0, 'UTF-8') !== false) {
                    return $rule;
                }
            }
        }
        return '';
    }
}
