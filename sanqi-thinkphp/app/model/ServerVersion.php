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

class ServerVersion extends Model
{
    protected $table = 'server_versions';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;
}
