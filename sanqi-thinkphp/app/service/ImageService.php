<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\service;

/**
 * 图片处理服务：生成列表缩略图、WebP 转换和失败日志。
 */
class ImageService
{
    const DEFAULT_THUMB_MAX_WIDTH = 720;
    const DEFAULT_THUMB_MAX_HEIGHT = 720;
    const DEFAULT_THUMB_QUALITY = 82;
    const DEFAULT_WEBP_QUALITY = 80;
    const DEFAULT_MAX_PIXELS = 25000000;

    protected static function config(string $key, $default = null)
    {
        try {
            return config('app.image.' . $key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function thumbnailUrl($url)
    {
        $url = trim((string)$url);
        if ($url === '' || strpos($url, '/upload/') !== 0) {
            return $url;
        }

        $sourcePath = self::publicPathFromUrl($url);
        if (!$sourcePath || !is_file($sourcePath)) {
            return $url;
        }

        $thumbPath = self::thumbPathForSource($sourcePath);
        $thumbUrl = self::urlFromPublicPath($thumbPath);

        if (is_file($thumbPath)) {
            return $thumbUrl ?: $url;
        }

        return $url;
    }

    public static function createThumbnailForUrl($url)
    {
        $sourcePath = self::publicPathFromUrl($url);
        if (!$sourcePath || !is_file($sourcePath)) {
            return false;
        }

        return self::createThumbnail($sourcePath, self::thumbPathForSource($sourcePath));
    }

    public static function dominantColorForUrl($url): string
    {
        $sourcePath = self::publicPathFromUrl($url);
        if (!$sourcePath || !is_file($sourcePath)) {
            return '';
        }

        return self::dominantColor($sourcePath);
    }

    public static function dominantColor($sourcePath): string
    {
        $info = @getimagesize($sourcePath);
        if (!$info || empty($info['mime']) || !self::withinPixelLimit($info)) {
            return '';
        }

        $image = self::createImageResource($sourcePath, $info['mime']);
        if (!$image) {
            return '';
        }

        $width = imagesx($image);
        $height = imagesy($image);
        if ($width <= 0 || $height <= 0) {
            imagedestroy($image);
            return '';
        }

        $stepX = max(1, (int)floor($width / 12));
        $stepY = max(1, (int)floor($height / 12));
        $r = 0;
        $g = 0;
        $b = 0;
        $count = 0;

        for ($y = (int)floor($stepY / 2); $y < $height; $y += $stepY) {
            for ($x = (int)floor($stepX / 2); $x < $width; $x += $stepX) {
                $rgb = imagecolorat($image, min($x, $width - 1), min($y, $height - 1));
                $colors = imagecolorsforindex($image, $rgb);
                $alpha = (int)($colors['alpha'] ?? 0);
                if ($alpha > 100) {
                    continue;
                }
                $r += (int)$colors['red'];
                $g += (int)$colors['green'];
                $b += (int)$colors['blue'];
                $count++;
            }
        }

        imagedestroy($image);
        if ($count <= 0) {
            return '';
        }

        return sprintf('#%02x%02x%02x', (int)round($r / $count), (int)round($g / $count), (int)round($b / $count));
    }

    public static function createThumbnail($sourcePath, $destPath, $maxWidth = null, $maxHeight = null)
    {
        $maxWidth = $maxWidth ?? (int) self::config('thumb_max_width', self::DEFAULT_THUMB_MAX_WIDTH);
        $maxHeight = $maxHeight ?? (int) self::config('thumb_max_height', self::DEFAULT_THUMB_MAX_HEIGHT);

        $info = @getimagesize($sourcePath);
        if (!$info || empty($info[0]) || empty($info[1]) || empty($info['mime'])) {
            self::logImageFailure('thumbnail_failed', $sourcePath, $destPath, 'invalid_image');
            return false;
        }

        if (!self::withinPixelLimit($info)) {
            self::logImageFailure('thumbnail_failed', $sourcePath, $destPath, 'pixel_limit_exceeded', self::pixelLimitContext($info));
            return false;
        }

        $source = self::createImageResource($sourcePath, $info['mime']);
        if (!$source) {
            self::logImageFailure('thumbnail_failed', $sourcePath, $destPath, 'create_source_failed');
            return false;
        }

        $width = (int)$info[0];
        $height = (int)$info[1];
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
        $thumbWidth = max(1, (int)round($width * $ratio));
        $thumbHeight = max(1, (int)round($height * $ratio));

        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        if (!$thumb) {
            imagedestroy($source);
            self::logImageFailure('thumbnail_failed', $sourcePath, $destPath, 'create_canvas_failed');
            return false;
        }

        self::preserveTransparency($thumb, $info['mime']);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        $dir = dirname($destPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $result = self::saveImageResource($thumb, $destPath, $info['mime']);
        imagedestroy($thumb);
        imagedestroy($source);

        if (!$result) {
            self::logImageFailure('thumbnail_failed', $sourcePath, $destPath, 'save_failed');
        }

        return $result;
    }

    public static function sanitizeImageFile($path)
    {
        $info = @getimagesize($path);
        if (!$info || empty($info['mime'])) {
            self::logImageFailure('sanitize_failed', $path, $path, 'invalid_image');
            return false;
        }

        if (!self::withinPixelLimit($info)) {
            self::logImageFailure('sanitize_failed', $path, $path, 'pixel_limit_exceeded', self::pixelLimitContext($info));
            return false;
        }

        $image = self::createImageResource($path, $info['mime']);
        if (!$image) {
            self::logImageFailure('sanitize_failed', $path, $path, 'create_source_failed', ['mime' => $info['mime']]);
            return false;
        }

        $tempPath = $path . '.safe.' . bin2hex(random_bytes(4));
        self::enableAlphaSave($image, $info['mime']);
        $result = self::saveImageResource($image, $tempPath, $info['mime']);
        imagedestroy($image);

        if (!$result || !is_file($tempPath) || filesize($tempPath) <= 0) {
            @unlink($tempPath);
            self::logImageFailure('sanitize_failed', $path, $tempPath, 'save_failed', ['mime' => $info['mime']]);
            return false;
        }

        if (!@rename($tempPath, $path)) {
            @unlink($tempPath);
            self::logImageFailure('sanitize_failed', $path, $tempPath, 'replace_failed', ['mime' => $info['mime']]);
            return false;
        }

        return true;
    }

    public static function convertToWebp($sourcePath, $destPath, $quality = null)
    {
        $quality = $quality ?? (int) self::config('webp_quality', self::DEFAULT_WEBP_QUALITY);

        if (!function_exists('imagewebp')) {
            self::logWebpFailure($sourcePath, $destPath, 'imagewebp_missing');
            return false;
        }

        $info = @getimagesize($sourcePath);
        if (!$info || empty($info['mime'])) {
            self::logWebpFailure($sourcePath, $destPath, 'invalid_image');
            return false;
        }

        if ($info['mime'] === 'image/webp') {
            return false;
        }

        if (!self::withinPixelLimit($info)) {
            self::logWebpFailure($sourcePath, $destPath, 'pixel_limit_exceeded', self::pixelLimitContext($info));
            return false;
        }

        $image = self::createImageResource($sourcePath, $info['mime']);
        if (!$image) {
            self::logWebpFailure($sourcePath, $destPath, 'create_source_failed', ['mime' => $info['mime']]);
            return false;
        }

        $dir = dirname($destPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $result = @imagewebp($image, $destPath, $quality);
        imagedestroy($image);

        if (!$result || !is_file($destPath) || filesize($destPath) <= 0) {
            self::logWebpFailure($sourcePath, $destPath, 'save_failed', ['mime' => $info['mime']]);
            @unlink($destPath);
            return false;
        }

        return true;
    }

    public static function logWebpFailure($sourcePath, $destPath, $reason, array $context = [])
    {
        self::logImageFailure('webp_convert_failed', $sourcePath, $destPath, $reason, $context);
    }

    public static function withinPixelLimit($imageInfo, $maxPixels = null)
    {
        $pixels = self::pixelCount($imageInfo);
        if ($pixels <= 0) {
            return false;
        }

        return $pixels <= self::maxPixels($maxPixels);
    }

    public static function pixelCount($imageInfo)
    {
        if (!is_array($imageInfo) || empty($imageInfo[0]) || empty($imageInfo[1])) {
            return 0;
        }

        $width = (int)$imageInfo[0];
        $height = (int)$imageInfo[1];
        if ($width <= 0 || $height <= 0) {
            return 0;
        }

        return $width * $height;
    }

    public static function maxPixels($maxPixels = null)
    {
        if ($maxPixels === null) {
            try {
                $maxPixels = config('app.upload.max_image_pixels', self::DEFAULT_MAX_PIXELS);
            } catch (\Throwable $e) {
                $maxPixels = self::DEFAULT_MAX_PIXELS;
            }
        }

        $maxPixels = (int)$maxPixels;
        return $maxPixels > 0 ? $maxPixels : self::DEFAULT_MAX_PIXELS;
    }

    protected static function pixelLimitContext($imageInfo)
    {
        $width = is_array($imageInfo) && isset($imageInfo[0]) ? (int)$imageInfo[0] : 0;
        $height = is_array($imageInfo) && isset($imageInfo[1]) ? (int)$imageInfo[1] : 0;

        return [
            'width' => $width,
            'height' => $height,
            'pixels' => self::pixelCount($imageInfo),
            'max_pixels' => self::maxPixels(),
        ];
    }

    protected static function createImageResource($path, $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : false;
            case 'image/png':
                return function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : false;
            case 'image/gif':
                return function_exists('imagecreatefromgif') ? @imagecreatefromgif($path) : false;
            case 'image/webp':
                return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false;
            default:
                return false;
        }
    }

    protected static function saveImageResource($image, $destPath, $mime)
    {
        $quality = (int) self::config('thumb_quality', self::DEFAULT_THUMB_QUALITY);

        switch ($mime) {
            case 'image/jpeg':
                return function_exists('imagejpeg') && @imagejpeg($image, $destPath, $quality);
            case 'image/png':
                return function_exists('imagepng') && @imagepng($image, $destPath, 6);
            case 'image/gif':
                return function_exists('imagegif') && @imagegif($image, $destPath);
            case 'image/webp':
                return function_exists('imagewebp') && @imagewebp($image, $destPath, $quality);
            default:
                return false;
        }
    }

    protected static function preserveTransparency($image, $mime)
    {
        if (in_array($mime, ['image/png', 'image/gif', 'image/webp'], true)) {
            self::enableAlphaSave($image, $mime);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $transparent);
        }
    }

    protected static function enableAlphaSave($image, $mime)
    {
        if (in_array($mime, ['image/png', 'image/gif', 'image/webp'], true)) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }
    }

    protected static function thumbPathForSource($sourcePath)
    {
        return dirname($sourcePath) . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . basename($sourcePath);
    }

    protected static function publicPathFromUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (!$path || strpos($path, '/upload/') !== 0) {
            return null;
        }

        $relative = ltrim(rawurldecode($path), '/');
        $publicPath = app()->getRootPath() . 'public/';
        $fullPath = $publicPath . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);
        $uploadRoot = realpath($publicPath . 'upload');
        $parent = realpath(dirname($fullPath));

        if (!$uploadRoot || !$parent || strpos($parent, $uploadRoot) !== 0) {
            return null;
        }

        return $fullPath;
    }

    protected static function urlFromPublicPath($path)
    {
        $publicPath = app()->getRootPath() . 'public/';
        $publicRoot = rtrim(str_replace('\\', '/', realpath($publicPath) ?: $publicPath), '/');
        $fullPath = str_replace('\\', '/', $path);
        if (strpos($fullPath, $publicRoot . '/') !== 0) {
            return null;
        }

        $relativePath = ltrim(substr($fullPath, strlen($publicRoot)), '/');
        $parts = array_map('rawurlencode', explode('/', $relativePath));
        return '/' . implode('/', $parts);
    }

    protected static function logImageFailure($event, $sourcePath, $destPath, $reason, array $context = [])
    {
        $logDir = app()->getRuntimePath() . 'log/';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $payload = [
            'time' => date('Y-m-d H:i:s'),
            'event' => $event,
            'reason' => $reason,
            'source' => $sourcePath,
            'dest' => $destPath,
            'context' => $context,
        ];

        @file_put_contents(
            $logDir . 'image_' . date('Ymd') . '.log',
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
