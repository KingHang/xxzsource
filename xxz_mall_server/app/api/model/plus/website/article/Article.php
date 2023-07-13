<?php

namespace app\api\model\plus\website\article;

use app\common\model\plugin\website\article\Article as ArticleModel;
use think\db\exception\DbException;

/**
 * 文章模型
 */
class Article extends ArticleModel
{
    /**
     * 获取文章列表
     * @param $category_id
     * @param $params
     * @return array
     * @throws DbException
     */
    public function getList($category_id, $params)
    {
        $list = $this->with(['image', 'category'])
            ->field('article_id,article_title,category_id,image_id,dec,url,author,create_time')
            ->where('is_delete', '=', 0);

        if ($category_id) {
            $list->where('category_id', '=', $category_id);
        }

        $list = $list->where('article_status','=',1)
            ->order(['article_sort' => 'asc', 'create_time' => 'desc'])
            ->paginate(15, false, [
                'query' => request()->request()
            ])->toArray();

        $arr = $list['data'];
        foreach ($arr as $key => $item) {
            $arr[$key]['pic'] = $item['image']['file_path'];
            unset($arr[$key]['image']);
            unset($arr[$key]['category']);
        }
        $list['data'] = $arr;
        return $list;
    }

    /**
     * 新增记录
     */
    public function add($data)
    {
        if ($data['show_type']=="10") {
            if (empty($data['article_content'])) {
                $this->error = '请输入文章内容';
                return false;
            }
        } else {
            if (empty($data['url'])) {
                $this->error = '请输入跳转链接';
                return false;
            }
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
     * 获取最新文章列表
     */
    public function getNews()
    {
        $list = $this->field('article_id,article_title,url,image_id,author,create_time,dec')
            //->with(['image', 'category'])
            ->where('is_delete', '=', 0)
            ->where('article_status', '=', 1)
            ->where('is_recommand', '=', 1)
            ->order(['article_sort' => 'asc', 'create_time' => 'desc'])
            ->limit(5)
            ->select();

        foreach ($list as $item) {
            $item['pic'] = $item->image->file_path;
            unset($item['image']);
        }

        return $list;
    }
}
