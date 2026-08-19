<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\service\NotificationService;

/**
 * 系统通知管理控制器
 */
class Notification extends \app\controller\Base
{
    /**
     * 发送系统公告页面
     */
    public function index()
    {
        return view('admin/notification', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'pageTitle' => '系统通知',
        ], $this->getAdminViewData()));
    }

    /**
     * 发送系统公告
     */
    public function send()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $title = strip_tags(trim($this->request->post('title', '')));
        $content = trim($this->request->post('content', ''));
        $target = $this->request->post('target', 'all');

        if (empty($title) || empty($content)) {
            return $this->error('标题和内容不能为空');
        }

        $usernames = null;
        if ($target === 'users') {
            $userList = $this->request->post('usernames', '');
            $usernames = array_filter(array_map('trim', explode(',', $userList)));
            if (empty($usernames)) {
                return $this->error('请指定目标用户');
            }
        }

        $count = NotificationService::sendSystemAnnouncement($title, $content, $usernames);
        return $this->success('发送成功，共通知 ' . $count . ' 位用户');
    }
}
