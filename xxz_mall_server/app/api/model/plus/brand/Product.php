<?php
declare (strict_types = 1);

namespace app\api\model\plus\brand;

use app\api\model\product\Goods as ProductModel;
use app\common\exception\BaseException;
use app\common\library\helper;
use app\common\model\plugin\agent\Product as AgentProductModel;
use app\common\model\plugin\brand\SignLog;
use app\common\model\settings\Settings as SettingModel;
use think\Model;

/**
 * @mixin \think\Model
 */
class Product extends SignLog
{
    /**
     * 立即购买：获取订单商品列表
     */
    public static function getOrderProductListByBrand($params)
    {
        // 品牌详情
        $seckills = self::detail($params['log_id']);
//        var_dump($seckills->toArray());die;
        if (empty($seckills)) {
            throw new BaseException(['msg' => '品牌商品不存在或已结束']);
        }
        // 品牌商品详情
        $product = ProductModel::detail($seckills['product_id']);
        //品牌商品sku信息
        $point_sku = null;
        if ($product['spec_type'] == 10) {
            $point_sku = $seckills['brandSku'][0];
        } else {
            //多规格
            foreach ($seckills['brandSku'] as $sku) {
                if ($sku['brand_product_sku_id'] == $params['brand_product_sku_id']) {
                    $point_sku = $sku;
                    break;
                }
            }
        }
        if ($point_sku == null) {
            throw new BaseException(['msg' => '品牌商品规格不存在']);
        }

        // 商品sku信息
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        $product['seckill_sku'] = $point_sku;
        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($productList as &$item) {
            // 商品单价
            $product_price = $item['product_sku']['product_price'];
            // 商品购买单价
            $item['product_price'] = $point_sku['brand_price'];
            // 商品购买数量
            $item['total_num'] = $params['product_num'];
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品购买总金额
            $item['total_price'] = $point_sku['brand_price'] * $item['total_num'];
            $item['brand_product_sku_id'] = $point_sku['brand_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
//            $item['product_source_id'] = $point_sku['brand_product_id'];
            // 品牌日id
            $item['brand_day_sign_id'] = $seckills['brandDaysign']['sign_id'];
            $item['sku_source_id'] = $point_sku['brand_product_sku_id'];
            // 品牌最大购买数
            $item['brand_product'] = [
                'buy_num' => $point_sku['buy_num']
            ];
            // 商品pv,开启了分销才计算
            $item['pv'] = 0;
            $item['total_pv'] = 0;
            $agent_product = AgentProductModel::detail($item['product_id'], $item['spec_sku_id']);
            if($agent_product){
                // 按商品比例计算PV
                $pv = $agent_product['pv'];
                if ($product_price > $item['product_price']) {
                    $pv = number_format( $pv*$item['product_price']/$product_price, 2, '.', '');
                }
                $item['pv'] = $pv;
                $item['total_pv'] = helper::bcmul($item['pv'], $params['product_num']);
            }
        }
        $supplierData[] = [
            'shop_supplier_id' => $product['shop_supplier_id'],
            'supplier' => $product['supplier'],
            'productList' => $productList
        ];
        unset($product['supplier']);
        return $supplierData;
    }

}
