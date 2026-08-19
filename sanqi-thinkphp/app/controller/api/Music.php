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
use app\service\ExternalRequestGuard;

/**
 * 音乐API控制器
 */
class Music extends Base
{
    private function guardExternalRequest(string $scope)
    {
        try {
            ExternalRequestGuard::assertAllowed($scope, request()->ip());
            return null;
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), '429');
        }
    }

    private function normalizeMusicUrl($url)
    {
        $url = preg_replace("/[\s\r\n]+/", "", (string)$url);
        if (is_numeric($url)) {
            return "/api/music/proxy?id=" . $url;
        }
        if (preg_match('/music\.163\.com\/song\/media\/outer\/url\?id=\d+/i', $url)) {
            return $url;
        }
        if (preg_match('/music\.163\.com\/.*[?&]id=(\d+)/i', $url, $matches)) {
            return "/api/music/proxy?id=" . $matches[1];
        }
        return $url;
    }

    private function extractProxyMusicId($url)
    {
        if (preg_match('/\/api\/music\/proxy\?id=(\d+)/i', (string)$url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function isConfiguredMusicPlayable($url)
    {
        $id = $this->extractProxyMusicId($url);
        if (!$id) {
            return true;
        }

        return $this->findNeteaseAudioUrl($id) !== null;
    }

    public function random()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }
        if ($limited = $this->guardExternalRequest('music_random')) {
            return $limited;
        }

        $siteConfig = $this->getSiteConfig();
        $music = $siteConfig['music'] ?? '';

        if (empty($music)) {
            return $this->error('暂无音乐');
        }

        $arr = array_values(array_filter(explode("\n", $music)));
        if (empty($arr)) {
            return $this->error('暂无音乐');
        }

        shuffle($arr);
        foreach ($arr as $musicLine) {
            $parts = explode("|", $musicLine);
            if (count($parts) < 2) {
                continue;
            }

            $musicName = $parts[0];
            $musicUrl = $this->normalizeMusicUrl($parts[1]);
            if (!$this->isConfiguredMusicPlayable($musicUrl)) {
                continue;
            }

            return $this->success('获取成功', [
                'name' => $musicName,
                'url' => $musicUrl,
                'mum' => $musicName,
                'muurl' => $musicUrl
            ]);
        }

        return $this->error('暂无可播放音乐');
    }

    public function netease()
    {
        if (!$this->request->isPost()) {
            return $this->error('请求方式错误');
        }
        if ($limited = $this->guardExternalRequest('music_netease')) {
            return $limited;
        }

        $id = $this->request->post('id', '');

        if (empty($id) || !is_numeric($id)) {
            return $this->error('参数错误');
        }

        $url = "https://music.163.com/api/song/detail?id={$id}&ids=[{$id}]";
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'header' => "User-Agent: Mozilla/5.0\r\nReferer: https://music.163.com/\r\n"
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if (!$response) {
            return $this->error('获取失败');
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['songs'][0])) {
            return $this->error('获取失败');
        }

        $song = $data['songs'][0];
        $songName = $song['name'] ?? '';
        $artist = isset($song['artists'][0]) ? $song['artists'][0]['name'] : '';
        $coverUrl = isset($song['album']['picUrl']) ? $song['album']['picUrl'] : '';
        $musicUrl = "/api/music/proxy?id=" . $id;

        return $this->success('获取成功', [
            'name' => $songName,
            'artist' => $artist,
            'cover' => $coverUrl,
            'url' => $musicUrl,
            'data' => $musicUrl . '|' . $songName . '|' . $artist . '|' . $coverUrl
        ]);
    }

    private function proxyHeaders()
    {
        return [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Referer: https://music.163.com/',
            'Accept: */*',
        ];
    }

    private function domainAllowed(string $host, string $domain): bool
    {
        $host = strtolower($host);
        $domain = strtolower($domain);
        if ($host === $domain) {
            return true;
        }
        $suffix = '.' . $domain;
        return strlen($host) > strlen($suffix) && substr($host, -strlen($suffix)) === $suffix;
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
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/cert.pem',
            '/etc/ssl/ca-bundle.pem',
            '/usr/local/share/certs/ca-root-nss.crt',
            'C:/Program Files/Git/mingw64/etc/ssl/certs/ca-bundle.crt',
            'C:/Program Files/Git/usr/ssl/certs/ca-bundle.crt',
            'C:/Program Files/PhpWebStudy-Data/server/CA/cacert.pem',
            'C:/Program Files/Python313/Lib/site-packages/certifi/cacert.pem',
            'D:/phpEnv/tools/phpMyAdmin/libraries/certs/cacert.pem',
            'D:/phpEnv/initFiles/tools/phpMyAdmin/libraries/certs/cacert.pem',
        ];

        if (DIRECTORY_SEPARATOR === '\\') {
            foreach ([
                'D:/BtSoft/phpmyadmin/*/libraries/certs/cacert.pem',
                'D:/phpEnv/*/phpMyAdmin/libraries/certs/cacert.pem',
            ] as $pattern) {
                $matches = glob($pattern) ?: [];
                foreach ($matches as $match) {
                    $candidates[] = $match;
                }
            }
        }

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
        if ($path === '') {
            return false;
        }

        foreach (explode(PATH_SEPARATOR, $openBaseDir) as $baseDir) {
            $baseDir = trim((string)$baseDir);
            if ($baseDir === '') {
                continue;
            }
            if ($baseDir === '.') {
                $baseDir = root_path();
            }

            $baseDir = rtrim(str_replace('\\', '/', $baseDir), '/');
            if ($baseDir === '') {
                continue;
            }

            if ($path === $baseDir || strpos($path, $baseDir . '/') === 0) {
                return true;
            }
        }

        return false;
    }

    private function proxyCurl($url, $timeout = 20, $maxBytes = 0, $allowedDomains = [], $headers = null)
    {
        $maxRedirects = 5;
        $currentUrl = $url;

        for ($i = 0; $i <= $maxRedirects; $i++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $currentUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_CONNECTTIMEOUT => 8,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_HTTPHEADER => $headers ?: $this->proxyHeaders(),
                CURLOPT_HEADER => true,
            ]);
            if ($caInfo = $this->curlCaInfo()) {
                curl_setopt($ch, CURLOPT_CAINFO, $caInfo);
            }

            $response = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($response === false) {
                return [
                    'body' => false, 'http_code' => 0,
                    'content_type' => '', 'effective_url' => $currentUrl, 'error' => $error,
                ];
            }

            // 3xx: 手动跟随重定向并校验域名
            if ($httpCode >= 300 && $httpCode < 400) {
                if (preg_match('/^Location:\s*(.+)$/mi', $response, $m)) {
                    $nextUrl = trim($m[1]);
                    // 相对路径转绝对路径
                    if (!preg_match('/^https?:\/\//i', $nextUrl)) {
                        $parts = parse_url($currentUrl);
                        $nextUrl = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '') . $nextUrl;
                    }
                    if (!empty($allowedDomains)) {
                        $host = strtolower(parse_url($nextUrl, PHP_URL_HOST) ?? '');
                        $allowed = false;
                        foreach ($allowedDomains as $d) {
                            if ($this->domainAllowed($host, $d)) {
                                $allowed = true;
                                break;
                            }
                        }
                        if (!$allowed) {
                            return [
                                'body' => false, 'http_code' => $httpCode,
                                'content_type' => '', 'effective_url' => $nextUrl, 'error' => 'redirect_domain_blocked',
                            ];
                        }
                    }
                    $currentUrl = $nextUrl;
                    continue;
                }
            }

            // 2xx: 用 double-CRLF 分割 header 和 body
            $bodyStart = strpos($response, "\r\n\r\n");
            $body = ($bodyStart !== false) ? substr($response, $bodyStart + 4) : $response;

            if ($maxBytes > 0 && strlen($body) > $maxBytes) {
                $body = substr($body, 0, $maxBytes);
            }

            return [
                'body' => $body,
                'http_code' => $httpCode,
                'content_type' => $contentType,
                'effective_url' => $currentUrl,
                'error' => '',
            ];
        }

        return [
            'body' => false, 'http_code' => 0,
            'content_type' => '', 'effective_url' => $currentUrl, 'error' => 'too_many_redirects',
        ];
    }

    private function proxyLog($id, $message, $context = [])
    {
        $dir = runtime_path() . 'log/';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $line = date('Y-m-d H:i:s') . ' id=' . $id . ' ' . $message;
        if (!empty($context)) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        @file_put_contents($dir . 'music_proxy_' . date('Y-m-d') . '.log', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function isAudioResponse($response)
    {
        $body = $response['body'] ?? '';
        $httpCode = (int)($response['http_code'] ?? 0);
        $contentType = strtolower((string)($response['content_type'] ?? ''));

        if (!in_array($httpCode, [200, 206], true) || $body === false || $body === '') {
            return false;
        }

        if (strpos($contentType, 'audio/') !== false || strpos($contentType, 'application/octet-stream') !== false) {
            return true;
        }

        return strlen($body) > 1024
            && strpos($contentType, 'text/html') === false
            && strpos($contentType, 'application/json') === false;
    }

    private function findNeteaseAudioUrl($id)
    {
        $apiUrls = [
            "https://music.163.com/api/song/enhance/player/url?ids=[{$id}]&br=320000",
            "https://music.163.com/api/song/enhance/player/url?ids=[{$id}]&br=128000",
        ];

        foreach ($apiUrls as $apiUrl) {
            $response = $this->proxyCurl($apiUrl, 15, 0, ['music.163.com']);
            if (empty($response['body'])) {
                $this->proxyLog($id, 'api_empty', [
                    'url' => $apiUrl,
                    'http_code' => $response['http_code'],
                    'error' => $response['error'],
                ]);
                continue;
            }

            $data = json_decode($response['body'], true);
            if (!$data || empty($data['data']) || !is_array($data['data'])) {
                $this->proxyLog($id, 'api_invalid', [
                    'url' => $apiUrl,
                    'http_code' => $response['http_code'],
                    'body' => substr((string)$response['body'], 0, 200),
                ]);
                continue;
            }

            foreach ($data['data'] as $song) {
                if (!empty($song['url'])) {
                    return $song['url'];
                }
            }

            $this->proxyLog($id, 'api_no_url', [
                'url' => $apiUrl,
                'data' => $data['data'],
            ]);
        }

        return null;
    }

    public function proxy()
    {
        if ($limited = $this->guardExternalRequest('music_proxy')) {
            return $limited;
        }

        $id = $this->request->get('id', '');
        if (empty($id) || !is_numeric((string)$id)) {
            abort(400, 'Bad Request');
        }

        $audioUrls = [];
        $apiAudioUrl = $this->findNeteaseAudioUrl((string)$id);
        if ($apiAudioUrl) {
            $audioUrls[] = $apiAudioUrl;
        }
        $audioUrls[] = "https://music.163.com/song/media/outer/url?id={$id}.mp3";
        $audioUrls[] = "http://music.163.com/song/media/outer/url?id={$id}.mp3";

        foreach (array_unique($audioUrls) as $audioUrl) {
            $response = $this->proxyCurl($audioUrl, 30, 20 * 1024 * 1024, ['music.163.com', '163.com']);
            if (!$this->isAudioResponse($response)) {
                $this->proxyLog((string)$id, 'audio_unavailable', [
                    'url' => $audioUrl,
                    'http_code' => $response['http_code'],
                    'content_type' => $response['content_type'],
                    'effective_url' => $response['effective_url'],
                    'error' => $response['error'],
                    'body' => substr((string)($response['body'] ?? ''), 0, 120),
                ]);
                continue;
            }

            $contentType = $response['content_type'] ?: 'audio/mpeg';
            if (stripos($contentType, 'text/') !== false) {
                $contentType = 'audio/mpeg';
            }

            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . strlen($response['body']));
            header('Accept-Ranges: bytes');
            header('Cache-Control: public, max-age=86400');
            echo $response['body'];
            exit;
        }

        abort(404, 'Not Found');
    }

    private function httpGet($url, $headers = [], $cookies = '', $timeout = 10)
    {
        if (!function_exists('curl_init')) {
            $this->proxyLog('music', 'curl_missing', ['url' => $url]);
            return false;
        }

        if (empty($headers)) {
            $headers = ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'];
        }
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        if ($caInfo = $this->curlCaInfo()) {
            curl_setopt($ch, CURLOPT_CAINFO, $caInfo);
        }
        if ($cookies) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        }
        $response = curl_exec($ch);
        if ($response === false) {
            $this->proxyLog('music', 'http_get_failed', [
                'url' => $url,
                'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
                'error' => curl_error($ch),
                'ca' => $this->curlCaInfo(),
            ]);
        }
        curl_close($ch);
        return $response;
    }

    private function extractQQSongmid(string $input): string
    {
        if (preg_match('/y\.qq\.com.*\/([a-zA-Z0-9]+)\.html/i', $input, $m)) {
            return $m[1];
        }
        if (preg_match('/[?&]songmid=([a-zA-Z0-9]+)/i', $input, $m)) {
            return $m[1];
        }
        if (preg_match('/[?&]songid=\d+.*[?&]songmid=([a-zA-Z0-9]+)/i', $input, $m)) {
            return $m[1];
        }
        return preg_replace('/[^a-zA-Z0-9]/', '', $input);
    }

    private function fetchQQSongInfo(string $songmid): array
    {
        $headers = [
            'Referer: https://y.qq.com/',
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Mobile/15E148 Safari/604.1',
        ];

        $url = 'https://c.y.qq.com/v8/fcg-bin/fcg_play_single_song.fcg?songmid=' . urlencode($songmid) . '&format=json';
        $response = $this->httpGet($url, $headers);
        $data = $response ? json_decode($response, true) : null;
        if (!empty($data['data'][0])) {
            return $data['data'][0];
        }

        $payload = [
            'comm' => ['ct' => 24, 'cv' => 0],
            'songinfo' => [
                'module' => 'music.pf_song_detail_svr',
                'method' => 'get_song_detail_yqq',
                'param' => ['song_mid' => $songmid],
            ],
        ];
        $response = $this->httpGet(
            'https://u.y.qq.com/cgi-bin/musicu.fcg?data=' . urlencode(json_encode($payload, JSON_UNESCAPED_SLASHES)),
            $headers
        );
        $data = $response ? json_decode($response, true) : null;
        return $data['songinfo']['data']['track_info'] ?? [];
    }

    public function qq()
    {
        try {
            if (!$this->request->isPost()) {
                return $this->error('请求方式错误');
            }
            if ($limited = $this->guardExternalRequest('music_qq')) {
                return $limited;
            }

            $input = trim((string)$this->request->post('id', ''));
            if (empty($input)) {
                return $this->error('参数错误');
            }

            $songmid = $this->extractQQSongmid($input);
            if ($songmid === '') {
                return $this->error('参数错误');
            }

            $song = $this->fetchQQSongInfo($songmid);
            if (empty($song)) {
                return $this->error('获取失败，歌曲不存在');
            }

            $songName = $song['title'] ?? $song['name'] ?? '';
            $artists = [];
            foreach (($song['singer'] ?? []) as $singer) {
                $artists[] = $singer['title'] ?? $singer['name'] ?? '';
            }
            $artist = implode(',', $artists);
            $albummid = $song['album']['mid'] ?? $song['album']['pmid'] ?? '';
            $cover = $albummid ? 'https://y.gtimg.cn/music/photo_new/T002R300x300M000' . $albummid . '.jpg' : '';

            return $this->success('获取成功', [
                'name' => $songName,
                'artist' => $artist,
                'cover' => $cover,
                'url' => '/api/music/qq-proxy?id=' . $songmid,
            ]);
        } catch (\Throwable $e) {
            $this->proxyLog('qq', 'qq_parse_exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->error('获取失败，请查看运行日志');
        }
    }

    private function getQQSongUrl($songmid)
    {
        $guid = (string)rand(111111111, 999999999);
        $filenames = [
            'M800' . $songmid . '.mp3',
            'M500' . $songmid . '.mp3',
            'C400' . $songmid . '.m4a',
        ];

        foreach ($filenames as $filename) {
            $rdata = json_encode([
                'req' => ['module' => 'CDN.SrfCdnDispatchServer', 'method' => 'GetCdnDispatch', 'param' => ['guid' => $guid, 'calltype' => 0, 'userip' => '']],
                'req_0' => [
                    'module' => 'vkey.GetVkeyServer',
                    'method' => 'CgiGetVkey',
                    'param' => [
                        'guid' => $guid,
                        'songmid' => [$songmid],
                        'songtype' => [0],
                        'filename' => [$filename],
                        'uin' => '0',
                        'loginflag' => 1,
                        'platform' => '20',
                    ],
                ],
                'comm' => ['uin' => 0, 'format' => 'json', 'ct' => 24, 'cv' => 0],
            ], JSON_UNESCAPED_SLASHES);

            $response = $this->httpGet(
                'https://u.y.qq.com/cgi-bin/musicu.fcg?data=' . urlencode($rdata),
                [
                    'Referer: https://y.qq.com/portal/player.html',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ]
            );

            if (!$response) {
                continue;
            }

            $data = json_decode($response, true);
            $sips = $data['req_0']['data']['sip'] ?? [];
            if (empty($sips)) {
                $sips = [
                    'https://isure.stream.qqmusic.qq.com/',
                    'https://dl.stream.qqmusic.qq.com/',
                    'https://ws.stream.qqmusic.qq.com/',
                ];
            }
            $infos = $data['req_0']['data']['midurlinfo'] ?? [];
            foreach ($infos as $info) {
                $purl = $info['purl'] ?? '';
                if ($purl === '') {
                    continue;
                }
                if (preg_match('/^https?:\/\//i', $purl)) {
                    return $purl;
                }
                if (strpos($purl, '//') === 0) {
                    return 'https:' . $purl;
                }
                foreach ($sips as $sip) {
                    if ($sip !== '') {
                        return rtrim($sip, '/') . '/' . ltrim($purl, '/');
                    }
                }
            }
        }
        return '';
    }

    public function qqProxy()
    {
        if ($limited = $this->guardExternalRequest('music_qq_proxy')) {
            return $limited;
        }

        $songmid = $this->request->get('id', '');
        if (empty($songmid)) {
            abort(400, 'Bad Request');
        }

        $audioUrl = $this->getQQSongUrl($songmid);
        if (empty($audioUrl)) {
            abort(404, 'Not Found');
        }

        $response = $this->proxyCurl($audioUrl, 30, 20 * 1024 * 1024, ['qqmusic.qq.com', 'aqqmusic.tc.qq.com'], [
            'Referer: https://y.qq.com/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: audio/webm,audio/ogg,audio/wav,audio/*;q=0.9,*/*;q=0.8',
        ]);
        if (!$this->isAudioResponse($response)) {
            abort(404, 'Not Found');
        }

        $contentType = $response['content_type'] ?: 'audio/mpeg';
        if (stripos($contentType, 'text/') !== false) {
            $contentType = 'audio/mpeg';
        }

        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . strlen($response['body']));
        header('Accept-Ranges: bytes');
        header('Cache-Control: public, max-age=3600');
        echo $response['body'];
        exit;
    }

    private function extractKugouHash(string $input): string
    {
        if (preg_match('/hash=([a-zA-Z0-9]+)/i', $input, $m)) {
            return strtoupper($m[1]);
        }
        if (preg_match('/\/song\/#hash=([a-zA-Z0-9]+)/i', $input, $m)) {
            return strtoupper($m[1]);
        }
        return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $input));
    }

    private function fetchKugouSong(string $hash): array
    {
        $headers = [
            'Referer: https://www.kugou.com/',
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1 Mobile/15E148 Safari/604.1',
        ];

        $url = 'https://m.kugou.com/app/i/getSongInfo.php?cmd=playInfo&hash=' . urlencode($hash);
        $response = $this->httpGet($url, $headers);
        $data = $response ? json_decode($response, true) : null;
        if (!empty($data['songName'])) {
            return [
                'name' => $data['songName'] ?? '',
                'artist' => $data['singerName'] ?? '',
                'cover' => str_replace('{size}', '150', $data['album_img'] ?? ''),
                'url' => $data['url'] ?? '',
            ];
        }

        $mid = md5((string)microtime(true) . randomString(8));
        $url = 'https://wwwapi.kugou.com/yy/index.php?r=play/getdata&hash=' . urlencode($hash) . '&dfid=-&mid=' . $mid . '&platid=4';
        $response = $this->httpGet($url, $headers);
        $data = $response ? json_decode($response, true) : null;
        $song = $data['data'] ?? [];
        if (!empty($song['song_name']) || !empty($song['audio_name'])) {
            return [
                'name' => $song['song_name'] ?? $song['audio_name'] ?? '',
                'artist' => $song['author_name'] ?? '',
                'cover' => str_replace('{size}', '150', $song['img'] ?? ''),
                'url' => $song['play_url'] ?? '',
            ];
        }

        return [];
    }

    public function kugou()
    {
        try {
            if (!$this->request->isPost()) {
                return $this->error('请求方式错误');
            }
            if ($limited = $this->guardExternalRequest('music_kugou')) {
                return $limited;
            }

            $input = trim((string)$this->request->post('id', ''));
            if (empty($input)) {
                return $this->error('参数错误');
            }

            $hash = $this->extractKugouHash($input);
            if ($hash === '') {
                return $this->error('参数错误');
            }

            $song = $this->fetchKugouSong($hash);
            if (empty($song) || empty($song['name'])) {
                return $this->error('获取失败，歌曲不存在');
            }

            if (empty($song['url'])) {
                return $this->error('该歌曲需要付费或无法获取');
            }

            return $this->success('获取成功', [
                'name' => $song['name'],
                'artist' => $song['artist'],
                'cover' => $song['cover'],
                'url' => '/api/music/kugou-proxy?hash=' . $hash,
            ]);
        } catch (\Throwable $e) {
            $this->proxyLog('kugou', 'kugou_parse_exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->error('获取失败，请查看运行日志');
        }
    }

    public function kugouProxy()
    {
        if ($limited = $this->guardExternalRequest('music_kugou_proxy')) {
            return $limited;
        }

        $hash = $this->extractKugouHash((string)$this->request->get('hash', ''));
        if ($hash === '') {
            abort(400, 'Bad Request');
        }

        $song = $this->fetchKugouSong($hash);
        $audioUrl = $song['url'] ?? '';
        if ($audioUrl === '') {
            abort(404, 'Not Found');
        }

        $response = $this->proxyCurl($audioUrl, 30, 20 * 1024 * 1024, ['kugou.com', 'kgimg.com'], [
            'Referer: https://www.kugou.com/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: audio/webm,audio/ogg,audio/wav,audio/*;q=0.9,*/*;q=0.8',
        ]);
        if (!$this->isAudioResponse($response)) {
            abort(404, 'Not Found');
        }

        $contentType = $response['content_type'] ?: 'audio/mpeg';
        if (stripos($contentType, 'text/') !== false) {
            $contentType = 'audio/mpeg';
        }

        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . strlen($response['body']));
        header('Accept-Ranges: bytes');
        header('Cache-Control: public, max-age=1800');
        echo $response['body'];
        exit;
    }

    private function extractKuwoRid(string $input): string
    {
        if (preg_match('/play_detail\/(\d+)/', $input, $m)) {
            return $m[1];
        }
        if (preg_match('/musicrid=(?:MUSIC_)?(\d+)/i', $input, $m)) {
            return $m[1];
        }
        if (preg_match('/(?:^|[?&])rid=(?:MUSIC_)?(\d+)/i', $input, $m)) {
            return $m[1];
        }
        if (preg_match('/\d+/', $input, $m)) {
            return $m[0];
        }
        return '';
    }

    private function kuwoHeaders(string $rid, string $token = ''): array
    {
        $headers = [
            'Referer: https://www.kuwo.cn/play_detail/' . $rid,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: application/json, text/plain, */*',
        ];
        if ($token !== '') {
            $headers[] = 'csrf: ' . $token;
            $headers[] = 'Cookie: kw_token=' . $token;
        }
        return $headers;
    }

    private function fetchKuwoInfo(string $rid): array
    {
        $token = strtoupper(md5(randomString(16)));
        $urls = [
            'https://m.kuwo.cn/newh5app/api/mobile/v1/music/info/' . $rid . '?httpsStatus=1&reqId=' . md5(uniqid((string)rand(), true)),
            'https://www.kuwo.cn/api/www/music/musicInfo?mid=' . $rid . '&httpsStatus=1&reqId=' . md5(uniqid((string)rand(), true)),
            'https://m.kuwo.cn/newh5/singles/songinfoandlrc?musicId=' . $rid,
        ];

        foreach ($urls as $url) {
            $response = $this->httpGet($url, $this->kuwoHeaders($rid, $token), 'BAIDU_RANDOM=' . strtolower($token));
            $data = $response ? json_decode($response, true) : null;
            $info = $data['data']['info'] ?? $data['data'] ?? $data['songinfo'] ?? [];
            if (!empty($info['name']) || !empty($info['songName'])) {
                return [
                    'name' => $info['name'] ?? $info['songName'] ?? '',
                    'artist' => $info['artist_name'] ?? $info['artist'] ?? $info['singer'] ?? '',
                    'cover' => $info['pic'] ?? $info['albumpic'] ?? $info['pic120'] ?? '',
                ];
            }
        }

        return [];
    }

    private function findKuwoAudioUrl(string $rid): string
    {
        $token = strtoupper(md5(randomString(16)));
        $urls = [
            'https://m.kuwo.cn/newh5app/api/mobile/v2/music/src/' . $rid . '?httpsStatus=1&reqId=' . md5(uniqid((string)rand(), true)),
            'https://www.kuwo.cn/api/v1/www/music/playUrl?mid=' . $rid . '&type=music&httpsStatus=1&reqId=' . md5(uniqid((string)rand(), true)),
            'https://antiserver.kuwo.cn/anti.s?type=convert_url3&rid=MUSIC_' . $rid . '&format=mp3&response=url',
        ];

        foreach ($urls as $url) {
            $response = $this->httpGet($url, $this->kuwoHeaders($rid, $token), 'BAIDU_RANDOM=' . strtolower($token));
            if (!$response) {
                continue;
            }
            $trimmed = trim($response);
            if (preg_match('/^https?:\/\//i', $trimmed)) {
                return $trimmed;
            }
            $data = json_decode($response, true);
            $playUrl = $data['data']['url'] ?? $data['data'] ?? $data['url'] ?? '';
            if (is_string($playUrl) && preg_match('/^https?:\/\//i', $playUrl)) {
                return $playUrl;
            }
        }

        return '';
    }

    public function kuwo()
    {
        try {
            if (!$this->request->isPost()) {
                return $this->error('请求方式错误');
            }
            if ($limited = $this->guardExternalRequest('music_kuwo')) {
                return $limited;
            }

            $input = trim((string)$this->request->post('id', ''));
            if (empty($input)) {
                return $this->error('参数错误');
            }

            $rid = $this->extractKuwoRid($input);
            if ($rid === '') {
                return $this->error('参数错误');
            }

            $info = $this->fetchKuwoInfo($rid);
            if (empty($info)) {
                return $this->error('获取失败，歌曲不存在');
            }

            return $this->success('获取成功', [
                'name' => $info['name'],
                'artist' => $info['artist'],
                'cover' => $info['cover'],
                'url' => '/api/music/kuwo-proxy?id=' . $rid,
            ]);
        } catch (\Throwable $e) {
            $this->proxyLog('kuwo', 'kuwo_parse_exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $this->error('获取失败，请查看运行日志');
        }
    }

    public function kuwoProxy()
    {
        if ($limited = $this->guardExternalRequest('music_kuwo_proxy')) {
            return $limited;
        }

        $rid = $this->extractKuwoRid((string)$this->request->get('id', ''));
        if ($rid === '') {
            abort(400, 'Bad Request');
        }

        $audioUrl = $this->findKuwoAudioUrl($rid);
        if ($audioUrl === '') {
            abort(404, 'Not Found');
        }

        $response = $this->proxyCurl($audioUrl, 30, 20 * 1024 * 1024, ['kuwo.cn', 'kuwo.com'], [
            'Referer: https://www.kuwo.cn/play_detail/' . $rid,
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept: audio/webm,audio/ogg,audio/wav,audio/*;q=0.9,*/*;q=0.8',
        ]);
        if (!$this->isAudioResponse($response)) {
            abort(404, 'Not Found');
        }

        $contentType = $response['content_type'] ?: 'audio/mpeg';
        if (stripos($contentType, 'text/') !== false) {
            $contentType = 'audio/mpeg';
        }

        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . strlen($response['body']));
        header('Accept-Ranges: bytes');
        header('Cache-Control: public, max-age=1800');
        echo $response['body'];
        exit;
    }
}
