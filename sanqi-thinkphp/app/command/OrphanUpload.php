<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\command;

use app\model\FileUpload;
use app\model\Essay;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument as InputArgument;
use think\console\Output;

class OrphanUpload extends Command
{
    protected function configure(): void
    {
        $this->setName('upload:cleanup')
            ->setDescription('清理孤儿上传文件')
            ->addArgument('mode', InputArgument::OPTIONAL, '清理模式: record=清理无文件记录, file=清理无记录文件, all=全部清理', 'all');
    }

    protected function execute(Input $input, Output $output): int
    {
        $mode = $input->getArgument('mode');
        $total = 0;

        if ($mode === 'record' || $mode === 'all') {
            $total += $this->cleanOrphanRecords($output);
        }

        if ($mode === 'file' || $mode === 'all') {
            $total += $this->cleanOrphanFiles($output);
        }

        if ($total === 0) {
            $output->info('没有发现需要清理的孤儿文件');
        }

        return 0;
    }

    /**
     * 清理 file_uploads 表中物理文件已不存在的记录
     */
    private function cleanOrphanRecords(Output $output): int
    {
        $output->info('检查无对应文件的数据库记录...');
        $publicPath = $this->app->getRootPath() . 'public';
        $count = 0;

        $records = FileUpload::select();
        foreach ($records as $record) {
            $url = $record->url;
            if (strpos($url, '/upload/') !== 0) {
                continue;
            }

            $filePath = $publicPath . $url;
            if (!is_file($filePath)) {
                $record->delete();
                $output->info("  删除记录: {$url}");
                $count++;
            }
        }

        $output->info("共清理 {$count} 条无文件记录");
        return $count;
    }

    /**
     * 清理磁盘上 file_uploads 表中没有记录的文件
     */
    private function cleanOrphanFiles(Output $output): int
    {
        $output->info('检查无数据库记录的磁盘文件...');
        $publicPath = $this->app->getRootPath() . 'public';
        $uploadDir = $publicPath . '/upload/';

        if (!is_dir($uploadDir)) {
            $output->info('upload 目录不存在，跳过');
            return 0;
        }

        // 收集所有文章中引用的 URL
        $articleUrls = $this->collectArticleUrls();

        // 收集 file_uploads 表中所有 URL
        $dbUrls = [];
        $records = FileUpload::field('url')->select();
        foreach ($records as $record) {
            $dbUrls[$record->url] = true;
        }

        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($uploadDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen($uploadDir));
            $relativePath = str_replace('\\', '/', $relativePath);
            $relativeUrl = '/upload/' . $relativePath;

            // 跳过 thumbs 目录
            if (preg_match('#/thumbs/#', $relativeUrl)) {
                continue;
            }

            // 在数据库或文章中被引用则跳过
            if (isset($dbUrls[$relativeUrl]) || isset($articleUrls[$relativeUrl])) {
                continue;
            }

            @unlink($file->getRealPath());
            $output->info("  删除文件: {$relativeUrl}");
            $count++;
        }

        // 清理空目录
        $this->removeEmptyDirs($uploadDir);

        $output->info("共清理 {$count} 个孤儿文件");
        return $count;
    }

    /**
     * 收集所有文章中引用的文件 URL
     */
    private function collectArticleUrls(): array
    {
        $urls = [];
        $articles = Essay::field('ptpimag,ptpvideo,article_cover')->select();

        foreach ($articles as $article) {
            $images = (string)($article['ptpimag'] ?? '');
            if ($images !== '') {
                foreach (explode('(+@+)', $images) as $url) {
                    if ($url !== '' && strpos($url, '/upload/') === 0) {
                        $urls[$url] = true;
                    }
                }
            }

            $video = (string)($article['ptpvideo'] ?? '');
            if ($video !== '') {
                $parts = explode('|', $video);
                if (!empty($parts[0]) && strpos($parts[0], '/upload/') === 0) {
                    $urls[$parts[0]] = true;
                }
            }

            $cover = (string)($article['article_cover'] ?? '');
            if ($cover !== '' && strpos($cover, '/upload/') === 0) {
                $urls[$cover] = true;
            }
        }

        return $urls;
    }

    private function removeEmptyDirs(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                @rmdir($file->getRealPath());
            }
        }
    }
}
