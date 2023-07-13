<?php

namespace app\common\enum\order;

use MyCLabs\Enum\Enum;

/**
 * 订单支付方式枚举类
 */
class OrderPayTypeEnum extends Enum
{
    // 余额支付
    const BALANCE = 10;

    // 微信支付
    const WECHAT = 20;

    // 支付宝支付
    const ALIPAY = 30;

    // 现金支付
    const CASH = 40;
    /**
     * 获取枚举数据
     */
    public static function data()
    {
        return [
            self::BALANCE => [
                'name' => '余额支付',
                'value' => self::BALANCE,
            ],
            self::WECHAT => [
                'name' => '微信支付',
                'value' => self::WECHAT,
            ],
            self::ALIPAY => [
                'name' => '支付宝支付',
                'value' => self::ALIPAY,
            ],
            self::CASH => [
                'name' => '现金支付',
                'value' => self::CASH,
            ],
        ];
    }

}