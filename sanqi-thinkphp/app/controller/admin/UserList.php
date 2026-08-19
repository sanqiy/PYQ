<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\model\User;
use app\model\Essay;
use app\model\Comm;
use app\model\Lcke;
use app\model\Message;
use app\service\AdminLogService;
use app\service\AdminListService;
use app\service\AdminSecurityService;
use app\service\FileUploadService;
use think\facade\Cache;

class UserList extends \app\controller\Base
{
    public function index()
    {
        $page = AdminListService::page($this->request->get('page', 1));
        $pageSize = AdminListService::pageSize();
        $offset = AdminListService::offset($page);

        $keyword = AdminListService::keyword($this->request->get('keyword', ''));
        $searchTooShort = $keyword !== '' && !AdminListService::canSearch($keyword);

        $query = User::order('id', 'desc');

        if (!$searchTooShort && $keyword !== '') {
            $like = AdminListService::prefixLike($keyword);
            $query->where(function ($q) use ($like) {
                $q->where('username', 'like', $like)
                  ->whereOr('name', 'like', $like)
                  ->whereOr('email', 'like', $like);
            });
        }

        $total = $query->count();
        $users = $query->limit($offset, $pageSize)->select()->toArray();

        return view('admin/userlist', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'keyword' => $keyword,
            'searchTooShort' => $searchTooShort,
            'pageTitle' => '用户管理'
        ], $this->getAdminViewData()));
    }

    public function update()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $id = intval($this->request->post('id', 0));
        $action = $this->request->post('action', '');
        $value = $this->request->post('value', '');

        if ($id <= 0 || $action === '') {
            return $this->error('参数错误');
        }

        $user = User::where('id', $id)->find();
        if (!$user) {
            return $this->error('用户不存在');
        }

        $userData = $user->toArray();

        switch ($action) {
            case 'edit':
                return $this->saveUser($id, $userData);

            case 'ban':
                User::where('id', $id)->update(['ban' => 1, 'bantime' => $value, 'passid' => randomString(64)]);
                AdminLogService::operation('user.ban', 'user:' . $id, ['username' => $userData['username'], 'bantime' => $value]);
                return $this->success('封禁成功');

            case 'unban':
                User::where('id', $id)->update(['ban' => 0, 'bantime' => '']);
                AdminLogService::operation('user.unban', 'user:' . $id, ['username' => $userData['username']]);
                return $this->success('解封成功');

            case 'permission':
                User::where('id', $id)->update(['essqx' => intval($value)]);
                AdminLogService::operation('user.permission', 'user:' . $id, ['username' => $userData['username'], 'essqx' => intval($value)]);
                return $this->success('更新成功');

            case 'delete':
                if (!AdminSecurityService::verifyAdminPassword($this->request->post('admin_password', ''))) {
                    return $this->error('管理员密码错误');
                }
                // 获取该用户所有文章的文件 URL
                $userArticles = Essay::where('ptpuser', $userData['username'])->select()->toArray();
                $allFileUrls = [];
                foreach ($userArticles as $article) {
                    $allFileUrls = array_merge($allFileUrls, FileUploadService::extractUrlsFromArticle($article));
                }
                try {
                    \think\facade\Db::startTrans();
                    User::where('id', $id)->delete();
                    Essay::where('ptpuser', $userData['username'])->force()->delete();
                    Comm::where('couser', $userData['username'])->force()->delete();
                    Lcke::where('luser', $userData['username'])->delete();
                    \think\facade\Db::commit();
                } catch (\Throwable $e) {
                    \think\facade\Db::rollback();
                    return $this->error('删除失败');
                }
                // 清理文件引用
                FileUploadService::cleanupArticleFiles($allFileUrls);
                AdminLogService::operation('user.delete', 'user:' . $id, ['username' => $userData['username']]);
                return $this->success('删除成功');

            default:
                return $this->error('未知操作');
        }
    }

    private function saveUser(int $id, array $oldUser)
    {
        $username = trim((string)$this->request->post('username', ''));
        $email = trim((string)$this->request->post('email', ''));
        $name = trim((string)$this->request->post('name', ''));
        $img = trim((string)$this->request->post('img', ''));
        $url = trim((string)$this->request->post('url', ''));
        $homeimg = trim((string)$this->request->post('homeimg', ''));
        $sign = trim((string)$this->request->post('sign', ''));

        if ($username === '' || $email === '' || $name === '') {
            return $this->error('账号、邮箱、昵称不能为空');
        }
        if (!preg_match('/^[A-Za-z0-9_@.\-]{3,64}$/', $username)) {
            return $this->error('账号格式错误');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('邮箱格式错误');
        }

        $exists = User::where('username', $username)->where('id', '<>', $id)->find();
        if ($exists) {
            return $this->error('账号已存在');
        }
        $exists = User::where('email', $email)->where('id', '<>', $id)->find();
        if ($exists) {
            return $this->error('邮箱已存在');
        }

        $ban = intval($this->request->post('ban', $oldUser['ban'] ?? 0));
        $essqx = intval($this->request->post('essqx', $oldUser['essqx'] ?? 0));
        $esseam = intval($this->request->post('esseam', $oldUser['esseam'] ?? 0));

        $updateData = [
            'username' => $username,
            'email' => $email,
            'name' => $name,
            'img' => $img,
            'url' => $url,
            'homeimg' => $homeimg,
            'sign' => $sign,
            'essqx' => $essqx,
            'esseam' => $esseam,
            'ban' => $ban,
            'bantime' => $this->request->post('bantime', '')
        ];
        if ($ban !== 0 && isFlag($oldUser['ban'] ?? 0) === 0) {
            $updateData['passid'] = randomString(64);
        }
        try {
            \think\facade\Db::startTrans();
            User::where('id', $id)->update($updateData);

            if ($username !== $oldUser['username']) {
                Essay::where('ptpuser', $oldUser['username'])->update(['ptpuser' => $username]);
                Comm::where('couser', $oldUser['username'])->update(['couser' => $username]);
                Lcke::where('luser', $oldUser['username'])->update(['luser' => $username]);
                Message::where('fuser', $oldUser['username'])->update(['fuser' => $username]);
                Message::where('suser', $oldUser['username'])->update(['suser' => $username]);
            }

            \think\facade\Db::commit();
        } catch (\Throwable $e) {
            \think\facade\Db::rollback();
            return $this->error('保存失败');
        }

        Cache::tag('article')->clear();
        AdminLogService::operation('user.edit', 'user:' . $id, [
            'old_username' => $oldUser['username'] ?? '',
            'new_username' => $username,
            'email_changed' => $email !== ($oldUser['email'] ?? ''),
            'name_changed' => $name !== ($oldUser['name'] ?? ''),
        ]);
        return $this->success('保存成功');
    }
}
