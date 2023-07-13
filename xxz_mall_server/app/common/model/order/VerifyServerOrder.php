<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
/**
 * 商品订单模型
 */
class VerifyServerOrder  extends BaseModel
{
    protected $name = 'face_verify_server_order';
    protected $pk = 'id';

    public function order()
    {
        return $this->hasOne('app\\common\\model\\order\\Order', 'order_id', 'exchange_order_id');
    }
    public function product()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderGoods', 'order_id', 'exchange_order_id');
    }

    public function getdetailForOrder($verify_code , $order_id)
    {
        return $this->where(['verify_code' => $verify_code , 'exchange_order_id' => $order_id])->find();
    }
}