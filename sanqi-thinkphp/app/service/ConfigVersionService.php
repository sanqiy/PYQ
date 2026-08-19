<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

use app\model\Configx;

class ConfigVersionService
{
    const KEY = 'admin_config_versions';
    const MAX_VERSIONS = 30;

    public static function all()
    {
        $row = Configx::where('title', self::KEY)->find();
        $versions = !empty($row['text']) ? json_decode($row['text'], true) : [];
        return is_array($versions) ? $versions : [];
    }

    public static function snapshot($note, $operator = '')
    {
        $config = SiteConfigService::getAll();
        $versions = self::all();
        array_unshift($versions, [
            'id' => date('YmdHis') . '_' . substr(md5(json_encode($config) . microtime(true)), 0, 8),
            'note' => (string)$note,
            'operator' => (string)$operator,
            'created_at' => date('Y-m-d H:i:s'),
            'config' => $config,
        ]);
        $versions = array_slice($versions, 0, self::MAX_VERSIONS);
        self::saveAll($versions);
        return $versions[0];
    }

    public static function find($id)
    {
        foreach (self::all() as $version) {
            if (($version['id'] ?? '') === $id) {
                return $version;
            }
        }
        return null;
    }

    public static function restore($id)
    {
        $version = self::find($id);
        if (!$version || empty($version['config']) || !is_array($version['config'])) {
            return false;
        }
        $config = $version['config'];
        // 兼容旧快照：移除 admin 表特有字段
        unset($config['id'], $config['username']);
        // 仅写入已知站点字段
        $filtered = array_intersect_key($config, array_flip(SiteConfigService::SITE_FIELDS));
        SiteConfigService::setMultiple($filtered);
        return true;
    }

    private static function saveAll(array $versions)
    {
        $json = json_encode($versions, JSON_UNESCAPED_UNICODE);
        $exists = Configx::where('title', self::KEY)->find();
        if ($exists) {
            Configx::where('title', self::KEY)->update(['text' => $json]);
        } else {
            Configx::create(['title' => self::KEY, 'text' => $json]);
        }
    }
}
