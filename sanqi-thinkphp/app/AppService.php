<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare (strict_types = 1);

namespace app;

use think\Service;
use think\facade\Cache;
use app\service\SiteConfigService;

class AppService extends Service
{
    public function register()
    {
    }

    public function boot()
    {
        $this->warmupCache();
    }

    protected function warmupCache()
    {
        try {
            $siteConfig = SiteConfigService::getAll();
            $essgs = max(1, min(50, (int) ($siteConfig['essgs'] ?? 10)));
            $now = date('Y-m-d H:i:s');

            // 获取置顶文章ID，从普通列表中排除
            $topIds = [];
            if (!empty($siteConfig['topes'])) {
                $topIds = array_values(array_filter(explode("\n", $siteConfig['topes'])));
            }

            Cache::tag('article')->remember("articles_latest_{$essgs}", function () use ($essgs, $now, $topIds) {
                $query = \app\model\Essay::where('ptpaud', '1')
                    ->where('ptpys', '<>', '0')
                    ->where('ptptime', '<=', $now);
                if (!empty($topIds)) {
                    $query->whereNotIn('cid', $topIds);
                }
                return $query->order('id', 'desc')
                    ->limit($essgs)
                    ->select()
                    ->toArray();
            }, 120);
        } catch (\Throwable $e) {
            // 预热失败不应阻塞应用启动
        }
    }
}
