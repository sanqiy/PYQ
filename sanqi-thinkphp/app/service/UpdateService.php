<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\service;

use think\facade\Cache;

class UpdateService
{
    protected static $serverBase = 'https://meimiao.de/server/api';

    /**
     * 检查是否有新版本
     * @return array|null 有更新返回 ['version' => '...', 'content' => '...']，无更新返回 null
     */
    public static function check(): ?array
    {
        $currentVersion = self::currentVersion();

        return Cache::remember('update_check', function () use ($currentVersion) {
            $url = self::$serverBase . '/version/check?' . http_build_query([
                'version' => $currentVersion,
            ]);

            $context = stream_context_create([
                'http' => [
                    'method'  => 'GET',
                    'timeout' => 3,
                    'ignore_errors' => true,
                ],
            ]);

            try {
                $response = @file_get_contents($url, false, $context);
                if ($response === false) {
                    return null;
                }

                $json = json_decode($response, true);
                if (!is_array($json) || ($json['code'] ?? 0) != 200) {
                    return null;
                }

                $data = $json['data'] ?? null;
                if (empty($data) || empty($data['version'])) {
                    return null;
                }

                return [
                    'version' => $data['version'],
                    'content' => $data['content'] ?? '',
                ];
            } catch (\Exception $e) {
                return null;
            }
        }, 3600);
    }

    /**
     * 获取当前应用版本号
     */
    public static function currentVersion(): string
    {
        return (string) config('app.app_version', '1.0.0');
    }

    /**
     * 上报安装信息到中心服务器
     */
    public static function reportToServer(string $adminUser = ''): bool
    {
        $data = json_encode([
            'domain'      => $_SERVER['HTTP_HOST'] ?? '',
            'ip'          => $_SERVER['SERVER_ADDR'] ?? '',
            'app_version' => self::currentVersion(),
            'php_version' => PHP_VERSION,
            'db_version'  => '',
            'admin_user'  => $adminUser,
        ], JSON_UNESCAPED_UNICODE);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => $data,
                'timeout' => 5,
                'ignore_errors' => true,
            ],
        ]);

        try {
            $response = @file_get_contents(self::$serverBase . '/install/report', false, $context);
            if ($response === false) {
                \think\facade\Log::warning('安装上报失败: 请求失败');
                return false;
            }
            $json = json_decode($response, true);
            if (($json['code'] ?? 0) != 200) {
                \think\facade\Log::warning('安装上报失败: ' . ($json['msg'] ?? '未知错误'));
                return false;
            }
            return true;
        } catch (\Exception $e) {
            \think\facade\Log::warning('安装上报异常: ' . $e->getMessage());
            return false;
        }
    }
}
