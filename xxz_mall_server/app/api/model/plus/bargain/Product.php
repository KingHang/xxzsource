<?php

namespace app\api\model\plus\bargain;

use app\common\exception\BaseException;
use app\common\library\helper;
use app\common\model\plugin\pricedown\Product as BargainProductModel;
use app\api\model\product\Goods as ProductModel;
use app\api\model\supplier\Purveyor as SupplierModel;
use app\api\model\supplier\ServiceApply;
use app\common\model\settings\Settings as SettingModel;
use app\common\model\plugin\agent\Product as AgentProductModel;

/**
 * 砍价商品模型
 */
class Product extends BargainProductModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'sales_initial',
        'total_sales',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];
    /**
     * 获取首页砍价商品显示
     */
    public function getProductList($bargain_activity_id, $limit, $shop_supplier_id = 0)
    {
        $where = ['is_delete' => 0];
        if ($shop_supplier_id > 0) {
            $where['shop_supplier_id'] = $shop_supplier_id;
        }
        // 获取列表数据
        $list = $this->with(['product.image.file', 'bargainSku'])
            ->where('bargain_activity_id', '=', $bargain_activity_id)
            ->where($where)
            ->where('status', '=', 10)
            ->limit($limit)
            ->visible(['product.product_id','product.product_name','product.file_path'])
            ->select();

        foreach ($list as $key => $product) {
            $bargain_arr = array_column($product['bargainSku']->toArray(), 'bargain_price');
            $product_arr = array_column($product['bargainSku']->toArray(), 'product_price');
            sort($bargain_arr);
            sort($product_arr);
            $product['bargain_price'] = current($bargain_arr);
            $product['product_price'] = current($product_arr);
            $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['bargainSku']);
            unset($product['product']['image']);
        }
        return $list;
    }

    /**
     * 获取商品列表
     */
    public static function getBargainProduct($params)
    {
        // 获取秒杀配置
        $config = SettingModel::getItem('pricedown');
        // 砍价任务详情
        $bargain = self::detail($params['bargain_product_id'], ['bargainSku']);
        $task = Task::detail($params['bargain_task_id']);
        if (empty($task)) {
            throw new BaseException(['msg' => '任务不存在']);
        }
        if ($task['is_buy'] == 1) {
            throw new BaseException(['msg' => '当前砍价任务商品已购买']);
        }
        if (empty($bargain)) {
            throw new BaseException(['msg' => '商品不存在或已结束']);
        }
        // 商品详情
        $product = ProductModel::detail($bargain['product_id']);

        // 商品sku信息
        $bargain_sku = null;
        if ($product['spec_type'] == 10) {
            $bargain_sku = $bargain['bargainSku'][0];
        } else {
            //多规格
            foreach ($bargain['bargainSku'] as $sku) {
                if ($sku['bargain_product_sku_id'] == $params['bargain_product_sku_id']) {
                    $bargain_sku = $sku;
                    break;
                }
            }
        }
        if ($bargain_sku == null) {
            throw new BaseException(['msg' => '商品规格不存在']);
        }
        // 商品sku信息
        $product['product_sku'] = self::getProductSku($product, $params['product_sku_id']);

        $product['bargain_sku'] = $bargain_sku;
        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($productList as &$item) {
            // 商品单价
            $product_price = $item['product_sku']['product_price'];
            // 商品单价
            $item['product_price'] = $task['actual_price'];
            // 商品购买数量
            $item['total_num'] = 1;
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品购买总金额
            $item['total_price'] = $task['actual_price'];
            $item['bargain_product_sku_id'] = $bargain_sku['bargain_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
            $item['product_source_id'] = $bargain_sku['bargain_product_id'];
            $item['sku_source_id'] = $bargain_sku['bargain_product_sku_id'];
            // 砍价活动id
            $item['activity_id'] = $bargain['bargain_activity_id'];
            // 砍价订单id
            $item['bill_source_id'] = $params['bargain_task_id'];
            // 商品pv,开启了分销才计算
            $item['pv'] = 0;
            $item['total_pv'] = 0;
            if($config['is_agent']){
                $agent_product = AgentProductModel::detail($item['product_id'], $item['spec_sku_id']);
                if($agent_product){
                    // 按商品比例计算PV
                    $pv = $agent_product['pv'];
                    if ($product_price > $item['product_price']) {
                        $pv = number_format( $pv*$item['product_price']/$product_price, 2, '.', '');
                    }
                    $item['pv'] = $pv;
                    $item['total_pv'] = helper::bcmul($item['pv'], !isset($params['product_num']) ? 1 : $params['product_num'] );
                }
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


    /**
     * 指定的商品规格信息
     */
    private static function getProductSku($product, $product_sku_id)
    {
        // 获取指定的sku
        $productSku = [];
        foreach ($product['sku'] as $item) {
            if ($item['product_sku_id'] == $product_sku_id) {
                $productSku = $item;
                break;
            }
        }
        if (empty($productSku)) {
            return false;
        }
        // 多规格文字内容
        $productSku['product_attr'] = '';
        if ($product['spec_type'] == 20) {
            $specRelData = helper::arrayColumn2Key($product['spec_rel'], 'spec_value_id');
            $attrs = explode('_', $productSku['spec_sku_id']);
            foreach ($attrs as $specValueId) {
                $productSku['product_attr'] .= $specRelData[$specValueId]['spec']['spec_name'] . ':'
                    . $specRelData[$specValueId]['spec_value'] . '; ';
            }
        }
        return $productSku;
    }

    /**
     * 获取列表页砍价数据
     * 目前未分页，后续有可能会分页
     */
    public function getActivityList($bargain_activity_id)
    {
        // 获取列表数据
        $list = $this->with(['product.image.file', 'bargainSku'])
            ->where('bargain_activity_id', '=', $bargain_activity_id)
            ->where('is_delete', '=', 0)
            ->visible(['product.product_id','product.product_name','product.file_path'])
            ->select();

        foreach ($list as $product) {
            $bargain_arr = array_column($product['bargainSku']->toArray(), 'bargain_price');
            $product_arr = array_column($product['bargainSku']->toArray(), 'product_price');
            sort($bargain_arr);
            sort($product_arr);
            $product['bargain_price'] =  current($bargain_arr);
            $product['product_price'] =  current($product_arr);
            $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['bargainSku']);
            unset($product['product']['image']);
        }
        return $list;
    }


    /**
     * 拼团商品详情
     */
    public function getBargainDetail($bargain_product_id)
    {

        $result = $this->with(['product'=>['image.file','contentImage.file'], 'bargainSku.productSku.image'])
            ->where('bargain_product_id', '=', $bargain_product_id)->find();

        if (!empty($result)) {
            $bargain_arr = array_column($result->toArray()['bargainSku'], 'bargain_price');
            $product_arr = array_column($result->toArray()['bargainSku'], 'product_price');
            sort($bargain_arr);
            sort($product_arr);
            $result['bargain_price'] =  current($bargain_arr);
            $result['line_price'] = current($product_arr);
            if (count($bargain_arr) > 1) {
                $result['bargain_high_price'] = end($bargain_arr);
                $result['line_high_price'] = end($product_arr);
            }
            $SupplierModel =new SupplierModel;
        if($result['shop_supplier_id']){
            $supplier = $SupplierModel::detail($result['shop_supplier_id'],['logo', 'category']);
            $supplier['logos'] = $supplier['logo']['file_path'];unset($supplier['logo']);
            $supplier['category_name'] = $supplier['category']['name'];unset($supplier['category']);
            $supplier->visible(['logos', 'category_name', 'name', 'shop_supplier_id', 'product_sales', 'server_score','store_type']);
            $server = (new ServiceApply())->getList($result['shop_supplier_id']);
        }else{
            $supplier = [];
            $server = [];
        }
            $result['supplier'] = $supplier;
            $result['server'] = $server;
        }
        return $result;
    }
}