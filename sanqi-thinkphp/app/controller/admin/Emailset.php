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
use app\service\ConfigVersionService;
use app\service\SiteConfigService;

/**
 * 邮件设置控制器
 */
class Emailset extends \app\controller\Base
{
    /**
     * 邮件设置页面
     */
    public function index()
    {
        $siteConfig = $this->getSiteConfig();

        return view('admin/emailset', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $this->getUser(),
            'pageTitle' => '邮件设置'
        ], $this->getAdminViewData()));
    }

    /**
     * 保存设置
     */
    public function save()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $emtype = $this->request->post('emtype', 'smtp');

        $data = [
            'emtype' => $emtype,
            'emydz' => $this->request->post('emydz', ''),
            'emssl' => $this->request->post('emssl', ''),
            'emduk' => intval($this->request->post('emduk', 0)),
            'emkey' => $this->request->post('emkey', ''),
            'emzh' => $this->request->post('emzh', ''),
            'emfs' => $this->request->post('emfs', ''),
            'emfszm' => $this->request->post('emfszm', ''),
            'aliyun_key' => $this->request->post('aliyun_key', ''),
            'aliyun_secret' => $this->request->post('aliyun_secret', ''),
            'aliyun_from' => $this->request->post('aliyun_from', '')
        ];

        $user = $this->getUser();
        ConfigVersionService::snapshot('邮件设置保存前', $user['username'] ?? '');
        SiteConfigService::setMultiple($data);
        \app\service\EmailService::clearSmtpCache();
        AdminLogService::operation('email.save', 'admin:1', ['driver' => $emtype]);

        return $this->success('保存成功');
    }

    /**
     * 发送测试邮件
     */
    public function test()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $email = $this->request->post('email', '');
        if (empty($email)) {
            return $this->error('请输入测试邮箱');
        }

        $siteConfig = $this->getSiteConfig();
        $body = \app\service\EmailTemplateService::decorate(
            '该邮件为系统测试邮件，若您收到此邮件代表您的网站邮件配置正确，可以正常收发邮件啦！',
            ['site_name' => $siteConfig['name'] ?? 'sanqi']
        );
        $result = sendEmailDetailed(
            $siteConfig,
            $email,
            '测试邮件',
            $body
        );

        if ($result['success'] ?? false) {
            AdminLogService::operation('email.test', 'admin:1', ['email' => $email, 'result' => 'success']);
            return $this->success('发送成功');
        } else {
            $errMsg = $result['message'] ?? '未知错误';
            AdminLogService::operation('email.test', 'admin:1', ['email' => $email, 'result' => 'failed', 'error' => $errMsg]);
            return $this->error('发送失败：' . $errMsg);
        }
    }
}
