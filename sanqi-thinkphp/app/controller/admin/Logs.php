<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\service\AdminLogService;

class Logs extends \app\controller\Base
{
    public function index()
    {
        $type = $this->request->get('type', 'operation');
        if (!in_array($type, ['operation', 'login'], true)) {
            $type = 'operation';
        }

        return view('admin/logs', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'type' => $type,
            'isLogin' => $type === 'login',
            'logs' => AdminLogService::read($type, 200),
            'pageTitle' => $type === 'login' ? '登录日志' : '操作日志'
        ], $this->getAdminViewData()));
    }
}
