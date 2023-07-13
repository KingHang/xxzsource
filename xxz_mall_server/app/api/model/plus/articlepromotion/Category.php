<?php

namespace app\api\model\plus\articlepromotion;

use app\common\model\plugin\articlepromotion\Category as CategoryModel;

/**
 * 推广文章分类模型
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

    public static function getList() {
    }
}
