<?php

namespace app\job\model\plus\activity;

use app\common\model\plugin\activity\ActivityOrder as ActivityOrderModel;

/**
 * 分销商订单模型
 */
class Order extends ActivityOrderModel
{
    /**
     * 获取订单列表
     */
    public function getCloseList($app_id)
    {
        return $this
            ->where('pay_status', '=', 10)
            ->where('order_status', '=', 10)
            ->where('pay_end_time', '<=', time()+24*60*60)
            ->where('is_delete', '=', 0)
            ->where('app_id' ,'=' ,$app_id)
            ->select();
    }

    /**
     * 获取活动已结束且待结算订单
     */
    public function getSettledList($app_id)
    {
        return $this->alias('o')->with('activity')
            ->join('activity_order_detail od', 'od.order_id = o.order_id', 'left')
            ->where([
                'o.order_status' => 30,
                'o.is_settled' => 0,
                'o.app_id' => $app_id,
            ])
            ->where('od.activity_time_end' , '<' ,time())
            ->select();
    }

}