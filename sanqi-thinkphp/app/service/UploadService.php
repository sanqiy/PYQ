<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

/**
 * 上传服务类
 */
class UploadService
{
    protected $cloudStorage;

    // 图片扩展名白名单
    protected static $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    // 视频扩展名白名单
    protected static $videoExts = ['mp4', 'webm', 'ogg'];
    // 附件扩展名白名单
    protected static $fileExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', '7z', 'tar', 'gz', 'txt', 'csv', 'md', 'json', 'xml', 'apk', 'ipa'];

    public function __construct()
    {
        $this->cloudStorage = new CloudStorageService();
    }

    /**
     * 安全获取文件扩展名（小写、去除非法字符）
     */
    protected function safeExt($filename, $allowedExts)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $ext = preg_replace('/[^a-z0-9]/', '', $ext);
        return in_array($ext, $allowedExts) ? $ext : null;
    }

    /**
     * 上传图片
     */
    public function uploadImage($file)
    {
        // 验证文件
        $validation = $this->validateImage($file);
        if (!$validation['success']) {
            return $validation;
        }

        // 计算 MD5，检查是否已存在
        $md5 = md5_file($file['tmp_name']);
        $existing = FileUploadService::findByMd5($md5);
        if ($existing) {
            FileUploadService::incrementRef($existing['url']);
            return [
                'success' => true,
                'url' => $existing['url'],
                'local_url' => $existing['url'],
                'thumb_url' => $existing['url'],
                'cloud_url' => '',
                'filename' => basename($existing['url']),
                'deduplicated' => true,
            ];
        }

        // 生成文件名（使用安全扩展名）
        $ext = $this->safeExt($file['name'], self::$imageExts);
        if (!$ext) {
            return ['success' => false, 'message' => '不支持的文件扩展名'];
        }
        $filename = 'img_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;
        $datePath = date('Ymd');
        $publicPath = app()->getRootPath() . 'public/';
        $savePath = $publicPath . "upload/image/{$datePath}/{$filename}";
        $remotePath = "meimiao/image/{$datePath}/{$filename}";

        // 创建目录
        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 移动文件
        if (!move_uploaded_file($file['tmp_name'], $savePath)) {
            return ['success' => false, 'message' => '上传失败'];
        }
        ImageService::sanitizeImageFile($savePath);

        // 转换为WebP
        $webpFilename = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
        $webpPath = $dir . '/' . $webpFilename;
        $webpRemotePath = "meimiao/image/{$datePath}/{$webpFilename}";

        if ($this->shouldCompressImages() && $this->convertToWebp($savePath, $webpPath)) {
            unlink($savePath);
            $savePath = $webpPath;
            $filename = $webpFilename;
            $remotePath = $webpRemotePath;
            // WebP 转换后重新计算 MD5
            $md5 = md5_file($savePath);
        }

        $thumbPath = dirname($savePath) . '/thumbs/' . basename($savePath);
        ImageService::createThumbnail($savePath, $thumbPath);

        // 上传到云存储
        $cloudUrl = '';
        if ($this->cloudStorage->upload($savePath, $remotePath)) {
            $cloudUrl = $this->cloudStorage->getUrl($remotePath);
            if (is_file($thumbPath)) {
                $this->cloudStorage->upload($thumbPath, dirname($remotePath) . '/thumbs/' . basename($remotePath));
            }
            $this->deleteLocalAfterCloudUpload($savePath);
            $this->deleteLocalAfterCloudUpload($thumbPath);
        }

        $localUrl = "/upload/image/{$datePath}/{$filename}";
        $thumbUrl = "/upload/image/{$datePath}/thumbs/{$filename}";
        $finalUrl = $cloudUrl ?: $localUrl;

        // 记录到 file_uploads 表
        FileUploadService::create($md5, $finalUrl, 'image');

        return [
            'success' => true,
            'url' => $finalUrl,
            'local_url' => $localUrl,
            'thumb_url' => is_file($thumbPath) ? $thumbUrl : $localUrl,
            'cloud_url' => $cloudUrl,
            'filename' => $filename
        ];
    }

    /**
     * 上传视频
     */
    public function uploadVideo($file)
    {
        // 验证文件
        $validation = $this->validateVideo($file);
        if (!$validation['success']) {
            return $validation;
        }

        // 计算 MD5，检查是否已存在
        $md5 = md5_file($file['tmp_name']);
        $existing = FileUploadService::findByMd5($md5);
        if ($existing) {
            FileUploadService::incrementRef($existing['url']);
            return [
                'success' => true,
                'url' => $existing['url'],
                'local_url' => $existing['url'],
                'cloud_url' => '',
                'filename' => basename($existing['url']),
                'deduplicated' => true,
            ];
        }

        // 生成文件名（使用安全扩展名）
        $ext = $this->safeExt($file['name'], self::$videoExts);
        if (!$ext) {
            return ['success' => false, 'message' => '不支持的文件扩展名'];
        }
        $filename = 'video_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;
        $datePath = date('Ymd');
        $publicPath = app()->getRootPath() . 'public/';
        $savePath = $publicPath . "upload/video/{$datePath}/{$filename}";
        $remotePath = "meimiao/video/{$datePath}/{$filename}";

        // 创建目录
        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 移动文件
        if (!move_uploaded_file($file['tmp_name'], $savePath)) {
            return ['success' => false, 'message' => '上传失败'];
        }
        // 上传到云存储
        $cloudUrl = '';
        if ($this->cloudStorage->upload($savePath, $remotePath)) {
            $cloudUrl = $this->cloudStorage->getUrl($remotePath);
            $this->deleteLocalAfterCloudUpload($savePath);
        }

        $localUrl = "/upload/video/{$datePath}/{$filename}";
        $finalUrl = $cloudUrl ?: $localUrl;

        // 记录到 file_uploads 表
        FileUploadService::create($md5, $finalUrl, 'video');

        return [
            'success' => true,
            'url' => $finalUrl,
            'local_url' => $localUrl,
            'cloud_url' => $cloudUrl,
            'filename' => $filename
        ];
    }

    /**
     * 上传附件文件
     */
    public function uploadFile($file)
    {
        if (!$file || !$file->isValid()) {
            return ['success' => false, 'message' => '上传失败'];
        }

        $originalName = $file->getOriginalName();
        $ext = $this->safeExt($originalName, self::$fileExts);
        if (!$ext) {
            return ['success' => false, 'message' => '不支持的文件类型，支持: ' . implode('/', self::$fileExts)];
        }

        $maxSize = 50 * 1024 * 1024; // 50MB
        try {
            $fileSize = $file->getSize();
        } catch (\Exception $e) {
            $fileSize = 0;
        }
        if ($fileSize > $maxSize) {
            return ['success' => false, 'message' => '文件大小不能超过50MB'];
        }

        $filename = 'file_' . time() . '_' . random_int(1000, 9999) . '.' . $ext;
        $datePath = date('Ymd');
        $publicPath = app()->getRootPath() . 'public/';
        $savePath = $publicPath . "upload/file/{$datePath}/{$filename}";

        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!move_uploaded_file($file->getRealPath(), $savePath)) {
            return ['success' => false, 'message' => '上传失败'];
        }

        $localUrl = "/upload/file/{$datePath}/{$filename}";

        return [
            'success' => true,
            'url' => $localUrl,
            'name' => $originalName,
            'size' => $fileSize,
        ];
    }

    /**
     * 上传头像（固定文件名覆盖写入）
     */
    public function uploadAvatar($file, $userId)
    {
        // 验证文件
        $validation = $this->validateImage($file);
        if (!$validation['success']) {
            return $validation;
        }

        // 获取原始扩展名
        $ext = $this->safeExt($file['name'], self::$imageExts);
        if (!$ext) {
            return ['success' => false, 'message' => '不支持的文件扩展名'];
        }

        $publicPath = app()->getRootPath() . 'public/';
        $dir = $publicPath . 'upload/avatar/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 删除旧头像（不同扩展名）
        $baseName = "{$userId}_avatar";
        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $oldExt) {
            $oldFile = $dir . $baseName . '.' . $oldExt;
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $filename = $baseName . '.' . $ext;
        $savePath = $dir . $filename;

        // 移动文件
        if (!move_uploaded_file($file['tmp_name'], $savePath)) {
            return ['success' => false, 'message' => '上传失败'];
        }
        ImageService::sanitizeImageFile($savePath);

        // 转换为WebP
        if ($this->shouldCompressImages() && $ext !== 'webp') {
            $webpPath = $dir . $baseName . '.webp';
            if ($this->convertToWebp($savePath, $webpPath)) {
                @unlink($savePath);
                $filename = $baseName . '.webp';
                $savePath = $webpPath;
            }
        }

        $localUrl = '/upload/avatar/' . $filename;

        return [
            'success' => true,
            'url' => $localUrl,
            'local_url' => $localUrl,
            'cloud_url' => '',
            'filename' => $filename
        ];
    }

    /**
     * 上传封面（固定文件名覆盖写入）
     */
    public function uploadCover($file, $userId)
    {
        // 验证文件
        $validation = $this->validateImage($file);
        if (!$validation['success']) {
            return $validation;
        }

        // 获取原始扩展名
        $ext = $this->safeExt($file['name'], self::$imageExts);
        if (!$ext) {
            return ['success' => false, 'message' => '不支持的文件扩展名'];
        }

        $publicPath = app()->getRootPath() . 'public/';
        $dir = $publicPath . 'upload/cover/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 删除旧封面（不同扩展名）
        $baseName = "{$userId}_cover";
        foreach (['jpg', 'jpeg', 'png', 'gif', 'webp'] as $oldExt) {
            $oldFile = $dir . $baseName . '.' . $oldExt;
            if (is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        $filename = $baseName . '.' . $ext;
        $savePath = $dir . $filename;

        // 移动文件
        if (!move_uploaded_file($file['tmp_name'], $savePath)) {
            return ['success' => false, 'message' => '上传失败'];
        }

        // 转换为WebP
        if ($this->shouldCompressImages() && $ext !== 'webp') {
            $webpPath = $dir . $baseName . '.webp';
            if ($this->convertToWebp($savePath, $webpPath)) {
                @unlink($savePath);
                $filename = $baseName . '.webp';
                $savePath = $webpPath;
            }
        }

        $localUrl = '/upload/cover/' . $filename;

        return [
            'success' => true,
            'url' => $localUrl,
            'local_url' => $localUrl,
            'cloud_url' => '',
            'filename' => $filename
        ];
    }

    /**
     * 验证图片文件
     */
    protected function validateImage($file)
    {
        if (!$file || $file['error'] !== 0) {
            return ['success' => false, 'message' => '上传失败'];
        }

        // 1. 扩展名白名单校验
        $ext = $this->safeExt($file['name'], self::$imageExts);
        if (!$ext) {
            return ['success' => false, 'message' => '只支持jpg/png/gif/webp格式'];
        }

        // 2. 图片内容校验（getimagesize验证是否为真实图片）
        $imageInfo = @getimagesize($file['tmp_name']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!$imageInfo || !in_array($imageInfo['mime'], $allowedMimes)) {
            return ['success' => false, 'message' => '文件内容不是有效的图片格式'];
        }

        if (!ImageService::withinPixelLimit($imageInfo)) {
            return ['success' => false, 'message' => '图片尺寸过大，请上传2500万像素以内的图片'];
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => '文件大小不能超过5MB'];
        }

        return ['success' => true];
    }

    /**
     * 验证视频文件
     */
    protected function validateVideo($file)
    {
        if (!$file || $file['error'] !== 0) {
            return ['success' => false, 'message' => '上传失败'];
        }

        // 1. 扩展名白名单校验
        $ext = $this->safeExt($file['name'], self::$videoExts);
        if (!$ext) {
            return ['success' => false, 'message' => '只支持mp4/webm/ogg格式'];
        }

        // 2. 服务端MIME类型校验
        $realMime = detectMimeType($file['tmp_name'], $ext);
        $allowedMimes = ['video/mp4', 'video/webm', 'video/ogg'];
        if (!in_array($realMime, $allowedMimes)) {
            return ['success' => false, 'message' => '文件内容不是有效的视频格式'];
        }

        $maxSize = 50 * 1024 * 1024; // 50MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => '文件大小不能超过50MB'];
        }

        return ['success' => true];
    }

    /**
     * 转换图片为WebP格式
     */
    protected function convertToWebp($sourcePath, $destPath, $quality = 80)
    {
        return ImageService::convertToWebp($sourcePath, $destPath, $quality);
    }

    protected function shouldCompressImages()
    {
        static $enabled = null;
        if ($enabled !== null) {
            return $enabled;
        }

        try {
            $value = SiteConfigService::get('imgpres', '1');
            $enabled = (string)($value ?? '1') === '1';
        } catch (\Throwable $e) {
            $enabled = true;
        }

        return $enabled;
    }

    protected function shouldKeepLocalAfterCloud()
    {
        static $enabled = null;
        if ($enabled !== null) {
            return $enabled;
        }

        try {
            $config = \app\model\Configx::where('title', 'upyun')->find();
            if ($config && !empty($config['text'])) {
                $cloudConfig = json_decode($config['text'], true);
                if (is_array($cloudConfig) && array_key_exists('keep_local_after_cloud', $cloudConfig)) {
                    $enabled = (string)$cloudConfig['keep_local_after_cloud'] === '1';
                    return $enabled;
                }
            }
        } catch (\Throwable $e) {
            $enabled = true;
            return $enabled;
        }

        $enabled = config('app.upload.keep_local_after_cloud', true) !== false;
        return $enabled;
    }

    protected function deleteLocalAfterCloudUpload($path)
    {
        if ($this->shouldKeepLocalAfterCloud()) {
            return;
        }

        if (!is_string($path) || $path === '' || !is_file($path)) {
            return;
        }

        $realPath = realpath($path);
        $uploadRoot = realpath(app()->getRootPath() . 'public/' . 'upload');
        if ($realPath && $uploadRoot && strpos($realPath, $uploadRoot) === 0) {
            @unlink($realPath);
        }
    }

    /**
     * 删除文件
     */
    public function deleteFile($url)
    {
        $publicPath = app()->getRootPath() . 'public/';

        // 本地文件
        if (strpos($url, '/upload/') === 0) {
            $localPath = realpath($publicPath . ltrim($url, '/'));
            $uploadRoot = realpath($publicPath . 'upload');
            // 防止路径遍历：确保最终路径在upload目录内
            if ($localPath && $uploadRoot && strpos($localPath, $uploadRoot) === 0 && file_exists($localPath)) {
                $thumbPath = dirname($localPath) . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . basename($localPath);
                unlink($localPath);
                if (is_file($thumbPath)) {
                    @unlink($thumbPath);
                }
            }
        }

        // 云存储文件
        if (strpos($url, 'http') === 0) {
            $remotePath = str_replace($this->cloudStorage->getUrl(''), '', $url);
            $this->cloudStorage->delete($remotePath);
        }
    }
}
