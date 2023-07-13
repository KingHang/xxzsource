<?php

namespace app\api\service\order\paysuccess\source;


/**
 * 拼团订单支付成功后的回调
 */
class AssemblePaySuccessService
{
    /**
     * 回调方法
     */
    public function onPaySuccess($order)
    {
        return true;
    }
}