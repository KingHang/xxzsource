<?php

namespace app\api\service\order\paysuccess\source;

use app\common\enum\order\OrderSourceEnum;

/**
 * 支付成功辅助工厂类
 */
class PaySourceSuccessFactory
{
    public static function getFactory($type = OrderSourceEnum::MASTER)
    {
        switch ($type) {
            case OrderSourceEnum::MASTER:
                return new MasterPaySuccessService();
                break;
            case OrderSourceEnum::POINTS;
                return new PointsPaySuccessService();
                break;
            case OrderSourceEnum::SECKILL:
                return new SeckillPaySuccessService();
                break;
            case OrderSourceEnum::BARGAIN:
                return new BargainPaySuccessService();
                break;
            case OrderSourceEnum::ASSEMBLE:
                return new AssemblePaySuccessService();
                break;
            case OrderSourceEnum::AGENT:
                return new MasterPaySuccessService();
                break;
            case OrderSourceEnum::SERVER:
                return new ServerPaySuccessService();
                break;
            case OrderSourceEnum::CARD:
                return new CardPaySuccessService();
                break;
            case OrderSourceEnum::BENEFIT:
                return new BenefitCardPaySuccessService();
                break;
        }
    }
}