<?php
declare (strict_types = 1);

namespace app\common\model\promote;

use think\Model;

/**
 * @mixin \think\Model
 */
class ArticleShare extends Model
{
    protected $pk = 'id';
    protected $name = 'promote_article_share';
    public function user()
    {
        return $this->hasOne('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }
}
