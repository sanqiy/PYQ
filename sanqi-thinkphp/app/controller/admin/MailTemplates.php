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
use app\service\EmailTemplateService;

class MailTemplates extends \app\controller\Base
{
    public function index()
    {
        return view('admin/mail_templates', array_merge([
            'siteConfig' => $this->getSiteConfig(),
            'user' => $this->getUser(),
            'templates' => EmailTemplateService::all(),
            'vars' => EmailTemplateService::vars(),
            'pageTitle' => '邮件模板'
        ], $this->getAdminViewData()));
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }
        $templates = $this->request->post('templates', []);
        if (!is_array($templates)) {
            return $this->error('参数错误');
        }
        EmailTemplateService::save($templates);
        AdminLogService::operation('mail_templates.save', 'configx:email_templates', []);
        return $this->success('保存成功');
    }
}
