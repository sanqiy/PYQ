<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\service\AdminLogService;
use app\service\CommentSecurityService;
use app\service\ConfigVersionService;
use app\service\SiteConfigService;

class Authority extends \app\controller\Base
{
    protected $defaults = [
        'zt' => '1',
        'regqx' => '0',
        'readonly' => '0',
        'loginkg' => '1',
        'lnkzt' => '0',
        'kqsy' => '0',
        'ptpfan' => '1',
        'notname' => '0',
        'imgpres' => '1',
        'viscomm' => '0',
        'vislike_cancel' => '1',
        'comaud' => '0',
        'ptpaud' => '0',
        'rosdomain' => '1',
        'daymode' => '1',
        'gotop' => '1',
        'search' => '1',
        'videoauplay' => '0',
        'regverify' => '0',
        'email_push' => '1',
        'norightclick' => '0',
    ];

    public function index()
    {
        $raw = $this->getSiteConfig();
        $siteConfig = array_merge($this->defaults, is_array($raw) ? $raw : $raw->toArray());
        $siteConfig['piccir'] = SiteConfigService::get('piccir', '0');

        return view('admin/authority', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $this->getUser(),
            'groups' => $this->getGroups(),
            'pageTitle' => '权限设置',
        ], $this->getAdminViewData()));
    }

    protected function getGroups(): array
    {
        return [
            [
                'title' => '站点开关',
                'items' => [
                    ['name' => 'zt', 'label' => '站点状态', 'icon' => 'mdi-web', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '关闭后前台显示维护提示'],
                    ['name' => 'regqx', 'label' => '注册开关', 'icon' => 'mdi-account-plus', 'on' => '0', 'off' => '-1', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '关闭后新用户无法注册'],
                    ['name' => 'readonly', 'label' => '新用户只读', 'icon' => 'mdi-account-lock', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '开启后新注册用户只能评论，不能发布文章'],
                    ['name' => 'loginkg', 'label' => '登录开关', 'icon' => 'mdi-login', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '关闭后无法登录'],
                ],
            ],
            [
                'title' => '内容与互动',
                'items' => [
                    ['name' => 'ptpfan', 'label' => '关注可见', 'icon' => 'mdi-eye', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '开启后仅关注者可见完整内容'],
                    ['name' => 'notname', 'label' => '匿名发布', 'icon' => 'mdi-incognito', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '允许用户匿名发布文章'],
                    ['name' => 'imgpres', 'label' => '图片压缩', 'icon' => 'mdi-image-size-select-large', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '上传图片自动压缩'],
                    ['name' => 'viscomm', 'label' => '游客评论', 'icon' => 'mdi-comment-account', 'on' => '0', 'off' => '-1', 'onText' => '允许', 'offText' => '禁止', 'default' => '0', 'help' => '是否允许游客发表评论'],
                    ['name' => 'vislike_cancel', 'label' => '游客取消点赞', 'icon' => 'mdi-heart-off', 'on' => '1', 'off' => '0', 'onText' => '允许', 'offText' => '禁止', 'default' => '1', 'help' => '是否允许游客取消自己点过的赞'],
                    ['name' => 'comaud', 'label' => '评论审核', 'icon' => 'mdi-stamper', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '开启后评论需审核才显示'],
                    ['name' => 'ptpaud', 'label' => '文章审核', 'icon' => 'mdi-file-check', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '开启后文章需审核才显示'],
                ],
            ],
            [
                'title' => '功能开关',
                'items' => [
                    ['name' => 'lnkzt', 'label' => '友链功能', 'icon' => 'mdi-link', 'on' => '0', 'off' => '1', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '是否开启友链功能'],
                    ['name' => 'kqsy', 'label' => '搜索引擎', 'icon' => 'mdi-magnify', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '是否允许搜索引擎收录'],
                    ['name' => 'rosdomain', 'label' => '自定义域名', 'icon' => 'mdi-domain', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '是否允许自定义域名'],
                    ['name' => 'daymode', 'label' => '日间模式', 'icon' => 'mdi-white-balance-sunny', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '默认使用日间模式'],
                    ['name' => 'gotop', 'label' => '回到顶部', 'icon' => 'mdi-arrow-up', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '显示回到顶部按钮'],
                    ['name' => 'search', 'label' => '搜索功能', 'icon' => 'mdi-magnify', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '是否开启搜索功能'],
                    ['name' => 'videoauplay', 'label' => '视频自动播放', 'icon' => 'mdi-play-circle', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '视频是否自动播放'],
                    ['name' => 'regverify', 'label' => '注册验证', 'icon' => 'mdi-shield-check', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '注册时需要邮箱验证'],
                    ['name' => 'piccir', 'label' => '圆形头像', 'icon' => 'mdi-account-circle', 'on' => '1', 'off' => '0', 'onText' => '圆形', 'offText' => '方形', 'default' => '0', 'help' => '头像显示为圆形或方形'],
                    ['name' => 'email_push', 'label' => '邮件推送', 'icon' => 'mdi-email-alert', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '1', 'help' => '关闭后点赞、评论、回复不再发送邮件通知'],
                    ['name' => 'norightclick', 'label' => '禁止右键', 'icon' => 'mdi-cursor-default-off', 'on' => '1', 'off' => '0', 'onText' => '开启', 'offText' => '关闭', 'default' => '0', 'help' => '开启后禁止页面右键菜单'],
                ],
            ],
        ];
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $data = [
            'zt' => $this->choice('zt', ['0', '1'], '1'),
            'regqx' => $this->choice('regqx', ['-1', '0'], '0'),
            'readonly' => $this->choice('readonly', ['0', '1'], '0'),
            'loginkg' => $this->choice('loginkg', ['0', '1'], '1'),
            'lnkzt' => $this->choice('lnkzt', ['0', '1'], '0'),
            'kqsy' => $this->choice('kqsy', ['0', '1'], '0'),
            'ptpfan' => $this->choice('ptpfan', ['0', '1'], '1'),
            'notname' => $this->choice('notname', ['0', '1'], '0'),
            'imgpres' => $this->choice('imgpres', ['0', '1'], '1'),
            'viscomm' => $this->choice('viscomm', ['-1', '0'], '0'),
            'vislike_cancel' => $this->choice('vislike_cancel', ['0', '1'], '1'),
            'comaud' => $this->choice('comaud', ['0', '1'], '0'),
            'ptpaud' => $this->choice('ptpaud', ['0', '1'], '0'),
            'rosdomain' => $this->choice('rosdomain', ['0', '1'], '1'),
            'daymode' => $this->choice('daymode', ['0', '1'], '1'),
            'gotop' => $this->choice('gotop', ['0', '1'], '1'),
            'search' => $this->choice('search', ['0', '1'], '1'),
            'videoauplay' => $this->choice('videoauplay', ['0', '1'], '0'),
            'regverify' => $this->choice('regverify', ['0', '1'], '0'),
            'email_push' => $this->choice('email_push', ['0', '1'], '1'),
            'norightclick' => $this->choice('norightclick', ['0', '1'], '0'),
        ];

        $user = $this->getUser();
        ConfigVersionService::snapshot('权限设置保存前', $user['username'] ?? '');
        SiteConfigService::setMultiple($data);

        $piccir = $this->choice('piccir', ['0', '1'], '0');
        SiteConfigService::set('piccir', $piccir);

        $commentSecurity = CommentSecurityService::getConfig();
        CommentSecurityService::saveConfig([
            'audit_enabled' => (int)$data['comaud'],
            'keywords' => $commentSecurity['keywords'] ?? '',
            'blacklist' => $commentSecurity['blacklist'] ?? '',
        ]);

        AdminLogService::operation('authority.save', 'admin:1', [
            'fields' => array_keys($data),
            'piccir' => $piccir,
        ]);

        return $this->success('保存成功');
    }

    protected function choice(string $name, array $allowed, string $default): string
    {
        $value = (string)$this->request->post($name, $default);
        return in_array($value, $allowed, true) ? $value : $default;
    }
}
