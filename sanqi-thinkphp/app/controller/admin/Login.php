<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

/**
 * 后台不再提供独立登录页，统一使用前台登录弹窗。
 */
class Login extends \app\controller\Base
{
    public function index()
    {
        if ($this->getUser() && $this->isAdmin()) {
            return redirect('/admin');
        }

        return redirect('/?login=1&redirect=' . rawurlencode('/admin'));
    }

    protected function login()
    {
        return redirect('/?login=1&redirect=' . rawurlencode('/admin'));
    }

    protected function verify2fa()
    {
        return redirect('/?login=1&redirect=' . rawurlencode('/admin'));
    }

    protected function logout()
    {
        \app\service\AuthService::clearLogin();
        return redirect('/');
    }
}
