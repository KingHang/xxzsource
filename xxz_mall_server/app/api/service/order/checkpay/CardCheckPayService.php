<?php

namespace app\api\service\order\checkpay;

use app\api\model\plus\benefit\BenefitCard as BenefitCardModel;
use app\common\enum\product\DeductStockTypeEnum;

/**
 * 主订单支付检查服务类
 */
class CardCheckPayService extends CheckPayService
{
    /**
     * 判断订单是否允许付款
     */
    public function checkOrderStatus($order)
    {
        // 判断订单状态
        if (!$this->checkOrderStatusCommon($order)) {
            return false;
        }
        // 判断商品状态、库存
        if (!$this->checkProductStatus($order['product'])) {
            return false;
        }
        return true;
    }

    /**
     * 判断商品状态、库存 (未付款订单)
     */
    protected function checkProductStatus($productList)
    {
        foreach ($productList as $product) {
            // 获取商品的sku信息
            $productSku = $this->getOrderProductSku($product['product_id']);
            // 付款减库存
            if ($product['total_num'] > $productSku['stock']) {
                $this->error = "很抱歉，权益卡 [{$product['product_name']}] 库存不足";
                return false;
            }
        }
        return true;
    }

    /**
     * 获取指定的商品sku信息
     */
    private function getOrderProductSku($productId)
    {
        return BenefitCardModel::detail($productId);
    }

}