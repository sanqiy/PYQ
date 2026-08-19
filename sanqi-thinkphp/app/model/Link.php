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
 * 友情链接表
 *
 * @property int    $id     主键ID
 * @property string $url    链接地址
 * @property string $urls   链接说明
 * @property string $urlimg 链接图标
 */
class Link extends Model
{
    protected $name = 'link';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    protected $type = [
        'id' => 'integer',
    ];
}
