<?php

namespace app\api\service\order\paysuccess\type;

use app\common\enum\order\OrderTypeEnum;

/**
 * 支付成功辅助工厂类
 */
class PayTypeSuccessFactory
{
    public static function getFactory($out_trade_no, $attach)
    {
        switch ($attach['order_type']) {
            case OrderTypeEnum::MASTER:
                return new MasterPaySuccessService($out_trade_no, $attach);
                break;
            case OrderTypeEnum::CASH;
                return new CashPaySuccessService($out_trade_no);
                break;
            case OrderTypeEnum::PLAN;
                return new PlanPaySuccessService($out_trade_no);
                break;
            case OrderTypeEnum::BALANCE;
                return new BalancePaySuccessService($out_trade_no);
                break;
            case OrderTypeEnum::ACTIVITY;
                return new ActivityPaySuccessService($out_trade_no);
                break;
        }
    }
}