<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller;

/**
 * 找回密码控制器
 */
class Repass extends Base
{
    public function index()
    {
        // 获取站点配置
        $siteConfig = $this->getSiteConfig();
        $username = $this->request->get('useke', '');

        // 渲染视图
        return view('repass/index', [
            'siteConfig' => $siteConfig,
            'username' => $username,
            'pageTitle' => '找回密码',
            'pageJs' => 'repass.js',
            'hideHeader' => true,
            'hideFooter' => true,
        ]);
    }
}
