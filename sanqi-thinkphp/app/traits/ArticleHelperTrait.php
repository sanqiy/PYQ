<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\traits;

trait ArticleHelperTrait
{
    /**
     * 批量预取用户头像和昵称，填充到 helper 静态缓存中
     * 在渲染文章/评论列表前调用，将 N 次查询合并为 1 次
     */
    protected function prefetchUsers(array $usernames): void
    {
        $usernames = array_values(array_unique(array_filter($usernames)));
        if (empty($usernames)) {
            return;
        }

        $users = \think\facade\Db::name('user')
            ->whereIn('username', $usernames)
            ->field('username,name,img,email')
            ->select();

        foreach ($users as $u) {
            $name = trim((string)($u['name'] ?: $u['username']));
            $img = buildAvatarUrl($u, '/assets/img/tx.png');

            // 填充到 getUserDisplayName 的静态缓存
            $GLOBALS['__mm_display_name_cache'][$u['username']] = $name;
            // 填充到 getUserAvatar 的静态缓存
            $GLOBALS['__mm_avatar_cache'][$u['username']] = $img;
        }
    }

    protected function prefetchPostMeta(array $articles, $user = null)
    {
        $cids = [];
        foreach ($articles as $article) {
            if (!empty($article['cid'])) {
                $cids[(string)$article['cid']] = true;
            }
        }

        $cids = array_keys($cids);
        $likesByCid = [];
        $commentsByCid = [];
        $likedCids = [];

        foreach ($cids as $cid) {
            $likesByCid[$cid] = [];
            $commentsByCid[$cid] = [];
            $likedCids[$cid] = false;
        }

        if (empty($cids)) {
            return [
                'likesByCid' => $likesByCid,
                'commentsByCid' => $commentsByCid,
                'likedCids' => $likedCids,
            ];
        }

        // 批量查询点赞
        $likes = \app\model\Lcke::whereIn('lwz', $cids)
            ->order('id', 'asc')
            ->select();
        foreach ($likes as $like) {
            $lwz = (string)($like['lwz'] ?? '');
            if ($lwz !== '' && isset($likesByCid[$lwz])) {
                $likesByCid[$lwz][] = $like;
            }
        }

        // 批量查询评论（排除未审核和已删除）
        $comments = \app\model\Comm::whereIn('wzcid', $cids)
            ->where('comaud', '<>', '0')
            ->where('comaud', '<>', '-1')
            ->order('wzcid', 'asc')
            ->order('id', 'asc')
            ->select();
        foreach ($comments as $comment) {
            $wzcid = (string)($comment['wzcid'] ?? '');
            if ($wzcid !== '' && isset($commentsByCid[$wzcid])) {
                $commentsByCid[$wzcid][] = $comment;
            }
        }

        // 批量查询当前用户已点赞状态
        $likeUser = '';
        if (!empty($user['username'])) {
            $likeUser = (string)$user['username'];
        } else {
            $visitorId = session('visykmz_userip');
            if (empty($visitorId)) {
                $visitor = visitorIdentity();
                $visitorId = $visitor['id'] ?? '';
            }
            if (!empty($visitorId)) {
                $likeUser = (string)$visitorId;
            }
        }

        if ($likeUser !== '') {
            $likedCidList = \app\model\Lcke::whereIn('lwz', $cids)
                ->where('luser', $likeUser)
                ->column('lwz');
            foreach ($likedCidList as $likedCid) {
                $key = (string)$likedCid;
                if (isset($likedCids[$key])) {
                    $likedCids[$key] = true;
                }
            }
        }

        return [
            'likesByCid' => $likesByCid,
            'commentsByCid' => $commentsByCid,
            'likedCids' => $likedCids,
        ];
    }
}
