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
use app\service\AdminSecurityService;
use app\service\ConfigVersionService;

class ConfigVersions extends \app\controller\Base
{
    public function index()
    {
        return view('admin/config_versions', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'versions' => ConfigVersionService::all(),
            'pageTitle' => '配置版本'
        ], $this->getAdminViewData()));
    }

    public function snapshot()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }
        $user = $this->getUser();
        $version = ConfigVersionService::snapshot($this->request->post('note', '手动快照'), $user['username'] ?? '');
        AdminLogService::operation('config.snapshot', 'admin:1', ['version' => $version['id']]);
        return $this->success('快照已创建');
    }

    public function restore()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }
        if (!AdminSecurityService::verifyAdminPassword($this->request->post('admin_password', ''))) {
            return $this->error('管理员密码错误');
        }
        $id = $this->request->post('id', '');
        if (!ConfigVersionService::restore($id)) {
            return $this->error('版本不存在');
        }
        AdminLogService::operation('config.restore', 'admin:1', ['version' => $id]);
        return $this->success('回滚成功');
    }
}
