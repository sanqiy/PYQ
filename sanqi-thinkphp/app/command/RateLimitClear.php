<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\command;

use app\service\RateLimitService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class RateLimitClear extends Command
{
    protected function configure(): void
    {
        $this->setName('ratelimit:clear')
            ->setDescription('清理过期的限流记录文件');
    }

    protected function execute(Input $input, Output $output): int
    {
        $output->info('开始清理过期限流记录...');

        $count = RateLimitService::cleanup(5000);

        $output->info("清理完成，共清理 {$count} 个过期文件");
        return 0;
    }
}
