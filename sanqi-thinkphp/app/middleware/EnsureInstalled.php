<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\middleware;

/**
 * 未安装时自动跳转到安装页面
 */
class EnsureInstalled
{
    public function handle($request, \Closure $next)
    {
        // 安装路由本身不拦截，避免死循环
        $path = strtolower($request->pathinfo() ?: '');
        if (str_starts_with($path, 'install')) {
            return $next($request);
        }

        $lockFile = app()->getRuntimePath() . 'install.lock';
        if (!file_exists($lockFile)) {
            return redirect('/install');
        }

        return $next($request);
    }
}
