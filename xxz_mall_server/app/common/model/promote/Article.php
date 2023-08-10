<?php
declare (strict_types = 1);

namespace app\common\model\promote;

use app\common\model\BaseModel;
use think\Model;

/**
 * @mixin \think\Model
 */
class Article extends BaseModel
{
    protected $pk = 'article_id';
    protected $name = 'promote_article';
    protected $append = [
        'log_share'
    ];

    /**
     * 分类图片
     */
    public function images()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }
    /**
     * 分类
     */
    public function category()
    {
        return $this->hasOne('app\\common\\model\\promote\\ArticleCategory', 'category_id', 'category_id');
    }
    /**
     * log
     */
    public function log()
    {
        return $this->hasOne('app\\common\\model\\promote\\ArticleLog', 'article_id', 'article_id');
    }
    /**
     * log
     */
    public function share()
    {
        return $this->hasOne('app\\common\\model\\promote\\ArticleShare', 'article_id', 'article_id');
    }
    public function getLogShareAttr($value,$data)
    {
        $value['log'] = ArticleLog::withJoin(['user'=>['nickName']])->where('article_id',$data['article_id'])->select();
        $value['share'] = ArticleShare::withJoin(['user'=>['nickName']])->where('article_id',$data['article_id'])->select();
        return $value;
    }

}
