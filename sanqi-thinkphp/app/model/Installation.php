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

class Installation extends Model
{
    protected $table = 'installations';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;
}
