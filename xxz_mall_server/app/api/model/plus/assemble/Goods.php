<?php

namespace app\api\model\plus\assemble;

use app\common\exception\BaseException;
use app\common\model\plugin\groupsell\Goods as AssembleProductModel;
use app\api\model\product\Goods as ProductModel;
use app\api\model\supplier\Purveyor as SupplierModel;
use app\api\model\supplier\ServiceApply;
use app\common\model\settings\Settings as SettingModel;
use app\common\model\plugin\agent\Product as AgentProductModel;
use app\common\library\helper;

/**
 * 限时拼团模型
 */
class Goods extends AssembleProductModel
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
     * 获取首页拼团商品显示
     */
    public function getProductList($assemble_activity_id, $limit, $shop_supplier_id = 0)
    {
        $where = ['is_delete' => 0];
        if ($shop_supplier_id > 0) {
            $where['shop_supplier_id'] = $shop_supplier_id;
        }
        // 获取列表数据
        $list = $this->with(['product.image.file', 'assembleSku'])
            ->where('assemble_activity_id', '=', $assemble_activity_id)
            ->where($where)
            ->limit($limit)
            ->visible(['product.product_id','product.product_name','product.file_path'])
            ->select();

        foreach ($list as $key => $product) {
            $assemble_arr = array_column($product['assembleSku']->toArray(), 'assemble_price');
            $product_arr = array_column($product['assembleSku']->toArray(), 'product_price');
            sort($assemble_arr);
            sort($product_arr);
            $product['assemble_price'] = current($assemble_arr);
            $product['product_price'] = current($product_arr);
            $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['assembleSku']);
            unset($product['product']['image']);
        }
        return $list;
    }

    /**
     * 获取列表页拼团数据
     * 目前未分页，后续有可能会分页
     */
    public function getActivityList($assemble_activity_id)
    {
        // 获取列表数据
        $list = $this->with(['product.image.file', 'assembleSku'])
            ->where('assemble_activity_id', '=', $assemble_activity_id)
            ->where('is_delete', '=', 0)
            ->where('status', '=', 10)
            ->visible(['product.product_id','product.product_name','product.file_path'])
            ->select();


        foreach ($list as $product) {
            $assemble_arr = array_column($product['assembleSku']->toArray(), 'assemble_price');
            $product_arr = array_column($product['assembleSku']->toArray(), 'product_price');
            sort($assemble_arr);
            sort($product_arr);
            $product['assemble_price'] =  current($assemble_arr);
            $product['product_price'] =  current($product_arr);
            $product['product']['file_path'] = $product['product']['image'][0]['file_path'];
            unset($product['assembleSku']);
            unset($product['product']['image']);
        }
        return $list;
    }

    /**
     * 获取拼团商品列表
     */
    public static function getAssembleProduct($params)
    {
        // 获取拼团配置
        $config = SettingModel::getItem('assemble');
        // 拼团详情
        $assemble = self::detail($params['assemble_product_id'], ['assembleSku']);

        if (empty($assemble)) {
            throw new BaseException(['msg' => '拼团商品不存在或已结束']);
        }
        // 拼团商品详情
        $product = ProductModel::detail($assemble['product_id']);
        // 拼团商品sku信息
        $assemble_sku = null;
        if ($product['spec_type'] == 10) {
            $assemble_sku = $assemble['assembleSku'][0];
        } else {
            //多规格
            foreach ($assemble['assembleSku'] as $sku) {
                if ($sku['assemble_product_sku_id'] == $params['assemble_product_sku_id']) {
                    $assemble_sku = $sku;
                    break;
                }
            }
        }
        if ($assemble_sku == null) {
            throw new BaseException(['msg' => '拼团商品规格不存在']);
        }

        // 拼团商品sku信息
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        $product['assemble_sku'] = $assemble_sku;
        // 拼团商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        foreach ($productList as &$item) {
            // 商品单价
            $product_price = $item['product_sku']['product_price'];
            // 商品单价
            $item['product_price'] = $assemble_sku['assemble_price'];
            // 商品购买数量
            $item['total_num'] = $params['product_num'];
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品购买总金额
            $item['total_price'] = $assemble_sku['assemble_price'] * $item['total_num'];
            $item['point_num'] = $assemble_sku['point_num'];
            $item['assemble_product_sku_id'] = $assemble_sku['assemble_product_sku_id'];
            $item['product_sku_id'] = $params['product_sku_id'];
            $item['product_source_id'] = $assemble_sku['assemble_product_id'];
            $item['sku_source_id'] = $assemble_sku['assemble_product_sku_id'];
            // 拼团活动id
            $item['activity_id'] = $assemble['assemble_activity_id'];
            // 拼团订单id
            $item['bill_source_id'] = $params['assemble_bill_id'];
            // 拼团最大购买数
            $item['assemble_product'] = [
                'limit_num' => $assemble['limit_num']
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
     * 拼团商品详情
     */
    public function getAssembleDetail($assemble_product_id)
    {
        $result = $this->with(['product'=>['image.file','contentImage.file'], 'assembleSku.productSku.image'])
            ->where('groupsell_goods_id', '=', $assemble_product_id)->find();

        if (!empty($result)) {
            $assemble_arr = array_column($result->toArray()['assembleSku'], 'assemble_price');
            $product_arr = array_column($result->toArray()['assembleSku'], 'product_price');
            sort($assemble_arr);
            sort($product_arr);
            $result['assemble_price'] =  current($assemble_arr);
            $result['line_price'] = current($product_arr);
            if (count($assemble_arr) > 1) {
                $res['assemble_high_price'] = end($assemble_arr);
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
