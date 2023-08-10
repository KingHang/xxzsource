<?php

namespace app\api\model\home;

use app\common\model\home\HomePurveyor as PageSupplierModel;
use app\api\model\goods\Goods as ProductModel;
use app\api\model\goods\Category as CategoryModel;
use app\api\model\plugin\flashsell\Goods as SeckillProductModel;
use app\api\model\plugin\flashsell\Active as SeckillActiveModel;
use app\api\model\plugin\groupsell\Goods as AssembleProductModel;
use app\api\model\plugin\groupsell\Active as AssembleActiveModel;
use app\api\model\plugin\pricedown\Goods as BargainProductModel;
use app\api\model\plugin\pricedown\Active as BargainActiveModel;
use app\api\model\plugin\voucher\Voucher;

/**
 * app分类页模板模型
 */
class HomePurveyor extends PageSupplierModel
{
    /**
     * DIY页面详情
     */
    public static function getPageData($user, $shop_supplier_id)
    {
        // 页面详情
        $detail = parent::getHomePage($shop_supplier_id);
        if ($detail['setting_type'] != 1) {
            return  [];
        }
        // 页面diy元素
        $items = $detail['page_data']['items'];
        // 页面顶部导航
        isset($detail['page_data']['page']) && $items['page'] = $detail['page_data']['page'];

        // 获取动态数据
        $model = new self;

        foreach ($items as $key => $item) {
            unset($items[$key]['defaultData']);
            if ($item['type'] === 'window') {
                $items[$key]['data'] = array_values($item['data']);
            } else if ($item['type'] === 'product') {
                $items[$key]['data'] = $model->getProductList($user, $item,$shop_supplier_id);
            } else if ($item['type'] === 'category') {
                $items[$key]['data'] = $model->getCategoryList($item);
                $items[$key]['list'] = $model->getCategoryProductList($user, $item, 0,$shop_supplier_id);
            } else if ($item['type'] === 'coupon') {
                $items[$key]['data'] = $model->getCouponList($user, $item,$shop_supplier_id);
            } else if ($item['type'] === 'news') {
                $items[$key]['data'] = $model->getArticleList($item);
            } else if ($item['type'] === 'special') {
                $items[$key]['data'] = $model->getSpecialList($item);
            } else if ($item['type'] === 'seckillProduct') {
                // 如果没有活动，则不显示
                $item_data = $model->getSeckillList($item,$shop_supplier_id);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'assembleProduct') {
                // 如果没有活动，则不显示
                $item_data = $model->getAssembleList($item,$shop_supplier_id);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'bargainProduct') {
                // 如果没有活动，则不显示
                $item_data = $model->getBargainList($item,$shop_supplier_id);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'live') {
                $items[$key]['data'] = $model->getLiveList($item);
            } else if ($item['type'] === 'bonusProduct') {
                // 如果没有活动，则不显示
                $item_data = $model->getBonusList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            }
        }
        return ['page' => $items['page'], 'items' => $items];
    }
    /**
     * 商品分类组件：获取商品列表
     * @param $user
     * @param $item
     * @param $category_id
     * @return array
     */
    private function getCategoryProductList($user, $item, $category_id,$shop_supplier_id)
    {
        // 获取商品数据
        $model = new ProductModel;
        $productList = $model->getList([
            'type' => 'sell',
            'category_id' => $category_id,
            'sortType' => $item['params']['productSort'],
            'list_rows' => $item['params']['showNum'],
            'audit_status' => 10,
            'shop_supplier_id' => $shop_supplier_id
        ], $user);

        if ($productList->isEmpty()) return [];

        // 格式化商品列表
        $data = [];

        foreach ($productList as $product) {
            $show_sku = ProductModel::getShowSku($product);
            $data[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'selling_point' => $product['selling_point'],
                'image' => $product['image'][0]['file_path'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $show_sku['product_price'],
                'line_price' => $show_sku['line_price'],
                'product_sales' => $product['product_sales'],
            ];
        }

        return $data;
    }
    /**
     * 商品组件：获取商品列表
     */
    private function getProductList($user, $item, $shop_supplier_id)
    {
        // 获取商品数据
        $model = new ProductModel;
        if ($item['params']['source'] === 'choice') {
            // 数据来源：手动
            $productIds = array_column($item['data'], 'product_id');
            $productList = $model->getListByIdsFromApi($productIds, $user);
        } else {
            // 数据来源：自动
            $productList = $model->getList([
                'type' => 'sell',
                'category_id' => $item['params']['auto']['category'],
                'sortType' => $item['params']['auto']['productSort'],
                'list_rows' => $item['params']['auto']['showNum'],
                'audit_status' => 10,
                'shop_supplier_id' => $shop_supplier_id
            ], $user);
        }
        if ($productList->isEmpty()) return [];
        // 格式化商品列表
        $data = [];
        foreach ($productList as $product) {
            $show_sku = ProductModel::getShowSku($product);
            $data[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'selling_point' => $product['selling_point'],
                'image' => $product['image'][0]['file_path'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $show_sku['product_price'],
                'line_price' => $show_sku['line_price'],
                'product_sales' => $product['product_sales'],
            ];
        }
        return $data;
    }

    /**
     * 商品分类组件：获取商品分类列表
     * @param $item
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    private function getCategoryList($item)
    {
        // 获取商品分类数据
        if ($item['params']['source'] === 'choice') {
            // 数据来源：手动
            $categoryIds = array_column($item['data'], 'category_id');
            $categoryList = (new CategoryModel)->getFirstCategoryByIds($categoryIds);
        } else {
            // 数据来源：自动
            $categoryList = CategoryModel::getFirstCategory();
        }

        if ($categoryList->isEmpty()) return [];

        // 格式化商品分类列表
        $data = [['category_id' => 0, 'name' => '全部']];

        foreach ($categoryList as $category) {
            $data[] = [
                'category_id' => $category['category_id'],
                'name' => $category['name']
            ];
        }
        return $data;
    }
    /**
     * 优惠券组件：获取优惠券列表
     */
    private function getCouponList($user, $item, $shop_supplier_id)
    {
        // 获取优惠券数据
        return (new Voucher)->getList($user, $item['params']['limit'], true,0, $shop_supplier_id);
    }

    /**
     * 获取限时秒杀
     */
    private function getSeckillList($item,$shop_supplier_id)
    {
        // 获取秒杀数据
        $seckill = SeckillActiveModel::getActive();
        if ($seckill) {
            $product_model = new SeckillProductModel;
            $seckill['product_list'] = $product_model->getProductList($seckill['flashsell_activity_id'], $item['params']['showNum'],$shop_supplier_id)->toArray();
            if (empty($seckill['product_list'])) {
                $seckill = [];
            }
        }
        return $seckill;
    }

    /**
     * 获取限时拼团
     */
    private function getAssembleList($item,$shop_supplier_id)
    {
        // 获取拼团数据
        $assemble = AssembleActiveModel::getActive();
        if ($assemble) {
            $product_model = new AssembleProductModel;
            $product_list = $product_model->getProductList($assemble['groupsell_activity_id'], $item['params']['showNum'],$shop_supplier_id)->toArray();
            if (empty($product_list)) {
                $assemble = [];
            }
            $assemble['product_list'] = $product_list;
        }
        return $assemble;
    }
    /**
     * 获取限时砍价
     */
    private function getBargainList($item,$shop_supplier_id)
    {
        // 获取拼团数据
        $bargain = BargainActiveModel::getActive();
        if ($bargain) {
            $product_model = new BargainProductModel;
            $bargain['product_list'] = $product_model->getProductList($bargain['pricedown_activity_id'], $item['params']['showNum'],$shop_supplier_id)->toArray();
            if (empty($bargain['product_list'])) {
                $bargain = [];
            }
        }
        return $bargain;
    }
}
