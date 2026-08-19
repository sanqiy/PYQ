<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\controller\Base;
use app\model\Comm;
use app\service\AdminListService;
use app\service\AdminLogService;
use app\service\CommentSecurityService;
use app\service\NotificationService;
use think\facade\Db;

class Auditco extends Base
{
    public function index()
    {
        $page = AdminListService::page($this->request->get('page', 1));
        $pageSize = AdminListService::pageSize();
        $offset = AdminListService::offset($page);
        $status = (string)$this->request->get('status', 'pending');
        $keyword = AdminListService::keyword($this->request->get('keyword', ''));
        $hasEmailColumn = $this->hasColumn('comm', 'coemail');
        $hasHistoryTable = $this->hasTable('comment_edit_history');

        $query = Comm::order('id', 'desc');
        if ($status === 'pending') {
            $query->where('comaud', 0);
        } elseif ($status === 'approved') {
            $query->where('comaud', 1);
        } elseif ($status === 'rejected') {
            $query->where('comaud', -1);
        }

        if ($keyword !== '') {
            $like = '%' . strtr($keyword, ['\\' => '\\\\', '%' => '\\%', '_' => '\\_']) . '%';
            $fields = $hasEmailColumn ? 'cotext|coname|coemail|ip|wzcid' : 'cotext|coname|ip|wzcid';
            $query->where(function ($q) use ($like, $fields) {
                $q->where($fields, 'like', $like);
            });
        }

        $total = $query->count();
        $comments = $query->limit($offset, $pageSize)->select()->toArray();
        foreach ($comments as &$comment) {
            $comment['keyword_hits'] = CommentSecurityService::keywordHits((string)($comment['cotext'] ?? ''));
            $comment['edit_history'] = $hasHistoryTable
                ? Db::name('comment_edit_history')
                    ->where('ecid', (string)($comment['ecid'] ?? ''))
                    ->order('id', 'desc')
                    ->limit(5)
                    ->select()
                    ->toArray()
                : [];
            if (!$hasEmailColumn) {
                $comment['coemail'] = '';
            }
        }
        unset($comment);

        return view('admin/auditco', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'comments' => $comments,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'status' => $status,
            'keyword' => $keyword,
            'hasEmailColumn' => $hasEmailColumn,
            'hasHistoryTable' => $hasHistoryTable,
            'pageTitle' => '评论审核',
        ], $this->getAdminViewData()));
    }

    public function audit()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $ecid = strip_tags(trim((string)$this->request->post('ecid', '')));
        $action = (string)$this->request->post('action', '');
        if ($ecid === '' || $action === '') {
            return $this->error('参数错误');
        }

        return $this->handleOne($ecid, $action);
    }

    public function batch()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $action = (string)$this->request->post('action', '');
        $ids = $this->request->post('ids', []);
        if (!is_array($ids)) {
            $ids = explode(',', (string)$ids);
        }
        $ids = array_values(array_unique(array_filter(array_map(function ($id) {
            return strip_tags(trim((string)$id));
        }, $ids))));

        if ($action === '' || empty($ids)) {
            return $this->error('请选择评论和操作');
        }
        if (!in_array($action, ['approve', 'reject', 'delete'], true)) {
            return $this->error('不支持的批量操作');
        }

        $ok = 0;
        foreach ($ids as $ecid) {
            $res = $this->applyAuditAction($ecid, $action, false);
            if ($res) {
                $ok++;
            }
        }

        AdminLogService::operation('comment.batch_' . $action, 'comment', ['count' => $ok]);
        return $this->success('批量操作完成：' . $ok . ' 条');
    }

    public function edit()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $ecid = strip_tags(trim((string)$this->request->post('ecid', '')));
        $text = trim((string)$this->request->post('cotext', ''));
        if ($ecid === '' || $text === '') {
            return $this->error('评论内容不能为空');
        }

        $comment = Comm::where('ecid', $ecid)->find();
        if (!$comment) {
            return $this->error('评论不存在');
        }

        $oldText = (string)$comment['cotext'];
        if ($oldText === $text) {
            return $this->success('内容未变化');
        }

        Comm::where('ecid', $ecid)->update(['cotext' => $text]);
        $admin = $this->getUser();
        if ($this->hasTable('comment_edit_history')) {
            Db::name('comment_edit_history')->insert([
                'ecid' => $ecid,
                'admin_user' => (string)($admin['username'] ?? ''),
                'old_text' => $oldText,
                'new_text' => $text,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        AdminLogService::operation('comment.edit', 'comment:' . $ecid);

        return $this->success('评论已更新');
    }

    public function blacklist()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $ecid = strip_tags(trim((string)$this->request->post('ecid', '')));
        $field = (string)$this->request->post('field', '');
        $comment = Comm::where('ecid', $ecid)->find();
        if (!$comment) {
            return $this->error('评论不存在');
        }

        $map = [
            'ip' => (string)($comment['ip'] ?? ''),
            'email' => $this->hasColumn('comm', 'coemail') ? (string)($comment['coemail'] ?? '') : '',
            'name' => (string)($comment['coname'] ?? ''),
        ];
        if (!isset($map[$field]) || trim($map[$field]) === '') {
            return $this->error('没有可拉黑的值');
        }

        $added = CommentSecurityService::addBlacklistItems([$map[$field]]);
        AdminLogService::operation('comment.blacklist_' . $field, 'comment:' . $ecid, ['added' => $added]);

        return $this->success($added ? '已加入评论黑名单' : '黑名单中已存在');
    }

    private function handleOne(string $ecid, string $action)
    {
        if (!in_array($action, ['approve', 'reject', 'delete'], true)) {
            return $this->error('未知操作');
        }
        if (!$this->applyAuditAction($ecid, $action, true)) {
            return $this->error('评论不存在');
        }

        $messages = [
            'approve' => '审核通过',
            'reject' => '已拒绝',
            'delete' => '删除成功',
        ];
        return $this->success($messages[$action]);
    }

    private function applyAuditAction(string $ecid, string $action, bool $writeLog): bool
    {
        $comment = Comm::where('ecid', $ecid)->field('couser,wzcid,ecid')->find();
        if (!$comment) {
            return false;
        }

        if ($action === 'approve') {
            Comm::where('ecid', $ecid)->update(['comaud' => 1]);
            if ($writeLog) AdminLogService::operation('comment.approve', 'comment:' . $ecid);
            NotificationService::sendAuditNotify($comment['couser'], 'comment', $comment['wzcid'], true);
            return true;
        }

        if ($action === 'reject') {
            Comm::where('ecid', $ecid)->update(['comaud' => -1]);
            if ($writeLog) AdminLogService::operation('comment.reject', 'comment:' . $ecid);
            NotificationService::sendAuditNotify($comment['couser'], 'comment', $comment['wzcid'], false);
            return true;
        }

        Db::table('comm')->where('ecid', $ecid)->delete();
        if ($writeLog) AdminLogService::operation('comment.delete', 'comment:' . $ecid);
        return true;
    }

    private function hasColumn(string $table, string $column): bool
    {
        static $cache = [];
        $key = $table . '.' . $column;
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                return $cache[$key] = false;
            }
            $tableName = $this->physicalTableName($table);
            $rows = Db::query("SHOW COLUMNS FROM `{$tableName}` LIKE '{$column}'");
            return $cache[$key] = !empty($rows);
        } catch (\Throwable $e) {
            return $cache[$key] = false;
        }
    }

    private function hasTable(string $table): bool
    {
        static $cache = [];
        if (array_key_exists($table, $cache)) {
            return $cache[$table];
        }

        try {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
                return $cache[$table] = false;
            }
            $tableName = $this->physicalTableName($table);
            $rows = Db::query("SHOW TABLES LIKE '{$tableName}'");
            return $cache[$table] = !empty($rows);
        } catch (\Throwable $e) {
            return $cache[$table] = false;
        }
    }

    private function physicalTableName(string $table): string
    {
        $default = (string)(config('database.default') ?: 'mysql');
        $prefix = (string)(config('database.connections.' . $default . '.prefix') ?: '');
        return $prefix . $table;
    }
}
