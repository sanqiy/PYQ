<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\middleware;

use think\exception\HttpException;
use think\exception\ValidateException;
use think\facade\Log;

/**
 * 异常日志中间件 — 记录请求上下文信息
 */
class ExceptionLog
{
    public function handle($request, \Closure $next)
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            $this->logException($request, $e);
            throw $e;
        }
    }

    protected function logException($request, \Throwable $e): void
    {
        // ValidateException 和 HttpException 由框架正常处理，不记录
        if ($e instanceof ValidateException || $e instanceof HttpException) {
            return;
        }

        $context = [
            'url'    => $request->url(),
            'method' => $request->method(),
            'ip'     => $request->ip(),
            'code'   => $e->getCode(),
            'file'   => $e->getFile(),
            'line'   => $e->getLine(),
        ];

        if ($request->isAjax()) {
            $context['post_keys'] = array_keys($request->post() ?: []);
        }

        Log::error('Unhandled exception: ' . $e->getMessage(), $context);
    }
}
