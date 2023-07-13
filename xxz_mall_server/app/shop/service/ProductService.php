<?php

namespace app\shop\service;

use app\common\model\plugin\agent\Setting as AgentSetting;
use app\common\service\product\BaseProductService;
use app\shop\model\goods\Category as CategoryModel;
use app\shop\model\settings\Delivery as DeliveryModel;
use app\shop\model\user\Grade as GradeModel;
use app\shop\model\plugin\benefit\Benefit as BenefitModel;

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
        $agentSetting = AgentSetting::getItem('commission');
        $basicSetting = AgentSetting::getItem('basic');

        // 权益
        $benefit = (new BenefitModel())->getList(['type'=>1]);
        // 权益默认选中第一个
        $benefit_id = 0;
        if (!empty($benefit) && $scene == 'add') {
            $benefit_id = $benefit[0]['benefit_id'];
        }
        return compact('benefit_id','benefit','category', 'delivery', 'gradeList', 'specData', 'isSpecLocked', 'agentSetting', 'basicSetting');
    }
}