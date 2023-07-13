<?php

namespace app\api\model\plus\website\album;

use think\facade\Cache;
use app\common\model\plugin\website\album\Category as CategoryModel;
use app\api\model\plus\website\album\Album as AlbumModel;

/**
 * 文章分类模型
 */
class Category extends CategoryModel
{
    /**
     * 分类详情
     */
    public static function detail($category_id)
    {
        return static::find($category_id);
    }

    /**
     * 添加新记录
     */
    public function add($data)
    {
        if($this->checkExist($data['cate_title'])){
            $this->error = '该标题已存在,请更换标题';
            return false;
        
        }
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
        if($this->checkExist($data['cate_title'],$data['category_id'])){
            $this->error = '该标题已存在,请更换标题';
            return false;
        
        }
        $data['create_time'] = strtotime($data['create_time']);
        $data['update_time'] = time();
        return $this->save($data);
    }

    /**
     * 删除分类
     */
    public function remove()
    {
        // 判断是否存在内容
        $articleCount = AlbumModel::getArticleTotal(['category_id' => $this['category_id']]);
        if ($articleCount > 0) {
            $this->error = '该分类下存在' . $articleCount . '个内容，不允许删除';
            return false;
        }
        return $this->delete();
    }

    /**
     * 删除缓存
     */
    private function deleteCache()
    {
        return Cache::delete('article_category_' . self::$app_id);
    }


     private function checkExist($title,$id =0 )
     {
        return !!$this->where('cate_title', '=', $title)->where('category_id', '<>', $id)
            ->value('category_id');
         
     }
     
     
}