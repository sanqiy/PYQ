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
 * 扩展配置表
 *
 * @property int    $id    主键ID
 * @property string $title 配置标题
 * @property string $text  配置内容
 */
class Configx extends Model
{
    protected $name = 'configx';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    protected $type = [
        'id' => 'integer',
    ];
}
