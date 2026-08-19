<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

class ContentFeatureService
{
    const CONFIG_KEY = 'content_features';
    protected static $configCache = null;

    public static function getConfig()
    {
        if (self::$configCache !== null) {
            return self::$configCache;
        }

        $row = \app\model\Configx::where('title', self::CONFIG_KEY)->find();
        $config = !empty($row['text']) ? json_decode($row['text'], true) : [];
        if (!is_array($config)) {
            $config = [];
        }

        self::$configCache = array_merge([
            'drafts_enabled' => 0,
            'tags_enabled' => 0,
        ], $config);

        return self::$configCache;
    }

    public static function saveConfig(array $config)
    {
        self::$configCache = null;
        $current = self::getConfig();
        $config = array_merge($current, [
            'drafts_enabled' => !empty($config['drafts_enabled']) ? 1 : 0,
            'tags_enabled' => !empty($config['tags_enabled']) ? 1 : 0,
        ]);

        $json = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $exists = \app\model\Configx::where('title', self::CONFIG_KEY)->find();
        if ($exists) {
            \app\model\Configx::where('title', self::CONFIG_KEY)->update(['text' => $json]);
        } else {
            \app\model\Configx::create(['title' => self::CONFIG_KEY, 'text' => $json]);
        }
    }

    public static function draftsEnabled()
    {
        $config = self::getConfig();
        return !empty($config['drafts_enabled']);
    }

    public static function tagsEnabled()
    {
        $config = self::getConfig();
        return !empty($config['tags_enabled']);
    }
}
