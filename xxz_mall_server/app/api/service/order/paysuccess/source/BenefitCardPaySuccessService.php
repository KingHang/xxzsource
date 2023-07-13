<?php

namespace app\api\service\order\paysuccess\source;

use app\common\model\order\OrderGoods;
use app\common\service\order\OrderCompleteService;
use app\common\enum\order\OrderTypeEnum;

/**
 * 普通订单支付成功后的回调
 */
class BenefitCardPaySuccessService
{
    /**
     * 回调方法
     */
    public function onPaySuccess($order)
    {
        // 计次商品 设置有效期
        (new OrderGoods())->setTimesProduct($order['product'],2);
        // 服务商品 标记订单已完成，无需发货
            $order->save([
                'delivery_status' => 20,
                'delivery_time' => time(),
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30,
            ]);
            // 执行订单完成后的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$order], $order['app_id']);
        return true;
    }

}