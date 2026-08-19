<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

/**
 * 邮件辅助函数
 */

/**
 * 发送邮件（全局快捷方法）
 *
 * @param array  $siteConfig 站点配置（兼容旧调用，实际由 EmailService 内部加载）
 * @param string $to         收件人
 * @param string $subject    邮件主题
 * @param string $body       邮件正文
 * @return bool
 */
function sendEmail($siteConfig, $to, $subject, $body)
{
    $service = new \app\service\EmailService();
    $result = $service->send($to, $subject, $body);
    return $result['success'] ?? false;
}

/**
 * 发送邮件并返回详细结果（含错误信息）
 */
function sendEmailDetailed($siteConfig, $to, $subject, $body)
{
    $service = new \app\service\EmailService();
    return $service->send($to, $subject, $body);
}
