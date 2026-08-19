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
 * 设置控制器
 */
class Setup extends Base
{
    public function index()
    {
        // 要求登录
        $loginCheck = $this->requireLogin();
        if ($loginCheck !== true) {
            return $loginCheck;
        }

        // 获取站点配置
        $siteConfig = $this->getSiteConfig();
        $user = $this->getUser();

        // 生成CSRF Token
        if (!session('allkey')) {
            session('allkey', bin2hex(random_bytes(16)));
        }
        $allkey = session('allkey');

        // 渲染视图
        $commonViewData = $this->getCommonViewData();

        return view('setup/index', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $user,
            'allkey' => $allkey,
            'pageTitle' => '设置',
            'pageJs' => 'setup.js',
            'mainClass' => 'setup-main',
            'compactHeader' => true,
            'headerBackUrl' => '/home',
            'headerTitle' => '设置',
            'hideFooter' => true,
        ], $commonViewData));
    }
}
