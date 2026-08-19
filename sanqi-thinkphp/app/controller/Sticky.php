<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller;

use app\model\Essay;
use app\model\User as UserModel;
use think\facade\Cache;

/**
 * 置顶文章列表控制器
 */
class Sticky extends Base
{
    public function index($type = '')
    {
        // 获取站点配置
        $siteConfig = $this->getSiteConfig();
        $currentUser = $this->getUser();

        // 获取管理员置顶文章ID列表
        $topIds = [];
        if (!empty($siteConfig['topes'])) {
            $topIds = array_values(array_filter(explode("\n", $siteConfig['topes'])));
        }

        $stickyArticles = [];
        $pageTitle = '置顶文章';
        $targetUser = null;
        $now = date('Y-m-d H:i:s');

        if ($type === 'home') {
            // 当前用户的置顶文章（管理员置顶 + 个人置顶）
            if ($currentUser) {
                $adminTop = !empty($topIds) ? array_filter($this->getTopArticles($topIds, $now), function ($article) use ($currentUser) {
                    return ($article['ptpuser'] ?? '') === $currentUser['username'];
                }) : [];
                $userTop = $this->getUserTopArticles($currentUser['username'], $now);
                $stickyArticles = $this->mergeStickyArticles($adminTop, $userTop);
                $pageTitle = '我的置顶文章';
            }
        } elseif (!empty($type)) {
            // 根据hash查找用户
            $users = UserModel::select()->toArray();
            foreach ($users as $u) {
                if (md5(md5($u['username'])) === $type) {
                    $targetUser = $u;
                    break;
                }
            }

            if ($targetUser) {
                $adminTop = !empty($topIds) ? array_filter($this->getTopArticles($topIds, $now), function ($article) use ($targetUser) {
                    return ($article['ptpuser'] ?? '') === $targetUser['username'];
                }) : [];
                $userTop = $this->getUserTopArticles($targetUser['username'], $now);
                $stickyArticles = $this->mergeStickyArticles($adminTop, $userTop);
                $pageTitle = $targetUser['name'] . '的置顶文章';
            }
        } else {
            // 全站所有置顶文章
            $adminTop = !empty($topIds) ? $this->getTopArticles($topIds, $now) : [];
            $userTop = $this->getAllUserTopArticles($now);
            $stickyArticles = $this->mergeStickyArticles($adminTop, $userTop);
            $pageTitle = '全部置顶文章';
        }

        // 渲染视图
        $commonViewData = $this->getCommonViewData();

        return view('sticky/index', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $currentUser,
            'targetUser' => $targetUser,
            'stickyArticles' => $stickyArticles,
            'pageTitle' => $pageTitle,
            'pageJs' => 'home.js',
            'headerBackUrl' => '/',
            'headerTitle' => '&#32622;&#39030;',
            'hideProfileHeader' => true,
            'hideFooter' => true,
        ], $commonViewData));
    }

    /**
     * 获取管理员置顶文章列表
     */
    private function getTopArticles(array $topIds, string $now): array
    {
        return Cache::tag('article')->remember('sticky_list_articles', function () use ($topIds, $now) {
            $rows = Essay::whereIn('cid', $topIds)
                ->where('ptpaud', '1')
                ->where('ptpys', '<>', '0')
                ->where('ptptime', '<=', $now)
                ->field('id,cid,ptpuser,is_anonymous,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,tags,article_title,article_cover,cover_color,user_top')
                ->select()
                ->toArray();

            $byCid = [];
            foreach ($rows as $row) {
                $byCid[$row['cid']] = $row;
            }
            $sorted = [];
            foreach ($topIds as $cid) {
                if (isset($byCid[$cid])) {
                    $sorted[] = $byCid[$cid];
                }
            }
            return $sorted;
        }, 300);
    }

    /**
     * 获取指定用户的个人置顶文章
     */
    private function getUserTopArticles(string $username, string $now): array
    {
        $cacheKey = 'user_top_articles_' . $username;
        return Cache::tag('article')->remember($cacheKey, function () use ($username, $now) {
            return Essay::where('user_top', 1)
                ->where('ptpuser', $username)
                ->where('ptpaud', '1')
                ->where('ptpys', '<>', '0')
                ->where('ptptime', '<=', $now)
                ->field('id,cid,ptpuser,is_anonymous,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,tags,article_title,article_cover,cover_color,user_top')
                ->order('id', 'desc')
                ->select()
                ->toArray();
        }, 300);
    }

    /**
     * 获取所有用户的个人置顶文章
     */
    private function getAllUserTopArticles(string $now): array
    {
        return Cache::tag('article')->remember('all_user_top_articles', function () use ($now) {
            return Essay::where('user_top', 1)
                ->where('ptpaud', '1')
                ->where('ptpys', '<>', '0')
                ->where('ptptime', '<=', $now)
                ->field('id,cid,ptpuser,is_anonymous,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,tags,article_title,article_cover,cover_color,user_top')
                ->order('id', 'desc')
                ->select()
                ->toArray();
        }, 300);
    }

    /**
     * 合并管理员置顶和用户置顶，去重（管理员置顶优先）
     */
    private function mergeStickyArticles(array $adminTop, array $userTop): array
    {
        $seen = [];
        $merged = [];
        foreach ($adminTop as $article) {
            $cid = $article['cid'];
            if (!isset($seen[$cid])) {
                $seen[$cid] = true;
                $merged[] = $article;
            }
        }
        foreach ($userTop as $article) {
            $cid = $article['cid'];
            if (!isset($seen[$cid])) {
                $seen[$cid] = true;
                $merged[] = $article;
            }
        }
        return $merged;
    }
}
