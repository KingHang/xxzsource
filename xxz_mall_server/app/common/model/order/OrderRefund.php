<?php

namespace app\common\model\order;

use app\common\model\BaseModel;

/**
 * 售后管理模型
 */
class OrderRefund extends BaseModel
{
    protected $name = 'order_refund';
    protected $pk = 'order_refund_id';

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User');
    }

    /**
     * 关联订单主表
     */
    public function orderMaster()
    {
        return $this->belongsTo('app\\common\\model\\order\\Order');
    }

    /**
     * 关联订单商品表
     */
    public function orderproduct()
    {
        return $this->belongsTo('app\\common\\model\\order\\OrderGoods', 'order_goods_id', 'order_goods_id');
    }

    /**
     * 关联图片记录表
     */
    public function image()
    {
        return $this->hasMany('app\\common\\model\\order\\OrderRefundImage');
    }

    /**
     * 关联物流公司表
     */
    public function express()
    {
        return $this->belongsTo('app\\api\\model\\setting\\Express');
    }

    /**
     * 关联用户表
     */
    public function address()
    {
        return $this->hasOne('app\\api\\model\\order\\OrderRefundAddress');
    }

    /**
     * 关联供应商表
     */
    public function supplier()
    {
        return $this->belongsTo('app\\common\\model\\purveyor\\Purveyor', 'purveyor_id', 'purveyor_id');
    }

    /**
     * 售后类型
     */
    public function getTypeAttr($value)
    {
        $status = [10 => '退货退款', 20 => '换货', 30 => '仅退款'];
        return ['text' => $status[$value], 'value' => $value];
    }
    /**
     * 售后类型
     */
    public function getPlateStatusAttr($value)
    {
        $status = [0 => '未申请', 10 => '待审核',20 => '已同意', 30 => '已拒绝'];
        return ['text' => $status[$value], 'value' => $value];
    }
    /**
     * 商家是否同意售后
     */
    public function getIsAgreeAttr($value)
    {
        $status = [0 => '待审核', 10 => '已同意', 20 => '已拒绝'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 售后单状态
     */
    public function getStatusAttr($value)
    {
        $status = [0 => '进行中', 10 => '已拒绝', 20 => '已完成', 30 => '已取消'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 售后单详情
     */
    public static function detail($where)
    {
        is_array($where) ? $filter = $where : $filter['order_refund_id'] = (int)$where;
        return (new static())->with(['order_master', 'image.file', 'orderproduct.image', 'express', 'address', 'user'])->where($filter)->find();
    }

    /**
     * 获取退款订单总数 (可指定某天)
     * 已同意的退款
     */
    public function getOrderRefundData($startDate = null, $endDate = null, $type, $shop_supplier_id)
    {
        $model = $this;
        $model = $model->where('create_time', '>=', strtotime($startDate));
        if(is_null($endDate)){
            $model = $model->where('create_time', '<', strtotime($startDate) + 86400);
        }else{
            $model = $model->where('create_time', '<', strtotime($endDate) + 86400);
        }

        if($shop_supplier_id > 0){
            $model = $model->where('purveyor_id', '=', $shop_supplier_id);
        }

        $model = $model->where('is_agree', '=', 10);

        if($type == 'order_refund_money'){
            // 退款金额
            return $model->sum('refund_money');
        }else if($type == 'order_refund_total'){
            // 退款数量
            return $model->count();
        }
        return 0;
    }
}