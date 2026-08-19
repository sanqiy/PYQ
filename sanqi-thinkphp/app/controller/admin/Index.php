<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\model\Essay;
use app\model\User;
use app\model\Comm;
use app\service\UpdateService;
use think\facade\Cache;

/**
 * 后台首页控制器
 */
class Index extends \app\controller\Base
{
    /**
     * 仪表盘
     */
    public function index()
    {
        // 统计数据
        $userCount = User::count();
        $articleCount = Essay::count();
        $commentCount = Comm::count();
        $uploadSize = (int)Cache::remember('admin_upload_size', function () {
            return $this->directorySize(app()->getRootPath() . 'public/' . 'upload/');
        }, 300);
        $uploadSizeText = $this->formatBytes($uploadSize);
        $todayArticleCount = Essay::whereRaw('DATE(ptptime) = CURDATE()')->count();
        $todayCommentCount = Comm::whereRaw('DATE(cotime) = CURDATE()')->count();

        // 待审核数量
        $pendingArticleCount = Essay::where('ptpaud', 0)->count();
        $pendingCommentCount = Comm::where('comaud', 0)->count();

        // 最新用户
        $latestUsers = User::order('id', 'desc')->limit(5)->select()->toArray();

        // 最新文章
        $latestArticles = Essay::order('id', 'desc')->limit(5)->select()->toArray();

        // 站点配置
        $siteConfig = $this->getSiteConfig();

        // 检查版本更新
        $updateInfo = UpdateService::check();

        return view('admin/index', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $this->getUser(),
            'updateInfo' => $updateInfo,
            'userCount' => $userCount,
            'articleCount' => $articleCount,
            'commentCount' => $commentCount,
            'uploadSizeText' => $uploadSizeText,
            'todayArticleCount' => $todayArticleCount,
            'todayCommentCount' => $todayCommentCount,
            'pendingArticleCount' => $pendingArticleCount,
            'pendingCommentCount' => $pendingCommentCount,
            'latestUsers' => $latestUsers,
            'latestArticles' => $latestArticles,
            'pageTitle' => '后台管理'
        ], $this->getAdminViewData()));
    }

    private function directorySize(string $dir): int
    {
        if (!is_dir($dir)) {
            return 0;
        }
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        $bytes = max(0, $bytes);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $i === 0 ? 0 : 2) . ' ' . $units[$i];
    }

    /**
     * 手动上报安装信息到中心服务器
     */
    public function reReport()
    {
        $user = $this->getUser();
        $result = UpdateService::reportToServer($user['username'] ?? '');
        if ($result) {
            return json(['code' => 200, 'msg' => '上报成功']);
        }
        return json(['code' => 400, 'msg' => '上报失败，请检查网络连接']);
    }
}
