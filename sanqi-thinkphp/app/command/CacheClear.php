<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class CacheClear extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:clear')
            ->setDescription('清理运行时缓存（模板缓存、路由缓存、数据缓存）');
    }

    protected function execute(Input $input, Output $output): int
    {
        $runtimePath = $this->app->getRuntimePath();
        $cleared = 0;

        // 模板缓存
        $tempPath = $runtimePath . 'temp/';
        $cleared += $this->removeFiles($tempPath, '*.php');

        // 数据缓存
        $cachePath = $runtimePath . 'cache/';
        $cleared += $this->removeFiles($cachePath, '*.php');

        // 路由缓存
        $routeFile = $runtimePath . 'route_list.php';
        if (is_file($routeFile)) {
            @unlink($routeFile);
            $cleared++;
        }

        // 配置缓存
        $configFile = $runtimePath . 'config.php';
        if (is_file($configFile)) {
            @unlink($configFile);
            $cleared++;
        }

        $output->info("缓存清理完成，共清理 {$cleared} 个文件");
        return 0;
    }

    private function removeFiles(string $dir, string $pattern): int
    {
        if (!is_dir($dir)) {
            return 0;
        }

        $count = 0;
        $files = glob($dir . $pattern);
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                    $count++;
                }
            }
        }

        // 清理空子目录
        $this->removeEmptyDirs($dir);

        return $count;
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
            if ($file->isDir() && $file->getFilename() !== '.' && $file->getFilename() !== '..') {
                @rmdir($file->getRealPath());
            }
        }
    }
}
