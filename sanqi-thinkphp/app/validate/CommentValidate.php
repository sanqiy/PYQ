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

class CommentValidate extends Validate
{
    protected $rule = [
        'tieid'    => 'require',
        'pltext'   => 'require|max:1000',
        'vis_name' => 'requireIf:__is_guest__|max:20',
        'vis_email'=> 'requireIf:__is_guest__|email',
        'page'     => 'integer|>=:1',
        'wzcidd'   => 'require',
        'plid'     => 'require',
    ];

    protected $message = [
        'tieid.require'       => '文章ID不能为空',
        'pltext.require'      => '评论内容不能为空',
        'pltext.max'          => '评论内容不能超过1000个字符',
        'vis_name.requireIf'  => '昵称不能为空',
        'vis_name.max'        => '昵称不能超过20个字符',
        'vis_email.requireIf' => '邮箱不能为空',
        'vis_email.email'     => '邮箱格式不正确',
        'page.integer'        => '页码必须为整数',
        'page.>='             => '页码必须大于0',
        'wzcidd.require'      => '文章ID不能为空',
        'plid.require'        => '评论ID不能为空',
    ];

    protected $scene = [
        'submit' => ['tieid', 'pltext'],
        'load'   => ['wzcidd'],
        'delete' => ['plid'],
    ];
}
