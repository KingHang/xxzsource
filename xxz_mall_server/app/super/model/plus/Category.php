<?php

namespace app\super\model\plus;

use app\common\model\plugin\plus\Category as CategoryModel;
use app\super\model\Access as AccessModel;
/**
 * 插件分类模型
 */
class Category extends CategoryModel
{
    /**
     * 获取所有插件
     */
    public static function getAll()
    {
        $model = new static();
        $list = $model->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
        // 查询分类下的插件
        foreach ($list as $category){
            $category['children'] = AccessModel::getListByPlusCategoryId($category['plugin_category_id']);
        }
        return $list;
    }

    /**
     * 删除权限
     */
    public function remove()
    {
        return $this->delete();
    }
}