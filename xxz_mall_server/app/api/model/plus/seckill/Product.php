<?php

namespace app\api\model\plus\seckill;

use app\common\exception\BaseException;
use app\common\model\plugin\flashsell\Product as SeckillProductModel;
use app\api\model\product\Goods as ProductModel;
use app\api\model\supplier\Purveyor as SupplierModel;
use app\api\model\supplier\ServiceApply;
use app\common\model\settings\Settings as SettingModel;
use app\common\model\plugin\agent\Product as AgentProductModel;
use app\common\library\helper;

/**
 * 限时秒杀模型
 */
class Product extends SeckillProductModel
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
     * 获取商品列表（用于订单结算）
     */
    public static function getSeckillProduct($params)
    {
        // 获取秒杀配置
        $config = SettingModel::getItem('seckill');
        // 秒杀任务详情
        $seckills = self::detail($params['seckill_product_id'], ['seckillSku']);

        if (empty($seckills)) {
            throw new BaseException(['msg' => '秒杀商品不存在或已结束']);
        }
        // 秒杀商品详情
        $product = ProductModel::detail($seckills['product_id'], ['seckillSku']);

        // 积分商品sku信息
        $point_sku = null;
        if ($product['spec_type'] == 10) {
            $point_sku = $seckills['seckillSku'][0];
        } else {
            //多规格
            foreach ($seckills['seckillSku'] as $sku) {
                if ($sku['seckill_product_sku_id'] == $params['seckill_product_sku_id']) {
                    $point_sku = $sku;
                    break;
                }
            }
        }
        if ($point_sku == null) {
            throw new BaseException(['msg' => '秒杀商品规格不存在']);
        }

        // 商品sku信息
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        $product['seckill_sku'] = $point_sku;
        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($productList as &$item) {
            // 商品单价
            $product_price = $item['product_sku']['product_price'];
            // 商品秒杀单价
            $item['product_price'] = $point_sku['seckill_price'];
            // 商品购买数量
            $item['total_num'] = $params['product_num'];
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品购买总金额
            $item['total_price'] = $point_sku['seckill_price'] * $item['total_num'];
            $item['seckill_product_sku_id'] = $point_sku['seckill_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
            $item['product_source_id'] = $point_sku['seckill_product_id'];
            // 秒杀活动id
            $item['activity_id'] = $seckills['seckill_activity_id'];
            $item['sku_source_id'] = $point_sku['seckill_product_sku_id'];
            // 秒杀最大购买数
            $item['seckill_product'] = [
                'limit_num' => $seckills['limit_num']
            ];
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
                    $item['total_pv'] = helper::bcmul($item['pv'], $params['product_num']);
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
     * 获取首页秒杀商品显示
     */
    public function getProductList($seckill_activity_id, $limit, $shop_supplier_id = 0)
    {
        $where = ['is_delete' => 0];
        if ($shop_supplier_id > 0) {
            $where['shop_supplier_id'] = $shop_supplier_id;
        }
        // 获取列表数据
        $list = $this->with(['product.image.file', 'seckillSku'])
            ->where('seckill_activity_id', '=', $seckill_activity_id)
            ->where('status', '=', 10)
            ->where($where)
            ->limit($limit)
            ->visible(['product.product_id','product.product_name','product.file_path'])
            ->select();

        foreach ($list as $product) {
            $seckill_arr = array_column($product['seckillSku']->toArray(), 'seckill_price');
            $product_arr = array_column($product['seckillSku']->toArray(), 'product_price');
            sort($seckill_arr);
            sort($product_arr);
            $product['seckill_price'] =  current($seckill_arr);
            $product['product_price'] =  current($product_arr);
            $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['seckillSku']);
            unset($product['product']['image']);
        }
        return $list;
    }

    /**
     * 列表页秒杀商品
     * 目前未分页，后续有可能会分页
     */
    public function getActivityList($seckill_activity_id){
        // 获取列表数据
        $list = $this->with(['product.image.file', 'seckillSku'])
            ->where('seckill_activity_id', '=', $seckill_activity_id)
            ->where('is_delete', '=', 0)
            ->visible(['product.product_id','product.product_name','product.file_path'])
            ->select();

        foreach ($list as $key => $product) {
            $seckill_arr = array_column($product['seckillSku']->toArray(), 'seckill_price');
            $product_arr = array_column($product['seckillSku']->toArray(), 'product_price');
            sort($seckill_arr);
            sort($product_arr);
            $product['seckill_price'] =  current($seckill_arr);
            $product['product_price'] =  current($product_arr);
            $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['seckillSku']);
            unset($product['product']['image']);

        }
        return $list;
    }

    public function getSeckillDetail($seckill_product_id)
    {
        $result = $this->with(['product'=>['image.file','contentImage.file'], 'seckillSku.productSku.image'])
            ->where('seckill_product_id', '=', $seckill_product_id)->find();

        if (!empty($result)) {
            $seckill_arr = array_column($result->toArray()['seckillSku'], 'seckill_price');
            $product_arr = array_column($result->toArray()['seckillSku'], 'product_price');
            sort($seckill_arr);
            sort($product_arr);
            $result['seckill_price'] =  current($seckill_arr);
            $result['line_price'] = current($product_arr);
            if (count($seckill_arr) > 1) {
                $res['seckill_high_price'] = end($seckill_arr);
                $res['line_high_price'] = end($product_arr);
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
