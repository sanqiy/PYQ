<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息
     */
    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * 渲染异常为 HTTP 响应
     */
    public function render($request, Throwable $e): Response
    {
        // ValidateException → 统一格式
        if ($e instanceof ValidateException) {
            return $this->renderApiError($e->getError(), 422, '422');
        }

        // 模型未找到 → 404
        if ($e instanceof ModelNotFoundException || $e instanceof DataNotFoundException) {
            $message = $e instanceof ModelNotFoundException
                ? '数据不存在'
                : '数据不存在';
            if ($this->isApiRequest($request)) {
                return $this->renderApiError($message, 404, '404');
            }
        }

        // HTTP 异常
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            $message = $this->httpStatusMessage($statusCode);

            if ($this->isApiRequest($request)) {
                return $this->renderApiError($message, $statusCode, (string)$statusCode);
            }

            // 404 页面
            if ($statusCode === 404) {
                return Response::create($message, 'html', 404);
            }
        }

        // API 请求的未捕获异常 → JSON 格式
        if ($this->isApiRequest($request)) {
            $message = config('app.debug') ? $e->getMessage() : '服务器内部错误';
            return $this->renderApiError($message, 500, '500');
        }

        // 非 API 请求交给系统默认处理
        return parent::render($request, $e);
    }

    /**
     * 判断是否为 API 请求
     */
    protected function isApiRequest($request): bool
    {
        $path = $request->pathinfo();
        return str_starts_with($path, 'api/') || $request->isAjax();
    }

    /**
     * 渲染 API 统一错误格式
     */
    protected function renderApiError(string $message, int $httpCode, string $code): Response
    {
        return json(['code' => (string)$code, 'msg' => $message])->code($httpCode);
    }

    /**
     * HTTP 状态码对应消息
     */
    protected function httpStatusMessage(int $code): string
    {
        $map = [
            400 => '请求参数错误',
            401 => '请先登录',
            403 => '没有权限',
            404 => '页面不存在',
            405 => '请求方式错误',
            429 => '请求过于频繁，请稍后再试',
            500 => '服务器内部错误',
            502 => '网关错误',
            503 => '服务不可用',
        ];
        return $map[$code] ?? '请求错误 (' . $code . ')';
    }
}
