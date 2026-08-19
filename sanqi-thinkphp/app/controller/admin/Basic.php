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

class Basic extends \app\controller\Base
{
    const DEFAULT_ICON = '/assets/img/favicon.png';
    const DEFAULT_LOGO = '/assets/img/logo.png';
    const DEFAULT_HOMEIMG = '/assets/img/thumbnailbg.svg';

    public function index()
    {
        $siteConfig = $this->withImageDefaults($this->getSiteConfig());

        return view('admin/basic', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $this->getUser(),
            'icon' => $siteConfig['icon'] ?? self::DEFAULT_ICON,
            'logo' => $siteConfig['logo'] ?? self::DEFAULT_LOGO,
            'homimg' => $siteConfig['homimg'] ?? self::DEFAULT_HOMEIMG,
            'pageTitle' => '基本设置'
        ], $this->getAdminViewData()));
    }

    public function save()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $currentConfig = $this->getSiteConfig();
        $pagepassInput = trim((string)$this->request->post('pagepass', ''));
        $pagepassClear = intval($this->request->post('pagepass_clear', 0)) === 1;
        $pagepassValue = $currentConfig['pagepass'] ?? '';
        if ($pagepassClear) {
            $pagepassValue = '';
        } elseif ($pagepassInput !== '') {
            $pagepassValue = sitePasswordHash($pagepassInput);
        } elseif ($pagepassValue !== '' && strpos((string)$pagepassValue, 'hmac$') !== 0) {
            $pagepassValue = sitePasswordHash($pagepassValue);
        }

        $data = [
            'name' => $this->request->post('name', ''),
            'subtitle' => $this->request->post('subtitle', ''),
            'icon' => $this->request->post('icon', self::DEFAULT_ICON),
            'logo' => $this->request->post('logo', self::DEFAULT_LOGO),
            'sign' => $this->request->post('sign', ''),
            'copyright' => $this->request->post('copyright', ''),
            'beian' => $this->request->post('beian', ''),
            'homimg' => $this->request->post('homimg', self::DEFAULT_HOMEIMG),
            'essgs' => intval($this->request->post('essgs', 10)),
            'commgs' => intval($this->request->post('commgs', 10)),
            'scfont' => siteFontValue((string)$this->request->post('scfont', 'default')),
            'pagepass' => $pagepassValue,
            'music' => $this->request->post('music', ''),
            'poster_random_api' => trim($this->request->post('poster_random_api', '')),
        ];
        $data = $this->withImageDefaults($data);

        $user = $this->getUser();
        ConfigVersionService::snapshot('基础设置保存前', $user['username'] ?? '');
        SiteConfigService::setMultiple($data);
        $this->clearSiteConfigCache();
        AdminLogService::operation('basic.save', 'admin:1', [
            'fields' => array_keys($data),
            'pagepass_changed' => $pagepassInput !== '' || $pagepassClear,
        ]);

        return $this->success('保存成功');
    }

    public function upload()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $field = $this->request->post('type', '');
        if (!in_array($field, ['icon', 'logo', 'homimg'], true)) {
            return $this->error('上传类型错误');
        }

        $file = $this->fileToArray($this->request->file('file'));
        if (!$file || ($file['error'] ?? 1) !== 0) {
            return $this->error('上传失败');
        }

        // Favicon 支持 ICO 格式
        if ($field === 'icon' && $this->isIcoFile($file)) {
            $result = $this->uploadStaticFile($file, 'favicon', ['ico']);
        } else {
            $result = $this->uploadStaticImage($file, $field);
        }

        if (empty($result['success'])) {
            return $this->error($result['message'] ?? '上传失败');
        }

        SiteConfigService::set($field, $result['url']);
        $this->clearSiteConfigCache();
        AdminLogService::operation('basic.upload', 'admin:1', [
            'field' => $field,
            'url' => $result['url'],
        ]);

        return $this->success('上传成功', ['url' => $result['url']]);
    }

    /**
     * 上传静态图片（favicon/logo/bg），固定文件名覆盖写入
     */
    protected function uploadStaticImage($file, string $field): array
    {
        // 验证图片
        $imageInfo = @getimagesize($file['tmp_name']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!$imageInfo || !in_array($imageInfo['mime'], $allowedMimes)) {
            return ['success' => false, 'message' => '文件内容不是有效的图片格式'];
        }

        // 文件名映射
        $nameMap = [
            'icon' => 'favicon',
            'logo' => 'logo',
            'homimg' => 'bg',
        ];
        $baseName = $nameMap[$field] ?? $field;

        // 扩展名
        $mimeExtMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        $ext = $mimeExtMap[$imageInfo['mime']] ?? 'png';

        $dir = app()->getRootPath() . 'public/static/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $baseName . '.' . $ext;
        $savePath = $dir . $filename;

        // 删除旧文件（不同扩展名）
        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp', 'ico'] as $oldExt) {
            $oldFile = $dir . $baseName . '.' . $oldExt;
            if ($oldFile !== $savePath && is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        if (!move_uploaded_file($file['tmp_name'], $savePath)) {
            return ['success' => false, 'message' => '上传失败'];
        }

        return [
            'success' => true,
            'url' => '/static/' . $filename,
        ];
    }

    /**
     * 上传静态文件（ICO 等）
     */
    protected function uploadStaticFile($file, string $baseName, array $allowedExts): array
    {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            return ['success' => false, 'message' => '不支持的文件格式'];
        }

        if (($file['size'] ?? 0) > 1024 * 1024) {
            return ['success' => false, 'message' => '文件不能超过1MB'];
        }

        $dir = app()->getRootPath() . 'public/static/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $baseName . '.' . $ext;
        $savePath = $dir . $filename;

        // 删除旧文件
        foreach (['ico', 'png', 'jpg', 'jpeg', 'gif', 'webp'] as $oldExt) {
            $oldFile = $dir . $baseName . '.' . $oldExt;
            if ($oldFile !== $savePath && is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        if (!move_uploaded_file($file['tmp_name'], $savePath)) {
            return ['success' => false, 'message' => '上传失败'];
        }

        return [
            'success' => true,
            'url' => '/static/' . $filename,
        ];
    }

    protected function withImageDefaults(array $config): array
    {
        if (empty($config['icon'])) {
            $config['icon'] = self::DEFAULT_ICON;
        }
        if (empty($config['logo'])) {
            $config['logo'] = self::DEFAULT_LOGO;
        }
        if (empty($config['homimg'])) {
            $config['homimg'] = self::DEFAULT_HOMEIMG;
        }
        return $config;
    }

    protected function isIcoFile($file): bool
    {
        if (!$file || !isset($file['name'])) {
            return false;
        }
        return strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) === 'ico';
    }
}
