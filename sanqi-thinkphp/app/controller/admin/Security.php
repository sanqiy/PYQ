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
use app\service\AdminSecurityService;

class Security extends \app\controller\Base
{
    public function index()
    {
        $config = AdminSecurityService::getTwoFactorConfig();
        if (empty($config['totp_secret'])) {
            $config['totp_secret'] = AdminSecurityService::generateTotpSecret();
            AdminSecurityService::saveTwoFactorConfig($config);
        }

        $totpSecret = $config['totp_secret'] ?? '';
        $adminUser = $this->getUser();
        $adminName = $adminUser['username'] ?? $adminUser['name'] ?? 'admin';
        $siteConfig = $this->getSiteConfig();
        $siteName = $siteConfig['name'] ?? '美妙的';
        $otpauth = $totpSecret !== '' ? 'otpauth://totp/' . rawurlencode($siteName) . ':' . rawurlencode($adminName) . '?secret=' . $totpSecret . '&issuer=' . rawurlencode($siteName) : '';

        return view('admin/security', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $adminUser,
            'twoFactor' => $config,
            'totpSecret' => $totpSecret,
            'otpauth' => $otpauth,
            'pageTitle' => '安全设置'
        ], $this->getAdminViewData()));
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $enabled = intval($this->request->post('enabled', 0)) === 1 ? 1 : 0;
        $method = $this->request->post('method', 'email');
        if (!in_array($method, ['email', 'totp'], true)) {
            $method = 'email';
        }

        $config = AdminSecurityService::getTwoFactorConfig();
        if (empty($config['totp_secret'])) {
            $config['totp_secret'] = AdminSecurityService::generateTotpSecret();
        }
        $config['enabled'] = $enabled;
        $config['method'] = $method;
        AdminSecurityService::saveTwoFactorConfig($config);
        if ($enabled === 1) {
            $adminUser = $this->getUser();
            if ($adminUser) {
                AdminSecurityService::markTwoFactorVerified($adminUser);
            }
        } else {
            AdminSecurityService::clearTwoFactorVerified();
        }
        AdminLogService::operation('security.2fa.save', 'admin_2fa', ['enabled' => $enabled, 'method' => $method]);

        return $this->success('保存成功');
    }

    public function regenerateTotp()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $config = AdminSecurityService::getTwoFactorConfig();
        $config['totp_secret'] = AdminSecurityService::generateTotpSecret();
        AdminSecurityService::saveTwoFactorConfig($config);
        AdminLogService::operation('security.2fa.regenerate_totp', 'admin_2fa');

        return $this->success('已重新生成密钥', ['secret' => $config['totp_secret']]);
    }
}
