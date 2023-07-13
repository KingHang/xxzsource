<?php

namespace app\common\service\product\factory;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\plugin\pricedown\BargainSku as ProductSkuModel;
use app\common\model\plugin\pricedown\Product as ProductModel;
use app\common\model\plugin\pricedown\Task as TaskModel;
/**
 * 商品来源-普通商品扩展类
 */
class BargainProductService extends ProductService
{
    /**
     * 更新商品库存 (针对下单减库存的商品)
     */
    public function updateProductStock($productList)
    {
        foreach ($productList as $product) {
            // 下单减库存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::CREATE) {
                $sku = ProductSkuModel::detail($product['sku_source_id']);
                try{
                    // 主库存减少
                    (new ProductModel)->where('bargain_product_id', '=', $sku['bargain_product_id'])->dec('stock', $product['total_num'])->update();
                    // 下单减库存
                    (new ProductSkuModel)->where('bargain_product_sku_id', '=', $sku['bargain_product_sku_id'])->dec('bargain_stock', $product['total_num'])->update();
                }catch (\Exception $e){
                    log_write('pricedown updateProductStock'. $e->getMessage());
                }
            }
        }
    }

    /**
     * 更新商品库存销量（订单付款后）
     */
    public function updateStockSales($productList)
    {
        foreach ($productList as $product) {
            $sku = ProductSkuModel::detail($product['sku_source_id']);
            // 记录商品的销量
            (new ProductModel)->where('bargain_product_id', '=', $sku['bargain_product_id'])->inc('total_sales', $product['total_num'])->update();
            // 付款减库存
            if ($product['deduct_stock_type'] == DeductStockTypeEnum::PAYMENT) {
                try{
                    // 主库存减少
                    (new ProductModel)->where('bargain_product_id', '=', $sku['bargain_product_id'])->dec('stock', $product['total_num'])->update();
                    // 下单减库存
                    (new ProductSkuModel)->where('bargain_product_sku_id', '=', $sku['bargain_product_sku_id'])->dec('bargain_stock', $product['total_num'])->update();
                }catch (\Exception $e){
                    log_write('pricedown updateStockSales'. $e->getMessage());
                }
            }
            //修改订单为已购买,砍价成功
            (new TaskModel)->where('bargain_task_id', '=', $product['bill_source_id'])->data([
                'is_buy' => 1,
                'status' => 1
            ])->update();
        }
        return true;
    }

    /**
     * 回退商品库存
     */
    public function backProductStock($productList, $isPayOrder = false)
    {
        $productSkuData = [];
        foreach ($productList as $product) {
            // 未付款订单并且创建时减库存，回退库存
            if (!$isPayOrder && $product['deduct_stock_type'] == DeductStockTypeEnum::CREATE) {
                $point_sku = ProductSkuModel::detail($product['product_source_id']);
                // 回退主库存
                (new ProductModel)->where('bargain_product_id', '=', $point_sku['bargain_product_id'])->inc('stock')->update();
                // 回退sku库存
                (new ProductSkuModel)->where('bargain_product_sku_id', '=', $point_sku['bargain_product_sku_id'])->inc('bargain_stock')->update();
            }
        }
    }

}