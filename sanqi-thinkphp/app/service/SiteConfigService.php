<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\service;

use app\model\Configx;
use app\model\Admin;
use think\facade\Cache;

/**
 * 站点配置统一服务
 * 所有原 admin 表字段（id/username 除外）迁移至 configx 键值表后，
 * 通过此服务进行读写，对上层返回与原 getSiteConfig() 相同的扁平数组。
 */
class SiteConfigService
{
    /** 所有站点配置字段名（不含 id、username） */
    const SITE_FIELDS = [
        'name', 'subtitle', 'icon', 'logo', 'zt', 'homimg', 'sign',
        'music', 'essgs', 'commgs', 'lnkzt', 'regqx', 'kqsy',
        'comaud', 'ptpaud', 'ptpfan', 'loginkg', 'notname', 'imgpres',
        'rosdomain', 'daymode', 'gotop', 'search', 'videoauplay',
        'regverify', 'pagepass', 'emydz', 'emssl', 'emduk', 'emkey',
        'emzh', 'emfs', 'emfszm', 'emtype', 'aliyun_key',
        'aliyun_secret', 'aliyun_from', 'date', 'copyright', 'beian',
        'topes', 'scfont', 'viscomm', 'vislike_cancel', 'musplay', 'email_push',
        'norightclick', 'piccir', 'readonly',
        'poster_random_api',
    ];

    /** 字段默认值（仅列出非空默认值，其余为空字符串） */
    const DEFAULTS = [
        'name'       => '',
        'subtitle'   => '',
        'icon'       => '',
        'logo'       => '',
        'zt'         => '1',
        'homimg'     => '',
        'sign'       => '',
        'music'      => '',
        'essgs'      => '10',
        'commgs'     => '10',
        'lnkzt'      => '0',
        'regqx'      => '0',
        'kqsy'       => '0',
        'comaud'     => '0',
        'ptpaud'     => '0',
        'ptpfan'     => '1',
        'loginkg'    => '1',
        'notname'    => '0',
        'imgpres'    => '1',
        'rosdomain'  => '1',
        'daymode'    => '1',
        'gotop'      => '1',
        'search'     => '1',
        'videoauplay'=> '0',
        'regverify'  => '0',
        'pagepass'   => '',
        'emydz'      => '',
        'emssl'      => '',
        'emduk'      => '',
        'emkey'      => '',
        'emzh'       => '',
        'emfs'       => '',
        'emfszm'     => '',
        'emtype'     => '',
        'aliyun_key' => '',
        'aliyun_secret' => '',
        'aliyun_from'   => '',
        'date'       => '',
        'copyright'  => '',
        'beian'      => '',
        'topes'      => '',
        'scfont'     => 'default',
        'viscomm'    => '0',
        'vislike_cancel' => '1',
        'musplay'    => '',
        'norightclick' => '0',
        'piccir'     => '0',
        'readonly'   => '0',
        'poster_random_api'  => '',
    ];

    protected static $staticCache = null;

    /**
     * 获取全部站点配置（扁平数组，与原 getSiteConfig() 返回格式一致）
     */
    public static function getAll(): array
    {
        if (self::$staticCache !== null) {
            return self::$staticCache;
        }

        self::$staticCache = Cache::remember('site_config', function () {
            return self::loadAll();
        }, 300);

        return self::$staticCache;
    }

    /**
     * 获取单个配置项
     */
    public static function get(string $key, string $default = ''): string
    {
        $all = self::getAll();
        return (string)($all[$key] ?? $default);
    }

    /**
     * 设置单个配置项
     */
    public static function set(string $key, $value): void
    {
        // 如果 configx 尚未迁移，先从 admin 表导入全部站点字段
        if (!self::isMigrated()) {
            $row = Admin::limit(1)->find();
            if ($row) {
                self::importFromAdminRow($row->toArray());
            }
        }

        if (in_array($key, self::SITE_FIELDS, true)) {
            self::upsert($key, $value);
        }
        self::clearCache();
    }

    /**
     * 批量设置配置项
     */
    public static function setMultiple(array $pairs): void
    {
        // 如果 configx 尚未迁移，先从 admin 表导入全部站点字段
        if (!self::isMigrated()) {
            $row = Admin::limit(1)->find();
            if ($row) {
                self::importFromAdminRow($row->toArray());
            }
        }

        foreach ($pairs as $key => $value) {
            if (in_array($key, self::SITE_FIELDS, true)) {
                self::upsert((string)$key, $value);
            }
        }
        self::clearCache();
    }

    /**
     * 清除静态缓存和 Cache 缓存
     */
    public static function clearCache(): void
    {
        self::$staticCache = null;
        Cache::delete('site_config');
    }

    /**
     * 检测是否已完成迁移（configx 中是否存在站点字段）
     */
    public static function isMigrated(): bool
    {
        return Configx::whereIn('title', self::SITE_FIELDS)->count() > 0;
    }

    /**
     * 从旧 admin 行数据导入（供迁移脚本使用）
     */
    public static function importFromAdminRow(array $adminRow): void
    {
        $skip = ['id', 'username'];
        foreach ($adminRow as $key => $value) {
            if (in_array($key, $skip, true)) {
                continue;
            }
            if (in_array($key, self::SITE_FIELDS, true)) {
                self::upsert($key, (string)$value);
            }
        }
    }

    // ----------------------------------------------------------------
    // 内部方法
    // ----------------------------------------------------------------

    /**
     * 从 configx 加载所有站点字段；未迁移时回退读 admin 表
     */
    protected static function loadAll(): array
    {
        if (!self::isMigrated()) {
            // 过渡期兼容：configx 无站点字段时回退读 admin 表
            $row = Admin::limit(1)->find();
            if ($row) {
                $config = $row->toArray();
                unset($config['id']);
                return array_merge(self::DEFAULTS, $config);
            }
            return self::DEFAULTS;
        }

        $rows = Configx::whereIn('title', self::SITE_FIELDS)->select();
        $config = [];
        foreach ($rows as $row) {
            $decoded = json_decode((string)$row['text'], true);
            $config[$row['title']] = ($decoded !== null) ? (string)$decoded : (string)$row['text'];
        }

        return array_merge(self::DEFAULTS, $config);
    }

    /**
     * upsert 单个字段到 configx
     */
    protected static function upsert(string $key, $value): void
    {
        $text = json_encode((string)$value, JSON_UNESCAPED_UNICODE);
        $exists = Configx::where('title', $key)->find();
        if ($exists) {
            Configx::where('title', $key)->update(['text' => $text]);
        } else {
            Configx::create(['title' => $key, 'text' => $text]);
        }
    }
}
