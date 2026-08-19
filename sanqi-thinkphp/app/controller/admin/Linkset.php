<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\model\Link;
use app\service\AdminLogService;

/**
 * 友链管理控制器
 */
class Linkset extends \app\controller\Base
{
    /**
     * 友链列表
     */
    public function index()
    {
        $links = Link::order('id', 'asc')->select()->toArray();
        $siteConfig = $this->getSiteConfig();

        return view('admin/linkset', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $this->getUser(),
            'links' => $links,
            'pageTitle' => '友链管理'
        ], $this->getAdminViewData()));
    }

    /**
     * 添加友链
     */
    public function add()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $name = $this->request->post('name', '');
        $url = $this->request->post('url', '');
        $img = $this->request->post('img', '');

        if (empty($name) || empty($url)) {
            return $this->error('名称和链接不能为空');
        }

        if (empty($img)) {
            $img = $this->fetchAndCacheFavicon($url);
        }

        $link = Link::create([
            'urls' => $name,
            'url' => $url,
            'urlimg' => $img
        ]);
        AdminLogService::operation('link.add', 'link:' . $link->id, ['name' => $name, 'url' => $url]);

        return $this->success('添加成功');
    }

    /**
     * 更新友链
     */
    public function update()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $id = intval($this->request->post('id', 0));
        $name = $this->request->post('name', '');
        $url = $this->request->post('url', '');
        $img = $this->request->post('img', '');

        if ($id <= 0 || empty($name) || empty($url)) {
            return $this->error('参数错误');
        }

        if (empty($img)) {
            $img = $this->fetchAndCacheFavicon($url);
        }

        Link::where('id', $id)->update([
            'urls' => $name,
            'url' => $url,
            'urlimg' => $img
        ]);
        AdminLogService::operation('link.update', 'link:' . $id, ['name' => $name, 'url' => $url]);

        return $this->success('更新成功');
    }

    /**
     * 获取并缓存网站favicon，返回本地URL
     */
    private function fetchAndCacheFavicon(string $url): string
    {
        $domain = parse_url($url, PHP_URL_HOST);
        if (!$domain) {
            return '';
        }

        $icoDir = app()->getRootPath() . 'public' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'ico';
        if (!is_dir($icoDir)) {
            @mkdir($icoDir, 0755, true);
        }

        $filename = preg_replace('/[^a-zA-Z0-9.\-]/', '_', $domain) . '.png';
        $filepath = $icoDir . DIRECTORY_SEPARATOR . $filename;
        $publicUrl = '/upload/ico/' . $filename;

        // 已有缓存直接返回
        if (is_file($filepath) && filesize($filepath) > 0) {
            return $publicUrl;
        }

        // 获取 favicon
        $faviconUrl = 'https://favicon.cccyun.cc/' . $domain;
        $imageData = $this->httpGet($faviconUrl);

        if ($imageData !== '' && strlen($imageData) > 100) {
            @file_put_contents($filepath, $imageData);
            return $publicUrl;
        }

        // 备用：直接请求网站 /favicon.ico
        $imageData = $this->httpGet('https://' . $domain . '/favicon.ico');
        if ($imageData !== '' && strlen($imageData) > 100) {
            @file_put_contents($filepath, $imageData);
            return $publicUrl;
        }

        return '';
    }

    /**
     * HTTP GET 请求
     */
    private function httpGet(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 2,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; FaviconFetcher/1.0)',
        ]);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code === 200 && is_string($data)) ? $data : '';
    }

    /**
     * 删除友链
     */
    public function delete()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $id = intval($this->request->post('id', 0));
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        Link::where('id', $id)->delete();
        AdminLogService::operation('link.delete', 'link:' . $id);
        return $this->success('删除成功');
    }
}
