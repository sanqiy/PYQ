<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\middleware;

use app\service\AdminSecurityService;

class ServerAdminAuth extends AuthMiddleware
{
    public function handle($request, \Closure $next)
    {
        $path = strtolower(trim($request->pathinfo(), '/'));
        $loginRedirect = '/?login=1&redirect=' . rawurlencode('/server/admin');

        if ($path === 'server/admin/login') {
            $auth = $this->authenticate();
            if ($auth['status'] === 'ok' && (($auth['user']['role'] ?? '') === 'admin')) {
                if (!AdminSecurityService::isTwoFactorVerified($auth['user'])) {
                    return redirect($loginRedirect);
                }
                return redirect('/server/admin');
            }
            return redirect($loginRedirect);
        }

        $auth = $this->authenticate();
        if ($auth['status'] !== 'ok') {
            if ($request->isAjax()) {
                return $this->jsonError('请先登录', 401);
            }
            return redirect($loginRedirect);
        }

        if (($auth['user']['role'] ?? '') !== 'admin') {
            return $this->jsonError('无管理员权限', 403);
        }

        if (!AdminSecurityService::isTwoFactorVerified($auth['user'])) {
            if ($request->isAjax()) {
                return $this->jsonError('请先完成管理员二次验证', 401);
            }
            return redirect($loginRedirect);
        }

        return $next($request);
    }
}
