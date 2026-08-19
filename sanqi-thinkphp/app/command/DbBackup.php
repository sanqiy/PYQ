<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\command;

use app\service\DatabaseService;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument as InputArgument;
use think\console\Output;

class DbBackup extends Command
{
    protected function configure(): void
    {
        $this->setName('db:backup')
            ->setDescription('备份数据库')
            ->addArgument('keep', InputArgument::OPTIONAL, '保留最近N份备份，0=不清理旧备份', '0');
    }

    protected function execute(Input $input, Output $output): int
    {
        $output->info('开始备份数据库...');

        try {
            $result = DatabaseService::backup();
            $output->info("备份成功: {$result['filename']}");
        } catch (\Throwable $e) {
            $output->error('备份失败: ' . $e->getMessage());
            return 1;
        }

        // 清理旧备份
        $keep = max(0, (int)$input->getArgument('keep'));
        if ($keep > 0) {
            $this->cleanOldBackups($keep, $output);
        }

        return 0;
    }

    private function cleanOldBackups(int $keep, Output $output): void
    {
        $backupDir = $this->app->getRuntimePath() . 'backup/';
        if (!is_dir($backupDir)) {
            return;
        }

        $files = glob($backupDir . 'backup_*.sql');
        if (!$files || count($files) <= $keep) {
            return;
        }

        // 按修改时间排序，最新的在前
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $toDelete = array_slice($files, $keep);
        foreach ($toDelete as $file) {
            @unlink($file);
            $output->info('已删除旧备份: ' . basename($file));
        }
    }
}
