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

class AuthValidate extends Validate
{
    protected $rule = [
        'zh'        => 'require|length:3,32',
        'mm'        => 'require|length:3,72',
        'email'     => 'require|email',
        'useke'     => 'require|length:3,32',
        'useem'     => 'require|email',
        'safyzm'    => 'require',
        'safxmm'    => 'require|length:8,72',
    ];

    protected $message = [
        'zh.require'       => '账号不能为空',
        'zh.length'        => '账号长度为3-32位',
        'mm.require'       => '密码不能为空',
        'mm.length'        => '密码长度为3-72位',
        'email.require'    => '邮箱不能为空',
        'email.email'      => '邮箱格式不正确',
        'useke.require'    => '账号不能为空',
        'useke.length'     => '账号长度为3-32位',
        'useem.require'    => '邮箱不能为空',
        'useem.email'      => '邮箱格式不正确',
        'safyzm.require'   => '验证码不能为空',
        'safxmm.require'   => '新密码不能为空',
        'safxmm.length'    => '密码长度为8-72位',
    ];

    protected $scene = [
        'login'    => ['zh', 'mm'],
        'register' => ['zh', 'mm', 'email'],
        'repass'   => ['useke', 'useem'],
        'reset'    => ['safyzm', 'safxmm'],
    ];
}
