<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\admin;

use app\model\Configx;
use app\service\AdminLogService;
use app\service\AdminSecurityService;
use app\service\CloudStorageService;

/**
 * 云存储设置控制器
 */
class Cloudset extends \app\controller\Base
{
    /**
     * 云存储设置页面
     */
    public function index()
    {
        $siteConfig = $this->getSiteConfig();

        // 获取云存储配置
        $configRow = Configx::where('title', 'upyun')->find();
        $cloudConfig = ['type' => 'upyun'];
        if ($configRow && !empty($configRow->text)) {
            $decoded = json_decode($configRow->text, true);
            if (is_array($decoded)) {
                $cloudConfig = array_merge($cloudConfig, $decoded);
            }
        }

        return view('admin/cloudset', array_merge([
            'siteConfig' => $siteConfig,
            'user' => $this->getUser(),
            'cloudConfig' => $cloudConfig,
            's3ProviderDefaults' => CloudStorageService::getS3ProviderDefaults($cloudConfig['s3_provider'] ?? 's3'),
            'pageTitle' => '云存储设置'
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

        $type = $this->request->post('type', 'upyun');
        $keepLocal = $this->request->post('keep_local_after_cloud', '1') === '1' ? '1' : '0';
        if (!AdminSecurityService::verifyAdminPassword($this->request->post('admin_password', ''))) {
            return $this->error('管理员密码错误');
        }

        if ($type === 'upyun') {
            $data = [
                'type' => 'upyun',
                'bucketName' => $this->request->post('bucketName', ''),
                'operatorName' => $this->request->post('operatorName', ''),
                'operatorPassword' => $this->request->post('operatorPassword', ''),
                'operatorurl' => $this->request->post('operatorurl', ''),
                'keep_local_after_cloud' => $keepLocal
            ];
        } elseif ($type === 'aliyun') {
            $data = [
                'type' => 'aliyun',
                'accessKeyId' => $this->request->post('accessKeyId', ''),
                'accessKeySecret' => $this->request->post('accessKeySecret', ''),
                'endpoint' => $this->request->post('endpoint', ''),
                'bucket' => $this->request->post('bucket', ''),
                'operatorurl' => $this->request->post('operatorurl', ''),
                'keep_local_after_cloud' => $keepLocal
            ];
        } else {
            $data = [
                'type' => 's3',
                's3_provider' => $this->request->post('s3_provider', 's3'),
                'accessKeyId' => $this->request->post('accessKeyId', ''),
                'accessKeySecret' => $this->request->post('accessKeySecret', ''),
                'endpoint' => $this->request->post('endpoint', ''),
                'region' => $this->request->post('region', 'us-east-1'),
                'bucket' => $this->request->post('bucket', ''),
                'operatorurl' => $this->request->post('operatorurl', ''),
                'keep_local_after_cloud' => $keepLocal
            ];
        }

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        // 检查是否已存在
        $exists = Configx::where('title', 'upyun')->find();
        if ($exists) {
            Configx::where('title', 'upyun')->update(['text' => $json]);
        } else {
            Configx::create(['title' => 'upyun', 'text' => $json]);
        }
        CloudStorageService::clearConfigCache();
        AdminLogService::operation('cloud.save', 'configx:upyun', ['type' => $type]);

        return $this->success('保存成功');
    }

    /**
     * 获取 S3 服务商默认配置（AJAX）
     */
    public function s3Defaults()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $provider = $this->request->post('s3_provider', 's3');
        $defaults = CloudStorageService::getS3ProviderDefaults($provider);

        return json(['code' => 1, 'data' => $defaults]);
    }

    /**
     * 测试连接
     */
    public function test()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        $configRow = Configx::where('title', 'upyun')->find();
        if (!$configRow || empty($configRow->text)) {
            return $this->error('请先保存配置');
        }

        $cloudConfig = json_decode($configRow->text, true);
        $type = $cloudConfig['type'] ?? 'upyun';

        // 测试上传一个小文件
        $testFile = app()->getRuntimePath() . 'cloud_test.txt';
        file_put_contents($testFile, 'cloud storage test');

        $service = new CloudStorageService();
        $remotePath = 'meimiao/test/cloud_test.txt';

        $result = $service->upload($testFile, $remotePath);

        if (file_exists($testFile)) {
            unlink($testFile);
        }

        if ($result) {
            // 删除测试文件
            $service->delete($remotePath);
            AdminLogService::operation('cloud.test', 'configx:upyun', ['result' => 'success']);
            return $this->success('连接测试成功');
        } else {
            AdminLogService::operation('cloud.test', 'configx:upyun', ['result' => 'failed']);
            return $this->error('连接测试失败，请检查配置');
        }
    }
}
