<?php

namespace app\api\model\goods;

use app\common\model\goods\Category as CategoryModel;

/**
 * 商品分类模型
 */
class Category extends CategoryModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'update_time'
    ];

}