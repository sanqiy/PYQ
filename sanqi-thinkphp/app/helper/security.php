<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 安全过滤与HTML清理函数
 */

/**
 * 安全过滤（BUG-08: 移除 addslashes，仅保留 htmlspecialchars）
 */
function safeFilter(array|string $value): array|string
{
    if (is_array($value)) {
        return array_map('safeFilter', $value);
    }
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * 清理XSS代码
 */
function cleanXss(array|string $value): array|string
{
    if (is_array($value)) {
        return array_map('cleanXss', $value);
    }
    $value = strip_tags($value);
    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    return $value;
}

/**
 * 朋友圈文章内容白名单过滤。
 * 保留换行、表情图片和少量文本标签，移除脚本、事件属性和危险协议。
 */
function cleanArticleHtml(array|string $html): array|string
{
    if (is_array($html)) {
        return array_map('cleanArticleHtml', $html);
    }

    $html = str_replace("\0", '', (string)$html);
    $html = preg_replace('/<!--.*?-->/s', '', $html);
    $html = preg_replace('/<\s*(script|style|iframe|object|embed|link|meta|svg|math|form|input|button|textarea|select|video|audio|source)\b[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $html);
    $html = preg_replace('/<\s*\/?\s*(script|style|iframe|object|embed|link|meta|svg|math|form|input|button|textarea|select|video|audio|source)\b[^>]*>/is', '', $html);

    return preg_replace_callback('/<\s*(\/?)([a-zA-Z0-9]+)([^>]*)>/s', function ($match) {
        $closing = $match[1] === '/';
        $tag = strtolower($match[2]);
        $attrText = $match[3] ?? '';
        $allowedTags = ['br', 'span', 'img', 'a'];

        if (!in_array($tag, $allowedTags, true)) {
            return '';
        }

        if ($tag === 'br') {
            return '<br>';
        }

        if ($closing) {
            return in_array($tag, ['span', 'a'], true) ? '</' . $tag . '>' : '';
        }

        $attrs = cleanHtmlAttributes($tag, $attrText);
        return '<' . $tag . $attrs . '>';
    }, $html);
}

function cleanHtmlAttributes(string $tag, string $attrText): string
{
    $allowed = [
        'span' => ['class', 'lang', 'title'],
        'img'  => ['src', 'alt', 'class', 'title'],
        'a'    => ['href', 'class', 'title', 'target', 'rel', 'data-fancybox', 'data-caption'],
    ];

    $attrs = '';
    if (!preg_match_all('/([a-zA-Z_:][-a-zA-Z0-9_:.]*)\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))/s', $attrText, $matches, PREG_SET_ORDER)) {
        return '';
    }

    foreach ($matches as $match) {
        $name = strtolower($match[1]);
        $value = $match[3] !== '' ? $match[3] : ($match[4] !== '' ? $match[4] : ($match[5] ?? ''));

        // 拒绝事件属性和内联样式
        if (strpos($name, 'on') === 0 || $name === 'style') {
            continue;
        }

        // 只允许显式白名单中的属性，不再放行任意 data-*
        if (!in_array($name, $allowed[$tag] ?? [], true)) {
            continue;
        }

        // URL 类属性校验
        if (in_array($name, ['href', 'src'], true) && !isSafeHtmlUrl($value)) {
            continue;
        }

        // span 的 class 只允许 owo 表情样式
        if ($tag === 'span' && $name === 'class' && !preg_match('/^[a-zA-Z0-9_\- ]+$/', $value)) {
            continue;
        }

        if ($name === 'target' && !in_array($value, ['_blank', '_self'], true)) {
            continue;
        }

        $attrs .= ' ' . $name . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
    }

    if ($tag === 'a' && strpos($attrs, ' target="_blank"') !== false && strpos($attrs, ' rel=') === false) {
        $attrs .= ' rel="noopener noreferrer"';
    }

    return $attrs;
}

function isSafeHtmlUrl(string $url): bool
{
    $url = trim((string)$url);
    if ($url === '') {
        return false;
    }
    if (preg_match('/[\x00-\x1F\x7F]/', $url)) {
        return false;
    }
    if (preg_match('#^\s*(javascript|vbscript|data|file)\s*:#i', $url)) {
        return false;
    }
    return preg_match('#^(https?:)?//#i', $url)
        || preg_match('#^/(?!/)#', $url)
        || preg_match('~^[A-Za-z0-9_\-./?&=%+#:]+$~', $url);
}

/**
 * 头部自定义样式过滤，只允许 style 标签内的 CSS 文本。
 * BUG-13: 移除 htmlspecialchars，原样输出 CSS。
 */
function cleanHeadStyleHtml(string $html): string
{
    $html = (string)$html;
    if ($html === '') {
        return '';
    }

    $out = '';
    if (preg_match_all('/<style\b[^>]*>(.*?)<\/style>/is', $html, $matches)) {
        foreach ($matches[1] as $css) {
            $css = preg_replace('/expression\s*\(/i', '', $css);
            $css = preg_replace('/javascript\s*:/i', '', $css);
            $css = preg_replace('/@import\b/i', '', $css);
            $css = str_replace('</style', '<\/style', $css);
            $out .= '<style>' . $css . '</style>';
        }
    }

    return $out;
}

/**
 * HTML转义快捷函数（供视图模板使用）
 */
function sanitizeHtml(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * 安全URL（拒绝javascript:等危险协议）
 */
function sanitizeUrl(string $url): string
{
    $url = trim((string)$url);
    if (preg_match('#^\s*(javascript|vbscript|data)\s*:#i', $url)) {
        return '#';
    }
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}
