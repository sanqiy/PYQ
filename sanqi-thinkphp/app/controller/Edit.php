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
use app\model\ArticleAttachment;
use app\model\Poll as PollModel;
use app\service\ContentFeatureService;

/**
 * 发布/编辑控制器
 */
class Edit extends Base
{
    public function index($cid = null)
    {
        // 要求登录
        $loginCheck = $this->requireLogin();
        if ($loginCheck !== true) {
            return $loginCheck;
        }

        // 获取站点配置
        $siteConfig = $this->getSiteConfig();
        $user = $this->getUser();

        // 检查发布权限
        if (!in_array((string)($user['essqx'] ?? '0'), ['1', '2'], true)) {
            return response('您没有发布权限', 403);
        }

        // 编辑模式
        $article = null;
        if ($cid) {
            $article = Essay::where('cid', $cid)->find();
            if (!$article) {
                return response('文章不存在', 404);
            }
            // 转换为数组，确保模板中 {php} 块可以正常访问
            $article = $article->toArray();
            // 检查权限
            if ($article['ptpuser'] !== $user['username'] && !$this->isAdmin()) {
                return response('无权编辑', 403);
            }
        }

        // 生成CSRF Token
        if (!session('allkey')) {
            session('allkey', bin2hex(random_bytes(16)));
        }
        $allkey = session('allkey');

        // 预处理编辑数据
        $editData = self::prepareEditData($article, $siteConfig);
        $commonViewData = $this->getCommonViewData();

        // 渲染视图
        return view('edit/index', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $user,
            'article' => $article,
            'contentFeatures' => array_merge(ContentFeatureService::getConfig(), [
                'articleTitle' => true,
                'articleCover' => true,
                'markdown' => true,
            ]),
            'allkey' => $allkey,
            'pageTitle' => $article ? '编辑文章' : '发布文章',
            'pageJs' => 'edit.js',
            'mainClass' => 'setup-main edit-main',
            'compactHeader' => true,
            'headerBackUrl' => !empty($article['cid']) ? '/view/' . $article['cid'] : '/',
            'headerTitle' => !empty($article) ? '&#32534;&#36753;' : '&#21457;&#24067;',
            'headerRightHtml' => '<div class="sh-main-head-top-right-s-fas"><button type="submit" form="edit-form" id="submit" onclick="setSaveStatus(\'publish\')">' . (!empty($article) ? '&#20445;&#23384;' : '&#21457;&#24067;') . '</button></div>',
            'hideFooter' => true,
        ], $editData, $commonViewData));
    }

    /**
     * 预处理编辑数据，避免模板中使用 {php} 块
     */
    private static function prepareEditData(?array $article, array $siteConfig): array
    {
        $isEdit = !empty($article);
        $editCid = $article['cid'] ?? '';
        $editText = $article['ptptext'] ?? '';
        $editType = $article['ptplx'] ?? 'only';
        if ($editType !== 'article') {
            $editText = str_replace('<br>', "\n", $editText);
        }

        $editArticleTitle = $article['article_title'] ?? '';
        $editArticleCover = $article['article_cover'] ?? '';
        $editCoverColor = $article['cover_color'] ?? '';
        $editArticleTemplate = $article['article_template'] ?? '';

        // 扫描可用的文章模板
        $articleTemplates = [];
        $tplDir = app()->getRootPath() . 'app' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'view';
        if (is_dir($tplDir)) {
            $files = glob($tplDir . DIRECTORY_SEPARATOR . 'article_*.html');
            foreach ($files as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                if ($name === 'article_content') {
                    continue; // 跳过默认模板
                }
                $articleTemplates[] = $name;
            }
            sort($articleTemplates);
        }
        $editImages = [];
        if (!empty($article['ptpimag'])) {
            $editImages = array_values(array_filter(array_map('trim', explode('(+@+)', (string)$article['ptpimag']))));
        }

        $editVideo = ['url' => '', 'cover' => ''];
        if (!empty($article['ptpvideo'])) {
            $videoParts = explode('|', (string)$article['ptpvideo']);
            $editVideo = [
                'url' => $videoParts[0] ?? '',
                'cover' => $videoParts[1] ?? '',
            ];
        }

        $editMusic = ['url' => '', 'name' => '', 'artist' => '', 'cover' => ''];
        if (!empty($article['ptpmusic'])) {
            $musicParts = explode('|', (string)$article['ptpmusic']);
            $editMusic = [
                'url' => $musicParts[0] ?? '',
                'name' => $musicParts[1] ?? '',
                'artist' => $musicParts[2] ?? '',
                'cover' => $musicParts[3] ?? '',
            ];
        }

        $editRadio = $editType === 'video' ? '2' : ($editType === 'music' ? '3' : ($editType === 'article' ? '4' : '1'));
        $editLocation = $article['ptpdw'] ?? '';
        $editIsAd = isFlag($article['ptpgg'] ?? 0) === 1;
        $editAdUrl = $article['ptpggurl'] ?? '';
        $allowComment = isFlag($article['commauth'] ?? 1, 1) !== 0;
        $editTags = $article['tags'] ?? '';
        $editIsAnonymous = isFlag($article['is_anonymous'] ?? 0) === 1;

        $editTime = '';
        if (!empty($article['ptptime'])) {
            $editTime = date('Y-m-d H:i:s', strtotime($article['ptptime']));
        }
        if ($editTime === '') {
            $editTime = date('Y-m-d H:i:s');
        }

        $notname = isFlag($siteConfig['notname'] ?? 0) === 1;

        $draftsEnabled = ContentFeatureService::draftsEnabled();
        $tagsEnabled = ContentFeatureService::tagsEnabled();
        $editDraftKey = $isEdit && $editCid !== '' ? 'article_' . $editCid : (string)session('article_new_draft_key');
        if ($editDraftKey === '') {
            $editDraftKey = 'new_' . bin2hex(random_bytes(12));
            session('article_new_draft_key', $editDraftKey);
        }

        // 加载已有附件
        $editAttachments = [];
        if ($isEdit && !empty($editCid)) {
            $atts = \think\facade\Db::table('article_attachments')->where('article_cid', $editCid)->order('sort_order', 'asc')->select()->toArray();
            foreach ($atts as $att) {
                $editAttachments[] = [
                    'type' => $att['type'],
                    'url'  => $att['file_url'],
                    'name' => $att['file_name'],
                    'desc' => $att['file_desc'] ?? '',
                    'size' => (int) $att['file_size'],
                    'code' => $att['extract_code'],
                ];
            }
        }

        // 加载已有投票
        $editPoll = null;
        if ($isEdit && !empty($editCid)) {
            $pollRow = PollModel::where('article_cid', $editCid)->find();
            if ($pollRow) {
                $editPoll = [
                    'question' => $pollRow->question,
                    'options'  => $pollRow->options,
                    'type'     => $pollRow->type,
                    'expire_at' => $pollRow->expire_at ?? '',
                ];
            }
        }

        return [
            'isEdit' => $isEdit,
            'editCid' => $editCid,
            'editText' => $editText,
            'editType' => $editType,
            'editArticleTitle' => $editArticleTitle,
            'editArticleCover' => $editArticleCover,
            'editCoverColor' => $editCoverColor,
            'editImages' => $editImages,
            'editVideo' => $editVideo,
            'editMusic' => $editMusic,
            'editRadio' => $editRadio,
            'editLocation' => $editLocation,
            'editIsAd' => $editIsAd,
            'editAdUrl' => $editAdUrl,
            'allowComment' => $allowComment,
            'editIsAnonymous' => $editIsAnonymous,
            'editTags' => $editTags,
            'editTime' => $editTime,
            'notname' => $notname,
            'draftsEnabled' => $draftsEnabled,
            'editDraftKey' => $editDraftKey,
            'tagsEnabled' => $tagsEnabled,
            'editAttachments' => $editAttachments,
            'editPoll' => $editPoll,
            'editArticleTemplate' => $editArticleTemplate,
            'articleTemplates' => $articleTemplates,
        ];
    }
}
