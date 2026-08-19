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
use app\model\Comm;
use app\model\Essay;
use app\service\CommentSecurityService;
use app\service\NotificationService;
use app\validate\CommentValidate;
use think\facade\Db;
use think\facade\View;

/**
 * Comment API controller.
 */
class Comment extends Base
{
    /**
     * Submit a comment.
     */
    public function submit()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $articleCid = $this->request->post('tieid', '');
        $commentText = $this->request->post('pltext', '');
        $replyName = $this->request->post('sh-tiehf', $this->request->post('tiehf', ''));
        $replyUser = $this->request->post('sh-tieea', $this->request->post('tieea', ''));

        $validate = new CommentValidate();
        $validate->scene('submit');
        if (!$validate->batch(true)->check(['tieid' => $articleCid, 'pltext' => $commentText])) {
            return $this->error($validate->getError());
        }

        $article = Essay::where('cid', $articleCid)->find();
        if (!$article) {
            return $this->error('文章不存在');
        }

        if (isFlag($article['commauth'] ?? 1, 1) !== 1) {
            return $this->error('评论已关闭');
        }

        $siteConfig = $this->getSiteConfig();
        $user = $this->getUser();
        if ($user) {
            $commentUser = $user['username'];
            $commentName = $user['name'] ?: $user['username'];
            $commentImg = $user['img'];
            $commentUrl = '';
            $commentEmail = $user['email'] ?? '';
            $visName = '';
            $visEmail = '';
            $visUrl = '';
        } else {
            if (isFlag($siteConfig['viscomm'] ?? 0) === -1) {
                return $this->error('游客评论已关闭');
            }

            $visName = trim((string)$this->request->post('vis_name', ''));
            $visEmail = trim((string)$this->request->post('vis_email', ''));
            $visUrl = trim((string)$this->request->post('vis_url', ''));

            $guestValidate = new \think\Validate([
                'vis_name' => 'require|max:20',
                'vis_email' => 'require|email',
            ], [
                'vis_name.require' => '昵称不能为空',
                'vis_name.max' => '昵称不能超过20个字符',
                'vis_email.require' => '邮箱不能为空',
                'vis_email.email' => '邮箱格式不正确',
            ]);
            if (!$guestValidate->batch(true)->check(['vis_name' => $visName, 'vis_email' => $visEmail])) {
                return $this->error($guestValidate->getError());
            }

            $visitor = visitorIdentity();
            $commentUser = $visitor['id'];
            $commentName = $visName;
            $commentImg = getVisitorAvatarByEmail($visEmail);
            $commentUrl = $visUrl;
            $commentEmail = $visEmail;
            session('visykmz_userzh', $commentName);
        }

        $clientIp = request()->ip();
        try {
            CommentSecurityService::assertRateLimit($clientIp, $user && $this->isAdmin());
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }

        $ecid = uniqueId();
        $security = CommentSecurityService::evaluate($commentText, [
            'username' => $commentUser,
            'name' => $commentName,
            'email' => $commentEmail,
            'url' => $commentUrl,
            'ip' => $clientIp,
        ], $user && $this->isAdmin());
        if (!$security['allowed']) {
            return $this->error($security['reason'] ?: '评论被拦截');
        }
        $comaud = $security['audit'] ? 0 : 1;

        $replyNameForSave = $replyName ?: 'false';
        if ($replyUser !== '' && strpos((string)$replyUser, 'vis#-[') === false && strpos((string)$replyUser, ']-#vis') === false) {
            $replyNameForSave = 'false';
        }

        $commentInsert = [
            'ecid' => $ecid,
            'couser' => $commentUser,
            'coname' => $user ? '' : $commentName,
            'courl' => $commentUrl,
            'wzcid' => $articleCid,
            'cotext' => $commentText,
            'cotime' => date('Y-m-d H:i:s'),
            'bconame' => $replyNameForSave,
            'bcouser' => $replyUser ?: 'false',
            'comaud' => $comaud,
            'ip' => $clientIp,
            'coimg' => $user ? '' : $commentImg,
        ];
        if (!$user && $this->hasColumn('comm', 'coemail')) {
            $commentInsert['coemail'] = $commentEmail;
        }
        Db::name('comm')->strict(false)->insert($commentInsert);
        CommentSecurityService::hitRateLimit($clientIp, $user && $this->isAdmin());
        if (!$user) {
            setVisitorCommentProfileCookies($visName, $visEmail, $visUrl);
        }

        if ($comaud === 1) {
            $commenter = $user
                ? ['username' => $user['username'], 'name' => $user['name'] ?: $user['username'], 'img' => $user['img']]
                : ['username' => $commentUser, 'name' => $commentName, 'img' => $commentImg];
            NotificationService::sendCommentNotify(
                $commenter,
                $article['ptpuser'],
                $articleCid,
                $commentText,
                $ecid
            );
            NotificationService::sendMentionNotify(
                $commenter,
                $articleCid,
                $commentText,
                $ecid
            );
        }

        $comment = Comm::where('ecid', $ecid)->find();
        $html = View::fetch('component/comment_item', [
            'comment' => $comment,
            'articleCid' => $articleCid,
        ]);

        $msg = $comaud === 1 ? '评论成功' : '评论已提交，等待审核';
        return $this->success($msg, [
            'html' => $html,
        ]);
    }

    /**
     * Load more comments.
     */
    public function load()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $page = max(1, intval($this->request->post('page', 1)));
        $articleCid = strip_tags(trim((string)$this->request->post('wzcidd', '')));
        $siteConfig = $this->getSiteConfig();
        $commgs = max(1, min(100, isFlag($siteConfig['commgs'] ?? 10, 10)));
        $offset = ($page - 1) * $commgs;

        $comments = Comm::where('wzcid', $articleCid)
            ->where('comaud', '<>', '0')
            ->where('comaud', '<>', '-1')
            ->order('id', 'asc')
            ->limit($offset, $commgs)
            ->select();

        $commenterNames = [];
        foreach ($comments as $c) {
            $commenterNames[] = $c['couser'] ?? '';
            $commenterNames[] = $c['bcouser'] ?? '';
        }
        $this->prefetchUsers($commenterNames);

        $html = '';
        foreach ($comments as $comment) {
            $html .= View::fetch('component/comment_item', [
                'comment' => $comment,
                'articleCid' => $articleCid,
            ]);
        }

        return $this->success('加载成功', ['html' => $html]);
    }

    /**
     * Delete a comment.
     */
    public function delete()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) return;

        $user = $this->getUser();
        $ecid = $this->request->post('plid', '');

        $validate = new CommentValidate();
        $validate->scene('delete');
        if (!$validate->batch(true)->check(['plid' => $ecid])) {
            return $this->error($validate->getError());
        }

        $comment = Comm::where('ecid', $ecid)->find();
        if (!$comment) {
            return $this->error('评论不存在');
        }

        $article = Essay::where('cid', $comment['wzcid'])->find();
        if ($comment['couser'] !== $user['username'] &&
            ($article && $article['ptpuser'] !== $user['username']) &&
            !$this->isAdmin()) {
            return $this->error('无权删除');
        }

        Db::table('comm')->where('ecid', $ecid)->delete();
        return $this->success('删除成功');
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

    private function physicalTableName(string $table): string
    {
        $default = (string)(config('database.default') ?: 'mysql');
        $prefix = (string)(config('database.connections.' . $default . '.prefix') ?: '');
        return $prefix . $table;
    }
}
