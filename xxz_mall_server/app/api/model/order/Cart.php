<?php

namespace app\api\model\order;

use app\api\model\order\Order as OrderModel;
use think\facade\Cache;
use app\api\model\goods\Goods as ProductModel;
use app\api\model\goods\GoodsSku as ProductSkuModel;
use app\common\library\helper;
use app\common\model\store\Store AS StoreModel;

/**
 * 购物车管理
 */
class Cart
{
    // 错误信息
    public $error = '';
    //用户
    private $user;
    // 用户id
    private $user_id;
    // 购物车列表
    private static $cart = [];
    // $clear 是否清空购物车
    private $clear = false;

    /**
     * 构造方法
     */
    public function __construct($user)
    {   
        $this->user = $user;
        $this->user_id = $user['user_id'];
        static::$cart = Cache::get('cart_' . $this->user_id) ?: [];
    }

    /**
     * 购物车列表 (含商品信息)
     */
    public function getList($cartIds = null, $type = 0 ,$is_show_gift = 0)
    {
        // 获取购物车商品列表
        return $this->getOrderProductList($cartIds, $type ,$is_show_gift);
    }

    /**
     * 获取购物车列表
     */
    public function getCartList($cartIds = null)
    {
        if (empty($cartIds)) return static::$cart;
        $cartList = [];
        $indexArr = (strpos($cartIds, ',') !== false) ? explode(',', $cartIds) : [$cartIds];
        foreach ($indexArr as $index) {
            isset(static::$cart[$index]) && $cartList[$index] = static::$cart[$index];
        }
        return $cartList;
    }

    /**
     * 获取购物车中的商品列表
     */
    public function getAgentOrderProductList($product_list,$data = [])
    {
        // 商品列表
        $productList = [];
        if (empty($product_list)) {
            return $productList;
        }
//        $product_list = $this->mergeProductList($product_list);
        // 购物车中所有商品id集
        $productIds = array_unique(helper::getArrayColumn($product_list, 'product_id'));
        // 获取并格式化商品数据
        $sourceData = (new ProductModel)->getListByIds($productIds, null);
        $sourceData = helper::arrayColumn2Key($sourceData, 'product_id');
        // 供应商信息
        $supplierData = [];
        // 格式化购物车数据列表
        foreach ($product_list as $key => $item) {
            // 判断商品不存在则自动删除
            if (!isset($sourceData[$item['product_id']])) {
                unset($product_list[$key]);
                continue;
            }
            // 商品信息
            $product = clone $sourceData[$item['product_id']];
            // 判断商品是否已删除
            if ($product['is_delete']) {
                unset($product_list[$key]);
                continue;
            }
            // 商品sku信息
            $product['product_sku'] = ProductModel::getProductSku($product, $item['product_sku_id']);
            $product['product_sku_id'] = $item['product_sku_id'];
            $product['spec_sku_id'] = $product['product_sku']['spec_sku_id'];
            // 商品sku不存在则自动删除
            if (empty($product['product_sku'])) {
                unset($product_list[$key]);
                continue;
            }
            // 获取门店距离
            if (!empty($data['longitude']) && !empty($data['latitude'])) {
                $product['store_list'] = (new StoreModel())->sortByDistance($product['store_list'], $data['longitude'], $data['latitude']);
            }
            // 商品单价
            $product['product_price'] = $product['product_sku']['product_price'];
            $product['count_times'] = $product['verify_num'];
            // 购买数量
            $product['total_num'] = $item['total_num'];
            // 商品总价
            $product['total_price'] = bcmul($product['product_price'], $item['total_num'], 2);
            // 供应商
            $product['shop_supplier_id'] = $item['shop_supplier_id'];
            $product['supplier_price'] = bcmul($product['supplier_price'], $item['total_num'], 2);
            $product['total_pv'] = 0;
            $productList[] = $product->hidden(['category', 'content', 'image']);
        }
        $supplierIds = array_unique(helper::getArrayColumn($productList, 'shop_supplier_id'));
        foreach ($supplierIds as $supplierId) {
            $supplierData[] = [
                'shop_supplier_id' => $supplierId,
                'supplier' => SupplierModel::detail($supplierId),
                'productList' => $this->getProductBySupplier($supplierId, $productList)
            ];
        }
        return $supplierData;
    }
    /**
     * 获取购物车中的商品列表
     */
    public function getOrderProductList($cartIds, $type = 0 ,$is_show_gift = 0)
    {
        // 购物车商品列表
        $productList = [];
        // 获取购物车列表
        $cartList = $this->getCartList($cartIds);
        if (empty($cartList)) {
            $this->setError('当前购物车没有商品');
            return $productList;
        }
        // 购物车中所有商品id集
        $productIds = array_unique(helper::getArrayColumn($cartList, 'product_id'));
        // 获取并格式化商品数据
        $sourceData = (new ProductModel)->getListByIds($productIds, null);
        $sourceData = helper::arrayColumn2Key($sourceData, 'product_id');
        // 供应商信息
        $supplierData = [];
        // 格式化购物车数据列表
        foreach ($cartList as $key => $item) {
            // 判断商品不存在则自动删除
            if (!isset($sourceData[$item['product_id']])) {
                $this->delete($key);
                continue;
            }
            // 商品信息
            $product = clone $sourceData[$item['product_id']];
            // 判断商品是否已删除
            if ($product['is_delete']) {
                $this->delete($key);
                continue;
            }
            // 重置商品sku
            if ($type) {
                $product['sku'] = ProductSkuModel::getSkuList($product['product_id']);
            }

            // 商品sku信息
            $product['product_sku'] = ProductModel::getProductSku($product, $item['product_sku_id']);
            $product['product_sku_id'] = $item['product_sku_id'];
            $product['spec_sku_id'] = $product['product_sku']['spec_sku_id'];
            // 商品sku不存在则自动删除
            if (empty($product['product_sku'])) {
                $this->delete($key);
                continue;
            }
            // 商品单价
            $product['product_price'] = $product['product_sku']['product_price'];

            // 设置商品展示的数据
            if ($type) {
                (new ProductModel)->setProductGradeMoney($this->user, $product);
            }

            // 购买数量
            $product['total_num'] = $item['product_num'];
            // 商品总价
            $product['total_price'] = bcmul($product['product_price'], $item['product_num'], 2);
            // 供应商
            $product['shop_supplier_id'] = $item['shop_supplier_id'];
            $product['supplier_price'] = bcmul($product['supplier_price'], $item['product_num'], 2);

            // 商品pv,开启了分销才计算
            $product['pv'] = 0;
            $product['total_pv'] = 0;

            $productList[] = $product->hidden(['category', 'content', 'image']);
            if ($is_show_gift ==1)
            {
                if ($product['is_open_gift']){
                    $productList = (new Order())::getGiftProduct($product['product_id'],$product['product_sku_id'],$productList,$product['total_num']);
                }
                foreach ($productList as &$arr) {
                    if (isset($arr['gift_status']) && $arr['gift_status']==1)
                    {
                        $arr['total_num'] = $arr['gift_num'] * intval($arr['total_num']);
                        $arr['total_price'] = 0;
                    }
                }
            }
        }
        $supplierIds = array_unique(helper::getArrayColumn($productList, 'shop_supplier_id'));
        foreach($supplierIds as $supplierId){
            $supplierData[] = [
                'shop_supplier_id' => $supplierId,
                'supplier' => SupplierModel::detail($supplierId),
                'productList' => $this->getProductBySupplier($supplierId, $productList)
            ];
        }
        return $supplierData;
    }

    /**
     * 获取购物车中的商品列表
     */
    public function getAgentOrderTypeList($product_list)
    {
        // 购物车中所有商品id集
        $productIds = array_unique(helper::getArrayColumn($product_list, 'product_id'));
        // 获取并格式化商品数据
        $list = (new ProductModel)->where('product_id', 'in', $productIds)
            ->where('is_delete', '=', 0)
            ->group('product_type')
            ->column('product_type');
        return $list;
    }
    private function getProductBySupplier($supplierId, $productList){
        $result = [];
        foreach ($productList as $product){
            if($product['shop_supplier_id'] == $supplierId){
                array_push($result, $product);
            }
        }
        return $result;
    }

    /**
     * 加入购物车
     */
    public function add($productId, $productNum, $productSkuId)
    {
        // 购物车商品索引
        $index = "{$productId}_{$productSkuId}";
        // 加入购物车后的商品数量
        $cartProductNum = $productNum + (isset(static::$cart[$index]) ? static::$cart[$index]['product_num'] : 0);
        // 获取商品信息
        $product = ProductModel::detail($productId);
        // 验证商品能否加入
        if (!$this->checkProduct($product, $productSkuId, $cartProductNum)) {
            return false;
        }
        // 记录到购物车列表
        static::$cart[$index] = [
            'product_id' => $productId,
            'product_num' => $cartProductNum,
            'product_sku_id' => $productSkuId,
            'shop_supplier_id' => $product['shop_supplier_id'],
            'create_time' => time()
        ];
        return true;
    }

    /**
     * 验证商品是否可以购买
     */
    private function checkProduct($product, $productSkuId, $cartProductNum)
    {
        // 判断商品是否下架
        if (!$product || $product['is_delete'] || $product['product_status']['value'] != 10) {
            $this->setError('很抱歉，商品信息不存在或已下架');
            return false;
        }
        // 商品sku信息
        $product['product_sku'] = ProductModel::getProductSku($product, $productSkuId);
        // 判断商品库存
        if ($cartProductNum > $product['product_sku']['stock_num']) {
            $this->setError('很抱歉，商品库存不足');
            return false;
        }
        // 是否是会员商品
        if(count($product['grade_ids']) > 0 && $product['grade_ids'][0] != ''){
            if(!in_array($this->user['grade_id'], $product['grade_ids'])){
                $this->setError('很抱歉，此商品仅特定会员可购买');
                return false;
            }
        }
        // 是否超过最大购买数
        if($product['limit_num'] > 0){
            $hasNum = OrderModel::getHasBuyOrderNum($this->user['user_id'], $product['product_id']);
            if($hasNum + $product['total_num'] > $product['limit_num']){
                $this->error = "很抱歉，购买超过此商品最大限购数量";
                return false;
            }
        }
        return true;
    }

    /**
     * 减少购物车中某商品数量
     */
    public function sub($productId, $productSkuId)
    {
        $index = "{$productId}_{$productSkuId}";
        static::$cart[$index]['product_num'] > 1 && static::$cart[$index]['product_num']--;
    }

    /**
     * 删除购物车中指定商品
     * @param string $cartIds (支持字符串ID集)
     */
    public function delete($cartIds)
    {
        $indexArr = array_filter(trim(strpos($cartIds, ','), ',') !== false ? explode(',', $cartIds) : [$cartIds]);
        foreach ($indexArr as $index) {
            if (isset(static::$cart[$index])) unset(static::$cart[$index]);
        }
    }

    /**
     * 获取当前用户购物车商品总数量(含件数)
     */
    public function getTotalNum()
    {
        return helper::getArrayColumnSum(static::$cart, 'product_num');
    }

    /**
     * 获取当前用户购物车商品总数量(不含件数)
     */
    public function getProductNum()
    {
        return count(static::$cart);
    }

    /**
     * 析构方法
     * 将cart数据保存到缓存文件
     */
    public function __destruct()
    {
        $this->clear !== true && Cache::set('cart_' . $this->user_id, static::$cart, 86400 * 15);
    }

    /**
     * 清空当前用户购物车
     */
    public function clearAll($cartIds = null)
    {
        if (empty($cartIds)) {
            $this->clear = true;
            Cache::delete('cart_' . $this->user_id);
        } else {
            $this->delete($cartIds);
        }
    }

    /**
     * 设置错误信息
     */
    private function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 获取错误信息
     */
    public function getError()
    {
        return $this->error;
    }

}