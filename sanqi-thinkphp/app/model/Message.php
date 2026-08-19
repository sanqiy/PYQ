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
 * 通知消息表
 *
 * @property int    $id         主键ID
 * @property string $fuser      发送用户
 * @property string $fimg       用户头像
 * @property string $fname      用户昵称
 * @property string $suser      接收用户
 * @property int    $type       通知类型
 * @property string $related_id 关联ID
 * @property string $title      消息标题
 * @property string $text       消息内容
 * @property string $ftime      消息时间
 * @property int    $msg        已读状态
 */
class Message extends Model
{
    protected $name = 'message';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    // 通知类型
    const TYPE_COMMENT = 0; // 评论通知
    const TYPE_LIKE    = 1; // 点赞通知
    const TYPE_AUDIT   = 2; // 审核结果通知
    const TYPE_SYSTEM  = 3; // 系统公告
    const TYPE_EMAIL   = 4; // 邮件发送状态
    const TYPE_MENTION = 5; // @提及通知

    // 消息状态
    const STATUS_UNREAD  = 0;
    const STATUS_READ    = 1;
    const STATUS_DELETED = -1;

    // 类型显示名称
    const TYPE_NAMES = [
        self::TYPE_COMMENT => '评论',
        self::TYPE_LIKE    => '点赞',
        self::TYPE_AUDIT   => '审核',
        self::TYPE_SYSTEM  => '系统',
        self::TYPE_EMAIL   => '邮件',
        self::TYPE_MENTION => '提及',
    ];

    // 类型默认标题
    const TYPE_TITLES = [
        self::TYPE_COMMENT => '评论了你的文章',
        self::TYPE_LIKE    => '赞了你的文章',
        self::TYPE_AUDIT   => '审核结果',
        self::TYPE_SYSTEM  => '系统通知',
        self::TYPE_EMAIL   => '邮件发送',
        self::TYPE_MENTION => '在评论中提到了你',
    ];

    protected $type = [
        'id'        => 'integer',
        'type'      => 'integer',
        'msg'       => 'integer',
    ];

    /**
     * 是否未读
     */
    public function isUnread(): bool
    {
        return $this->msg === self::STATUS_UNREAD;
    }

    /**
     * 是否已删除
     */
    public function isDeleted(): bool
    {
        return $this->msg === self::STATUS_DELETED;
    }

    /**
     * 通知类型显示名称
     */
    public function getTypeName(): string
    {
        return self::TYPE_NAMES[$this->type] ?? '未知';
    }

    /**
     * 通知标题（基于类型）
     */
    public function getDisplayTitle(): string
    {
        return self::TYPE_TITLES[$this->type] ?? $this->title;
    }

    /**
     * 是否与文章相关
     */
    public function hasRelatedArticle(): bool
    {
        return in_array($this->type, [self::TYPE_COMMENT, self::TYPE_LIKE, self::TYPE_AUDIT, self::TYPE_MENTION])
            && !empty($this->related_id);
    }

    // ---- 查询 Scope ----

    /**
     * 按通知类型筛选
     */
    public function scopeOfType($query, int $type)
    {
        return $query->where('type', $type);
    }

    /**
     * 仅未读
     */
    public function scopeUnread($query)
    {
        return $query->where('msg', self::STATUS_UNREAD);
    }

    /**
     * 未删除
     */
    public function scopeNotDeleted($query)
    {
        return $query->where('msg', '<>', self::STATUS_DELETED);
    }

    /**
     * 给某个接收者
     */
    public function scopeForUser($query, string $username)
    {
        return $query->where('suser', $username);
    }
}
