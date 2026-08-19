<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\model\User;
use app\model\Essay;
use app\model\Comm;
use app\model\Link;
use app\model\Configx;
use app\service\AdminLogService;
use app\service\AdminSecurityService;

class Uploads extends \app\controller\Base
{
    public function index()
    {
        $referenced = $this->referencedUploads();
        $files = $this->scanUploads($referenced);

        return view('admin/uploads', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'files' => $files,
            'totalSize' => $this->formatBytes(array_sum(array_column($files, 'bytes'))),
            'unusedCount' => count(array_filter($files, function ($file) {
                return empty($file['used']);
            })),
            'pageTitle' => '上传文件管理'
        ], $this->getAdminViewData()));
    }

    public function delete()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }
        if (!AdminSecurityService::verifyAdminPassword($this->request->post('admin_password', ''))) {
            return $this->error('管理员密码错误');
        }

        $url = $this->normalizeUploadUrl($this->request->post('file', ''));
        if ($url === '') {
            return $this->error('文件参数错误');
        }
        if (isset($this->referencedUploads()[$url])) {
            return $this->error('该文件仍被引用，不能删除');
        }

        $publicPath = app()->getRootPath() . 'public/';
        $path = realpath($publicPath . ltrim($url, '/'));
        $root = realpath($publicPath . 'upload');
        if (!$path || !$root || strpos($path, $root) !== 0 || !is_file($path)) {
            return $this->error('文件不存在');
        }

        // 删除原图
        unlink($path);

        // 删除缩略图
        $thumbPath = dirname($path) . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . basename($path);
        if (is_file($thumbPath)) {
            @unlink($thumbPath);
        }

        AdminLogService::operation('upload.delete_unused', $url, []);
        return $this->success('删除成功');
    }

    private function scanUploads(array $referenced): array
    {
        $root = app()->getRootPath() . 'public/' . 'upload/';
        if (!is_dir($root)) {
            return [];
        }

        $publicPath = app()->getRootPath() . 'public/';
        $rows = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($publicPath)));
            $url = '/' . ltrim($relative, '/');
            // 跳过缩略图目录
            if (strpos($url, '/thumbs/') !== false) {
                continue;
            }
            $rows[] = [
                'url' => $url,
                'size' => $this->formatBytes($file->getSize()),
                'bytes' => $file->getSize(),
                'time' => date('Y-m-d H:i:s', $file->getMTime()),
                'used' => isset($referenced[$url]),
                'ext' => strtolower(pathinfo($url, PATHINFO_EXTENSION))
            ];
        }
        usort($rows, function ($a, $b) {
            if ($a['used'] === $b['used']) {
                return strcmp($b['time'], $a['time']);
            }
            return $a['used'] ? 1 : -1;
        });
        return $rows;
    }

    private function referencedUploads(): array
    {
        $refs = [];
        $modelFields = [
            [User::class, ['img', 'homeimg']],
            [Essay::class, ['ptpimag', 'ptpvideo']],
            [Link::class, ['urlimg']],
            [Configx::class, ['text']],
        ];

        foreach ($modelFields as [$modelClass, $fields]) {
            $rows = $modelClass::field($fields)->select()->toArray();
            foreach ($rows as $row) {
                foreach ($row as $value) {
                    foreach ($this->extractUploadUrls((string)$value) as $url) {
                        $refs[$url] = true;
                    }
                }
            }
        }
        return $refs;
    }

    private function extractUploadUrls(string $text): array
    {
        $urls = [];
        if (preg_match_all('#(?:https?://[^/]+)?(?:\.?/)?upload/[^\s"\'<>()]+#i', $text, $matches)) {
            foreach ($matches[0] as $match) {
                $urls[] = $this->normalizeUploadUrl($match);
            }
        }
        return array_values(array_filter(array_unique($urls)));
    }

    private function normalizeUploadUrl(string $value): string
    {
        $value = trim(str_replace('\\', '/', $value));
        if ($value === '') {
            return '';
        }
        $path = parse_url($value, PHP_URL_PATH);
        $value = $path ?: $value;
        $value = preg_replace('#^\./#', '/', $value);
        if (strpos($value, '/') !== 0) {
            $value = '/' . $value;
        }
        if (strpos($value, '/upload/') !== 0) {
            return '';
        }
        return $value;
    }

    private function formatBytes(int $bytes): string
    {
        $bytes = max(0, $bytes);
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $i === 0 ? 0 : 2) . ' ' . $units[$i];
    }
}
