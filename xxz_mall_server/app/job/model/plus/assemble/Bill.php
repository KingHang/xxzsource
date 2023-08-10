<?php

namespace app\job\model\plus\assemble;

use app\common\enum\order\OrderSourceEnum;
use app\common\library\helper;
use app\common\model\plugin\groupsell\Bill as BillModel;
use app\common\model\plugin\groupsell\BillUser as BillUserModel;
use app\common\model\order\Order as OrderModel;
use app\common\service\order\OrderRefundService;

/**
 * 参与记录模型
 */
class Bill extends BillModel
{
    /**
     * 获取待关闭订单
     */
    public function getCloseIds($fail_type = 0){
        return $this->alias('bill')
            ->join('groupsell_activity activity', 'bill.groupsell_activity_id = activity.groupsell_activity_id','left')
            ->where('bill.status', '=', 10)
            ->where('activity.fail_type', '=', $fail_type)
            ->whereTime('bill.end_time', '<=', time())
            ->select();
    }

    /**
     * 关闭订单
     * @param $billIds
     */
    public function close($billIds){
        // 更新记录
        $this->startTrans();
        try {
            //修改拼团状态
            $this->where('groupsell_bill_id', 'in', $billIds)->save(['status' => 30]);
            //修改订单状态，并退款
            $bill_user_model = new BillUserModel();
            $orderList = $bill_user_model->field(['order_id'])
                ->where('groupsell_bill_id', 'in', $billIds)
                ->select();
            $orderIds = helper::getArrayColumn($orderList, 'order_id');
            //修改订单状态，拼团状态
            (new OrderModel)->where('order_id', 'in', $orderIds)->save([
                'order_status' => 20,
                'assemble_status' => 30
            ]);
            $this->commit();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
        }
        // 退款
        $this->orderRefund();
    }

    /**
     * 拼团失败订单退款
     */
    private function orderRefund(){
        //查找待退款的拼团订单，每次取100条
        $orderList = (new OrderModel)->where('order_source', '=', OrderSourceEnum::ASSEMBLE)
            ->where('order_status', '=', 20)
            ->where('is_refund', '=', 0)
            ->where('pay_status', '=', 20)
            ->where('pay_source', '<>', '')
            ->limit(100)
            ->select();
        foreach ($orderList as $order){
            try {
                // 执行退款操作
                if((new OrderRefundService)->execute($order)){
                    // 更新订单状态
                    $order->save([
                        'is_refund' => 1
                    ]);
                }
            } catch (\Exception $e) {
                $this->error = '订单ID：' . $order['order_id'] . ' 退款失败，错误信息：' . $e->getMessage();
            }
        }
        return true;
    }

    /**
     * 拼团成功订单
     * @param $billIds
     */
    public function success($billIds){
        // 更新记录
        $this->startTrans();
        try {
            //修改拼团状态
            $this->where('groupsell_bill_id', 'in', $billIds)->save(['status' => 20]);
            $order_list = (new BillUserModel)
                ->field(['order_id'])
                ->where('groupsell_bill_id', 'in', $billIds)
                ->select();
            $orderIds = helper::getArrayColumn($order_list, 'order_id');
            //更新主订单表拼团状态
            (new OrderModel)->where('order_id', 'in', $orderIds)
                ->save([
                    'assemble_status' => 20
                ]);
            $this->commit();
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
        }
        // 退款
        $this->orderRefund();
    }
}