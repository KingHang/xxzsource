<?php
declare (strict_types = 1);

namespace app\common\model\promote;

use think\Model;

/**
 * @mixin \think\Model
 */
class ArticleLog extends Model
{
    protected $pk = 'log_id';
    protected $name = 'promote_article_log';
    public function user()
    {
        return $this->hasOne('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }
}
