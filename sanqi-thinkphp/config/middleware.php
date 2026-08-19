<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

// 中间件配置
return [
    // 全局中间件
    'global'   => [
        \app\middleware\EnsureInstalled::class,
        \app\middleware\ExceptionLog::class,
    ],
    // 别名或分组
    'alias'    => [
        'csrf'           => \app\middleware\CsrfVerify::class,
        'admin_auth'     => \app\middleware\AdminAuth::class,
        'exception_log'  => \app\middleware\ExceptionLog::class,
        'check_installed'=> \app\middleware\CheckInstalled::class,
        'ensure_installed'=> \app\middleware\EnsureInstalled::class,
        'server_admin_auth' => \app\middleware\ServerAdminAuth::class,
    ],
    // 优先级设置，此数组中的中间件会按照数组中的顺序优先执行
    'priority' => [],
];
