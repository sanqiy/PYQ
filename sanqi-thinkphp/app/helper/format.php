<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 日期时间与格式化函数
 */

/**
 * 时间戳转友好时间
 */
function formatFriendlyDate(int|string $timestamp): string
{
    if (is_string($timestamp)) {
        $ts = strtotime($timestamp);
        $timestamp = $ts !== false ? $ts : 0;
    }

    $diff = time() - $timestamp;

    if ($diff < 60) {
        return '刚刚';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . '分钟前';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . '小时前';
    } elseif ($diff < 2592000) {
        return floor($diff / 86400) . '天前';
    } elseif ($diff < 31536000) {
        return floor($diff / 2592000) . '个月前';
    } else {
        return floor($diff / 31536000) . '年前';
    }
}

/**
 * 获取日期标签
 */
function dateLabel(int|string $date): string
{
    if (is_string($date)) {
        $ts = strtotime($date);
        $date = $ts !== false ? $ts : 0;
    }

    $today = strtotime('today');
    $yesterday = strtotime('yesterday');

    if ($date >= $today) {
        return '今天';
    } elseif ($date >= $yesterday) {
        return '昨天';
    } else {
        return date('m月d日', $date);
    }
}

/**
 * 获取年份标签
 */
function yearLabel(int|string $date): string
{
    if (is_string($date)) {
        $ts = strtotime($date);
        $date = $ts !== false ? $ts : 0;
    }
    return date('Y年', $date);
}

/**
 * 格式化文件大小
 */
function formatSize(int|float $bytes): string
{
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
