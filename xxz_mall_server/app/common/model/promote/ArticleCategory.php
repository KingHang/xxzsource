<?php
declare (strict_types = 1);

namespace app\common\model\promote;

use think\Model;

/**
 * @mixin \think\Model
 */
class ArticleCategory extends Model
{
    protected $pk = 'category_id';
    protected $name = 'promote_article_category';
}
