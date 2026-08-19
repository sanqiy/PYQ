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
 * 文章表
 *
 * @property int    $id             主键ID
 * @property string $ptpuser        发布用户
 * @property int    $is_anonymous   是否匿名发布
 * @property string $article_title  文章标题
 * @property string $article_cover  文章封面
 * @property string $cover_color    封面色调
 * @property string $ptptext        文章内容
 * @property string $ptpimag        文章图片
 * @property string $ptpvideo       文章视频
 * @property string $ptpmusic       文章音乐
 * @property string $ptplx          文章类型
 * @property string $ptpdw          文章定位
 * @property string $tags           标签
 * @property string $ptptime        发布时间
 * @property int    $ptpgg          公告状态
 * @property string $ptpggurl       公告链接
 * @property int    $ptpys          文章可见性
 * @property int    $commauth       评论权限
 * @property int    $ptpaud         审核状态
 * @property string $ip             发布IP
 * @property string $cid            内容ID
 * @property int    $user_top       用户置顶
 * @property string $article_template 文章模板
 *
 * @property-read User $author
 * @property-read Comm[] $comments
 * @property-read Lcke[] $likes
 */
class Essay extends Model
{
    use SoftDelete;

    protected $name = 'essay';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;
    protected $deleteTime = 'delete_time';

    protected $type = [
        'id'        => 'integer',
        'ptpgg'     => 'integer',
        'ptpys'     => 'integer',
        'commauth'  => 'integer',
        'ptpaud'    => 'integer',
        'is_anonymous' => 'integer',
        'user_top'  => 'integer',
    ];

    /**
     * 发布者用户
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'ptpuser', 'username');
    }

    /**
     * 文章评论
     */
    public function comments()
    {
        return $this->hasMany(Comm::class, 'wzcid', 'cid');
    }

    /**
     * 文章点赞
     */
    public function likes()
    {
        return $this->hasMany(Lcke::class, 'lwz', 'cid');
    }

    /**
     * 图片列表（按分隔符拆分）
     */
    public function getImageListAttr($value, array $data): array
    {
        if (empty($data['ptpimag'])) {
            return [];
        }
        return array_values(array_filter(explode('(+@+)', $data['ptpimag'])));
    }

    /**
     * 标签列表
     */
    public function getTagListAttr($value, array $data): array
    {
        if (empty($data['tags'])) {
            return [];
        }
        return array_values(array_filter(explode(',', $data['tags'])));
    }

    /**
     * 是否为广告
     */
    public function isAd(): bool
    {
        return $this->ptpgg === 1;
    }

    /**
     * 是否已审核
     */
    public function isApproved(): bool
    {
        return $this->ptpaud === 1;
    }

    /**
     * 是否可见
     */
    public function isVisible(): bool
    {
        return $this->ptpys !== 0;
    }

    /**
     * 是否长文章类型
     */
    public function isArticle(): bool
    {
        return $this->ptplx === 'article';
    }

    /**
     * 评论是否开启
     */
    public function commentEnabled(): bool
    {
        return $this->commauth === 1;
    }
}
