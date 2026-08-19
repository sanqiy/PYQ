<?php

/**
 * sanqi
 *
 * @copyright Copyright (c) sanqi
 * @link      https://xaacn.com
 */

declare(strict_types=1);

namespace app\model;

use think\Model;

class ArticleAttachment extends Model
{
    protected $name = 'article_attachments';
    protected $pk = 'id';
    protected $autoWriteTimestamp = false;
}
