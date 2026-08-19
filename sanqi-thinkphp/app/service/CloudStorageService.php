<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

/**
 * 云存储服务类
 * 支持又拍云、阿里云OSS、S3兼容存储（Cloudflare R2 / Backblaze B2 / Amazon S3 / MinIO / DigitalOcean Spaces）
 */
class CloudStorageService
{
    protected $config = [];
    protected static $configLoaded = false;
    protected static $cachedConfig = [];
    protected static $cachedType = '';
    protected $type = ''; // upyun / aliyun / s3

    // 允许的云存储路径前缀
    protected static $allowedPrefixes = [
        'meimiao/image/',
        'meimiao/video/',
        'meimiao/avatar/',
        'meimiao/cover/',
    ];

    /**
     * 清理远程路径，防止路径遍历
     */
    protected static function sanitizePath($path)
    {
        // 移除空字节
        $path = str_replace("\0", '', $path);
        // 统一使用正斜杠
        $path = str_replace('\\', '/', $path);
        // 移除前导斜杠
        $path = ltrim($path, '/');

        // 规范化路径，解析掉 .. 和 .
        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '..' || $part === '') {
                continue;
            }
            if ($part !== '.') {
                $parts[] = $part;
            }
        }
        $path = implode('/', $parts);

        // 白名单前缀校验
        $allowed = false;
        foreach (self::$allowedPrefixes as $prefix) {
            if (strpos($path, $prefix) === 0) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            throw new \InvalidArgumentException('不允许的云存储路径: ' . $path);
        }

        return $path;
    }

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * 加载云存储配置
     */
    protected function loadConfig()
    {
        if (self::$configLoaded) {
            $this->config = self::$cachedConfig;
            $this->type = self::$cachedType;
            return;
        }

        self::$configLoaded = true;
        self::$cachedConfig = [];
        self::$cachedType = '';

        $config = \app\model\Configx::where('title', 'upyun')->find();
        if ($config && !empty($config['text'])) {
            $decoded = json_decode($config['text'], true);
            if (is_array($decoded)) {
                self::$cachedConfig = $decoded;
                self::$cachedType = $decoded['type'] ?? 'upyun';
            }
        }

        $this->config = self::$cachedConfig;
        $this->type = self::$cachedType;
    }

    public static function clearConfigCache()
    {
        self::$configLoaded = false;
        self::$cachedConfig = [];
        self::$cachedType = '';
    }

    /**
     * 上传文件到云存储
     */
    public function upload($localPath, $remotePath)
    {
        if (empty($this->config)) {
            return false;
        }

        $remotePath = self::sanitizePath($remotePath);

        switch ($this->type) {
            case 'upyun':
                return $this->uploadToUpyun($localPath, $remotePath);
            case 'aliyun':
                return $this->uploadToAliyun($localPath, $remotePath);
            case 's3':
                return $this->uploadToS3($localPath, $remotePath);
            default:
                return false;
        }
    }

    /**
     * 删除云存储文件
     */
    public function delete($remotePath)
    {
        if (empty($this->config)) {
            return false;
        }

        $remotePath = self::sanitizePath($remotePath);

        switch ($this->type) {
            case 'upyun':
                return $this->deleteFromUpyun($remotePath);
            case 'aliyun':
                return $this->deleteFromAliyun($remotePath);
            case 's3':
                return $this->deleteFromS3($remotePath);
            default:
                return false;
        }
    }

    /**
     * 获取文件URL
     */
    public function getUrl($remotePath)
    {
        if (empty($this->config)) {
            return '';
        }

        $domain = $this->config['operatorurl'] ?? '';
        if (empty($domain)) {
            return '';
        }

        $remotePath = self::sanitizePath($remotePath);

        return rtrim($domain, '/') . '/' . $remotePath;
    }

    /**
     * 获取 S3 provider 默认配置
     */
    public static function getS3ProviderDefaults($provider)
    {
        $defaults = [
            'r2' => [
                'endpoint' => '',
                'region' => 'auto',
                'endpoint_hint' => '{accountId}.r2.cloudflarestorage.com',
            ],
            'b2' => [
                'endpoint' => 's3.us-west-004.backblazeb2.com',
                'region' => 'us-west-004',
                'endpoint_hint' => 's3.{region}.backblazeb2.com',
            ],
            's3' => [
                'endpoint' => 's3.us-east-1.amazonaws.com',
                'region' => 'us-east-1',
                'endpoint_hint' => 's3.{region}.amazonaws.com',
            ],
            'minio' => [
                'endpoint' => '',
                'region' => 'us-east-1',
                'endpoint_hint' => 'localhost:9000 或 your-minio-server.com',
            ],
            'spaces' => [
                'endpoint' => 'nyc3.digitaloceanspaces.com',
                'region' => 'us-east-1',
                'endpoint_hint' => '{region}.digitaloceanspaces.com',
            ],
        ];

        return $defaults[$provider] ?? $defaults['s3'];
    }

    /**
     * 上传到又拍云
     */
    protected function uploadToUpyun($localPath, $remotePath)
    {
        $bucketName = $this->config['bucketName'] ?? '';
        $operatorName = $this->config['operatorName'] ?? '';
        $operatorPassword = $this->config['operatorPassword'] ?? '';

        if (empty($bucketName) || empty($operatorName) || empty($operatorPassword)) {
            return false;
        }

        $url = "https://v0.api.upyun.com/{$bucketName}/{$remotePath}";
        $fileContent = file_get_contents($localPath);

        if ($fileContent === false) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$operatorName}:{$operatorPassword}");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Length: ' . strlen($fileContent)
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return $httpCode === 200;
    }

    /**
     * 从又拍云删除
     */
    protected function deleteFromUpyun($remotePath)
    {
        $bucketName = $this->config['bucketName'] ?? '';
        $operatorName = $this->config['operatorName'] ?? '';
        $operatorPassword = $this->config['operatorPassword'] ?? '';

        if (empty($bucketName) || empty($operatorName) || empty($operatorPassword)) {
            return false;
        }

        $url = "https://v0.api.upyun.com/{$bucketName}/{$remotePath}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$operatorName}:{$operatorPassword}");

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    // ========== S3 兼容存储 ==========

    /**
     * 上传到 S3 兼容存储
     */
    protected function uploadToS3($localPath, $remotePath)
    {
        $accessKeyId = $this->config['accessKeyId'] ?? '';
        $accessKeySecret = $this->config['accessKeySecret'] ?? '';
        $endpoint = $this->config['endpoint'] ?? '';
        $bucket = $this->config['bucket'] ?? '';
        $region = $this->config['region'] ?? 'us-east-1';

        if (empty($accessKeyId) || empty($accessKeySecret) || empty($endpoint) || empty($bucket)) {
            return false;
        }

        $fileContent = file_get_contents($localPath);
        if ($fileContent === false) {
            return false;
        }

        $contentType = mime_content_type($localPath) ?: 'application/octet-stream';
        $host = $this->buildS3Host($bucket, $endpoint);
        $uri = $this->buildS3Uri($bucket, $remotePath);
        $url = 'https://' . $host . $uri;

        $amzDate = gmdate('Ymd\THis\Z');
        $dateStamp = gmdate('Ymd');

        $headers = [
            'host' => $host,
            'content-type' => $contentType,
            'x-amz-content-sha256' => hash('sha256', $fileContent),
            'x-amz-date' => $amzDate,
        ];

        $signedHeaders = 'content-type;host;x-amz-content-sha256;x-amz-date';
        $signature = $this->signS3Request('PUT', $uri, '', $headers, $fileContent, $region, $dateStamp, $amzDate);

        $authHeader = "AWS4-HMAC-SHA256 Credential={$accessKeyId}/{$dateStamp}/{$region}/s3/aws4_request, SignedHeaders={$signedHeaders}, Signature={$signature}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Host: {$host}",
            "Content-Type: {$contentType}",
            "x-amz-content-sha256: " . hash('sha256', $fileContent),
            "x-amz-date: {$amzDate}",
            "Authorization: {$authHeader}",
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    /**
     * 从 S3 兼容存储删除
     */
    protected function deleteFromS3($remotePath)
    {
        $accessKeyId = $this->config['accessKeyId'] ?? '';
        $accessKeySecret = $this->config['accessKeySecret'] ?? '';
        $endpoint = $this->config['endpoint'] ?? '';
        $bucket = $this->config['bucket'] ?? '';
        $region = $this->config['region'] ?? 'us-east-1';

        if (empty($accessKeyId) || empty($accessKeySecret) || empty($endpoint) || empty($bucket)) {
            return false;
        }

        $host = $this->buildS3Host($bucket, $endpoint);
        $uri = $this->buildS3Uri($bucket, $remotePath);
        $url = 'https://' . $host . $uri;

        $amzDate = gmdate('Ymd\THis\Z');
        $dateStamp = gmdate('Ymd');

        $headers = [
            'host' => $host,
            'x-amz-content-sha256' => hash('sha256', ''),
            'x-amz-date' => $amzDate,
        ];

        $signedHeaders = 'host;x-amz-content-sha256;x-amz-date';
        $signature = $this->signS3Request('DELETE', $uri, '', $headers, '', $region, $dateStamp, $amzDate);

        $authHeader = "AWS4-HMAC-SHA256 Credential={$accessKeyId}/{$dateStamp}/{$region}/s3/aws4_request, SignedHeaders={$signedHeaders}, Signature={$signature}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Host: {$host}",
            "x-amz-content-sha256: " . hash('sha256', ''),
            "x-amz-date: {$amzDate}",
            "Authorization: {$authHeader}",
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200 || $httpCode === 204;
    }

    /**
     * 是否使用 S3 path-style（MinIO 等）
     */
    protected function isS3PathStyle()
    {
        $provider = $this->config['s3_provider'] ?? '';
        return $provider === 'minio';
    }

    /**
     * 构建 S3 主机名
     */
    protected function buildS3Host($bucket, $endpoint)
    {
        if ($this->isS3PathStyle()) {
            return $endpoint;
        }
        return $bucket . '.' . $endpoint;
    }

    /**
     * 构建 S3 资源 URI（含 path-style bucket）
     */
    protected function buildS3Uri($bucket, $remotePath)
    {
        if ($this->isS3PathStyle()) {
            return '/' . $bucket . '/' . $remotePath;
        }
        return '/' . $remotePath;
    }

    /**
     * AWS Signature V4 签名
     */
    protected function signS3Request($method, $uri, $queryString, $headers, $payload, $region, $dateStamp, $amzDate)
    {
        $accessKeySecret = $this->config['accessKeySecret'] ?? '';

        // Canonical Request
        $canonicalUri = $uri;
        $canonicalQueryString = $queryString;
        $canonicalHeaders = '';
        $signedHeaderKeys = array_keys($headers);
        sort($signedHeaderKeys);
        foreach ($signedHeaderKeys as $key) {
            $canonicalHeaders .= $key . ':' . trim($headers[$key]) . "\n";
        }
        $signedHeaders = implode(';', $signedHeaderKeys);
        $payloadHash = hash('sha256', $payload);

        $canonicalRequest = $method . "\n" . $canonicalUri . "\n" . $canonicalQueryString . "\n" . $canonicalHeaders . "\n" . $signedHeaders . "\n" . $payloadHash;

        // String to Sign
        $scope = "{$dateStamp}/{$region}/s3/aws4_request";
        $stringToSign = "AWS4-HMAC-SHA256\n{$amzDate}\n{$scope}\n" . hash('sha256', $canonicalRequest);

        // Signing Key
        $kDate = hash_hmac('sha256', $dateStamp, 'AWS4' . $accessKeySecret, true);
        $kRegion = hash_hmac('sha256', $region, $kDate, true);
        $kService = hash_hmac('sha256', 's3', $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

        return hash_hmac('sha256', $stringToSign, $kSigning);
    }

    /**
     * 上传到阿里云OSS
     */
    protected function uploadToAliyun($localPath, $remotePath)
    {
        $accessKeyId = $this->config['accessKeyId'] ?? '';
        $accessKeySecret = $this->config['accessKeySecret'] ?? '';
        $endpoint = $this->config['endpoint'] ?? '';
        $bucket = $this->config['bucket'] ?? '';

        if (empty($accessKeyId) || empty($accessKeySecret) || empty($endpoint) || empty($bucket)) {
            return false;
        }

        // 构建签名
        $date = gmdate('D, d M Y H:i:s T');
        $contentMd5 = base64_encode(md5_file($localPath));
        $contentType = mime_content_type($localPath);
        $signString = "PUT\n{$contentMd5}\n{$contentType}\n{$date}\n/{$bucket}/{$remotePath}";

        $signature = base64_encode(hash_hmac('sha1', $signString, $accessKeySecret, true));

        $url = "https://{$bucket}.{$endpoint}/{$remotePath}";
        $fileContent = file_get_contents($localPath);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Date: {$date}",
            "Content-Type: {$contentType}",
            "Content-Md5: {$contentMd5}",
            "Authorization: OSS {$accessKeyId}:{$signature}"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    /**
     * 从阿里云OSS删除
     */
    protected function deleteFromAliyun($remotePath)
    {
        $accessKeyId = $this->config['accessKeyId'] ?? '';
        $accessKeySecret = $this->config['accessKeySecret'] ?? '';
        $endpoint = $this->config['endpoint'] ?? '';
        $bucket = $this->config['bucket'] ?? '';

        if (empty($accessKeyId) || empty($accessKeySecret) || empty($endpoint) || empty($bucket)) {
            return false;
        }

        $date = gmdate('D, d M Y H:i:s T');
        $signString = "DELETE\n\n\n{$date}\n/{$bucket}/{$remotePath}";

        $signature = base64_encode(hash_hmac('sha1', $signString, $accessKeySecret, true));

        $url = "https://{$bucket}.{$endpoint}/{$remotePath}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Date: {$date}",
            "Authorization: OSS {$accessKeyId}:{$signature}"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }
}
