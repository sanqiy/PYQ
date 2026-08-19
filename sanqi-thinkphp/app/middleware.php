<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

// 全局中间件定义文件
return [
    // 未安装时跳转安装页
    \app\middleware\EnsureInstalled::class,
    // Session初始化
    \think\middleware\SessionInit::class,
    // CSRF验证（全局POST保护）
    \app\middleware\CsrfVerify::class,
];
