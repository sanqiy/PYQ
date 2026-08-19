<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\server;

use app\model\Installation;
use app\model\ServerVersion;

class Api
{
    /**
     * 接收安装上报
     * POST /server/api/install/report
     */
    public function installReport()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data) || empty($data['domain'])) {
            return json(['code' => 400, 'msg' => '参数错误']);
        }

        $domain = strtolower(trim($data['domain']));
        $now = date('Y-m-d H:i:s');

        Installation::create([
            'domain'      => $domain,
            'ip'          => $data['ip'] ?? '',
            'app_version' => $data['app_version'] ?? '1.0.0',
            'php_version' => $data['php_version'] ?? '',
            'db_version'  => $data['db_version'] ?? '',
            'admin_user'  => $data['admin_user'] ?? '',
            'installed_at' => $now,
            'last_seen_at' => $now,
        ]);

        return json(['code' => 200, 'msg' => 'ok']);
    }

    /**
     * 版本检查
     * GET /server/api/version/check?version=1.0.0
     */
    public function versionCheck()
    {
        $currentVersion = trim((string) request()->get('version', '0'));

        $latest = ServerVersion::where('is_active', 1)
            ->order('id', 'desc')
            ->find();

        if (!$latest) {
            return json(['code' => 200, 'data' => null]);
        }

        if (version_compare($latest['version'], $currentVersion, '>')) {
            return json([
                'code' => 200,
                'data' => [
                    'version' => $latest['version'],
                    'content' => $latest['content'],
                ],
            ]);
        }

        return json(['code' => 200, 'data' => null]);
    }
}
