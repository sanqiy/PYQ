<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\api;

use app\controller\Base;
use app\model\Essay;
use app\model\User as UserModel;
use app\service\ContentFeatureService;
use think\facade\View;

/**
 * 个人中心API控制器
 */
class HomeApi extends Base
{
    /**
     * 加载更多文章（个人中心）
     */
    public function loadMore()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        // 要求登录
        $user = $this->getUser();
        $getUser = strip_tags(trim((string)$this->request->post('getuser', '')));
        $siteConfig = $this->getSiteConfig();
        $essgs = max(1, min(50, (int)($siteConfig['essgs'] ?? 10)));
        // page 仅作为旧客户端 fallback；新流程优先使用 last_id cursor。
        $offset = max(0, intval($this->request->post('page', 0)));
        $lastId = max(0, intval($this->request->post('last_id', 0)));

        // 确定查询用户
        if (!empty($getUser)) {
            // 公开主页
            $targetUser = UserModel::where('username', $getUser)->find();
            if (!$targetUser) {
                return $this->error('用户不存在');
            }
            $query = Essay::where('ptpuser', $getUser)
                ->where('ptpys', '1')
                ->where('ptpaud', '1')
                ->where('ptptime', '<=', date('Y-m-d H:i:s'));
        } else {
            // 个人中心
            if (!$this->requireLogin()) return;
            $user = $this->getUser();
            $query = Essay::where('ptpuser', $user['username']);
            if (!ContentFeatureService::draftsEnabled()) {
                $query->where('ptpaud', '<>', '-2');
            }
        }

        // 获取文章列表
        $fields = 'id,cid,ptpuser,is_anonymous,ptptext,ptpimag,ptpvideo,ptpmusic,ptplx,ptpdw,ptptime,ptpgg,ptpggurl,ptpys,commauth,ptpaud,tags,article_title,article_cover,cover_color,user_top';
        if ($lastId > 0) {
            $articles = (clone $query)->where('id', '<', $lastId)
                ->field($fields)
                ->order('id', 'desc')
                ->limit($essgs + 1)
                ->select();
        } else {
            $articles = (clone $query)->field($fields)
                ->order('id', 'desc')
                ->limit($offset, $essgs + 1)
                ->select();
        }

        $articlesArr = $articles->toArray();
        $hasMore = count($articlesArr) > $essgs;
        if ($hasMore) {
            $articlesArr = array_slice($articlesArr, 0, $essgs);
        }

        // 批量预取作者用户数据
        $authorNames = [];
        foreach ($articlesArr as $a) {
            $authorNames[] = $a['ptpuser'] ?? '';
        }
        $this->prefetchUsers($authorNames);

        // 渲染文章HTML
        $html = '';
        foreach ($articlesArr as $article) {
            $html .= View::fetch('/component/timeline_item', [
                'post' => $article,
                'siteConfig' => $siteConfig,
                'showAuditLock' => empty($getUser),
                'appendFms' => false,
            ]);
        }

        // 统一 JSON 格式返回
        $loaded = count($articlesArr);
        $lastArticle = $loaded > 0 ? $articlesArr[$loaded - 1] : null;
        $nextCursor = $lastArticle ? (int)$lastArticle['id'] : $lastId;
        return $this->success('加载成功', [
            'html' => $html,
            'total' => null,
            'loaded' => $loaded,
            'offset' => $offset + $loaded,
            'next_cursor' => $nextCursor,
            'hasMore' => $hasMore,
        ]);
    }
}
