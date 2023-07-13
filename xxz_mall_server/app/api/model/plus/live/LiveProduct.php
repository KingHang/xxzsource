<?php

namespace app\api\model\plus\live;

use app\common\model\plugin\live\LiveProduct as LiveProductModel;
/**
 * 直播模型
 */
class LiveProduct extends LiveProductModel
{

    /**
     * 获取商品列表
     */
    public function getList($data)
    {
        return $this->with(['product.image.file'])
            ->where('shop_supplier_id', '=', $data['shop_supplier_id'])
            ->order(['create_time' => 'asc'])
            ->paginate($data);
    }


    /**
     * 保存
     */
    public function add($user, $productIds)
    {
        if(!isset($productIds) || $productIds == ''){
            return true;
        }
        $this->startTrans();
        try {
            $productList = [];
            $productIdArr = explode(',', $productIds);
            foreach ($productIdArr as $productId) {
                $productList[] = [
                    'user_id' => $user['user_id'],
                    'shop_supplier_id' => $user['supplierUser']['shop_supplier_id'],
                    'product_id' => $productId,
                    'app_id' => self::$app_id
                ];
            }
            $this->saveAll($productList);
            // 事务提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    public function remove($product_id){
        return $this->where('product_id', '=', $product_id)
            ->delete();
    }
    //查询已经添加产品
    public function livProduct($shop_supplier_id){
        return $this->where('shop_supplier_id','=',$shop_supplier_id)->column('product_id');
    }
}