<?php

namespace app\api\model\order;

use app\common\model\order\OrderRefundLog as OrderRefundLogModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 售后单处理进度模型
 */
class OrderRefundLog extends OrderRefundLogModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = ['app_id'];

    /**
     * 获取用户售后单处理进度列表
     * @param $order_refund_id
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList($order_refund_id)
    {
        return $this->where('order_refund_id', '=', $order_refund_id)->order(['create_time' => 'desc'])->select()->toArray();
    }
}
