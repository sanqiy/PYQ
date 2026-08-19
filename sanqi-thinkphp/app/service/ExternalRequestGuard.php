<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\service;

class ExternalRequestGuard
{
    public static function assertAllowed(string $scope, string $ip): void
    {
        $rl = config('ratelimit.external.ip') ?: ['max' => 20, 'window' => 60, 'lockout' => 300];
        $safeScope = preg_replace('/[^a-z0-9_.-]/i', '_', $scope);

        RateLimitService::assertAllowed(
            'external:' . $safeScope . ':ip:' . $ip,
            (int)$rl['max'],
            (int)$rl['window'],
            '外部解析请求过于频繁，请{minutes}分钟后再试',
            (int)$rl['lockout']
        );
    }
}
