<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\controller\api;

use app\BaseController;
use app\service\ExternalRequestGuard;

class Douyin extends BaseController
{
    private $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1';
    private $allowedHosts = ['v.douyin.com', 'www.douyin.com', 'm.douyin.com', 'www.iesdouyin.com', 'iesdouyin.com'];

    private function parseLog($message, $context = [])
    {
        $dir = runtime_path() . 'log/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $line = date('Y-m-d H:i:s') . ' ' . $message;
        if (!empty($context)) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        @file_put_contents($dir . 'douyin_parse_' . date('Y-m-d') . '.log', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function curlCaInfo(): string
    {
        static $caInfo = null;
        if ($caInfo !== null) {
            return $caInfo;
        }

        $candidates = [
            (string)config('app.curl_cainfo', ''),
            (string)env('CURL_CAINFO', ''),
            (string)ini_get('curl.cainfo'),
            (string)ini_get('openssl.cafile'),
            root_path() . 'cacert.pem',
            public_path() . 'cacert.pem',
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim(str_replace('\\', '/', (string)$candidate));
            if ($candidate !== '' && $this->openBaseDirAllows($candidate) && is_file($candidate)) {
                $caInfo = $candidate;
                return $caInfo;
            }
        }

        $caInfo = '';
        return $caInfo;
    }

    private function openBaseDirAllows(string $path): bool
    {
        $openBaseDir = trim((string)ini_get('open_basedir'));
        if ($openBaseDir === '') {
            return true;
        }

        $path = str_replace('\\', '/', $path);
        foreach (explode(PATH_SEPARATOR, $openBaseDir) as $baseDir) {
            $baseDir = trim((string)$baseDir);
            if ($baseDir === '') {
                continue;
            }
            if ($baseDir === '.') {
                $baseDir = root_path();
            }
            $baseDir = rtrim(str_replace('\\', '/', $baseDir), '/');
            if ($path === $baseDir || strpos($path, $baseDir . '/') === 0) {
                return true;
            }
        }

        return false;
    }

    public function parse()
    {
        try {
            ExternalRequestGuard::assertAllowed('douyin_parse', $this->request->ip());
        } catch (\RuntimeException $e) {
            return json(['code' => 429, 'msg' => $e->getMessage()], 429);
        }

        $url = trim((string)$this->request->post('url', ''));
        if (empty($url)) {
            return json(['code' => 400, 'msg' => '请提供抖音链接']);
        }

        // 提取抖音链接
        $douyinUrl = $this->extractDouyinUrl($url);
        if (!$douyinUrl) {
            return json(['code' => 400, 'msg' => '无效的抖音链接格式']);
        }
        if (!$this->isAllowedDouyinUrl($douyinUrl)) {
            return json(['code' => 400, 'msg' => '只允许解析抖音链接']);
        }

        $videoId = null;
        $pageHtml = '';

        // 如果是纯数字，直接作为视频ID
        if (is_numeric($douyinUrl)) {
            $videoId = $douyinUrl;
        } else {
            // 获取重定向后的URL
            $redirectedUrl = $this->getRedirectedUrl($douyinUrl);
            // 从重定向URL中提取视频ID
            if ($redirectedUrl) {
                $videoId = $this->extractVideoId($redirectedUrl);
            }
            // 如果URL中无法提取，尝试从重定向页面HTML中提取
            if (!$videoId && $redirectedUrl) {
                $pageHtml = $this->httpGet($redirectedUrl, [
                    'User-Agent: ' . $this->ua,
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                    'Referer: https://www.douyin.com/',
                ]);
                $videoId = $this->extractVideoIdFromHtml($pageHtml);
            }
            // 最后尝试直接从短链页面提取
            if (!$videoId) {
                if (empty($pageHtml)) {
                    $pageHtml = $this->httpGet($douyinUrl, [
                        'User-Agent: ' . $this->ua,
                        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                    ]);
                }
                $videoId = $this->extractVideoIdFromHtml($pageHtml);
            }
        }

        if (!$videoId) {
            $this->parseLog('video_id_missing', [
                'input' => mb_substr($url, 0, 300, 'UTF-8'),
                'extracted_url' => $douyinUrl,
                'redirected_url' => $redirectedUrl ?? '',
                'html_length' => strlen((string)$pageHtml),
            ]);
            return json(['code' => 500, 'msg' => '无法提取视频ID，请检查链接是否有效']);
        }

        // 获取视频信息页面
        $infoUrl = "https://www.iesdouyin.com/share/video/{$videoId}/";
        $html = $this->httpGet($infoUrl, [
            'User-Agent: ' . $this->ua,
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
            'Referer: https://www.douyin.com/',
        ]);

        if (empty($html)) {
            // 如果 iesdouyin 失败，使用之前获取的页面HTML
            $html = $pageHtml;
        }

        $title = '';
        $cover = '';
        $videoUrl = "https://aweme.snssdk.com/aweme/v1/play/?video_id={$videoId}&ratio=720p&line=0";

        if (!empty($html)) {
            // 方法1: 解析 window._ROUTER_DATA
            if (preg_match('/window\._ROUTER_DATA\s*=\s*(\{.*?\});?\s*</s', $html, $matches)) {
                $jsonData = json_decode($matches[1], true);
                if ($jsonData && isset($jsonData['loaderData'])) {
                    foreach ($jsonData['loaderData'] as $value) {
                        if (isset($value['videoInfoRes']['item_list'][0])) {
                            $item = $value['videoInfoRes']['item_list'][0];
                            $title = $item['desc'] ?? '';
                            if (isset($item['video']['play_addr']['uri'])) {
                                $videoUrl = 'https://aweme.snssdk.com/aweme/v1/play/?video_id=' . $item['video']['play_addr']['uri'] . '&ratio=720p&line=0';
                            }
                            if (isset($item['video']['cover']['url_list'][0])) {
                                $cover = $item['video']['cover']['url_list'][0];
                            }
                            break;
                        }
                    }
                }
            }

            // 方法2: 从meta标签获取
            if (empty($title)) {
                if (preg_match('/<meta\s+property="og:title"\s+content="([^"]+)"/i', $html, $m)) {
                    $title = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
                } elseif (preg_match('/<title>([^<]+)<\/title>/i', $html, $m)) {
                    $title = preg_replace('/\s*-\s*抖音.*$/i', '', trim($m[1]));
                }
            }
            if (empty($cover)) {
                if (preg_match('/<meta\s+property="og:image"\s+content="([^"]+)"/i', $html, $m)) {
                    $cover = $m[1];
                }
            }

            // 方法3: JSON字段匹配
            if (empty($title) && preg_match('/"desc"\s*:\s*"([^"]+)"/', $html, $m)) {
                $title = $m[1];
            }
            if (empty($cover) && preg_match('/"cover"\s*:\s*\{[^}]*"url_list"\s*:\s*\["([^"]+)"/', $html, $m)) {
                $cover = $m[1];
            }
        }

        return json([
            'code' => 0,
            'msg'  => 'ok',
            'data' => [
                'video' => $videoUrl,
                'cover' => $cover,
                'title' => $title,
            ]
        ]);
    }

    private function extractDouyinUrl($text)
    {
        $text = trim((string)$text);
        $patterns = [
            '/https?:\/\/v\.douyin\.com\/[A-Za-z0-9_-]+\/?(?:\?[^\s]*)?/i',
            '/https?:\/\/www\.douyin\.com\/video\/\d+(?:\?[^\s]*)?/i',
            '/https?:\/\/m\.douyin\.com\/video\/\d+(?:\?[^\s]*)?/i',
            '/https?:\/\/www\.iesdouyin\.com\/share\/video\/\d+\/?(?:\?[^\s]*)?/i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->cleanExtractedUrl($matches[0]);
            }
        }
        if (preg_match('/https?:\/\/[^\s]+douyin[^\s]*/i', $text, $matches)) {
            return $this->cleanExtractedUrl($matches[0]);
        }
        if (preg_match('/^\d+$/', trim($text))) {
            return trim($text);
        }
        return false;
    }

    private function cleanExtractedUrl(string $url): string
    {
        $url = trim($url);
        $url = preg_replace('/[，。、“”‘’）)\]}]+$/u', '', $url);
        return $url;
    }

    private function isAllowedDouyinUrl($url)
    {
        if (preg_match('/^\d+$/', (string)$url)) {
            return true;
        }

        $host = strtolower((string)(parse_url((string)$url, PHP_URL_HOST) ?: ''));
        return in_array($host, $this->allowedHosts, true);
    }

    private function extractVideoId($url)
    {
        if (preg_match('/^\d+$/', $url)) {
            return $url;
        }
        $patterns = [
            '/\/video\/(\d+)/',
            '/\/share\/video\/(\d+)/',
            '/(\d{19})/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        return false;
    }

    private function extractVideoIdFromHtml($html)
    {
        if (empty($html)) return false;
        $patterns = [
            '/"aweme_id"\s*:\s*"(\d+)"/',
            '/"awemeId"\s*:\s*"(\d+)"/',
            '/"itemId"\s*:\s*"(\d+)"/',
            '/"video_id"\s*:\s*"(\d+)"/',
            '/aweme[_-]?id[=:]\s*["\']?(\d{15,25})/',
            '/video[_-]?id[=:]\s*["\']?(\d{15,25})/',
            '/modal_id[=:]\s*["\']?(\d{15,25})/',
            '/\/video\/(\d{15,25})/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                return $m[1];
            }
        }
        return false;
    }

    private function getRedirectedUrl($url)
    {
        $maxRedirects = 5;
        $count = 0;
        $current = $url;
        while ($count < $maxRedirects) {
            if (!$this->isAllowedDouyinUrl($current)) {
                return '';
            }
            $result = $this->requestRedirect($current, $count === 0);
            $response = $result['response'];
            $info = $result['info'];

            if (isset($info['redirect_url']) && !empty($info['redirect_url'])) {
                $location = $this->normalizeRedirectUrl((string)$info['redirect_url'], $current);
                if (!$this->isAllowedDouyinUrl($location)) {
                    return '';
                }
                $current = $location;
                $count++;
                continue;
            }
            if (preg_match('/Location:\s*(.+?)(?:\r\n|\n)/i', $response, $m)) {
                $location = $this->normalizeRedirectUrl(trim($m[1]), $current);
                if (!$this->isAllowedDouyinUrl($location)) {
                    return '';
                }
                $current = $location;
                $count++;
                continue;
            }
            break;
        }
        return $current;
    }

    private function normalizeRedirectUrl(string $location, string $current): string
    {
        $location = trim($location);
        if ($location === '') {
            return '';
        }
        if (preg_match('/^https?:\/\//i', $location)) {
            return $location;
        }

        $parsed = parse_url($current);
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        if ($host === '') {
            return '';
        }

        if (strpos($location, '//') === 0) {
            return $scheme . ':' . $location;
        }
        if (strpos($location, '/') === 0) {
            return $scheme . '://' . $host . $location;
        }

        $basePath = isset($parsed['path']) ? rtrim(dirname($parsed['path']), '/\\') : '';
        return $scheme . '://' . $host . ($basePath ? $basePath . '/' : '/') . $location;
    }

    private function requestRedirect(string $url, bool $allowGetFallback): array
    {
        $result = $this->curlRedirectRequest($url, true);
        if ($allowGetFallback && empty($result['location']) && (int)($result['info']['http_code'] ?? 0) < 300) {
            $fallback = $this->curlRedirectRequest($url, false);
            if (!empty($fallback['location']) || !empty($fallback['response'])) {
                return $fallback;
            }
        }
        return $result;
    }

    private function curlRedirectRequest(string $url, bool $head): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => $head,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => [
                'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                'Referer: https://www.douyin.com/',
            ],
        ]);
        if ($caInfo = $this->curlCaInfo()) {
            curl_setopt($ch, CURLOPT_CAINFO, $caInfo);
        }
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $this->parseLog('redirect_failed', [
                'url' => $url,
                'method' => $head ? 'HEAD' : 'GET',
                'error' => $error,
                'http_code' => $info['http_code'] ?? 0,
                'ca' => $this->curlCaInfo(),
            ]);
            $response = '';
        }

        $location = '';
        if (isset($info['redirect_url']) && !empty($info['redirect_url'])) {
            $location = $info['redirect_url'];
        } elseif (preg_match('/Location:\s*(.+?)(?:\r\n|\n)/i', (string)$response, $m)) {
            $location = trim($m[1]);
        }

        if ($location !== '') {
            $info['redirect_url'] = $location;
        }

        return [
            'response' => (string)$response,
            'info' => $info,
            'location' => $location,
        ];
    }

    private function httpGet($url, $headers = [])
    {
        if (!$this->isAllowedDouyinUrl($url)) {
            return '';
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        if ($caInfo = $this->curlCaInfo()) {
            curl_setopt($ch, CURLOPT_CAINFO, $caInfo);
        }
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($result === false) {
            $this->parseLog('http_get_failed', [
                'url' => $url,
                'error' => curl_error($ch),
                'http_code' => $code,
                'ca' => $this->curlCaInfo(),
            ]);
        }
        curl_close($ch);
        return ($code === 200 && $result !== false) ? $result : '';
    }
}
