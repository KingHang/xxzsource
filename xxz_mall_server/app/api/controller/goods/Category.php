<?php

namespace app\api\controller\goods;

use app\api\model\goods\Category as CategoryModel;
use app\api\controller\Controller;
use app\common\model\home\HomeCategory as PageCategoryModel;

/**
 * 商品分类控制器
 */
class Category extends Controller
{
    /**
     * 分类页面
     */
    public function index()
    {
        // 分类模板
        $template = PageCategoryModel::detail();
        // 商品分类列表
        $list = array_values(CategoryModel::getCacheTree());
        return $this->renderSuccess('', compact('template','list'));
    }

}