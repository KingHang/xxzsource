<?php

namespace app\api\model\server;

use app\common\model\server\Server as ServerModel;
use app\common\model\facerecognition\ServiceMid;
use app\common\library\helper;
use app\common\model\purveyor\Purveyor as SupplierModel;
use app\common\model\store\Store AS StoreModel;

/**
 * 服务模型
 * Class Server
 * @package app\faceRecognition\model\server
 */
class Server extends ServerModel
{
    /**
     * 关联商品规格关系表
     */
    public function ApiserverMid()
    {
        return $this->hasMany('app\\common\\model\\facerecognition\\ServiceMid', 'server_id','server_id')->order(['id' => 'asc']);
    }


    /**
     * 获取商品列表
     */
    public function getList($param, $userInfo = false)
    {
        if (!isset($param['store_id']) || empty($param['store_id'])) {
            $this->error = '请选择门店';
            return false;
        }
        // 获取商品列表
        $data =$this->getServerList($param);

        // 隐藏api属性
        !$data->isEmpty() && $data->hidden(['category', 'content', 'image','sku']);
        // 整理列表数据并返回
        return $data;
    }
    /**
     * 获取商品列表
     */
    public function getServerList($params)
    {
        $where = array(
            'product.is_delete' => 0,
            'product.is_disabled'   => 1,
            'product.status'    => 10,
            'product.app_id'    =>  self::$app_id,
            'product.is_on_shelf_store' => array(1,2)
        );

        $bindsql = (new ServiceMid())->alias('ServerMid')->field('ServerMid.id')
            ->where(array('ServerMid.store_id' => $params['store_id'] , 'ServerMid.mid_is_disabled' => 1 ))->fetchSql()->count();
        $model = self::alias('product')->where($where)->whereRaw('(product.is_on_shelf_store = 1 or (product.is_on_shelf_store = 2 AND (' . $bindsql . ' AND product.server_id = ServerMid.server_id) > 0))');

        if (!empty($params['category_id'])) {
            $model->where('product.category_id','=',$params['category_id']);
        }
        if (!empty($params['search'])) {
            $model->where('product.server_name', 'like',   "%" . strtolower($params['search']) . "%");
        }
        $sort = ['product_sales' => 'desc'];
        // 执行查询
        $list = $model->alias('product')
            ->field(['product.*', 'sales_actual as product_sales',
                'product.server_name as product_name , server_no as product_no,server_price as product_price,server_min as server_time,sort as product_sort'
            ])
            ->with(['category', 'image.file', 'sku', 'supplier','ApiserverMid.midStore'])
            ->join('supplier supplier', 'product.shop_supplier_id = supplier.shop_supplier_id', 'left')
            ->where('supplier.is_delete', '=', 0)
            ->where('supplier.status', '=', 0)
            ->where('supplier.is_recycle', '=', 0)
            ->order($sort)
            ->paginate($params);
        // 整理列表数据并返回
        return $this->setServerListData($list, true);
    }

    /**
     * 设置服务展示的数据
     * @param $data
     * @param bool $isMultiple
     * @param callable|null $callback
     * @return mixed
     */
    protected function setServerListData($data, $isMultiple = true, callable $callback = null , $params = [])
    {
        if (!$isMultiple) $dataSource = [&$data]; else $dataSource = &$data;
        // 整理服务列表数据
        foreach ($dataSource as &$server) {
            // 商品主图
            $server['server_image'] = $server['image'][0]['file_path'];
            // 服务默认规格
            $server['server_sku'] = self::getShowSku($server);
            $server['server_min_price'] = $server['server_price'];
            $server['server_max_price'] = $server['server_price'];
            if (!empty($server['sku'])) {
                $price = helper::getArraySize($server['sku']->toArray(),'server_price');
                $server['server_min_price'] = $price['min'];
                $server['server_max_price'] = $price['max'];
            }
            $server['store'] = $this->setStoreList($server['ApiserverMid'],$params);

            unset($server['ApiserverMid']);
            // 回调函数
            is_callable($callback) && call_user_func($callback, $server);
        }
        return $data;
    }

    public function setStoreList($ApiserverMid,$params = [])
    {
        // 处理门店列表
        $store = array();
        if (!empty($ApiserverMid)) {
            foreach ($ApiserverMid as &$item){
                if (isset($item['midStore']) && !empty($item['midStore'])) {
                    $store[] = $item['midStore']->toArray();
                }
            }
        }
        if (!empty($params['longitude']) && !empty($params['latitude'])) {
            $store = (new StoreModel())->sortByDistance($store, $params['longitude'], $params['latitude'],1);
        }
        return $store;
    }
    /**
     * 显示的sku，目前取最低价
     * @param $server
     * @return mixed
     */
    public static function getShowSku($server)
    {
        //如果是单规格
        if ($server['spec_type'] == 10) {
            return $server['sku'][0];
        } else {
            //多规格返回最低价
            foreach ($server['sku'] as $sku) {
                if ($server['server_price'] == $sku['server_price']) {
                    return $sku;
                }
            }
        }
        // 如果找不到返回第一个
        return $server['sku'][0];
    }

    public function getDetails($server_id)
    {
        $where = array(
            'is_delete' => 0,
            'is_disabled'   => 1,
            'status'    => 10,
            'app_id'    => self::$app_id,
        );

        // 获取商品详情
        $detail = $this->field(['*', 'sales_actual as product_sales',
            'server_name as product_name , server_no as product_no,server_price as product_price,server_min as server_time,sort as product_sort'
        ])->where($where)->with([
            'image' => ['file'],
            'sku' => ['image'],
            'spec_rel' => ['spec'],
            'ApiserverMid.midStore'
        ])->where('server_id', '=', $server_id)
            ->find();
        // 判断商品的状态
        if (empty($detail)) {
            $this->error = '很抱歉，商品信息不存在或已下架';
            return false;
        }
        // 多规格商品sku信息
        $detail['product_multi_spec'] = null;
        if ($detail['spec_type'] == 20) {
            $detail['product_multi_spec'] = $this->getManySpecData($detail['spec_rel'], $detail['sku']);
        }
        $detail['store_list'] = $this->setStoreList($detail);
        unset($detail['ApiserverMid']);
        return $detail;
    }

    /**
     * 商品多规格信息
     */
    public function getManySpecData($specRel, $skuData)
    {
        // spec_attr
        $specAttrData = [];
        foreach ($specRel as $item) {
            if (!isset($specAttrData[$item['spec_id']])) {
                $specAttrData[$item['spec_id']] = [
                    'group_id' => $item['spec']['spec_id'],
                    'group_name' => $item['spec']['spec_name'],
                    'spec_items' => [],
                ];
            }
            $specAttrData[$item['spec_id']]['spec_items'][] = [
                'item_id' => $item['spec_value_id'],
                'spec_value' => $item['spec_value'],
            ];
        }
        // spec_list
        $specListData = [];
        foreach ($skuData as $item) {
            $image = (isset($item['image']) && !empty($item['image'])) ? $item['image'] : ['file_id' => 0, 'file_path' => ''];
            $specListData[] = [
                'server_sku_id' => $item['server_sku_id'],
                'spec_sku_id' => $item['spec_sku_id'],
                'rows' => [],
                'spec_form' => [
                    'image_id' => $image['file_id'],
                    'image_path' => $image['file_path'],
                    'server_no' => $item['server_no'],
                    'server_price' => $item['server_price'],
                    'product_no' => $item['server_no'],
                    'product_price' => $item['server_price'],
                    'server_weight' => $item['server_weight'],
                    'line_price' => $item['line_price'],
                    'stock_num' => $item['stock_num'],
                    'supplier_price' => $item['supplier_price'],
                ],
            ];
        }
        return ['spec_attr' => array_values($specAttrData), 'spec_list' => $specListData];
    }

    /**
     * 合并服务
     * @param $server_list
     * @return array
     */
    public function mergeServerList($server_list)
    {
        $list = [];
        $server_list_arr = [];
        if (!empty($server_list)) {
            foreach ($server_list as $item) {
                $key = $item['server_id'] . '_' . $item['server_sku_id'];
                if (isset($server_list_arr[$key])) {
                    $server_list_arr[$key]['total_num'] += $item['total_num'];
                } else {
                    $server_list_arr[$key] = $item;
                }
            }
        }
        return $server_list_arr;
    }
    /**
     * 获取服务列表
     */
    public function getSeverList($server_list,$params)
    {
        // 商品列表
        $serverList = [];
        if (empty($server_list)) {
            return $serverList;
        }
//        $server_list = $this->mergeServerList($server_list);
        // 购物车中所有商品id集
        $serverIds = array_unique(helper::getArrayColumn($server_list, 'server_id'));
        // 获取并格式化商品数据
        $sourceData = $this->getListByIds($serverIds,$params);

        $sourceData = helper::arrayColumn2Key($sourceData, 'server_id');
        // 供应商信息
        $supplierData = [];

        // 格式化购物车数据列表
        foreach ($server_list as $key => $item) {
            // 判断商品不存在则自动删除
            if (!isset($sourceData[$item['server_id']])) {
                unset($server_list[$key]);
                continue;
            }
            // 商品信息
            $server = clone $sourceData[$item['server_id']];
            // 判断商品是否已删除
            if ($server['is_delete']) {
                unset($server_list[$key]);
                continue;
            }
            // 商品sku信息
            $sku_arr = self::getServerSkuById($server, $item['server_sku_id']);
            $server['server_sku'] = $sku_arr['serverSku'];
            $server['image_file'] = $sku_arr['image_file'] ? $sku_arr['image_file'] : (isset($server['image'][0]['file_path']) ? $server['image'][0]['file_path'] : '');
            $server['image_id'] = $sku_arr['image_id'] ? $sku_arr['image_id'] : (isset($server['image'][0]['image_id']) ? $server['image'][0]['image_id'] : '');

            $server['server_sku_id'] = $item['server_sku_id'];
            $server['spec_sku_id'] = $server['server_sku']['spec_sku_id'];

            // 商品sku不存在则自动删除
            if (empty($server['server_sku'])) {
                unset($server_list[$key]);
                continue;
            }
            // 获取支持门店集合
            $store_ids = [];
            if ($server['is_on_shelf_store'] == 1) {
                // 全部门店
                $store_ids = (new StoreModel())->getStoreIds(true);
            } elseif ($server['is_on_shelf_store'] == 2) {
                // 指定门店
                $store_ids = (new ServiceMid())->getStoreIds($server['server_id']);
            }
            $server['store_ids'] = !empty($store_ids) ? implode(',',$store_ids) : '';
            // 商品单价
            $server['product_price'] = $server['server_sku']['server_price'];
            $server['server_price'] = $server['server_sku']['server_price'];
            // 购买数量
            $server['total_num'] = $item['total_num'];
            // 商品总价
            $server['total_price'] = bcmul($server['product_price'], $item['total_num'], 2);
            // 供应商
            $server['shop_supplier_id'] = $item['shop_supplier_id'];
            $server['supplier_price'] = bcmul($server['supplier_price'], $item['total_num'], 2);
            $serverList[] = $server->hidden(['category', 'content', 'image']);
        }
        $supplierIds = array_unique(helper::getArrayColumn($serverList, 'shop_supplier_id'));
        foreach ($supplierIds as $supplierId) {
            $supplierData[] = [
                'shop_supplier_id' => $supplierId,
                'supplier' => SupplierModel::detail($supplierId),
                'serverList' => $this->getServerBySupplier($supplierId, $serverList)
            ];
        }
        return $supplierData;
    }
    /**
     * 按供应商归属服务
     * @param $supplierId
     * @param $productList
     * @return array
     */
    private function getServerBySupplier($supplierId, $productList){
        $result = [];
        foreach ($productList as $product){
            if($product['shop_supplier_id'] == $supplierId){
                array_push($result, $product);
            }
        }
        return $result;
    }
    /**
     * 根据商品id集获取商品列表
     */
    public function getListByIds($serverIds,$params = [])
    {
        $model = $this;
        $filter = [];
        // 筛选条件
        $where = array(
            'is_delete' => 0,
            'is_disabled'   => 1,
            'status'    => 10,
            'app_id'    => self::$app_id,
        );
        if (!empty($serverIds)) {
            $model = $model->orderRaw('field(server_id, ' . implode(',', $serverIds) . ')');
        }
        // 获取商品列表数据
        $data = $model->where($where)->with([ 'image.file', 'sku','ApiserverMid.midStore'])
            ->where($filter)
            ->where('server_id', 'in', $serverIds)
            ->select();

        // 整理列表数据并返回
        return $this->setServerListData($data, true,null,$params);
    }
}