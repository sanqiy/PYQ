<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\command;

use app\service\MigrationService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Migrate extends Command
{
    protected function configure(): void
    {
        $this->setName('db:migrate')
            ->setDescription('执行数据库迁移脚本（database/migrate_*.sql）');
    }

    protected function execute(Input $input, Output $output): int
    {
        $summary = MigrationService::summary();
        $pendingCount = count($summary['pending']);
        if ($pendingCount === 0) {
            $output->info('没有待执行迁移，当前版本：' . $summary['current']);
            return 0;
        }

        $output->info('发现 ' . $pendingCount . ' 个待执行迁移');
        $result = MigrationService::runPending();
        foreach ($result['results'] as $row) {
            $line = $row['migration'] . ' [' . $row['status'] . '] ' . $row['message'];
            if ($row['status'] === 'failed') {
                $output->error($line);
            } elseif ($row['status'] === 'skipped') {
                $output->warn($line);
            } else {
                $output->info($line);
            }
        }

        $output->info('缓存清理文件数：' . $result['cache_cleared']);
        return $result['failed'] > 0 ? 1 : 0;
    }
}
