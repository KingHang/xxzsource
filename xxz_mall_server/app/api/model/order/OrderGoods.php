<?php

namespace app\api\model\order;

use app\common\exception\BaseException;
use app\common\model\order\OrderGoods as OrderProductModel;
use app\api\model\store\Store as StoreModel;
use app\api\model\order\Order as OrderModel;
use app\common\service\qrcode\VerifyService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * 商品订单模型
 */
class OrderGoods extends OrderProductModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'content',
        'app_id',
        'create_time',
    ];

    /**
     * 获取未评价的商品
     */
    public function getNotCommentProductList($order_id)
    {
        return $this->where(['order_id' => $order_id, 'is_comment' => 0])->with(['orderM', 'image'])->select();
    }
    /**
     * 获取几次商品(卡券列表)
     */
    public function getList($params,$user_id)
    {
        $model = $this;
        return $model->withJoin(['image'=>['file_id','file_url','file_name','storage'], 'product'=>['product_name','sales_initial','sales_actual','product_type','store_ids','purveyor_id'],'orderM'])
            ->where('product.product_type',3)
            ->where('order_goods.user_id',$user_id)
//            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->select();
    }

    /**
     * 订单商品详情
     * @param $order_id
     * @param $product_id
     * @return array|Model
     * @throws BaseException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getUserOrderProductDetail($order_id, $product_id)
    {
        $info = $this->with(['express'])
            ->where('order_id', '=', $order_id)
            ->where('product_id', '=', $product_id)
            ->find();

        if (empty($info)) {
            throw new BaseException(['msg' => '订单不存在']);
        }

        return $info;
    }

    /**
     * 订单商品详情
     */
    public function productDetail($order_product_id,$user_id)
    {
        $info = $this
            ->field('delivery_time,app_id,verify_code,order_goods_id as order_product_id,order_goods_id,order_id,product_type,server_min,order_source,verify_num,already_verify,verify_limit_type,verify_days,verify_enddate,store_ids,total_num,product_name,goods_id as product_id,goods_id')
            ->where('order_goods_id' ,'=' , $order_product_id)
            ->where('user_id' , '=' , $user_id)
            ->find();
        if (empty($info)) {
            $this->error = '订单不存在';
            return false;
        }
        if ($info['product_type'] == 3 || in_array($info['order_source'],[1,2])) {
            // 获取支持门店
            if ($info['store_ids'] != '') {
                $store_list = (new StoreModel())->getListByIds(explode(',',$info['store_ids']));
            } else {
                // 获取订单供应商id
                $shop_supplier_id = (new OrderModel())->where('order_id' , '=' , $info['order_id'])->value('purveyor_id');
                $store_list = (new StoreModel())->getList(1,'','',false,[$shop_supplier_id]);
            }
            $info['store_list'] = $store_list;
            // 生成核销码
            $Qrcode = new VerifyService(
                $info['app_id'],
                $info['user_id'],
                $info['order_product_id'],
                $info['product_type'],
                $info['verify_code'],
                $info['product_type'] == 3 ? 1 : 2
            );
            $info['qrcode'] = $Qrcode->getImage();
        } else {
            $this->error = '该商品暂不支持核销';
            return false;
        }

        return $info;
    }
}