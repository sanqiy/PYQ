<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\model;

use think\Model;

class PollVote extends Model
{
    protected $name = 'poll_votes';
    protected $autoWriteTimestamp = false;
}
