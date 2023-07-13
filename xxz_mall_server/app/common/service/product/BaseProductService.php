<?php

namespace app\common\service\product;

use app\common\model\plugin\groupsell\Goods as AssembleProductModel;
use app\common\model\plugin\pricedown\Product as BargainProductModel;
use app\common\model\plugin\brand\SignLog as BrandProductModel;
use app\common\model\plugin\exchangepurch\Product as PointsProductModel;
use app\common\model\plugin\flashsell\Product as SeckillProductModel;
use app\common\model\plugin\giftcert\Product as GiftcertProductModel;

/**
 * 商品服务类,公共处理方法
 */
class BaseProductService
{
    /**
     * 商品多规格信息
     */
    public static function getSpecData($model = null)
    {
        // 商品sku数据
        if (!is_null($model) && $model['spec_type'] == 20) {
            return $model->getManySpecData($model['spec_rel'], $model['sku']);
        }
        return null;
    }

    /**
     * 验证商品是否允许删除
     */
    public static function checkSpecLocked($model = null, $scene = 'edit')
    {
        if ($model == null || $scene == 'copy') return false;
        $service = new static;
        // 积分
        if ($service->checkPointsProduct($model['product_id'])) return true;
        // 拼团
        if ($service->checkAssembleProduct($model['product_id'])) return true;
        // 砍价
        if ($service->checkBargainProduct($model['product_id'])) return true;
        // 秒杀
        if ($service->checkSeckillProduct($model['product_id'])) return true;
        //品牌
        if ($service->checkBrandProduct($model['product_id'])) return true;
        //赠送通证
        if ($service->checkGiftcertProduct($model['product_id'])) return true;
        return false;
    }

    /**
     * 验证商品是否参与了积分商品
     */
    private function checkPointsProduct($productId)
    {
        return PointsProductModel::isExistProductId($productId);
    }

    /**
     * 验证商品是否参与了拼团商品
     */
    private function checkAssembleProduct($productId)
    {
        return AssembleProductModel::isExistProductId($productId);
    }

    /**
     * 验证商品是否参与了砍价商品
     */
    private function checkBargainProduct($productId)
    {
        return BargainProductModel::isExistProductId($productId);
    }

    /**
     * 验证商品是否参与了秒杀商品
     */
    private function checkSeckillProduct($productId)
    {
        return SeckillProductModel::isExistProductId($productId);
    }

    /**
     * 验证商品是否参与了品牌商品
     */
    private function checkBrandProduct($productId)
    {
        return BrandProductModel::isExistProductId($productId);
    }
    /**
     * 验证商品是否参与了赠送通证商品
     */
    private function checkGiftcertProduct($productId)
    {
        return GiftcertProductModel::isExistProductId($productId);
    }

}