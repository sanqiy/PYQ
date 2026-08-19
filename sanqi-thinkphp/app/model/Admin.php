<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\model;

use think\Model;

/**
 * 管理员表（仅存储管理员用户名，站点配置已迁移至 configx）
 *
 * @property int    $id        主键ID
 * @property string $username  管理员账号
 */
class Admin extends Model
{
    protected $name = 'admin';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    protected $type = [
        'id' => 'integer',
    ];
}
