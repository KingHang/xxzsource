<?php

namespace app\common\model\order;

use app\common\model\BaseModel;

/**
 * 售后单处理进度模型
 */
class OrderRefundLog extends BaseModel
{
    protected $name = 'order_refund_log';

    protected $pk = 'id';

    protected $updateTime = false;

    /**
     * 处理状态文案
     * @param $value
     * @return array
     */
    public function getStatusAttr($value)
    {
        $text = [
            0 => '待审核',
            1 => '已审核',
            2 => '已寄回',
            3 => '退款中',
            4 => '已完成',
            5 => '已发出',
            6 => '已收货'
        ];

        return ['text' => $text[$value], 'value' => $value];
    }
}
