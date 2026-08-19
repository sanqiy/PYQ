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

class UserValidate extends Validate
{
    protected $rule = [
        'lx'      => 'require|in:aq,zlnc,zlqm,zlwz',
        'oldmm'   => 'length:8,72',
        'newmm'   => 'requireWith:oldmm|length:8,72',
        'email'   => 'email',
        'nc'      => 'require|max:10',
        'qm'      => 'max:50',
        'wz'      => 'url',
    ];

    protected $message = [
        'lx.require'      => '参数错误',
        'lx.in'           => '无效的更新类型',
        'oldmm.length'    => '原密码长度为8-72位',
        'newmm.requireWith' => '请输入新密码',
        'newmm.length'    => '新密码长度为8-72位',
        'email.email'     => '邮箱格式不正确',
        'nc.require'      => '昵称不能为空',
        'nc.max'          => '昵称最多10个字',
        'qm.max'          => '签名最多50个字',
        'wz.url'          => '网站格式不正确',
    ];

    protected $scene = [
        'update'         => ['lx'],
        'updateSecurity' => ['oldmm', 'newmm', 'email'],
        'updateName'     => ['nc'],
        'updateSign'     => ['qm'],
        'updateWebsite'  => ['wz'],
    ];
}
