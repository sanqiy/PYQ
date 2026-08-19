<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

class EmailTemplateService
{
    const KEY = 'email_templates';

    /**
     * 邮件正文统一 HTML 外壳
     */
    const SHELL = <<<'HTML'
<div style="margin-left: 8px; margin-top: 8px; margin-bottom: 8px; margin-right: 8px;">
    <span>
        <div id="cTMail-Wrap" style="word-break: break-all;box-sizing:border-box;text-align:center;min-width:320px; max-width:660px; border:1px solid #f6f6f6; background-color:#f7f8fa; margin:auto; padding:20px 0 30px; font-family:'helvetica neue',PingFangSC-Light,arial,'hiragino sans gb','microsoft yahei ui','microsoft yahei',simsun,sans-serif">
            <div style="width:92px; height:25px;margin:0 auto;">
                {{logo}}
            </div>
            <p style="height:2px;background-color: #00a4ff;border: 0;font-size:0;padding:0;width:100%;margin-top:20px;"></p>
            <div id="cTMail-inner" style="background-color:#fff; padding:23px 20px;box-shadow: 0px 1px 1px 0px rgba(122, 55, 55, 0.2);text-align:left;">
                <h1 id="cTMail-title" style="font-weight:bold;font-size:20px; line-height:36px; margin:0 0 16px;">{{site_name}}</h1>
                <p id="cTMail-userName" style="font-size:14px;color:#333; line-height:24px; margin:0;">尊敬的用户，您好！</p>
                <p class="cTMail-content" style="font-size: 14px; color: rgb(51, 51, 51); line-height: 24px; margin: 6px 0px 0px; overflow-wrap: break-word; word-break: break-all;">{{content}}</p>
                <p style="border-top: 1px solid rgb(234, 237, 240); margin: 20px 0px;"></p>
                <dl style="font-size: 14px; color: rgb(51, 51, 51); line-height: 18px;">
                    <dt style="margin: 0px 0px 8px; padding: 0px;">温馨提示：</dt>
                    <dd style="margin: 0px 0px 6px; padding: 0px; font-size: 12px; line-height: 22px;">1. 请确保不要将验证码告知他人，避免造成不必要的麻烦。</dd>
                    <dd style="margin: 0px 0px 6px; padding: 0px; font-size: 12px; line-height: 22px;">2. 如果您收到的验证码并非您本人请求或获取，请立即忽略此邮件。</dd>
                </dl>
                <p id="cTMail-sender" style="font-size: 14px; line-height: 26px; word-wrap: break-word; word-break: break-all; margin-top: 32px;">
                    此致<br>
                    <a href="{{site_url}}" rel="noopener" target="_blank"><strong>{{site_name}}</strong></a>
                </p>
            </div>
            <div id="cTMail-copy" style="text-align:center; font-size:12px; line-height:18px; color:#999;">
                <p style="text-align:center; margin:20px auto 14px auto;font-size:12px;color:#999;">此为系统邮件，请勿回复。</p>
                <p style="text-align:center;margin:0 auto 4px;">Copyright © {{site_name}} All rights reserved.</p>
            </div>
        </div>
    </span>
</div>
HTML;

    public static function defaults()
    {
        return [
            'verify_code' => [
                'name' => '通用验证码',
                'subject' => '验证码',
                'body' => '您的验证码是：<strong>{{code}}</strong>，{{minutes}}分钟内有效。'
            ],
            'repass_code' => [
                'name' => '找回密码验证码',
                'subject' => '验证码',
                'body' => '您正在找回 {{site_name}} 的账号密码，验证码是：<strong>{{code}}</strong>，{{minutes}}分钟内有效。'
            ],
            'register_code' => [
                'name' => '注册验证码',
                'subject' => '{{site_name}} 注册验证码',
                'body' => '您正在注册 {{site_name}} 的账号，验证码是：<strong>{{code}}</strong>，{{minutes}}分钟内有效。'
            ],
            'comment_notify' => [
                'name' => '评论通知',
                'subject' => '新评论通知',
                'body' => '您的文章《{{article_title}}》有新评论：{{comment_content}}'
            ],
            'like_notify' => [
                'name' => '点赞通知',
                'subject' => '新点赞通知',
                'body' => '您的文章《{{article_title}}》被点赞了'
            ],
        ];
    }

    public static function all()
    {
        $row = \app\model\Configx::where('title', self::KEY)->find();
        $saved = !empty($row['text']) ? json_decode($row['text'], true) : [];
        return array_replace_recursive(self::defaults(), is_array($saved) ? $saved : []);
    }

    /**
     * 获取每个模板可用的变量列表
     */
    public static function vars()
    {
        $result = [];
        foreach (self::defaults() as $key => $tpl) {
            preg_match_all('/\{\{(\w+)\}\}/', $tpl['body'], $matches);
            $result[$key] = $matches[1] ? '{{' . implode('}}、{{', array_unique($matches[1])) . '}}' : '';
        }
        return $result;
    }

    public static function save(array $templates)
    {
        $defaults = self::defaults();
        $clean = [];
        foreach ($defaults as $key => $default) {
            $clean[$key] = [
                'name' => $default['name'],
                'subject' => trim((string)($templates[$key]['subject'] ?? $default['subject'])),
                'body' => (string)($templates[$key]['body'] ?? $default['body']),
            ];
        }
        $json = json_encode($clean, JSON_UNESCAPED_UNICODE);
        $exists = \app\model\Configx::where('title', self::KEY)->find();
        if ($exists) {
            \app\model\Configx::where('title', self::KEY)->update(['text' => $json]);
        } else {
            \app\model\Configx::create(['title' => self::KEY, 'text' => $json]);
        }
    }

    public static function render($key, array $vars = [])
    {
        $templates = self::all();
        $template = $templates[$key] ?? self::defaults()['verify_code'];
        $body = self::replace($template['body'], $vars);
        if (!self::isFullHtml($body)) {
            $body = self::decorate($body, $vars);
        }
        return [
            'subject' => self::replace($template['subject'], $vars),
            'body' => $body,
        ];
    }

    /**
     * 判断正文是否已是完整 HTML 文档
     */
    protected static function isFullHtml($body): bool
    {
        $b = ltrim((string)$body);
        return stripos($b, '<!doctype') === 0 || stripos($b, '<html') === 0;
    }

    /**
     * 用统一外壳装饰正文内容
     */
    public static function decorate($content, array $vars = [])
    {
        $siteName = $vars['site_name'] ?? 'sanqi';
        $siteUrl = (string)($vars['site_url'] ?? '');
        if ($siteUrl === '') {
            try {
                $siteUrl = (string)\think\facade\Request::domain();
            } catch (\Throwable $e) {
                $siteUrl = '';
            }
        }
        $siteUrl = rtrim($siteUrl, '/');
        $logo = $siteUrl !== ''
            ? '<a href="' . $siteUrl . '" rel="noopener" target="_blank" style="display:block;">'
                . '<img border="0" src="' . $siteUrl . '/assets/img/logo.png" alt="' . $siteName . '" style="width:92px; height:25px;display:block;"></a>'
            : '';
        return str_replace(
            ['{{site_name}}', '{{site_url}}', '{{logo}}', '{{content}}'],
            [$siteName, $siteUrl, $logo, (string)$content],
            self::SHELL
        );
    }

    private static function replace($text, array $vars)
    {
        $replace = [];
        foreach ($vars as $key => $value) {
            $replace['{{' . $key . '}}'] = (string)$value;
        }
        return strtr((string)$text, $replace);
    }
}
