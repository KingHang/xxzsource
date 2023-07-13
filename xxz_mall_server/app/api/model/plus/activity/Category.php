<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\Category as CategoryModel;

/**
 * 活动分类
 */
class Category extends CategoryModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'update_time',
        'create_time',
        'displayorder',
        'image_id'
    ];

    /**
     * 分类列表
     */
    public function getLists()
    {
        return $this->field('category_id,name')
            ->where(['is_delete' => 0 , 'app_id' => self::$app_id ])
            ->order(['displayorder' => 'asc', 'create_time' => 'desc'])
            ->select();
    }

}