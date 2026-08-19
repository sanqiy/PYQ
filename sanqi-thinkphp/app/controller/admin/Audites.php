<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\model\Essay;
use app\model\Comm;
use app\model\Lcke;
use app\model\ArticleAttachment;
use app\service\AdminLogService;
use app\service\AdminListService;
use app\service\FileUploadService;
use think\facade\Cache;
use think\facade\Db;
use app\service\ContentFeatureService;
use app\service\NotificationService;

class Audites extends \app\controller\Base
{
    public function index()
    {
        $page = AdminListService::page($this->request->get('page', 1));
        $pageSize = AdminListService::pageSize();
        $offset = AdminListService::offset($page);
        $contentFeatures = ContentFeatureService::getConfig();

        $status = $this->request->get('status', 'pending');

        $query = Essay::order('id', 'desc');
        if ($status === 'pending') {
            $query->where('ptpaud', 0);
        } elseif ($status === 'approved') {
            $query->where('ptpaud', 1);
        } elseif (!empty($contentFeatures['drafts_enabled']) && $status === 'draft') {
            $query->where('ptpaud', -2);
        } elseif ($status === 'rejected') {
            $query->where('ptpaud', -1);
        }

        $total = $query->count();
        $articles = $query->limit($offset, $pageSize)->select()->toArray();

        return view('admin/audites', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'articles' => $articles,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'status' => $status,
            'contentFeatures' => $contentFeatures,
            'pageTitle' => '文章审核'
        ], $this->getAdminViewData()));
    }

    public function audit()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $cid = strip_tags(trim((string)$this->request->post('cid', '')));
        $action = $this->request->post('action', '');
        if ($cid === '' || $action === '') {
            return $this->error('参数错误');
        }

        switch ($action) {
            case 'edit':
                return $this->saveArticle($cid);

            case 'approve':
                $article = Essay::where('cid', $cid)->field('ptpuser,cid')->find();
                Essay::where('cid', $cid)->update(['ptpaud' => 1]);
                Cache::tag('article')->clear();
                AdminLogService::operation('article.approve', 'article:' . $cid);
                if ($article) {
                    NotificationService::sendAuditNotify($article['ptpuser'], 'article', $cid, true);
                }
                return $this->success('审核通过');

            case 'reject':
                $article = Essay::where('cid', $cid)->field('ptpuser,cid')->find();
                Essay::where('cid', $cid)->update(['ptpaud' => -1]);
                Cache::tag('article')->clear();
                AdminLogService::operation('article.reject', 'article:' . $cid);
                if ($article) {
                    NotificationService::sendAuditNotify($article['ptpuser'], 'article', $cid, false);
                }
                return $this->success('已拒绝');

            case 'delete':
                $article = Essay::where('cid', $cid)->find();
                if (!$article) {
                    return $this->error('文章不存在');
                }
                $fileUrls = FileUploadService::extractUrlsFromArticle($article->toArray());
                try {
                    Db::startTrans();
                    Db::table('essay')->where('cid', $cid)->delete();
                    Db::table('comm')->where('wzcid', $cid)->delete();
                    Db::table('lcke')->where('lwz', $cid)->delete();
                    Db::table('article_attachments')->where('article_cid', $cid)->delete();
                    $poll = Db::table('polls')->where('article_cid', $cid)->find();
                    if ($poll) {
                        Db::table('poll_votes')->where('poll_id', $poll['id'])->delete();
                        Db::table('polls')->where('id', $poll['id'])->delete();
                    }
                    Db::commit();
                } catch (\Throwable $e) {
                    Db::rollback();
                    return $this->error('删除失败: ' . $e->getMessage());
                }
                FileUploadService::cleanupArticleFiles($fileUrls);
                Cache::tag('article')->clear();
                AdminLogService::operation('article.delete', 'article:' . $cid);
                return $this->success('删除成功');

            default:
                return $this->error('未知操作');
        }
    }

    private function saveArticle(string $cid)
    {
        $article = Essay::where('cid', $cid)->find();
        if (!$article) {
            return $this->error('文章不存在');
        }

        $articleData = $article->toArray();

        $type = $this->request->post('ptplx', $articleData['ptplx'] ?? 'only');
        if (!in_array($type, ['only', 'img', 'video', 'music', 'article'], true)) {
            $type = 'only';
        }

        $status = intval($this->request->post('ptpaud', $articleData['ptpaud'] ?? 1));
        $allowedStatuses = !empty(ContentFeatureService::getConfig()['drafts_enabled']) ? [-2, -1, 0, 1] : [-1, 0, 1];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 1;
        }

        $privacy = intval($this->request->post('ptpys', $articleData['ptpys'] ?? 1));
        if (!in_array($privacy, [0, 1], true)) {
            $privacy = 1;
        }

        // 长文章保留原始 HTML/Markdown，其他类型走 cleanArticleHtml
        $ptptext = $this->request->post('ptptext', '');
        if ($type !== 'article') {
            $ptptext = cleanArticleHtml($ptptext);
        }

        $updateData = [
            'ptptext' => $ptptext,
            'ptplx' => $type,
            'ptpimag' => $this->request->post('ptpimag', ''),
            'ptpvideo' => $this->request->post('ptpvideo', ''),
            'ptpmusic' => $this->request->post('ptpmusic', ''),
            'ptpdw' => $this->request->post('ptpdw', ''),
            'tags' => !empty(ContentFeatureService::getConfig()['tags_enabled']) ? $this->request->post('tags', '') : '',
            'ptptime' => $this->request->post('ptptime', $articleData['ptptime'] ?? date('Y-m-d H:i:s')),
            'ptpgg' => intval($this->request->post('ptpgg', 0)) === 1 ? 1 : 0,
            'ptpggurl' => $this->request->post('ptpggurl', ''),
            'ptpys' => $privacy,
            'commauth' => intval($this->request->post('commauth', 1)) === 1 ? 1 : 0,
            'ptpaud' => $status
        ];

        // 长文章专属字段
        if ($type === 'article') {
            $updateData['article_title'] = $this->request->post('article_title', '');
            $updateData['article_cover'] = $this->request->post('article_cover', '');
            $updateData['cover_color'] = $this->request->post('cover_color', '');
        }

        // 同步文件引用
        $oldUrls = FileUploadService::extractUrlsFromArticle($articleData);
        $newUrls = FileUploadService::extractUrlsFromArticle($updateData);
        FileUploadService::syncReferences($oldUrls, $newUrls);

        Essay::where('cid', $cid)->update($updateData);

        Cache::tag('article')->clear();
        AdminLogService::operation('article.edit', 'article:' . $cid, [
            'type' => $type,
            'status' => $status,
            'privacy' => $privacy,
        ]);
        return $this->success('保存成功');
    }
}
