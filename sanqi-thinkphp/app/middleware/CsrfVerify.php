<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\middleware;

class CsrfVerify
{
    public function handle($request, \Closure $next)
    {
        if (!$request->isPost()) {
            return $next($request);
        }

        $path = strtolower(trim($request->pathinfo(), '/'));
        $exempt = config('csrf.exempt') ?: [];
        $skipOrigin = config('csrf.skip_origin') ?: [];

        // 完全豁免的路由跳过所有检查
        if (in_array($path, $exempt)) {
            return $next($request);
        }

        // 同源检查（上传等multipart请求跳过）
        if (!in_array($path, $skipOrigin) && !$this->isSameOriginRequest()) {
            return json(['code' => '403', 'msg' => '请求来源验证失败，请刷新页面重试'], 403);
        }

        // CSRF token验证
        if (!$this->hasValidCsrfToken()) {
            return json(['code' => '403', 'msg' => 'Token验证失败，请刷新页面重试'], 403);
        }

        return $next($request);
    }

    protected function hasValidCsrfToken()
    {
        $token = request()->post('allkey', '');
        if ($token === '') {
            $token = request()->header('X-CSRF-Token', '');
        }

        $sessionToken = session('allkey');
        return $token !== '' && $sessionToken && hash_equals($sessionToken, $token);
    }

    protected function isSameOriginRequest()
    {
        $currentHost = $this->normalizeOriginHost(request()->host());
        if ($currentHost === '') {
            return true;
        }

        $allowedHosts = [$currentHost];

        // 检查 Origin 头
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        // 两个头都缺失时，要求 CSRF token 必须存在（非浏览器客户端场景）
        if ($origin === '' && $referer === '') {
            $token = request()->post('allkey', '') ?: request()->header('X-CSRF-Token', '');
            return $token !== '';
        }

        // 优先检查 Origin，其次 Referer
        $source = $origin ?: $referer;
        $sourceHost = $this->normalizeOriginUrl($source);

        if ($sourceHost === '') {
            return false;
        }

        return in_array($sourceHost, $allowedHosts, true);
    }

    protected function normalizeOriginUrl($url)
    {
        $host = parse_url((string)$url, PHP_URL_HOST);
        if (!$host) {
            return '';
        }

        $port = parse_url((string)$url, PHP_URL_PORT);
        return $this->normalizeOriginHost($host . ($port ? ':' . $port : ''));
    }

    protected function normalizeOriginHost($host)
    {
        $host = strtolower(trim((string)$host));
        if ($host === '') {
            return '';
        }

        $host = preg_replace('/\s+/', '', explode(',', $host)[0]);
        if ($host === '') {
            return '';
        }

        if (substr($host, -3) === ':80') {
            return substr($host, 0, -3);
        }
        if (substr($host, -4) === ':443') {
            return substr($host, 0, -4);
        }

        return $host;
    }
}
