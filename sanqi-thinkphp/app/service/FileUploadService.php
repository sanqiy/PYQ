<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\service;

use app\model\FileUpload;
use think\facade\Db;

/**
 * 文件上传服务 - MD5去重 + 引用计数
 */
class FileUploadService
{
    /**
     * 根据 MD5 查找已有文件
     */
    public static function findByMd5(string $md5): ?array
    {
        $record = FileUpload::where('md5', $md5)->find();
        return $record ? $record->toArray() : null;
    }

    /**
     * 根据 URL 查找文件记录
     */
    public static function findByUrl(string $url): ?array
    {
        $record = FileUpload::where('url', $url)->find();
        return $record ? $record->toArray() : null;
    }

    /**
     * 创建文件记录（存在相同MD5时复用已有记录）
     */
    public static function create(string $md5, string $url, string $type = 'image'): array
    {
        $existing = self::findByMd5($md5);
        if ($existing) {
            self::incrementRef($existing['url']);
            return $existing;
        }

        $record = FileUpload::create([
            'md5'       => $md5,
            'url'       => $url,
            'type'      => $type,
            'ref_count' => 1,
        ]);
        return $record->toArray();
    }

    /**
     * 增加引用计数
     */
    public static function incrementRef(string $url): void
    {
        FileUpload::where('url', $url)->inc('ref_count')->update();
    }

    /**
     * 减少引用计数，如果为 0 则删除文件和记录
     */
    public static function decrementRef(string $url): void
    {
        $record = FileUpload::where('url', $url)->find();
        if (!$record) {
            return;
        }

        if ($record->ref_count <= 1) {
            // 最后一条引用，删除文件和记录
            self::deleteFileByUrl($url);
            $record->delete();
        } else {
            // 原子递减，防止并发导致 ref_count 归零不及时
            FileUpload::where('url', $url)->where('ref_count', '>', 1)->dec('ref_count')->update();
        }
    }

    /**
     * 批量同步文章的文件引用
     * 对比新旧 URL 列表，增减引用计数
     */
    public static function syncReferences(array $oldUrls, array $newUrls): void
    {
        $removed = array_diff($oldUrls, $newUrls);
        $added = array_diff($newUrls, $oldUrls);

        foreach ($removed as $url) {
            self::decrementRef($url);
        }

        foreach ($added as $url) {
            // 检查是否已有记录
            $existing = self::findByUrl($url);
            if ($existing) {
                self::incrementRef($url);
            } else {
                // 没有记录，可能是旧数据，创建一条
                $md5 = self::calculateUrlMd5($url);
                if ($md5) {
                    $type = self::detectFileType($url);
                    self::create($md5, $url, $type);
                }
            }
        }
    }

    /**
     * 删除文章时清理所有文件引用
     */
    public static function cleanupArticleFiles(array $urls): void
    {
        foreach ($urls as $url) {
            self::decrementRef($url);
        }
    }

    /**
     * 从文章数据中提取所有文件 URL
     */
    public static function extractUrlsFromArticle(array $article): array
    {
        $urls = [];

        // 图片
        $images = (string)($article['ptpimag'] ?? '');
        if ($images !== '') {
            $urls = array_merge($urls, explode('(+@+)', $images));
        }

        // 视频
        $video = (string)($article['ptpvideo'] ?? '');
        if ($video !== '') {
            $parts = explode('|', $video);
            if (!empty($parts[0])) {
                $urls[] = $parts[0];
            }
        }

        // 文章封面
        $cover = (string)($article['article_cover'] ?? '');
        if ($cover !== '' && strpos($cover, '/upload/') === 0) {
            $urls[] = $cover;
        }

        return array_filter($urls, function ($url) {
            return $url !== '' && strpos($url, '/upload/') === 0;
        });
    }

    /**
     * 计算本地文件的 MD5
     */
    public static function calculateFileMd5(string $filePath): string
    {
        return md5_file($filePath);
    }

    /**
     * 计算 URL 对应本地文件的 MD5（仅限本地文件）
     */
    public static function calculateUrlMd5(string $url): ?string
    {
        if (strpos($url, '/upload/') !== 0) {
            return null;
        }

        $publicPath = app()->getRootPath() . 'public';
        $filePath = $publicPath . $url;

        if (!is_file($filePath)) {
            return null;
        }

        return md5_file($filePath);
    }

    /**
     * 根据 URL 检测文件类型
     */
    public static function detectFileType(string $url): string
    {
        $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        $videoExts = ['mp4', 'webm', 'ogg'];
        return in_array($ext, $videoExts) ? 'video' : 'image';
    }

    /**
     * 删除本地文件（含缩略图）
     */
    protected static function deleteFileByUrl(string $url): void
    {
        if (strpos($url, '/upload/') !== 0) {
            return;
        }

        $publicPath = app()->getRootPath() . 'public';
        $filePath = $publicPath . $url;

        if (is_file($filePath)) {
            @unlink($filePath);
        }

        // 删除缩略图
        $thumbPath = dirname($filePath) . '/thumbs/' . basename($filePath);
        if (is_file($thumbPath)) {
            @unlink($thumbPath);
        }
    }
}
