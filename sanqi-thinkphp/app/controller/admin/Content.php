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
use app\service\AdminLogService;
use app\service\CommentSecurityService;
use app\service\ContentFeatureService;
use app\service\SiteConfigService;
use think\facade\Cache;

class Content extends \app\controller\Base
{
    public function index()
    {
        $siteConfig = $this->getSiteConfig();
        $topIds = $this->parseTopIds($siteConfig['topes'] ?? '');
        $topArticles = [];

        if (!empty($topIds)) {
            $rows = Essay::whereIn('cid', $topIds)
                ->field('cid,ptptext,ptptime,ptpaud,ptpys')
                ->select()
                ->toArray();
            $byCid = [];
            foreach ($rows as $row) {
                $byCid[$row['cid']] = $row;
            }
            foreach ($topIds as $cid) {
                $topArticles[] = $byCid[$cid] ?? [
                    'cid' => $cid,
                    'ptptext' => '文章不存在或已删除',
                    'ptptime' => '',
                    'ptpaud' => '',
                    'ptpys' => '',
                ];
            }
        }

        return view('admin/content', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $this->getUser(),
            'contentFeatures' => ContentFeatureService::getConfig(),
            'commentSecurity' => CommentSecurityService::getConfig(),
            'topes' => $siteConfig['topes'] ?? '',
            'topArticles' => $topArticles,
            'pageTitle' => '内容管理',
        ], $this->getAdminViewData()));
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $topes = implode("\n", $this->parseTopIds($this->request->post('topes', '')));
        SiteConfigService::set('topes', $topes);

        ContentFeatureService::saveConfig([
            'drafts_enabled' => intval($this->request->post('drafts_enabled', 0)),
            'tags_enabled' => intval($this->request->post('tags_enabled', 0)),
        ]);

        CommentSecurityService::saveConfig([
            'audit_enabled' => intval($this->request->post('audit_enabled', 0)),
            'keywords' => $this->request->post('keywords', ''),
            'blacklist' => $this->request->post('blacklist', ''),
        ]);

        Cache::tag('article')->clear();
        AdminLogService::operation('content.save', 'admin:1', [
            'top_count' => count($this->parseTopIds($topes)),
            'drafts_enabled' => intval($this->request->post('drafts_enabled', 0)),
            'tags_enabled' => intval($this->request->post('tags_enabled', 0)),
            'comment_audit' => intval($this->request->post('audit_enabled', 0)),
        ]);

        return $this->success('保存成功');
    }

    protected function parseTopIds($value): array
    {
        $parts = preg_split('/\r\n|\r|\n|,|，|\s+/', (string)$value);
        $ids = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '' && preg_match('/^[A-Za-z0-9_-]{6,64}$/', $part) && !in_array($part, $ids, true)) {
                $ids[] = $part;
            }
        }
        return $ids;
    }
}
