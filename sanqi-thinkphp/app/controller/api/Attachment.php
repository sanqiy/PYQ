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
use app\model\ArticleAttachment;
use app\model\Essay;

class Attachment extends Base
{
    /**
     * 下载附件（需登录）
     */
    public function download()
    {
        if (!$this->requireLogin()) return;

        $id = (int) $this->request->get('id', 0);
        if ($id <= 0) {
            return $this->error('参数错误');
        }

        $attachment = ArticleAttachment::find($id);
        if (!$attachment) {
            return $this->error('附件不存在');
        }

        // 检查文章是否可见
        $article = Essay::where('cid', $attachment['article_cid'])
            ->where('ptpaud', 1)
            ->where('ptpys', 1)
            ->find();
        if (!$article) {
            return $this->error('文章不存在或已删除');
        }

        if ($attachment['type'] === 'link') {
            // 外链：返回链接信息
            return $this->success('ok', [
                'type' => 'link',
                'url' => $attachment['file_url'],
                'extract_code' => $attachment['extract_code'],
                'name' => $attachment['file_name'],
            ]);
        }

        // 上传文件：重定向到文件地址
        $url = $attachment['file_url'];
        if (strpos($url, 'http') !== 0) {
            $url = assetUrl($url);
        }
        return redirect($url);
    }
}
