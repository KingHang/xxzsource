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
use app\common\model\order\OrderTravelers;
use app\common\model\order\OrderBenefit;

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
     * 获取计次/旅游商品(卡券列表)
     */
    public function getList($params,$user_id,$type = 0)
    {
        $time = time(); // 格式化时间
        $model = $this;
        $where = ''; //原生sql条件
        $where1 = '';
        // 格式化状态
        $status = 0;
        $data_status = isset($params['data_status']) ? $params['data_status'] : 1; //使用状态 1、待使用，2、使用完，3、已过期
        switch ($data_status) {
            case 1:
                // 待使用
                $where1 = " AND ot.status = 0";
                $where = "  IF( order_product.product_type = 3 , order_product.verify_num = 0 OR order_product.already_verify < order_product.verify_num , 1) AND (order_product.verify_limit_type = 0 
                 OR (order_product.verify_limit_type in (1,2) and order_product.verify_enddate > '" . $time . "')
                 OR (order_product.verify_limit_type = 2 and IF(order_product.verify_enddate = 0 , 1 , order_product.verify_enddate > '" . $time . "')))";
                break;
            case 2:
                // 已用完
                $where1 = " AND ot.status = 1";
                $where = "  (IF(
                order_product.product_type = 3 
                , order_product.verify_num != 0 AND order_product.already_verify = order_product.verify_num
                , 1
                ))";

                break;
            case 3:
                // 已过期
                $where = "  (order_product.verify_limit_type in (1,2,3)  
                AND order_product.verify_enddate > 0 AND order_product.verify_enddate < " . $time . ") ";
                break;
        }
        // 出行人数量
        $travelers_number = (new OrderTravelers())->field(['count(*)'])
            ->where('order_product_id', 'EXP', "= `order_product`.`order_product_id`")->buildSql();
        $model = $model
            ->withJoin(['image'=>['file_id','file_url','file_name','storage'], 'product'=>['product_name','sales_initial','sales_actual','product_type','store_ids'],'orderM'])
            ->join('order_travelers ot' , 'ot.order_product_id = order_product.order_product_id','left')
            ->field('orderM.shop_supplier_id,order_product.*,ot.name,ot.mobile,ot.id_card,IF(order_product.product_type = 3,order_product.verify_code,ot.verify_code) as verify_code,ot.status,IF(order_product.product_type = 3,order_product.order_product_id,ot.id) as opt_id')
            ->where('orderM.pay_status' , '=' , 20)
            ->whereIn('orderM.order_status',[10,30])
            ->whereIn('order_product.product_type',[3,4])
            ->where('order_product.user_id',$user_id)
            ->whereRaw("IF(order_product.product_type = 4 , " . $travelers_number . $where1 . " > 0 , 1)")
            ->whereRaw($where)
            ->where('ot.status' , "<>" ,2)
            ->order(['order_product.create_time' => 'desc']);
        if ($type == 0) {
            return $model->paginate($params);
        } else {
            return $model->count();
        }
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
    public function productDetail($order_product_id,$user_id,$product_type=3,$verify_cod='')
    {
        if ($order_product_id == 0 && $verify_cod != '') {
            // 获取订单商品id
            if ($product_type == 4) {
                $order_product_id = (new OrderTravelers())->getIdByCode($verify_cod);
            }
        }
        $info = $this->with(['image'])->alias('order_product')
            ->field('image_id,order_product.delivery_time,order_product.app_id,order_product.order_product_id,order_product.order_id,product_type,order_product.server_min,order_product.order_source,order_product.verify_num,order_product.already_verify,order_product.verify_limit_type,order_product.verify_days,order_product.verify_enddate,order_product.store_ids,order_product.total_num,order_product.product_name,order_product.product_id,ot.name,ot.mobile,ot.id_card,IF(order_product.product_type = 3,order_product.verify_code,ot.verify_code) as verify_code,ot.status,IF(order_product.product_type = 3,order_product.order_product_id,ot.id) as opt_id')
            ->join('order_travelers ot' , 'ot.order_product_id = order_product.order_product_id','left')
            ->where(function ($query) use ($product_type, $order_product_id,$verify_cod) {
                if ($product_type == 3) {
                    if ($order_product_id == 0 && $verify_cod != '') {
                        $query->where('order_product.verify_code', '=', $verify_cod);
                    } else {
                        $query->where('order_product.order_product_id', '=', $order_product_id);
                    }
                } else {
                    if ($order_product_id == 0 && $verify_cod != '') {
                        $query->where('ot.verify_code', '=', $verify_cod);
                    } else {
                        $query->where('ot.id', '=', $order_product_id);
                    }
                }
            })
            ->find();
        if (empty($info)) {
            $this->error = '订单不存在';
            return false;
        }
        if (in_array($info['product_type'],[3,4]) || in_array($info['order_source'],[1,2])) {
            // 获取支持门店
            if ($info['store_ids'] != '') {
                $store_list = (new StoreModel())->getListByIds(explode(',',$info['store_ids']));
            } else {
                // 获取订单供应商id
                $shop_supplier_id = (new OrderModel())->where('order_id' , '=' , $info['order_id'])->value('shop_supplier_id');
                $store_list = (new StoreModel())->getList(1,'','',false,[$shop_supplier_id]);
            }
            $info['store_list'] = $store_list;
            $type = 1;
            if ($info['product_type'] == 4) {
                $type = 3;
            }
            // 生成核销码
            $Qrcode = new VerifyService(
                $info['app_id'],
                $info['user_id'],
                $info['opt_id'],
                $info['product_type'],
                $info['verify_code'],
                $type
            );
            $info['qrcode'] = $Qrcode->getImage();
        } else {
            $this->error = '该商品暂不支持核销';
            return false;
        }

        return $info;
    }

    /**
     * 获取指定权益可兑换的权益卡信息
    */
     public function getBenefitCardList($benefit_id,$number,$user_id)
     {
         $time = time();
         return $this->alias('p')
             ->with(['OrderBenefit','image'])
             ->field('p.verify_enddate,p.verify_limit_type,p.verify_days,p.order_product_id,p.product_id,p.order_id,p.product_name,p.image_id,p.product_type,p.order_source,p.delivery_time,p.verify_num,p.total_num,p.already_verify')
             ->join('order o','o.order_id = p.order_id')
             ->join('order_benefit ob' , 'ob.order_product_id = p.order_product_id')
             ->whereRaw("(
                 p.verify_limit_type = 0 
                 OR (p.verify_limit_type in (1,2) and p.verify_enddate > '" . $time . "')
                 OR (p.verify_limit_type = 3 and IF(verify_enddate = 0 , 1 , verify_enddate > '" . $time . "'))
             )")
             ->where('p.order_source','=' , 3)
             ->where('ob.benefit_id','=',$benefit_id)
             ->where('o.user_id','=',$user_id)
             ->where('ob.surplus_number' ,'>' ,0)
             ->where('o.pay_status' , '=' , 20)
             ->whereIn('o.order_status',[10,30])
             ->group('p.order_product_id')
             ->select()->toArray();
     }

     /**
      * 获取订单旅游商品
     */
     public function getOrderBenefitProduct($order_id = 0)
     {
         return $this->field('order_product_id,total_num')
             ->where('order_id','=',$order_id)
             ->where('product_type','=',4)
             ->find();
     }

     public function getMyBenefitCardList($data,$user_id)
     {
         $where = '';
         $data_status = isset($data['data_status']) ? $data['data_status'] : 1; //使用状态 1、待使用，2、使用完/已过期
         $time = time();
         $surplus_number = (new OrderBenefit())->field('SUM(surplus_number)')
             ->where('order_product_id', 'EXP', "= `op`.`order_product_id`")->buildSql();
         switch ($data_status) {
             case 1:
                 // 待使用
                 $where = "(verify_limit_type=0
                 OR (verify_limit_type=1 AND verify_enddate > '$time') 
                 OR (verify_limit_type in (2,3) AND (verify_enddate = 0 OR verify_enddate > '$time'))
                 ) AND $surplus_number > 0";
                 break;
             case 2:
                 // 已用完或者以过期
                 $where = "  (verify_limit_type in (1,2,3) 
                AND verify_enddate > 0 AND verify_enddate < " . $time . ") OR $surplus_number = 0";
         }
         return $this->alias('op')->with(['image','OrderBenefit.file','OrderBenefitTravelers'=>['order','product']])
             ->field('order_source,order_product_id,order_id,product_name,image_id,product_type,verify_enddate,verify_num,verify_limit_type,already_verify,delivery_time,verify_days,total_num')
             ->where('order_source','=' , 3)
             ->where('user_id' , '=' ,$user_id)
             ->whereRaw($where)
             ->order(['create_time' => 'desc'])
             ->paginate($data);
     }

    /**
     * 获取权益卡详情
     * @param $data
     * @param $user_id
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
     public function getMyBenefitCardDetail($data,$user_id)
     {
         $list =  $this->alias('op')->with(['image','OrderBenefit.file'])
             ->field('order_source,order_product_id,order_id,product_name,image_id,product_type,verify_enddate,verify_num,verify_limit_type,already_verify,delivery_time,verify_days,total_num')
             ->where('user_id' , '=' ,$user_id)
             ->where('order_product_id' , '=' , $data['order_product_id'])
             ->find($data);
         $OrderBenefitTravelers = (new OrderTravelers())->with(['order','product.benefit'])->where('status' , '<>' , 2)->where('card_order_product_id' , '=' , $data['order_product_id'])->select();
         $list['OrderBenefitTravelers'] = $OrderBenefitTravelers;
         return $list;
     }
}