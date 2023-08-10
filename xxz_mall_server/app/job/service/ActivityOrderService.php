<?php

namespace app\job\service;

use app\job\model\plus\activity\Order as OrderModel;
use app\common\library\helper;
use app\common\service\message\MessageService;
class ActivityOrderService
{
    // 模型
    private $model;

    // 自动关闭的订单id集
    private $closeOrderIds = [];
    private $supplierModel;

    /**
     * 构造方法
     * Order constructor.
     */
    public function __construct()
    {
        $this->model = new OrderModel;
    }

    /**
     * 活动结束订单自动完成和结算
     */
    public function settled($app_id)
    {
        // 获取自动完成和计算的订单
        // 条件1：订单状态：已完成
        // 条件2：活动已结束
        // 条件3：is_settled 为 0
        // 查询订单列表
        $orderList = $this->model->getSettledList($app_id);
        // 订单id集
        $orderIds = helper::getArrayColumn($orderList, 'order_id');
        // 订单结算服务
        $supplierData = []; // 累计结算金额
        $supplierCapitalData = array(); // 结算日志

       foreach($orderList as $order){
           if ($order['supplier_money'] > 0) {
               $supplierMoney = $order['supplier_money'];
               !isset($supplierData[$order['shop_supplier_id']]) && $supplierData[$order['shop_supplier_id']] = 0.00;
               $supplierMoney > 0 && $supplierData[$order['shop_supplier_id']] += $supplierMoney;
               // 商家结算记录
               $supplierCapitalData[] = [
                   'shop_supplier_id' => $order['shop_supplier_id'],
                   'money' => $supplierMoney,
                   'describe' => '活动’' . $order['activity'][0]['name'] . '‘报名结算，订单号：' . $order['order_no'],
                   'app_id' => $order['app_id']
               ];
           }
       }

        // 累积到供应商表记录
        $this->supplierModel->onBatchIncSupplierMoney($supplierData);
        // 供应商结算明细金额
        // 将订单设置为已结算
        $this->model->onBatchUpdate($orderIds, ['is_settled' => 1]);
    }

    /**
     * 未支付订单自动关闭
     */
    public function close($app_id)
    {
        // 查询截止时间未支付的订单
        $list = $this->model->getCloseList($app_id);
        $this->closeOrderIds = helper::getArrayColumn($list, 'order_id');
        // 取消订单事件
        if (!empty($this->closeOrderIds)) {
            // 批量更新订单状态为已取消
            return $this->model->onBatchUpdate($this->closeOrderIds, ['order_status' => 20]);
        }
        return true;
    }

    /**
     * 获取自动关闭的订单id集
     */
    public function getCloseOrderIds()
    {
        return $this->closeOrderIds;
    }

    /**
     * 活动报名通知
     * @param $app_id
     */
    public function sendActivityMsg($app_id)
    {
        $mdoel =  (new RemindModel());
        $list = $mdoel->getRemindActivityList($app_id);
        $data = [];
        if (!empty($list)) {
            foreach ($list as $item) {
               (new MessageService)->activityRemind($item);
                $data[] = [
                    'data' => ['status' => 2],
                    'where' => [
                        'id' => $item['id'],
                    ],
                ];
            }
        }
        count($data) > 0 && $mdoel->updateAll($data);
    }
}