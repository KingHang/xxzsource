<?php

namespace app\api\model\plus\website\album;

use app\common\model\plugin\website\album\album as AlbumModel;
use app\api\model\plus\website\album\Category as AlbumCategoryModel;

/**
 * 文章模型
 */
class Album extends AlbumModel
{
    /**
     * 获取内容列表按分类
     */
    public function getLists($params,$id)
    {

        return $this->with(['image', 'category'])
            ->where('is_delete', '=', 0) 
            ->where('category_id', '=', $id)
            ->order(['album_sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params, false, [
                'query' => request()->request()
            ]);

    }
    
    /**
     * 获取内容列表
     */
    
    public function getList($params)
    {

        return $this->with(['image', 'category'])
            ->where('is_delete', '=', 0) 
            ->order(['album_sort' => 'asc', 'create_time' => 'desc'])
            ->paginate($params, false, [
                'query' => request()->request()
            ]);

    }

    /**
     * 新增记录
     */
    public function add($data)
    {
        if (empty($data['article_content'])) {
            $this->error = '请输入文章内容';
            return false;
        }

        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 更新记录
     */
    public function edit($data)
    {
        if (empty($data['article_content'])) {
            $this->error = '请输入文章内容';
            return false;
        }
        return $this->save($data);
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

    /**
     * 获取文章总数量
     */
    public static function getArticleTotal($where)
    {
        $model = new static;
        return $model->where($where)->where('is_delete', '=', 0)->count();
    }
    
    
    /**
     * 获取轮播列表 
     */
     
    public function getAlbumtList($category_id)
    {
        $pid = (AlbumCategoryModel::where('type',$category_id)->value('category_id'));
        $list= [];
      
            //->with(['image', 'category'])
        $list = $this->field('album_id,album_title,url,image_id')
            ->where('category_id',$pid)
            ->where('album_status', '=', 1)
            ->where('is_delete', '=', 0) 
            ->order(['album_sort' => 'asc', 'create_time' => 'desc'])
            ->select();
        foreach ($list as $item) {
            $item['pic'] =$item->image->file_path;
            unset ($item['image']);
        }    
        return $list;
    }
}