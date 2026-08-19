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
 * 用户表
 *
 * @property int    $id       主键ID
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $email    邮箱
 * @property string $name     昵称
 * @property string $img      头像
 * @property string $url      网址
 * @property string $homeimg  主页图片
 * @property string $sign     签名
 * @property int    $essqx    文章权限
 * @property int    $esseam   邮件通知
 * @property string $regtime  注册时间
 * @property string $regip    注册IP
 * @property string $logtime  登录时间
 * @property string $logip    登录IP
 * @property int    $ban      封禁状态
 * @property string $bantime  封禁时间
 * @property string $passid   密码重置ID
 * @property string $role     角色（user/admin）
 * @property string $alipay_qr 支付宝收款码
 * @property string $wechat_qr 微信收款码
 *
 * @property-read Essay[] $essays
 * @property-read Comm[]  $comments
 * @property-read Lcke[]  $likes
 */
class User extends Model
{
    protected $name = 'user';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;

    protected $type = [
        'id'      => 'integer',
        'essqx'   => 'integer',
        'esseam'  => 'integer',
        'ban'     => 'integer',
    ];

    /**
     * 用户发布的文章
     */
    public function essays()
    {
        return $this->hasMany(Essay::class, 'ptpuser', 'username');
    }

    /**
     * 用户的评论
     */
    public function comments()
    {
        return $this->hasMany(Comm::class, 'couser', 'username');
    }

    /**
     * 用户的点赞
     */
    public function likes()
    {
        return $this->hasMany(Lcke::class, 'luser', 'username');
    }

    /**
     * 用户收到的消息
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'suser', 'username');
    }

    /**
     * 是否是管理员
     */
    public function isAdmin(): bool
    {
        return (string)($this->role ?? '') === 'admin';
    }

    /**
     * 是否被封禁
     */
    public function isBanned(): bool
    {
        if ($this->ban === 0) {
            return false;
        }

        if ($this->bantime === 'true' || $this->bantime === '' || $this->bantime === null) {
            return true;
        }

        $ts = strtotime($this->bantime);
        return $ts !== false && $ts > time();
    }

    /**
     * 是否有发布权限
     */
    public function canPublish(): bool
    {
        return in_array((string)$this->essqx, ['1', '2'], true);
    }

    /**
     * 头像URL（空则返回默认）
     */
    public function getAvatarUrlAttr(): string
    {
        return $this->img !== '' ? $this->img : '/assets/img/tx.png';
    }
}
