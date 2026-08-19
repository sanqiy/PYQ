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
use app\model\Comm;
use app\model\Lcke;
use app\model\ArticleAttachment;
use app\model\Poll as PollModel;
use app\model\PollVote;
use app\model\User as UserModel;

/**
 * 文章详情控制器
 */
class View extends Base
{
    public function index($cid)
    {
        // 获取站点配置
        $siteConfig = $this->getSiteConfig();
        $user = $this->getUser();

        // 获取文章
        $article = Essay::where('cid', $cid)->find();
        if (!$article) {
            return response('文章不存在', 404);
        }
        // 转换为数组，确保模板中 {php} 块可以正常访问
        $article = $article->toArray();

        // 检查访问权限
        $futurePost = !empty($article['ptptime']) && strtotime($article['ptptime']) > time();
        if (isFlag($article['ptpys'] ?? 1, 1) === 0 || isFlag($article['ptpaud'] ?? 1, 1) !== 1 || $futurePost) {
            if (!$user || ($article['ptpuser'] !== $user['username'] && !$this->isAdmin())) {
                return response('文章不存在或无权访问', 403);
            }
        }

        // 获取评论列表
        $commgs = max(1, min(100, isFlag($siteConfig['commgs'] ?? 10, 10)));
        $comments = Comm::where('wzcid', $cid)
            ->where('comaud', '<>', '0')
            ->where('comaud', '<>', '-1')
            ->order('id', 'asc')
            ->select()
            ->toArray();

        // 获取点赞列表
        $likes = Lcke::where('lwz', $cid)
            ->select()
            ->toArray();

        // 预取点赞用户信息（用于显示头像）
        $likeUsers = [];
        $likeUsernames = [];
        foreach ($likes as $like) {
            $luser = (string)($like['luser'] ?? '');
            if ($luser !== '' && !isVisitorUser($luser)) {
                $likeUsernames[$luser] = true;
            }
        }
        if (!empty($likeUsernames)) {
            $users = \app\model\User::whereIn('username', array_keys($likeUsernames))
                ->field('username,img')
                ->select()
                ->toArray();
            foreach ($users as $u) {
                $likeUsers[(string)$u['username']] = $u;
            }
        }

        // 预取当前用户点赞状态
        $liked = false;
        if (!empty($user['username'])) {
            $liked = Lcke::where('lwz', $cid)->where('luser', $user['username'])->find() ? true : false;
        } else {
            $visitor = visitorIdentity();
            if (!empty($visitor['id'])) {
                $liked = Lcke::where('lwz', $cid)->where('luser', $visitor['id'])->find() ? true : false;
            }
        }

        $canManage = $user && ($article['ptpuser'] === $user['username'] || $this->isAdmin());
        $isAdminUser = $user && $this->isAdmin();

        // 批量预取文章作者和评论者用户数据
        $prefetchNames = [$article['ptpuser']];
        foreach ($comments as $c) {
            $prefetchNames[] = $c['couser'] ?? '';
        }
        $this->prefetchUsers($prefetchNames);

        // 生成CSRF Token
        if (!session('allkey')) {
            session('allkey', bin2hex(random_bytes(16)));
        }
        $allkey = session('allkey');

        // 渲染视图
        $isArticleDetail = (($article['ptplx'] ?? '') === 'article');
        $articleTemplate = $isArticleDetail ? trim((string)($article['article_template'] ?? '')) : '';
        $commonViewData = $this->getCommonViewData();

        // 获取附件
        $attachments = \think\facade\Db::table('article_attachments')->where('article_cid', $cid)
            ->order('sort_order', 'asc')
            ->select()
            ->toArray();

        // 获取投票数据
        $poll = null;
        $pollData = PollModel::where('article_cid', $cid)->find();
        if ($pollData) {
            $pollUserId = '';
            if (!empty($user['username'])) {
                $pollUserId = $user['username'];
            } else {
                $pollUserId = 'vis#-[' . ($this->request->ip() ?? '127.0.0.1') . ']-#vis';
            }
            $options = $pollData->options;
            $counts = $pollData->getVoteCounts();
            $totalVotes = array_sum($counts);
            $hasVoted = $pollData->hasVoted($pollUserId);
            $myVotes = [];
            if ($hasVoted) {
                $myVotes = PollVote::where('poll_id', $pollData->id)
                    ->where('user_id', $pollUserId)
                    ->column('option_index');
            }
            $pollItems = [];
            foreach ($options as $i => $text) {
                $count = $counts[$i] ?? 0;
                $percent = $totalVotes > 0 ? round($count / $totalVotes * 100, 1) : 0;
                $pollItems[] = [
                    'index' => $i,
                    'text' => $text,
                    'count' => $count,
                    'percent' => $percent,
                    'selected' => in_array($i, $myVotes),
                ];
            }
            $poll = [
                'id' => $pollData->id,
                'question' => $pollData->question,
                'type' => $pollData->type,
                'expired' => $pollData->isExpired(),
                'total_votes' => $totalVotes,
                'has_voted' => $hasVoted,
                'options' => $pollItems,
            ];
        }

        return view('view/index', array_merge([
            'siteConfig' => $siteConfig,
            'posterRandomApi' => $siteConfig['poster_random_api'] ?? '',
            'user' => $user,
            'article' => $article,
            'articleTemplate' => $articleTemplate,
            'comments' => $comments,
            'likes' => $likes,
            'liked' => $liked,
            'likeUsers' => $likeUsers,
            'allkey' => $allkey,
            'commgs' => $commgs,
            'pageTitle' => ($isArticleDetail && !empty($article['article_title'])) ? $article['article_title'] : '详情',
            'pageJs' => 'view.js',
            'mainClass' => $isArticleDetail ? 'sh-article-view' : '',
            'hideProfileHeader' => $isArticleDetail,
            'hideCoverHeader' => $isArticleDetail,
            'hideFooter' => true,
            'headerBackUrl' => '/',
            'headerTitle' => '&#35814;&#24773;',
            'headerRightHtml' => '<div class="sh-main-head-top-right-s" onclick="viewsetk()"><i class="iconfont icon-gengduo al-sxb" id="top-right-1"></i></div>',
            'canManage' => $canManage,
            'isAdminUser' => $isAdminUser,
            'attachments' => $attachments,
            'poll' => $poll,
            'authorQr' => $this->getAuthorQr($article['ptpuser']),
            'extraCss' => ($isArticleDetail ? '<link rel="stylesheet" href="' . staticUrl('/assets/css/prism_mac.css') . '">' : '') . '<style>.sh-zanp{width:auto;margin-left:-55px;}.sh-attach-view{margin-top:12px;padding:10px 15px;background:var(--bg-secondary,#f5f5f5);border-radius:8px}.sh-attach-view-title{font-size:13px;font-weight:600;margin-bottom:8px;color:var(--text-primary,#333)}.sh-attach-view-item{display:flex;align-items:center;gap:8px;padding:6px 0;font-size:13px;border-bottom:1px solid var(--border-color,#eee)}.sh-attach-view-item:last-child{border-bottom:none}.sh-attach-view-item .sh-attach-view-info{flex:1;min-width:0;display:flex;flex-direction:column;gap:2px}.sh-attach-view-item .sh-attach-view-desc{font-size:12px;color:var(--text-secondary,#999);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sh-attach-view-item a{color:var(--theme,#4a90d9);text-decoration:none}.sh-attach-view-item a:hover{text-decoration:underline}.sh-attach-view-item .att-code-tag{font-size:11px;background:var(--theme,#4a90d9);color:#fff;padding:1px 6px;border-radius:3px;margin-left:4px}.sh-attach-view-item .att-size{color:var(--text-secondary,#999);font-size:12px}.sh-attach-view-item .att-login{font-size:11px;color:#e67e22;margin-left:4px}</style>',
            'extraJs' => $isArticleDetail
                ? '<script src="' . staticUrl('/assets/js/prism_dai.js') . '"></script><script src="' . staticUrl('/assets/js/article-view.js') . '"></script>'
                : '',
        ], $commonViewData));
    }

    /**
     * 获取文章作者的收款码
     */
    private function getAuthorQr(string $username): array
    {
        if (empty($username)) {
            return ['alipay_qr' => '', 'wechat_qr' => ''];
        }
        $author = UserModel::where('username', $username)
            ->field('alipay_qr,wechat_qr')
            ->find();
        if (!$author) {
            return ['alipay_qr' => '', 'wechat_qr' => ''];
        }
        return [
            'alipay_qr' => $author['alipay_qr'] ?? '',
            'wechat_qr' => $author['wechat_qr'] ?? '',
        ];
    }
}
