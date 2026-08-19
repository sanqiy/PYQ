<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller\api;

use app\controller\Base;
use app\service\UploadService;

/**
 * 上传API控制器
 */
class Upload extends Base
{
    protected $uploadService;

    protected function initialize()
    {
        parent::initialize();
        $this->uploadService = new UploadService();
    }

    /**
     * 上传图片
     */
    public function image()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) return;

        $file = $this->fileToArray($this->request->file('file'));
        $result = $this->uploadService->uploadImage($file);

        if ($result['success']) {
            return $this->success('上传成功', ['url' => $result['url']]);
        } else {
            return $this->error($result['message']);
        }
    }

    /**
     * 上传视频
     */
    public function video()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) return;

        $file = $this->fileToArray($this->request->file('file'));
        $result = $this->uploadService->uploadVideo($file);

        if ($result['success']) {
            return $this->success('上传成功', ['url' => $result['url']]);
        } else {
            return $this->error($result['message']);
        }
    }

    /**
     * 上传附件文件
     */
    public function file()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }

        if (!$this->requireLogin()) return;

        $file = $this->request->file('file');
        if (!$file) {
            return $this->error('请选择文件');
        }

        $uploadService = new \app\service\UploadService();
        $result = $uploadService->uploadFile($file);

        if ($result['success']) {
            try { $fallbackSize = $file->getSize(); } catch (\Exception $e) { $fallbackSize = 0; }
            return $this->success('上传成功', [
                'url' => $result['url'],
                'name' => $result['name'] ?? $file->getOriginalName(),
                'size' => $result['size'] ?? $fallbackSize,
            ]);
        } else {
            return $this->error($result['message']);
        }
    }
}
