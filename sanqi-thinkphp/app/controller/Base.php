<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\controller;

use app\BaseController as ThinkBaseController;
use think\File;

class Base extends ThinkBaseController
{
    use \app\traits\AuthTrait;
    use \app\traits\SiteConfigTrait;
    use \app\traits\ArticleHelperTrait;

    /**
     * 将 ThinkPHP UploadedFile 对象转换为数组格式（兼容 UploadService）
     */
    protected function fileToArray($file): ?array
    {
        if ($file === null) {
            return null;
        }
        if (is_array($file)) {
            return $file;
        }
        if ($file instanceof File) {
            try { $size = $file->getSize(); } catch (\Exception $e) { $size = 0; }
            return [
                'name' => $file->getOriginalName(),
                'tmp_name' => $file->getRealPath(),
                'error' => $file->isValid() ? UPLOAD_ERR_OK : UPLOAD_ERR_NO_FILE,
                'size' => $size,
                'type' => $file->getOriginalMime(),
            ];
        }
        return null;
    }

    /**
     * 统一成功响应
     * 返回格式: {code:'200', msg:'...', data:{...}}
     */
    protected function success($msg = '操作成功', $data = null)
    {
        $response = ['code' => '200', 'msg' => $msg];
        if ($data !== null) {
            $response['data'] = $data;
        }
        return json($response);
    }

    /**
     * 统一失败响应
     * 返回格式: {code:'400', msg:'...'}
     */
    protected function error($msg = '操作失败', $code = '400')
    {
        return json(['code' => $code, 'msg' => $msg]);
    }
}
