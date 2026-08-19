<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\middleware;

use app\service\AdminSecurityService;

class AdminAuth extends AuthMiddleware
{
    // 不需要认证的路由
    protected $except = [
        'admin/login',
    ];

    public function handle($request, \Closure $next)
    {
        $path = strtolower(trim($request->pathinfo(), '/'));

        // 后台不再使用独立登录页：管理员直接进后台，未登录回到前台登录弹窗。
        if ($path === 'admin/login') {
            $auth = $this->authenticate();
            if ($auth['status'] === 'ok' && (($auth['user']['role'] ?? '') === 'admin')) {
                if (!AdminSecurityService::isTwoFactorVerified($auth['user'])) {
                    return redirect('/?login=1&redirect=' . rawurlencode('/admin'));
                }
                return redirect('/admin');
            }
            return redirect('/?login=1&redirect=' . rawurlencode('/admin'));
        }

        // 登录相关路由跳过认证检查
        if (in_array($path, $this->except)) {
            return $next($request);
        }

        $auth = $this->authenticate();
        if ($auth['status'] !== 'ok') {
            return redirect('/?login=1&redirect=' . rawurlencode('/admin'));
        }

        if (($auth['user']['role'] ?? '') !== 'admin') {
            return $this->jsonError('无管理员权限');
        }

        if (!AdminSecurityService::isTwoFactorVerified($auth['user'])) {
            return redirect('/?login=1&redirect=' . rawurlencode('/admin'));
        }

        return $next($request);
    }
}
