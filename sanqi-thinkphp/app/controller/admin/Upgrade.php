<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\service\AdminLogService;
use app\service\MigrationService;

class Upgrade extends Base
{
    public function index()
    {
        return view('admin/upgrade', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'pageTitle' => '系统升级',
            'migration' => MigrationService::summary(),
        ], $this->getAdminViewData()));
    }

    public function migrate()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $result = MigrationService::runPending();
        AdminLogService::operation('system.migrate', 'migration', [
            'executed' => $result['executed'],
            'skipped' => $result['skipped'],
            'failed' => $result['failed'],
            'cache_cleared' => $result['cache_cleared'],
        ]);

        $msg = '迁移完成：执行 ' . $result['executed'] . ' 个';
        if ($result['skipped'] > 0) {
            $msg .= '，跳过 ' . $result['skipped'] . ' 个';
        }
        if ($result['failed'] > 0) {
            $msg .= '，失败 ' . $result['failed'] . ' 个';
        }
        $msg .= '，清理缓存文件 ' . $result['cache_cleared'] . ' 个';

        return $this->success($msg, $result);
    }
}
