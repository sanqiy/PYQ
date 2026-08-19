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
use app\model\User;
use think\facade\Cache;

/**
 * 个人中心控制器
 */
class Home extends Base
{
    public function index()
    {
        // 要求登录
        $loginCheck = $this->requireLogin();
        if ($loginCheck !== true) {
            return $loginCheck;
        }

        // 获取站点配置
        $siteConfig = $this->getSiteConfig();
        $sessionUser = $this->getUser();
        $user = User::where('id', $sessionUser['id'])->find() ?: $sessionUser;

        // 获取用户置顶文章
        $stickyArticles = [];
        $now = date('Y-m-d H:i:s');
        $stickyArticles = Cache::tag('article')->remember(
            'home_user_sticky_' . $user['username'],
            function () use ($user, $now) {
                return Essay::where('user_top', 1)
                    ->where('ptpuser', $user['username'])
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

        // 获取用户文章（包括未审核的）
        $essgs = max(1, min(50, (int)($siteConfig['essgs'] ?? 10)));
        $query = Essay::where('ptpuser', $user['username']);
        // 默认排除草稿
        $query->where('ptpaud', '<>', '-2');

        $articles = $query->order('id', 'desc')
            ->limit($essgs)
            ->select()
            ->toArray();

        // 生成CSRF Token
        if (!session('allkey')) {
            session('allkey', bin2hex(random_bytes(16)));
        }
        $allkey = session('allkey');

        // 渲染视图
        $commonViewData = $this->getCommonViewData();

        return view('home/index', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $user,
            'articles' => $articles,
            'stickyArticles' => $stickyArticles,
            'stickyLink' => '/sticky/home',
            'allkey' => $allkey,
            'pageTitle' => '个人中心',
            'pageJs' => 'home.js',
            'headerBackUrl' => '/',
            'headerRightHtml' => '<div class="sh-main-head-top-right-s"><a href="/setup"><i class="iconfont icon-a31shezhi al-sxb" id="top-right-2"></i></a></div>',
            'headerUser' => $user,
            'headerCoverUpload' => true,
            'initialOffset' => count($articles ?? [])
        ], $commonViewData));
    }
}
