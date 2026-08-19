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
 * 用户主页控制器
 */
class User extends Base
{
    public function index($hash)
    {
        // 获取站点配置
        $siteConfig = $this->getSiteConfig();
        $currentUser = $this->getUser();

        // 根据hash查找用户（通过缓存映射避免全表扫描）
        $cacheKey = 'user_hash_' . $hash;
        $username = Cache::get($cacheKey);
        if ($username === null) {
            $users = UserModel::field('id,username')->select()->toArray();
            foreach ($users as $u) {
                $mappedHash = md5(md5($u['username']));
                Cache::set('user_hash_' . $mappedHash, $u['username'], 86400);
                if ($mappedHash === $hash) {
                    $username = $u['username'];
                }
            }
        }

        if (empty($username)) {
            return response('用户不存在', 404);
        }

        $targetUser = UserModel::where('username', $username)->find();
        if (!$targetUser) {
            return response('用户不存在', 404);
        }
        $targetUser = $targetUser->toArray();

        // 获取用户置顶文章
        $stickyArticles = [];
        $now = date('Y-m-d H:i:s');
        $stickyArticles = Cache::tag('article')->remember(
            'user_sticky_articles_' . $targetUser['username'],
            function () use ($targetUser, $now) {
                return Essay::where('user_top', 1)
                    ->where('ptpuser', $targetUser['username'])
                    ->where('ptpaud', '1')
                    ->where('ptpys', '<>', '0')
                    ->where('ptptime', '<=', $now)
                    ->field('id,cid,ptpuser,is_anonymous,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,tags,article_title,article_cover,cover_color,user_top')
                    ->order('id', 'desc')
                    ->select()
                    ->toArray();
            },
            300
        );

        // 获取用户公开文章
        $essgs = max(1, min(50, (int)($siteConfig['essgs'] ?? 10)));
        $articles = Essay::where('ptpuser', $targetUser['username'])
            ->where('ptpys', '1')
            ->where('ptpaud', '1')
            ->where('ptptime', '<=', date('Y-m-d H:i:s'))
            ->order('id', 'desc')
            ->limit($essgs)
            ->select()
            ->toArray();

        // 渲染视图
        $commonViewData = $this->getCommonViewData();

        return view('user/index', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $currentUser,
            'targetUser' => $targetUser,
            'articles' => $articles,
            'stickyArticles' => $stickyArticles,
            'stickyLink' => '/sticky/' . $hash,
            'pageTitle' => $targetUser['name'] . '的主页',
            'pageJs' => 'home.js',
            'headerBackUrl' => '/',
        ], $commonViewData));
    }
}
