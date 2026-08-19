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

/**
 * 表情模型
 *
 * @property int    $id         主键
 * @property string $name       表情名称
 * @property string $code       触发码
 * @property string $filename   图片文件名
 * @property string $category   分类
 * @property int    $sort_order 排序
 * @property int    $status     状态 1启用 0禁用
 */
class Emoji extends Model
{
    protected $name = 'emoji';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    protected $type = [
        'id'         => 'integer',
        'sort_order' => 'integer',
        'status'     => 'integer',
    ];
}
