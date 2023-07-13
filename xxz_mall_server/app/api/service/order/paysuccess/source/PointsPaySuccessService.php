<?php

namespace app\api\service\order\paysuccess\source;


/**
 * 积分订单支付成功后的回调
 */
class PointsPaySuccessService
{
    /**
     * 回调方法
     */
    public function onPaySuccess($order)
    {
        return true;
    }
}