<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 评论表
 *
 * @property int    $id       主键ID
 * @property string $couser   评论用户
 * @property string $coname   用户昵称
 * @property string $courl    用户网址
 * @property string $cotext   评论内容
 * @property string $bcouser  被回复用户
 * @property string $bconame  被回复昵称
 * @property int    $comaud   审核状态
 * @property string $cotime   评论时间
 * @property string $ip       评论IP
 * @property string $wzcid    文章CID
 * @property string $ecid     评论CID
 *
 * @property-read User  $user
 * @property-read Essay $essay
 */
class Comm extends Model
{
    use SoftDelete;

    protected $name = 'comm';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;
    protected $deleteTime = 'delete_time';
    protected $schema = [
        'id' => 'int',
        'couser' => 'string',
        'coname' => 'string',
        'courl' => 'string',
        'coemail' => 'string',
        'coimg' => 'string',
        'cotext' => 'string',
        'bcouser' => 'string',
        'bconame' => 'string',
        'comaud' => 'int',
        'cotime' => 'datetime',
        'ip' => 'string',
        'wzcid' => 'string',
        'ecid' => 'string',
        'delete_time' => 'datetime',
    ];

    protected $type = [
        'id'     => 'integer',
        'comaud' => 'integer',
    ];

    /**
     * 评论者用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'couser', 'username');
    }

    /**
     * 所属文章
     */
    public function essay()
    {
        return $this->belongsTo(Essay::class, 'wzcid', 'cid');
    }

    /**
     * 是否已审核
     */
    public function isApproved(): bool
    {
        return $this->comaud === 1;
    }

    /**
     * 是否有回复对象
     */
    public function hasReply(): bool
    {
        return $this->bconame !== '' && $this->bconame !== 'false';
    }
}
