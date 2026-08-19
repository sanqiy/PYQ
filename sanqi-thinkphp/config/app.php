<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => false,

    // 静态资源版本号（用于缓存刷新）
    'asset_version'    => '1.1.1',

    // 应用版本号（用于版本更新检查）
    'app_version'      => '1.0.0',

    // 应用密钥（用于 Cookie/Token 签名，从 .env 的 APP_KEY 读取）
    'app_key'          => env('APP_KEY', ''),

    // cURL CA 证书路径，留空时自动尝试 PHP/系统常见 cacert.pem 位置
    'curl_cainfo'      => env('CURL_CAINFO', ''),
];
