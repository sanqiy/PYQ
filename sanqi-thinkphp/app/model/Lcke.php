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
 * 点赞表
 *
 * @property int    $id     主键ID
 * @property string $luser  点赞用户
 * @property string $lwz    文章标识
 * @property string $ltime  点赞时间
 *
 * @property-read Essay $essay
 */
class Lcke extends Model
{
    protected $name = 'lcke';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;
    protected $schema = [
        'id' => 'int',
        'luser' => 'string',
        'lname' => 'string',
        'limg' => 'string',
        'lwz' => 'string',
        'ltime' => 'datetime',
    ];

    protected $type = [
        'id' => 'integer',
    ];

    /**
     * 所属文章
     */
    public function essay()
    {
        return $this->belongsTo(Essay::class, 'lwz', 'cid');
    }
}
