<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\service;

use app\model\Message;
use app\model\User;
use app\model\Essay;

/**
 * 通知中心服务
 * 统一管理站内消息和邮件通知
 */
class NotificationService
{
    /**
     * 发送评论通知
     * @param array $commenter ['username','name','img']
     * @param string $articleAuthor 文章作者账号
     * @param string $articleCid 文章CID
     * @param string $commentText 评论内容
     * @param string|null $ecid 评论CID
     */
    public static function sendCommentNotify(
        array $commenter,
        string $articleAuthor,
        string $articleCid,
        string $commentText,
        ?string $ecid = null
    ): void {
        if ($articleAuthor === $commenter['username']) {
            return;
        }

        Message::create([
            'fuser'      => $commenter['username'],
            'fimg'       => $commenter['img'] ?? '',
            'fname'      => $commenter['name'] ?? $commenter['username'],
            'suser'      => $articleAuthor,
            'type'       => Message::TYPE_COMMENT,
            'related_id' => $articleCid,
            'title'      => '评论了你的文章',
            'text'       => $commentText,
            'ftime'      => date('Y-m-d H:i:s'),
            'msg'        => Message::STATUS_UNREAD,
        ]);

        self::trySendEmail($articleAuthor, 'comment', $articleCid, $commentText);
    }

    /**
     * 发送点赞通知
     * @param array $liker ['username','name','img']
     * @param string $articleAuthor 文章作者账号
     * @param string $articleCid 文章CID
     */
    public static function sendLikeNotify(
        array $liker,
        string $articleAuthor,
        string $articleCid
    ): void {
        if ($articleAuthor === $liker['username']) {
            return;
        }

        Message::create([
            'fuser'      => $liker['username'],
            'fimg'       => $liker['img'] ?? '',
            'fname'      => $liker['name'] ?? $liker['username'],
            'suser'      => $articleAuthor,
            'type'       => Message::TYPE_LIKE,
            'related_id' => $articleCid,
            'title'      => '赞了你的文章',
            'text'       => '',
            'ftime'      => date('Y-m-d H:i:s'),
            'msg'        => Message::STATUS_UNREAD,
        ]);

        self::trySendEmail($articleAuthor, 'like', $articleCid);
    }

    /**
     * 发送审核结果通知
     * @param string $authorUsername 文章/评论作者
     * @param string $contentType 'article'|'comment'
     * @param string $relatedId 文章cid
     * @param bool $approved 是否通过
     * @param string $reason 拒绝原因
     */
    public static function sendAuditNotify(
        string $authorUsername,
        string $contentType,
        string $relatedId,
        bool $approved,
        string $reason = ''
    ): void {
        $label = $contentType === 'article' ? '文章' : '评论';
        $text = $approved
            ? '您的' . $label . '已通过审核'
            : '您的' . $label . '未通过审核' . ($reason ? '：' . $reason : '');

        Message::create([
            'fuser'      => 'system',
            'fimg'       => '',
            'fname'      => '系统通知',
            'suser'      => $authorUsername,
            'type'       => Message::TYPE_AUDIT,
            'related_id' => $relatedId,
            'title'      => '审核结果',
            'text'       => $text,
            'ftime'      => date('Y-m-d H:i:s'),
            'msg'        => Message::STATUS_UNREAD,
        ]);
    }

    /**
     * 发送系统公告
     * @param string $title 公告标题
     * @param string $content 公告内容
     * @param array|null $usernames 目标用户，null=全体未封禁用户
     * @return int 发送数量
     */
    public static function sendSystemAnnouncement(
        string $title,
        string $content,
        ?array $usernames = null
    ): int {
        if ($usernames === null) {
            $users = User::where('ban', 0)
                ->field('username')
                ->select()
                ->toArray();
            $usernames = array_column($users, 'username');
        }

        if (empty($usernames)) {
            return 0;
        }

        $count = 0;
        $now = date('Y-m-d H:i:s');

        foreach (array_chunk($usernames, 500) as $chunk) {
            $data = [];
            foreach ($chunk as $username) {
                $data[] = [
                    'fuser'      => 'system',
                    'fimg'       => '',
                    'fname'      => '系统公告',
                    'suser'      => $username,
                    'type'       => Message::TYPE_SYSTEM,
                    'related_id' => '',
                    'title'      => $title,
                    'text'       => $content,
                    'ftime'      => $now,
                    'msg'        => Message::STATUS_UNREAD,
                ];
            }
            $count += (new Message())->insertAll($data);
        }

        return $count;
    }

    /**
     * 解析评论中的 @提及 并发送通知
     * @param array $commenter ['username','name','img']
     * @param string $articleCid 文章CID
     * @param string $commentText 评论内容
     * @param string|null $ecid 评论CID
     */
    public static function sendMentionNotify(
        array $commenter,
        string $articleCid,
        string $commentText,
        ?string $ecid = null
    ): void {
        $mentionedUsers = self::parseMentions($commentText);
        if (empty($mentionedUsers)) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        foreach ($mentionedUsers as $username) {
            // 不给自己发通知
            if ($username === $commenter['username']) {
                continue;
            }

            Message::create([
                'fuser'      => $commenter['username'],
                'fimg'       => $commenter['img'] ?? '',
                'fname'      => $commenter['name'] ?? $commenter['username'],
                'suser'      => $username,
                'type'       => Message::TYPE_MENTION,
                'related_id' => $articleCid,
                'title'      => '在评论中提到了你',
                'text'       => $commentText,
                'ftime'      => $now,
                'msg'        => Message::STATUS_UNREAD,
            ]);
        }
    }

    /**
     * 从文本中解析 @用户名 提及
     * @return string[] 去重后的有效用户名列表
     */
    public static function parseMentions(string $text): array
    {
        if (strpos($text, '@') === false) {
            return [];
        }

        // 匹配 @用户名（非空白字符，最长20字符）
        preg_match_all('/@([^\s@]{1,20})/u', $text, $matches);
        $names = array_unique($matches[1] ?? []);

        if (empty($names)) {
            return [];
        }

        // 查询匹配的用户（按 username 或 name 匹配）
        $users = User::where('ban', 0)
            ->where(function ($query) use ($names) {
                $query->whereIn('username', $names)
                    ->whereOr('name', 'in', $names);
            })
            ->field('username, name')
            ->select()
            ->toArray();

        $validUsernames = [];
        foreach ($users as $user) {
            $validUsernames[] = $user['username'];
        }

        return array_unique($validUsernames);
    }

    /**
     * 记录邮件发送状态
     */
    public static function recordEmailStatus(
        string $toUsername,
        string $relatedId,
        bool $success,
        string $errorMsg = ''
    ): void {
        Message::create([
            'fuser'      => 'system',
            'fimg'       => '',
            'fname'      => '邮件通知',
            'suser'      => $toUsername,
            'type'       => Message::TYPE_EMAIL,
            'related_id' => $relatedId,
            'title'      => $success ? '邮件发送成功' : '邮件发送失败',
            'text'       => $success ? '邮件通知已发送到您的邮箱' : '邮件发送失败：' . $errorMsg,
            'ftime'      => date('Y-m-d H:i:s'),
            'msg'        => Message::STATUS_UNREAD,
        ]);
    }

    /**
     * 尝试发送邮件通知
     */
    protected static function trySendEmail(
        string $username,
        string $type,
        string $articleCid,
        string $content = ''
    ): void {
        if (\app\service\SiteConfigService::get('email_push', '1') !== '1') {
            return;
        }

        $user = User::where('username', $username)->find();
        if (!$user || empty($user['email'])) {
            return;
        }

        if (intval($user['esseam']) !== 1) {
            return;
        }

        $article = Essay::where('cid', $articleCid)->field('article_title,ptptext')->find();
        $articleTitle = '';
        if ($article) {
            $articleTitle = $article['article_title'] ?: mb_substr(strip_tags($article['ptptext'] ?? ''), 0, 30);
        }

        try {
            $emailService = new EmailService();
            if ($type === 'comment') {
                $result = $emailService->sendCommentNotify($user['email'], $articleTitle, $content);
            } else {
                $result = $emailService->sendLikeNotify($user['email'], $articleTitle);
            }

            self::recordEmailStatus($username, $articleCid, $result['success'], $result['message'] ?? '');
        } catch (\Throwable $e) {
            self::recordEmailStatus($username, $articleCid, false, $e->getMessage());
        }
    }
}
