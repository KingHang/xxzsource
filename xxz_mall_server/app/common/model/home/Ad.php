<?php

namespace app\common\model\home;

use app\common\model\BaseModel;

/**
 * 广告模型
 */
class Ad extends BaseModel
{
    protected $name = 'ad';
    protected $pk = 'ad_id';

    /**
     * 获取全部
     */
    public function getAll()
    {
        return $this->order(['sort' => 'asc'])->select();
    }

    /**
     * 获取列表
     */
    public function getList($limit = 20)
    {
        return $this->with(['image','category'])->order(['sort' => 'asc'])
            ->paginate($limit);
    }

    /**
     * 广告详情
     */
    public static function detail($ad_id)
    {
        return (new static())->with(['image'])->find($ad_id);
    }
    /**
     * 获取列表
     */
    public function getLists($where,$limit = 20)
    {
         // 获取列表
        $data = $this->limit($limit)
                        ->where($where)
                        ->with(['image'])
                        ->order('sort asc')
                        ->select();

        // 隐藏api属性
        !$data->isEmpty() ;
        return $data;
    }
	 /**
     * 关联图片
     */
    public function image()
    {
        return $this->belongsTo('app\common\model\file\UploadFile', 'image_id', 'file_id');
    }
     /**
     * 关联分类表
     * @return \think\model\relation\BelongsTo
     */
    public function category()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->BelongsTo("app\\{$module}\\model\\home\\AdCategory", 'category_id', 'category_id');
    }
  
}