<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 基础工具函数
 */

/**
 * 将数据库/配置中的状态字段转为整数，用于严格比较。
 */
function isFlag(mixed $value, int $default = 0): int
{
    if ($value === null || $value === '') {
        return (int)$default;
    }

    if (is_bool($value)) {
        return $value ? 1 : 0;
    }

    if (is_int($value)) {
        return $value;
    }

    $value = trim((string)$value);
    return preg_match('/^-?\d+$/', $value) ? (int)$value : (int)$default;
}

/**
 * 生成随机字符串
 */
function randomString(int $length = 16, string $type = 'alnum'): string
{
    switch ($type) {
        case 'alnum':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'alpha':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 'numeric':
            $chars = '0123456789';
            break;
        case 'nozero':
            $chars = '123456789';
            break;
        default:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    }

    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $string;
}

/**
 * 生成唯一ID
 */
function uniqueId(): string
{
    return bin2hex(random_bytes(16));
}

/**
 * 截取字符串
 */
function strCut(string $string, int $length, string $suffix = '...'): string
{
    if (mb_strlen($string, 'UTF-8') <= $length) {
        return $string;
    }
    return mb_substr($string, 0, $length, 'UTF-8') . $suffix;
}

/**
 * 获取客户端IP
 */
function getClientIp(): string
{
    return request()->ip();
}

/**
 * 判断是否Ajax请求
 */
function isAjax(): bool
{
    return request()->isAjax();
}

/**
 * 判断是否POST请求
 */
function isPost(): bool
{
    return request()->isPost();
}

/**
 * 判断是否移动端
 */
function isMobile(): bool
{
    $userAgent = request()->header('user-agent', '');
    $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'Windows Phone', 'BlackBerry', 'Opera Mini', 'IEMobile'];

    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * 验证邮箱格式
 */
function siteFontOptions(): array
{
    return [
        'default' => [
            'label' => '跟随浏览器默认',
            'family' => '',
        ],
        'wechat_system' => [
            'label' => '公众号阅读字体',
            'family' => '-apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", Arial, sans-serif',
        ],
        'microsoft_yahei' => [
            'label' => '微软雅黑',
            'family' => '"Microsoft YaHei", "PingFang SC", Arial, sans-serif',
        ],
        'songti' => [
            'label' => '宋体阅读',
            'family' => '"Songti SC", SimSun, "Noto Serif CJK SC", serif',
        ],
        'kaiti' => [
            'label' => '楷体',
            'family' => '"Kaiti SC", KaiTi, STKaiti, serif',
        ],
    ];
}

function siteFontFamily(string $key): string
{
    $options = siteFontOptions();
    $key = trim($key) !== '' ? trim($key) : 'default';
    return (string)($options[$key]['family'] ?? $options['default']['family']);
}

function siteFontValue(string $key): string
{
    $options = siteFontOptions();
    $key = trim($key) !== '' ? trim($key) : 'default';
    return array_key_exists($key, $options) ? $key : 'default';
}

function isEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * 验证URL格式
 */
function isUrl(string $url): bool
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * 生成分页HTML
 */
function pagination(int $total, int $page, int $pageSize, string $urlPattern): string
{
    $totalPages = ceil($total / $pageSize);
    if ($totalPages <= 1) return '';

    $html = '<div class="pagination">';

    // 上一页
    if ($page > 1) {
        $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $page - 1, $urlPattern), ENT_QUOTES, 'UTF-8') . '">上一页</a>';
    }

    // 页码
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            $html .= '<span class="current">' . $i . '</span>';
        } else {
            $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $i, $urlPattern), ENT_QUOTES, 'UTF-8') . '">' . $i . '</a>';
        }
    }

    // 下一页
    if ($page < $totalPages) {
        $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $page + 1, $urlPattern), ENT_QUOTES, 'UTF-8') . '">下一页</a>';
    }

    $html .= '</div>';
    return $html;
}

/**
 * 检测文件MIME类型（自动回退：finfo → mime_content_type → 扩展名推断）
 */
function detectMimeType(string $filepath, string $ext = ''): string
{
    // 优先：fileinfo 扩展
    if (class_exists('finfo')) {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($filepath);
        if ($mime && $mime !== 'application/octet-stream') {
            return $mime;
        }
    }

    // 备用：mime_content_type（PHP 7.2+ 内置）
    if (function_exists('mime_content_type')) {
        $mime = mime_content_type($filepath);
        if ($mime && $mime !== 'application/octet-stream') {
            return $mime;
        }
    }

    // 兜底：按扩展名推断常见类型
    $ext = strtolower($ext ?: pathinfo($filepath, PATHINFO_EXTENSION));
    $map = [
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'gif' => 'image/gif', 'webp' => 'image/webp', 'bmp' => 'image/bmp',
        'svg' => 'image/svg+xml', 'ico' => 'image/x-icon',
        'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg',
        'mp3' => 'audio/mpeg', 'wav' => 'audio/wav', 'flac' => 'audio/flac',
        'pdf' => 'application/pdf', 'zip' => 'application/zip',
        'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    return $map[$ext] ?? 'application/octet-stream';
}

/**
 * 调试输出
 */
if (!function_exists('dump')) {
    function dump(mixed $var, bool $echo = true): string
    {
        $output = '<pre>' . htmlspecialchars(print_r($var, true), ENT_QUOTES, 'UTF-8') . '</pre>';
        if ($echo) {
            echo $output;
        }
        return $output;
    }
}
