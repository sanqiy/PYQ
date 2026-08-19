<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

/**
 * 邮件服务类
 * 支持 SMTP 和阿里云邮件推送
 */
class EmailService
{
    protected $config = [];

    /** @var \PHPMailer\PHPMailer\PHPMailer|null SMTP 连接复用 */
    protected static $smtpInstance = null;

    /**
     * 清除 SMTP 连接缓存（配置变更后调用）
     */
    public static function clearSmtpCache(): void
    {
        self::$smtpInstance = null;
    }

    public function __construct()
    {
        $this->loadConfig();
    }

    protected function loadConfig()
    {
        $this->config = SiteConfigService::getAll();
    }

    /**
     * 发送邮件（自动根据 emtype 选择驱动）
     */
    public function send($to, $subject, $body, $isHtml = true)
    {
        $driver = $this->config['emtype'] ?? 'smtp';

        if ($driver === 'aliyun') {
            return $this->sendByAliyun($to, $subject, $body, $isHtml);
        }

        return $this->sendBySmtp($to, $subject, $body, $isHtml);
    }

    /**
     * 通过 SMTP 发送（复用连接）
     */
    protected function sendBySmtp($to, $subject, $body, $isHtml = true)
    {
        if (empty($this->config['emydz']) || empty($this->config['emzh']) || empty($this->config['emkey'])) {
            return ['success' => false, 'message' => 'SMTP 配置不完整'];
        }

        try {
            $mail = $this->getSmtpInstance();

            // 清理上一次的收件人/附件
            $mail->clearAddresses();
            $mail->clearAttachments();

            $mail->setFrom($this->config['emfs'], $this->config['emfszm']);
            $mail->addAddress($to);
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return ['success' => true, 'message' => '发送成功'];
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            // 连接可能已断开，下次重建
            self::$smtpInstance = null;
            $errorMsg = $this->maskSensitiveInfo($e->getMessage());
            return ['success' => false, 'message' => 'SMTP 错误：' . $errorMsg];
        } catch (\Exception $e) {
            self::$smtpInstance = null;
            return ['success' => false, 'message' => '发送异常：' . $e->getMessage()];
        }
    }

    /**
     * 获取或创建 SMTP 实例（连接复用）
     */
    protected function getSmtpInstance(): \PHPMailer\PHPMailer\PHPMailer
    {
        $configKey = md5($this->config['emydz'] . $this->config['emzh'] . $this->config['emduk'] . $this->config['emssl']);

        if (self::$smtpInstance !== null && (self::$smtpInstance['_key'] ?? '') === $configKey) {
            return self::$smtpInstance['_mailer'];
        }

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $this->config['emydz'];
        $mail->SMTPAuth = true;
        $mail->Username = $this->config['emzh'];
        $mail->Password = $this->config['emkey'];
        $mail->CharSet = 'UTF-8';

        $port = (int)($this->config['emduk'] ?: 25);
        $ssl = $this->config['emssl'] ?? '';

        if ($ssl === '' || $ssl === 'auto') {
            if ($port === 465) {
                $ssl = 'ssl';
            } elseif ($port === 587) {
                $ssl = 'tls';
            }
        }

        if ($ssl === 'ssl') {
            $mail->SMTPSecure = 'ssl';
        } elseif ($ssl === 'tls') {
            $mail->SMTPSecure = 'tls';
        }

        $mail->Port = $port;

        self::$smtpInstance = ['_key' => $configKey, '_mailer' => $mail];
        return $mail;
    }

    /**
     * 脱敏 SMTP 错误信息
     */
    protected function maskSensitiveInfo(string $msg): string
    {
        // 隐藏密码、授权码等敏感信息
        $msg = preg_replace('/(password|pass|pwd|auth)[^\s]*\s*[:=]\s*\S+/i', '$1:***', $msg);
        return $msg;
    }

    /**
     * 通过阿里云邮件推送发送
     */
    protected function sendByAliyun($to, $subject, $body, $isHtml = true)
    {
        $accessKeyId = $this->config['aliyun_key'] ?? '';
        $accessKeySecret = $this->config['aliyun_secret'] ?? '';
        $fromAddress = $this->config['aliyun_from'] ?? '';
        $fromName = $this->config['emfszm'] ?? '';

        if (empty($accessKeyId) || empty($accessKeySecret) || empty($fromAddress)) {
            return ['success' => false, 'message' => '阿里云邮件推送配置不完整'];
        }

        $params = [
            'AccessKeyId' => $accessKeyId,
            'Format' => 'JSON',
            'RegionId' => 'cn-hangzhou',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid(mt_rand(), true),
            'SignatureVersion' => '1.0',
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'Version' => '2015-11-23',
            'Action' => 'SingleSendMail',
            'AccountName' => $fromAddress,
            'AddressType' => '1',
            'ReplyToAddress' => 'false',
            'Subject' => $subject,
            'ToAddress' => $to,
        ];

        if ($isHtml) {
            $params['HtmlBody'] = $body;
        } else {
            $params['TextBody'] = $body;
        }

        if (!empty($fromName)) {
            $params['FromAlias'] = $fromName;
        }

        $params['Signature'] = $this->signAliyunMail($params, $accessKeySecret);
        $url = 'https://dm.aliyuncs.com/?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $result = json_decode($response, true);
            if (isset($result['RequestId'])) {
                return ['success' => true, 'message' => '发送成功'];
            }
            return ['success' => false, 'message' => $result['Message'] ?? '发送失败'];
        }

        return ['success' => false, 'message' => 'HTTP 请求失败：' . $httpCode];
    }

    protected function signAliyunMail(array $params, $accessKeySecret)
    {
        ksort($params);
        $canonicalizedQueryString = '';
        foreach ($params as $key => $value) {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key) . '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentEncode(substr($canonicalizedQueryString, 1));
        return base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', true));
    }

    protected function percentEncode($value)
    {
        $result = urlencode($value);
        $result = str_replace('+', '%20', $result);
        $result = str_replace('*', '%2A', $result);
        $result = str_replace('%7E', '~', $result);
        return $result;
    }

    /**
     * 发送验证码
     */
    public function sendVerifyCode($to, $code)
    {
        $code = htmlspecialchars((string)$code, ENT_QUOTES, 'UTF-8');
        $template = EmailTemplateService::render('verify_code', [
            'code' => $code,
            'minutes' => '2',
            'site_name' => $this->config['name'] ?? ''
        ]);
        return $this->send($to, $template['subject'], $template['body']);
    }

    /**
     * 发送评论通知
     */
    public function sendCommentNotify($to, $articleTitle, $commentContent)
    {
        $articleTitle = htmlspecialchars((string)$articleTitle, ENT_QUOTES, 'UTF-8');
        $commentContent = htmlspecialchars((string)$commentContent, ENT_QUOTES, 'UTF-8');
        $template = EmailTemplateService::render('comment_notify', [
            'article_title' => $articleTitle,
            'comment_content' => $commentContent,
            'site_name' => $this->config['name'] ?? ''
        ]);
        return $this->send($to, $template['subject'], $template['body']);
    }

    /**
     * 发送点赞通知
     */
    public function sendLikeNotify($to, $articleTitle)
    {
        $articleTitle = htmlspecialchars((string)$articleTitle, ENT_QUOTES, 'UTF-8');
        $template = EmailTemplateService::render('like_notify', [
            'article_title' => $articleTitle,
            'site_name' => $this->config['name'] ?? ''
        ]);
        return $this->send($to, $template['subject'], $template['body']);
    }
}
