<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 表情渲染函数
 */

function renderArticleEmojis(string $html): string
{
    $map = articleEmojiMap();
    if (empty($map)) {
        return $html;
    }

    return strtr((string)$html, $map);
}

function articleEmojiMap(): array
{
    return \think\facade\Cache::remember('emoji_map', function () {
        $emojis = \app\model\Emoji::where('status', 1)
            ->order('sort_order', 'asc')
            ->select()
            ->toArray();

        if (empty($emojis)) {
            return [];
        }

        $map = [];
        foreach ($emojis as $emoji) {
            $url = '/assets/owo/' . $emoji['category'] . '/' . $emoji['filename'];
            $code = $emoji['code'];
            $img = '<img src="' . $url . '" class="sh-nr-bq-img" alt="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '">';
            $map[$code] = $img;
        }

        return $map;
    }, 3600);
}

/**
 * 获取表情列表（用于前端选择器渲染）
 */
function emojiPickerList(): array
{
    return \think\facade\Cache::remember('emoji_picker_list', function () {
        return \app\model\Emoji::where('status', 1)
            ->order('sort_order', 'asc')
            ->field('code, filename, category')
            ->select()
            ->toArray();
    }, 3600);
}

/**
 * 清除表情缓存（管理后台增删改后调用）
 */
function clearEmojiCache(): void
{
    \think\facade\Cache::delete('emoji_map');
    \think\facade\Cache::delete('emoji_picker_list');
}
