<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * URL与资源路径辅助函数
 */

/**
 * 列表缩略图 URL。非本地上传图片或缩略图生成失败时回退原图。
 */
function thumbUrl(string $url): string
{
    return \app\service\ImageService::thumbnailUrl((string)$url);
}

/**
 * 静态资源 URL，自动追加版本号参数用于发布后刷新浏览器缓存。
 * asset_version 配置值：
 *   - 非空字符串：使用该值作为全局版本号（如 '1.0.0'）
 *   - 'auto'：基于文件修改时间自动生成版本号（开发环境推荐）
 *   - 空/null：不追加版本号
 */
function staticUrl(string $url, ?string $version = null): string
{
    $url = trim((string)$url);
    if ($url === '') {
        return '';
    }

    $version = $version ?? config('app.asset_version', config('app.app_version', '1'));
    if ($version === '' || $version === null) {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }

    if ($version === 'auto') {
        $publicPath = public_path(ltrim($url, '/'));
        if (is_file($publicPath)) {
            $version = (string)filemtime($publicPath);
        } else {
            $version = '0';
        }
    }

    $separator = strpos($url, '?') === false ? '?' : '&';
    return htmlspecialchars($url . $separator . 'v=' . rawurlencode((string)$version), ENT_QUOTES, 'UTF-8');
}

/**
 * 将旧版 ./assets、./user、./upload 相对资源路径转换为站点根路径。
 */
function assetUrl(string $url): string
{
    $url = trim((string)$url);
    if ($url === '') {
        return '';
    }

    // 拒绝危险协议
    if (preg_match('#^\s*(javascript|vbscript)\s*:#i', $url)) {
        return '#';
    }

    if (preg_match('#^(https?:)?//#i', $url) || strpos($url, 'data:') === 0) {
        // 外部 http:// 升级为 https://，避免混合内容警告
        if (strpos($url, 'http://') === 0) {
            $url = 'https://' . substr($url, 7);
        }
        return $url;
    }

    if (strpos($url, './') === 0) {
        return '/' . substr($url, 2);
    }

    return $url;
}
