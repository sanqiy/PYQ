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
 * 安装状态检查中间件 — 已安装后禁止对安装路由的写操作
 */
class CheckInstalled
{
    public function handle($request, \Closure $next)
    {
        $lockFile = app()->getRuntimePath() . 'install.lock';

        if (!file_exists($lockFile)) {
            return $next($request);
        }

        // 已安装：禁止所有 POST 请求
        if ($request->isPost()) {
            return json(['code' => '403', 'msg' => '系统已安装，如需重新安装请删除 runtime/install.lock'], 403);
        }

        // 已安装：GET 请求仅允许 complete 页面
        $action = strtolower($request->action());
        if ($action !== 'complete') {
            return redirect('/install/complete');
        }

        return $next($request);
    }
}
