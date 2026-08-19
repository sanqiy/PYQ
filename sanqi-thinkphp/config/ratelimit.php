<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

// 接口限流配置
// max: 时间窗口内最大请求数, window: 时间窗口秒数, lockout: 触发限流后锁定秒数
return [
    // 登录接口
    'login' => [
        'ip'       => ['max' => 20, 'window' => 900,  'lockout' => 900],
        'username' => ['max' => 5,  'window' => 900,  'lockout' => 900],
    ],

    // 注册接口
    'register' => [
        'ip' => ['max' => 5, 'window' => 3600, 'lockout' => 3600],
    ],

    // 找回密码 - 发送验证码
    'repass_send' => [
        'ip'          => ['max' => 5, 'window' => 3600, 'lockout' => 3600],
        'email'       => ['max' => 1, 'window' => 60,   'lockout' => 60],
        'email_hour'  => ['max' => 5, 'window' => 3600, 'lockout' => 3600],
        'username'    => ['max' => 5, 'window' => 3600, 'lockout' => 3600],
    ],

    // 外部解析/代理接口
    'external' => [
        'ip' => ['max' => 20, 'window' => 60, 'lockout' => 300],
    ],
];
