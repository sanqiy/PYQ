<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'cache:clear'      => \app\command\CacheClear::class,
        'ratelimit:clear'  => \app\command\RateLimitClear::class,
        'db:backup'        => \app\command\DbBackup::class,
        'db:migrate'       => \app\command\Migrate::class,
        'upload:cleanup'   => \app\command\OrphanUpload::class,
    ],
];
