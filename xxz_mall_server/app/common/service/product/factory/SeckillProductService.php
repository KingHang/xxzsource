<?php

namespace app\common\service\product\factory;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\plugin\flashsell\FlashsellSku as ProductSkuModel;
use app\common\model\plugin\flashsell\Goods as ProductModel;

/**
 * 商品来源-普通商品扩展类
 */
class SeckillProductService extends ProductService
{
    /**
     * 更新商品库存 (针对下单减库存的商品)
     */
    public function updateProductStock($productList)
    {
        foreach ($productList as $product) {
            // 下单减库存
            $sku = ProductSkuModel::detail($product['sku_source_id']);
            // 参与人数
            (new ProductModel)->where('flashsell_goods_id', '=', $sku['flashsell_goods_id'])->inc('join_num')->update();
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::CREATE) {
                try{
                    // 主库存减少
                    (new ProductModel)->where('flashsell_goods_id', '=', $sku['flashsell_goods_id'])->dec('stock', $product['total_num'])->update();
                    // 下单减库存
                    (new ProductSkuModel)->where('flashsell_goods_sku_id', '=', $sku['flashsell_goods_sku_id'])->dec('seckill_stock', $product['total_num'])->update();
                }catch (\Exception $e){
                    log_write('flashsell updateProductStock'. $e->getMessage());
                }
            }
        }
    }

    public function updateStockSales($productList)
    {
        foreach ($productList as $product) {
            $sku = ProductSkuModel::detail($product['sku_source_id']);
            // 记录商品的销量
            (new ProductModel)->where('flashsell_goods_id', '=', $sku['flashsell_goods_id'])->inc('total_sales', $product['total_num'])->update();
            // 付款减库存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT) {
                try{
                    // 主库存减少
                    (new ProductModel)->where('flashsell_goods_id', '=', $sku['flashsell_goods_id'])->dec('stock', $product['total_num'])->update();
                    // 下单减库存
                    (new ProductSkuModel)->where('flashsell_goods_sku_id', '=', $sku['flashsell_goods_sku_id'])->dec('seckill_stock', $product['total_num'])->update();
                }catch (\Exception $e){
                    log_write('flashsell updateStockSales'. $e->getMessage());
                }
            }
        }
        return true;
    }


    public function backProductStock($productList, $isPayOrder = false)
    {
        foreach ($productList as $product) {
            // 1,未付款订单并且创建时减库存，回退库存 2,已付款订单回退
            if ((!$isPayOrder && $product['deduct_stock_type'] == DeductStockTypeEnum::CREATE)
                || $isPayOrder
            ) {
                $sku = ProductSkuModel::detail($product['sku_source_id']);
                // 回退主库存
                (new ProductModel)->where('flashsell_goods_id', '=', $sku['flashsell_goods_id'])->inc('stock', $product['total_num'])->update();
                // 回退sku库存
                (new ProductSkuModel)->where('flashsell_goods_sku_id', '=', $sku['flashsell_goods_sku_id'])->inc('seckill_stock', $product['total_num'])->update();
            }
        }
    }

}