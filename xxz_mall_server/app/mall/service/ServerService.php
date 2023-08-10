<?php

namespace app\mall\service;

use app\common\service\product\BaseProductService;
use app\mall\model\server\Category as CategoryModel;
use app\mall\model\server\Tag as TagModel;

/**
 * 商品服务类
 */
class ServerService extends BaseProductService
{
    /**
     * 商品管理公共数据
     */
    public static function getEditData($model = null, $scene = 'edit')
    {
        // 商品分类
        $category = CategoryModel::getAll();
        // 商品标签
        $tag = TagModel::getAll();
        // 商品sku数据
        $specData = static::getSpecData($model);
        // 商品规格是否锁定
//        $isSpecLocked = static::checkSpecLocked($model, $scene);
        return compact('category', 'specData', 'tag');
    }
}