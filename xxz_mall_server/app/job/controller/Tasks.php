<?php

namespace app\job\controller;


use app\common\service\order\OrderCompleteService;
use app\common\model\order\Order;
/**
 * 定时任务
 */
class Tasks
{
    /**
     * CFP分期释放
     */
    public function CfpStages()
    {
        $orderList = (new Order())->getToBeReleasedOrderList();
        $Tasks = new OrderCompleteService();
        $Tasks->setGiftcertBonus($orderList);
    }

}
