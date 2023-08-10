<?php

namespace app\mall\service;

use app\common\service\product\BaseProductService;
use app\mall\model\goods\Category as CategoryModel;
use app\mall\model\setting\Delivery as DeliveryModel;
use app\mall\model\user\Grade as GradeModel;

/**
 * 商品服务类
 */
class ProductService extends BaseProductService
{
    /**
     * 商品管理公共数据
     */
    public static function getEditData($model = null, $scene = 'edit')
    {
        // 商品分类
        $category = CategoryModel::getCacheTree();
        // 配送模板,仅仅查当前模板
        $delivery = [];
        // 配送模板
        $delivery = DeliveryModel::getAll(10001);
        // 会员等级列表
        $gradeList = GradeModel::getUsableList();
        // 商品sku数据
        $specData = static::getSpecData($model);
        // 商品规格是否锁定
        $isSpecLocked = static::checkSpecLocked($model, $scene);
        // 平台分销规则
        $agentSetting = [];
        $basicSetting = [];
        return compact('category', 'delivery', 'gradeList', 'specData', 'isSpecLocked', 'agentSetting', 'basicSetting');
    }
}