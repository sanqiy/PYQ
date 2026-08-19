<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\traits;

use app\service\SiteConfigService;
use app\service\ViewDataService;

trait SiteConfigTrait
{
    protected static $siteConfigCache = null;
    protected static $commonViewDataCache = null;
    protected static $adminViewDataCache = null;

    protected function getSiteConfig()
    {
        if (static::$siteConfigCache !== null) {
            return static::$siteConfigCache;
        }

        static::$siteConfigCache = SiteConfigService::getAll();
        return static::$siteConfigCache;
    }

    protected function clearSiteConfigCache(): void
    {
        static::$siteConfigCache = null;
        static::$commonViewDataCache = null;
        static::$adminViewDataCache = null;
        SiteConfigService::clearCache();
    }

    /**
     * 获取公共视图数据（用于 layout）
     * 包含：未读消息数、友链等
     */
    protected function getCommonViewData(): array
    {
        if (static::$commonViewDataCache !== null) {
            return static::$commonViewDataCache;
        }

        $user = $this->getUser();
        $siteConfig = $this->getSiteConfig();
        static::$commonViewDataCache = ViewDataService::getCommonViewData(
            $user['username'] ?? null,
            $siteConfig
        );
        return static::$commonViewDataCache;
    }

    /**
     * 获取后台布局数据（待审核数量等）
     */
    protected function getAdminViewData(): array
    {
        if (static::$adminViewDataCache !== null) {
            return static::$adminViewDataCache;
        }

        static::$adminViewDataCache = ViewDataService::getAdminViewData();
        return static::$adminViewDataCache;
    }
}
