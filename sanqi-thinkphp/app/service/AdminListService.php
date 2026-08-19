<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

class AdminListService
{
    const PAGE_SIZE = 20;
    const MAX_PAGE = 5000;
    const MAX_KEYWORD_LENGTH = 64;
    const MIN_KEYWORD_LENGTH = 2;

    public static function page($value)
    {
        return min(self::MAX_PAGE, max(1, intval($value)));
    }

    public static function pageSize()
    {
        return self::PAGE_SIZE;
    }

    public static function offset($page)
    {
        return ($page - 1) * self::PAGE_SIZE;
    }

    public static function keyword($value)
    {
        $keyword = trim((string)$value);
        $keyword = preg_replace('/\s+/u', ' ', $keyword);
        if (function_exists('mb_substr')) {
            $keyword = mb_substr($keyword, 0, self::MAX_KEYWORD_LENGTH, 'UTF-8');
        } else {
            $keyword = substr($keyword, 0, self::MAX_KEYWORD_LENGTH);
        }
        return $keyword;
    }

    public static function canSearch($keyword)
    {
        if ($keyword === '') {
            return false;
        }
        $length = function_exists('mb_strlen') ? mb_strlen($keyword, 'UTF-8') : strlen($keyword);
        return $length >= self::MIN_KEYWORD_LENGTH;
    }

    public static function prefixLike($keyword)
    {
        return strtr($keyword, ['\\' => '\\\\', '%' => '\\%', '_' => '\\_']) . '%';
    }

    public static function pageWindow($page, $totalPages, $radius = 2)
    {
        $totalPages = max(1, (int)$totalPages);
        $page = min(max(1, (int)$page), $totalPages);
        $start = max(1, $page - $radius);
        $end = min($totalPages, $page + $radius);

        if ($end - $start < $radius * 2) {
            $start = max(1, min($start, $end - $radius * 2));
            $end = min($totalPages, max($end, $start + $radius * 2));
        }

        return range($start, $end);
    }
}
