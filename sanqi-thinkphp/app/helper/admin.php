<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 后台管理与模板辅助函数
 */

/**
 * 获取权限配置值
 */
function authority_value(array $siteConfig, string $name, string $default = '0'): string
{
    return (string)($siteConfig[$name] ?? $default);
}

/**
 * 将数组编码为可在 HTML 属性中安全使用的 JSON 字符串
 */
function admin_json_attr(array $data): string
{
    return htmlspecialchars(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
}

/**
 * 后台菜单激活判断
 */
function admin_active(string $pageTitleText, array|string $names): string
{
    foreach ((array)$names as $name) {
        if ($pageTitleText === $name) {
            return 'active';
        }
    }
    return '';
}
