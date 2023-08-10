<?php

namespace app\api\service\order\checkpay;

use app\common\enum\order\OrderSourceEnum;

/**
 * 支付检查辅助工厂类
 */
class CheckPayFactory
{
    public static function getFactory($type = OrderSourceEnum::MASTER)
    {
        switch ($type) {
            case OrderSourceEnum::MASTER:
                return new MasterCheckPayService();
                break;
            case OrderSourceEnum::POINTS;
                return new PointsCheckPayService();
                break;
            case OrderSourceEnum::SECKILL:
                return new SeckillCheckPayService();
                break;
            case OrderSourceEnum::ASSEMBLE:
                return new AssembleCheckPayService();
                break;
            case OrderSourceEnum::BARGAIN:
                return new BargainCheckPayService();
                break;
        }
    }
}