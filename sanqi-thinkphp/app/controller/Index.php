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
use app\service\ContentFeatureService;
use think\facade\Cache;

/**
 * 首页控制器
 */
class Index extends Base
{
    public function index()
    {
        // 获取站点配置
        $siteConfig = $this->getSiteConfig();

        // 检查站点状态
        if (isFlag($siteConfig['zt'] ?? 1, 1) !== 1) {
            return response('站点维护中...', 503);
        }

        // 检查全站密码
        if (isSitePasswordEnabled($siteConfig['pagepass'] ?? '')) {
            if (!hasValidSitePasswordCookie($siteConfig['pagepass'])) {
                return view('component/pagepass', ['siteConfig' => $siteConfig]);
            }
        }

        // 获取当前用户
        $user = $this->getUser();
        if (!$user) {
            visitorIdentity();
        }

        // 获取搜索关键词
        $so = strip_tags(trim((string)$this->request->get('so', '')));
        $tagsEnabled = ContentFeatureService::tagsEnabled();
        $tag = $tagsEnabled ? trim((string)$this->request->get('tag', '')) : '';

        // 获取文章列表
        $essgs = max(1, min(50, (int)($siteConfig['essgs'] ?? 10)));
        $now = date('Y-m-d H:i:s');

        // 获取置顶文章（先获取，用于从普通列表中排除）
        $topArticles = [];
        $topIds = [];
        if (!empty($siteConfig['topes'])) {
            $topIds = array_values(array_filter(explode("\n", $siteConfig['topes'])));
            if (!empty($topIds)) {
                $topArticles = Cache::tag('article')->remember('top_articles', function () use ($topIds, $now) {
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
        }

        if (!empty($so) || $tag !== '') {
            // 搜索或按标签筛选时不使用缓存
            $query = Essay::where('ptpaud', '1')
                ->where('ptpys', '<>', '0')
                ->where('ptptime', '<=', $now);

            if (!empty($topIds)) {
                $query->whereNotIn('cid', $topIds);
            }
            if (!empty($so)) {
                // 同时搜索文章内容和标题
                $query->where(function ($q) use ($so) {
                    $q->where('ptptext', 'like', '%' . $so . '%')
                        ->whereOr('article_title', 'like', '%' . $so . '%');
                });
            }
            if ($tagsEnabled && $tag !== '') {
                $query->whereRaw('FIND_IN_SET(?, tags)', [$tag]);
            }

            $articles = $query->field('id,cid,ptpuser,is_anonymous,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,tags,article_title,article_cover,cover_color,user_top')
                ->order('id', 'desc')
                ->limit($essgs)
                ->select()
                ->toArray();
        } else {
            // 非搜索时使用缓存
            $cacheKey = "articles_latest_{$essgs}";
            $articles = Cache::tag('article')->remember($cacheKey, function () use ($now, $essgs, $topIds) {
                $query = Essay::where('ptpaud', '1')
                    ->where('ptpys', '<>', '0')
                    ->where('ptptime', '<=', $now);
                if (!empty($topIds)) {
                    $query->whereNotIn('cid', $topIds);
                }
                return $query->field('id,cid,ptpuser,is_anonymous,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,tags,article_title,article_cover,cover_color,user_top')
                    ->order('id', 'desc')
                    ->limit($essgs)
                    ->select()
                    ->toArray();
            }, 120);
        }

        // 生成CSRF Token
        if (!session('allkey')) {
            session('allkey', bin2hex(random_bytes(16)));
        }
        $allkey = session('allkey');

        $allArticles = array_merge($topArticles, $articles);
        $postMeta = $this->prefetchPostMeta($allArticles, $user);

        // 批量预取文章作者用户数据，避免逐个查询
        $authorNames = array_column($allArticles, 'ptpuser');
        $this->prefetchUsers($authorNames);

        $commonViewData = $this->getCommonViewData();

        // 渲染视图
        return view('index/index', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $user,
            'articles' => $articles,
            'topArticles' => $topArticles,
            'postLikes' => $postMeta['likesByCid'],
            'postComments' => $postMeta['commentsByCid'],
            'postLikedMap' => $postMeta['likedCids'],
            'allkey' => $allkey,
            'so' => $so,
            'tag' => $tag,
            'pageTitle' => $siteConfig['name'] ?? '朋友圈',
            'pageJs' => 'index.js'
        ], $commonViewData));
    }

    public function rss()
    {
        $siteConfig = $this->getSiteConfig();
        $siteName = $siteConfig['name'] ?? '朋友圈';
        $siteSubtitle = $siteConfig['subtitle'] ?? '';
        $baseUrl = $this->request->domain();
        $now = date('Y-m-d H:i:s');

        $articles = Essay::where('ptpaud', '1')
            ->where('ptpys', '<>', '0')
            ->where('ptptime', '<=', $now)
            ->field('id,cid,ptpuser,ptptext,ptptime,ptplx,article_title,is_anonymous')
            ->order('id', 'desc')
            ->limit(20)
            ->select()
            ->toArray();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '<title>' . htmlspecialchars($siteName) . '</title>' . "\n";
        $xml .= '<link>' . $baseUrl . '</link>' . "\n";
        $xml .= '<description>' . htmlspecialchars($siteSubtitle ?: $siteName) . '</description>' . "\n";
        $xml .= '<language>zh-cn</language>' . "\n";
        $xml .= '<atom:link href="' . $baseUrl . '/rss" rel="self" type="application/rss+xml"/>' . "\n";

        foreach ($articles as $article) {
            $cid = $article['cid'];
            $link = $baseUrl . '/view/' . $cid;
            $text = strip_tags((string)($article['ptptext'] ?? ''));
            $isArticle = ($article['ptplx'] ?? '') === 'article';
            $title = ($isArticle && !empty($article['article_title']))
                ? $article['article_title']
                : mb_substr($text, 0, 50, 'UTF-8');
            $description = htmlspecialchars(mb_substr($text, 0, 200, 'UTF-8'));
            $pubDate = date(DATE_RSS, strtotime($article['ptptime']));
            $author = (string)($article['ptpuser'] ?? '');

            $xml .= '<item>' . "\n";
            $xml .= '<title><![CDATA[' . $title . ']]></title>' . "\n";
            $xml .= '<link>' . $link . '</link>' . "\n";
            $xml .= '<guid>' . $link . '</guid>' . "\n";
            $xml .= '<description><![CDATA[' . $description . ']]></description>' . "\n";
            $xml .= '<pubDate>' . $pubDate . '</pubDate>' . "\n";
            $xml .= '<author>' . htmlspecialchars($author) . '</author>' . "\n";
            $xml .= '</item>' . "\n";
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return \think\Response::create($xml, 'html', 200)
            ->header(['Content-Type' => 'application/rss+xml; charset=utf-8']);
    }
}
