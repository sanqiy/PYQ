<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

namespace app\model;

use think\Model;

/**
 * 文件上传记录表
 *
 * @property int    $id         主键ID
 * @property string $md5        文件MD5哈希
 * @property string $url        文件URL路径
 * @property string $type       文件类型(image/video)
 * @property int    $ref_count  引用计数
 * @property string $created_at 创建时间
 */
class FileUpload extends Model
{
    protected $name = 'file_uploads';
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'created_at';
    protected $createTime = 'created_at';
    protected $updateTime = false;

    protected $type = [
        'id'        => 'integer',
        'ref_count' => 'integer',
    ];
}
