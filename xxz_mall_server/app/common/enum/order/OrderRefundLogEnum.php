<?php

namespace app\common\enum\order;

use MyCLabs\Enum\Enum;

/**
 * 订单售后处理状态
 */
class OrderRefundLogEnum extends Enum
{
    /**
     * 获取枚举数据
     * @param int $type 售后类型(10退货退款 20换货 30退款)
     * @return array
     */
    public static function data($type)
    {
        switch ($type) {
            case 10:
                return [
                    0 => [
                        'name' => '待审核',
                        'value' => 0
                    ],
                    1 => [
                        'name' => '已审核',
                        'value' => 1,
                    ],
                    2 => [
                        'name' => '已寄回',
                        'value' => 2,
                    ],
                    3 => [
                        'name' => '退款中',
                        'value' => 3,
                    ],
                    4 => [
                        'name' => '已完成',
                        'value' => 4,
                    ],
                ];
            case 20:
                return [
                    0 => [
                        'name' => '待审核',
                        'value' => 0
                    ],
                    1 => [
                        'name' => '已审核',
                        'value' => 1,
                    ],
                    3 => [
                        'name' => '退款中',
                        'value' => 3,
                    ],
                    4 => [
                        'name' => '已完成',
                        'value' => 4,
                    ],
                ];
            case 30:
                return [
                    0 => [
                        'name' => '待审核',
                        'value' => 0
                    ],
                    1 => [
                        'name' => '已审核',
                        'value' => 1,
                    ],
                    2 => [
                        'name' => '已寄回',
                        'value' => 2,
                    ],
                    5 => [
                        'name' => '已发出',
                        'value' => 5,
                    ],
                    6 => [
                        'name' => '已收货',
                        'value' => 6,
                    ],
                ];
        }
    }
}
