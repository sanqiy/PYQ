<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\service;

use app\model\Message;
use app\model\Comm;
use app\model\Lcke;
use app\model\Essay;
use app\model\Link;

/**
 * 视图数据预取服务
 * 将视图中的数据库查询集中到服务层
 */
class ViewDataService
{
    /**
     * 获取未读消息数量
     */
    public static function getUnreadMessageCount(?string $username): int
    {
        if (empty($username)) {
            return 0;
        }
        return Message::where('suser', $username)->where('msg', Message::STATUS_UNREAD)->count();
    }

    /**
     * 获取用户消息列表（用于消息弹窗）
     */
    public static function getUserMessages(?string $username): array
    {
        if (empty($username)) {
            return [];
        }
        return Message::where('suser', $username)
            ->where('msg', '<>', Message::STATUS_DELETED)
            ->order('id', 'desc')
            ->select()
            ->toArray();
    }

    /**
     * 批量获取消息关联的文章和操作信息
     * 返回格式：[messageId => ['article' => ..., 'articleCid' => ...]]
     */
    public static function prefetchMessageArticles(array $messages): array
    {
        $result = [];
        $cids = [];

        // 收集需要回退查找的旧数据
        $legacyLikes = [];
        $legacyComments = [];

        foreach ($messages as $msg) {
            $articleCid = '#-1';
            $msgType = $msg['type'] ?? 0;

            if ($msgType === Message::TYPE_LIKE || $msgType === Message::TYPE_COMMENT || $msgType === Message::TYPE_AUDIT || $msgType === Message::TYPE_MENTION) {
                if (!empty($msg['related_id'])) {
                    $articleCid = $msg['related_id'];
                } else {
                    // 兼容旧数据：收集待批量查询的条件
                    if (($msg['title'] ?? '') === '赞了你的文章') {
                        $legacyLikes[$msg['id']] = ['fuser' => $msg['fuser'], 'ftime' => $msg['ftime']];
                    } else {
                        $legacyComments[$msg['id']] = ['fuser' => $msg['fuser'], 'ftime' => $msg['ftime']];
                    }
                }
            }
            $cids[$msg['id']] = $articleCid;
        }

        // 批量查询旧数据的点赞
        if (!empty($legacyLikes)) {
            $likeConditions = array_map(fn($v) => $v['fuser'] . '|' . $v['ftime'], $legacyLikes);
            $likes = Lcke::whereIn('luser', array_column($legacyLikes, 'fuser'))
                ->select()->toArray();
            $likeMap = [];
            foreach ($likes as $like) {
                $key = $like['luser'] . '|' . $like['ltime'];
                $likeMap[$key] = $like['lwz'];
            }
            foreach ($legacyLikes as $msgId => $cond) {
                $key = $cond['fuser'] . '|' . $cond['ftime'];
                $cids[$msgId] = $likeMap[$key] ?? '#-1';
            }
        }

        // 批量查询旧数据的评论
        if (!empty($legacyComments)) {
            $comments = Comm::whereIn('couser', array_column($legacyComments, 'fuser'))
                ->select()->toArray();
            $commentMap = [];
            foreach ($comments as $comment) {
                $key = $comment['couser'] . '|' . $comment['cotime'];
                $commentMap[$key] = $comment['wzcid'];
            }
            foreach ($legacyComments as $msgId => $cond) {
                $key = $cond['fuser'] . '|' . $cond['ftime'];
                $cids[$msgId] = $commentMap[$key] ?? '#-1';
            }
        }

        // 批量查询文章
        $validCids = array_filter($cids, fn($cid) => $cid !== '#-1');
        $articles = [];
        if (!empty($validCids)) {
            $articleList = Essay::whereIn('cid', array_values($validCids))
                ->field('cid,ptplx,ptpimag,ptptext')
                ->select()
                ->toArray();
            foreach ($articleList as $article) {
                $articles[(string)$article['cid']] = $article;
            }
        }

        // 组装结果
        foreach ($messages as $msg) {
            $articleCid = $cids[$msg['id']] ?? '#-1';
            $result[$msg['id']] = [
                'articleCid' => $articleCid,
                'article' => $articleCid !== '#-1' ? ($articles[$articleCid] ?? null) : null,
            ];
        }

        return $result;
    }

    /**
     * 获取友链列表
     */
    public static function getLinks(): array
    {
        return Link::order('id', 'asc')->select()->toArray();
    }

    /**
     * 获取公共视图数据（用于 layout）
     * 包含：未读消息数、友链、消息列表等
     */
    public static function getCommonViewData(?string $username, array $siteConfig): array
    {
        $lnkztFlag = isFlag($siteConfig['lnkzt'] ?? 0);
        $messages = self::getUserMessages($username);
        $messageArticles = self::prefetchMessageArticles($messages);

        return [
            'unreadMessageCount' => self::getUnreadMessageCount($username),
            'links' => $lnkztFlag === 0 ? self::getLinks() : [],
            'messages' => $messages,
            'messageArticles' => $messageArticles,
        ];
    }

    /**
     * 获取后台布局数据（待审核数量等）
     */
    public static function getAdminViewData(): array
    {
        $pendingArticles = Essay::where('ptpaud', 0)->count();
        $pendingComments = Comm::where('comaud', 0)->count();

        return [
            'pendingArticles' => $pendingArticles,
            'pendingComments' => $pendingComments,
            'pendingTotal' => $pendingArticles + $pendingComments,
        ];
    }
}
