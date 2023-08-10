<?php
declare (strict_types = 1);

namespace app\mall\model\promote;

use think\Model;
use app\common\model\promote\Article as ArticleModel;

/**
 * @mixin \think\Model
 */
class Article extends ArticleModel
{
    public static function detail($article_id)
    {
        return self::with(['category','images','log','share'])
            ->withCount(['log'=> function($query){
                $query->where('is_like',1);
            }])
            ->withCount(['share'])
            ->field('article_id,article_title,article_desc,category_id,image_id,is_delete,virtual_views,actual_views,article_status')
            ->order(['create_time' => 'desc'])
            ->where('article_id',$article_id)->find();
    }
    public function getList($data = [])
    {
        $model = $this;
        // 检索查询条件
        $model = $model->setWhere($model,$data);
        // 获取数据列表
        return $model
            ->with(['category','images','log','share'])
            ->withCount(['log'=> function($query){
                $query->where('is_like',1);
            }])
            ->withCount(['share'])
            ->field('article_id,article_title,article_desc,category_id,image_id,is_delete,virtual_views,actual_views,article_status')
            ->order(['create_time' => 'desc'])
            ->paginate($data);
//                     ->fetchSql(true)->select();
//         dump($arr);
    }
    /**
     * 设置检索查询条件
     */
    private function setWhere($model, $data)
    {
        //搜索框关键字
        if (isset($data['search']) && $data['search'] != '') {
            $model = $model->where('article_title', 'like', '%' . trim($data['search']) . '%');
        }
        //type设备
        if (isset($data['category_id']) && $data['category_id'] != '') {
            $model = $model->where('category_id', '=', $data['category_id']);
        }
        return $model;
    }
}
