<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\validate;

use think\Validate;

class ArticleValidate extends Validate
{
    protected $rule = [
        'cid'            => 'require',
        'type'           => 'in:only,img,video,music,article',
        'article_title'  => 'requireIf:type,article|max:200',
        'text'           => 'requireIf:type,only|max:100000',
        'image_urls'     => 'array|max:15',
        'lx'             => 'require|in:sw,qx',
    ];

    protected $message = [
        'cid.require'           => '参数错误',
        'type.in'               => '无效的内容类型',
        'article_title.requireIf' => '请填写文章标题',
        'article_title.max'     => '标题不能超过200个字符',
        'text.requireIf'        => '内容不能为空',
        'text.max'              => '内容不能超过100000个字符',
        'image_urls.max'        => '不可超过15张图片',
        'lx.require'            => '参数错误',
        'lx.in'                 => '无效的操作类型',
    ];

    protected $scene = [
        'save'     => ['type'],
        'delete'   => ['cid'],
        'privacy'  => ['cid'],
        'pin'      => ['cid', 'lx'],
        'userPin'  => ['cid', 'lx'],
    ];
}
