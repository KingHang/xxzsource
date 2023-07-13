<?php

namespace app\shop\model\order;

use app\common\model\order\OrderRefund as OrderRefundModel;
use app\shop\model\user\User as UserModel;
use app\common\service\order\OrderRefundService;
use app\common\service\message\MessageService;
use app\common\model\plugin\agent\OrderDetail;

/**
 * 售后管理模型
 */
class OrderRefund extends OrderRefundModel
{
    /**
     * 获取售后单列表
     */
    public function getList($query = [])
    {

        $model = $this;
        // 查询条件：订单号
        if (isset($query['order_no']) && !empty($query['order_no'])) {
            $model = $model->where('order.order_no', 'like', "%{$query['order_no']}%");
        }
        // 查询条件：起始日期
        if (isset($query['create_time']) && !empty($query['create_time'])) {
            $sta_time = array_shift($query['create_time']);
            $end_time = array_pop($query['create_time']);
            $model = $model->whereBetweenTime('m.create_time', $sta_time, $end_time);
        }
        // 售后类型
        if (isset($query['type']) && $query['type'] > 0) {
            $model = $model->where('m.type', '=', $query['type']);
        }

        // 售后单状态(选项卡)
        if (isset($query['status']) && $query['status'] >= 0) {
            $model = $model->where('m.status', '=', $query['status']);
        }

        // 获取列表数据
        return $model->alias('m')
            ->field('m.*, order.order_no')
            ->with(['orderproduct.image', 'orderMaster', 'user', 'supplier'])
            ->join('order', 'order.order_id = m.order_id')
            ->order(['m.create_time' => 'desc'])
            ->paginate($query);
    }
    /**
     * 获取平台售后单列表
     */
    public function getplateList($query = [])
    {

        $model = $this;
        // 查询条件：订单号
        if (isset($query['order_no']) && !empty($query['order_no'])) {
            $model = $model->where('order.order_no', 'like', "%{$query['order_no']}%");
        }
        // 查询条件：起始日期
        if (isset($query['create_time']) && !empty($query['create_time'])) {
            $sta_time = array_shift($query['create_time']);
            $end_time = array_pop($query['create_time']);
            $model = $model->whereBetweenTime('m.create_time', $sta_time, $end_time);
        }
        // 售后类型
        if (isset($query['type']) && $query['type'] > 0) {
            $model = $model->where('m.type', '=', $query['type']);
        }

        // 售后单状态(选项卡)
        if (isset($query['plate_status']) && $query['plate_status'] == -1) {
            $model = $model->where('m.plate_status', '>', 0);
        }
        if (isset($query['plate_status']) &&  in_array($query['plate_status'], [10,20,30])) {
            $model = $model->where('m.plate_status', '=', $query['plate_status']);
        }
        if (isset($query['plate_status']) &&  $query['plate_status']==40) {
            $model = $model->where('m.status', '=', 20);
        }

        // 获取列表数据
        return $model->alias('m')
            ->field('m.*, order.order_no')
            ->with(['orderproduct.image', 'orderMaster', 'user', 'supplier'])
            ->join('order', 'order.order_id = m.order_id')
            ->order(['m.create_time' => 'desc'])
            ->paginate($query);
    }
    public function groupCount($query){
        $model = $this;
        // 查询条件：订单号
        if (isset($query['order_no']) && !empty($query['order_no'])) {
            $model = $model->where('order.order_no', 'like', "%{$query['order_no']}%");
        }
        // 查询条件：起始日期
        if (isset($query['create_time']) && !empty($query['create_time'])) {
            $sta_time = array_shift($query['create_time']);
            $end_time = array_pop($query['create_time']);
            $model = $model->whereBetweenTime('m.create_time', $sta_time, $end_time);
        }
        // 售后类型
        if (isset($query['type']) && $query['type'] > 0) {
            $model = $model->where('m.type', '=', $query['type']);
        }

        // 获取列表数据
        return $model->alias('m')
            ->field('m.status,COUNT(*) as total')
            ->join('order', 'order.order_id = m.order_id')
            ->group('m.status')->select()->toArray();
    }
    public function plategroupCount($query){
        $model = $this;
        // 查询条件：订单号
        if (isset($query['order_no']) && !empty($query['order_no'])) {
            $model = $model->where('order.order_no', 'like', "%{$query['order_no']}%");
        }
        // 查询条件：起始日期
        if (isset($query['create_time']) && !empty($query['create_time'])) {
            $sta_time = array_shift($query['create_time']);
            $end_time = array_pop($query['create_time']);
            $model = $model->whereBetweenTime('m.create_time', $sta_time, $end_time);
        }
        // 售后类型
        if (isset($query['type']) && $query['type'] > 0) {
            $model = $model->where('m.type', '=', $query['type']);
        }
        // 获取列表数据
        return $model->alias('m')
            ->field('m.plate_status,COUNT(*) as total')
            ->join('order', 'order.order_id = m.order_id')
            ->group('m.plate_status')->select()->toArray();

         
    }
    //获取已完成数
    public function finishcount(){
        $model = $this;
        return $model->where(['status'=>20,'plate_status'=>20])->count();
    }
    /**
     * 商家审核
     */
    public function audit($data)
    {
        if ($data['is_agree'] == 20 && empty($data['refuse_desc'])) {
            $this->error = '请输入拒绝原因';
            return false;
        }
        if ($data['is_agree'] == 10 && $this['type']['value'] != 30 && empty($data['address_id'])) {
            $this->error = '请选择退货地址';
            return false;
        }
        $this->startTrans();
        try {
            // 拒绝申请, 标记售后单状态为已拒绝
            $data['is_agree'] == 20 && $data['status'] = 10;
            // 同意换货申请, 标记售后单状态为已完成
            $data['is_agree'] == 10 && $this['type']['value'] == 20 && $data['status'] = 20;
            // 更新退款单状态
            $this->save($data);
            // 同意售后申请, 记录退货地址
            if ($data['is_agree'] == 10 && $this['type']['value'] != 30) {
                $model = new OrderRefundAddress();
                $model->add($this['order_refund_id'], $data['address_id']);
            }
            // 进度文案
            $refundLogDesc = '';
            $data['is_agree'] == 10 && $this['type']['value'] == 10 && $refundLogDesc = '您的退货退款审核已通过，请填写物流信息';
            $data['is_agree'] == 10 && $this['type']['value'] == 20 && $refundLogDesc = '您的换货审核已通过，请填写物流信息';
            $data['is_agree'] == 10 && $this['type']['value'] == 30 && $refundLogDesc = '您的退款审核已通过';
            $data['is_agree'] == 20 && $this['type']['value'] == 10 && $refundLogDesc = '您的退货退款审核已拒绝';
            $data['is_agree'] == 20 && $this['type']['value'] == 20 && $refundLogDesc = '您的换货审核已拒绝';
            $data['is_agree'] == 20 && $this['type']['value'] == 30 && $refundLogDesc = '您的退款审核已拒绝';
            // 记录售后处理进度
            (new OrderRefundLog())->save([
                'order_refund_id' => $this['order_refund_id'],
                'status' => 1,
                'desc' => $refundLogDesc,
                'app_id' => self::$app_id,
                'create_time' => time()
            ]);
            // 订单详情
            $order = Order::detail($this['order_id']);
            // 如果拒绝并且是当月订单 是回滚分销奖励及月统计
            $month = date('Y-m', time());// 当月
            $order_month = date('Y-m', strtotime($order['create_time']));// 下单月
            if ($order_month == $month && $data['is_agree'] == 20) {
                (new OrderDetail())->backInvalid($this['order_id']);
            }
            // 发送模板消息
            (new MessageService)->refund(self::detail($this['order_refund_id']), $order['order_no'], 'audit');
            // 如果是仅退款
            if ($data['is_agree'] == 10 && $this['type']['value'] == 30) {
                if ($data['refund_money'] > $order['pay_price']) {
                    $this->error = '退款金额不能大于商品实付款金额';
                    return false;
                }
                $this->refundMoney($order, $data);
            }
            // 事务提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
    /**
     * 平台审核
     */
    public function plateaudit($data)
    {
        if ($data['is_agree'] == 20 && empty($data['plate_desc'])) {
            $this->error = '请输入拒绝原因';
            return false;
        }
        $this->startTrans();
        try {
            // 拒绝申请, 标记售后单状态为已拒绝
            $data['is_agree'] == 20 && $data['status'] = 10 ;
            // 同意换货申请, 标记售后单状态为已完成
            $data['is_agree'] == 10 && $this['type']['value'] == 20 && $data['status'] = 20 ;
            $data['plate_status'] = $data['is_agree'] == 20?30:20;
            // 更新退款单状态
            $this->save($data);
            // 进度文案
            $refundLogDesc = '';
            $data['is_agree'] == 10 && $this['type']['value'] == 10 && $refundLogDesc = '您的退货退款审核已通过，请填写物流信息';
            $data['is_agree'] == 10 && $this['type']['value'] == 20 && $refundLogDesc = '您的换货审核已通过，请填写物流信息';
            $data['is_agree'] == 10 && $this['type']['value'] == 30 && $refundLogDesc = '您的退款审核已通过';
            $data['is_agree'] == 20 && $this['type']['value'] == 10 && $refundLogDesc = '您的退货退款审核已拒绝';
            $data['is_agree'] == 20 && $this['type']['value'] == 20 && $refundLogDesc = '您的换货审核已拒绝';
            $data['is_agree'] == 20 && $this['type']['value'] == 30 && $refundLogDesc = '您的退款审核已拒绝';
            // 记录售后处理进度
            (new OrderRefundLog())->save([
                'order_refund_id' => $this['order_refund_id'],
                'status' => 1,
                'desc' => $refundLogDesc,
                'app_id' => self::$app_id,
                'create_time' => time()
            ]);
            // 订单详情
            $order = Order::detail($this['order_id']);
            // 如果是同意退款，则直接退款
            if($data['plate_status'] == 20){
                if ($data['refund_money'] > $order['pay_price']) {
                    $this->error = '退款金额不能大于商品实付款金额';
                    return false;
                }
                $this->refundMoney($order, $data);
            }
            // 发送模板消息
            (new MessageService)->refund(self::detail($this['order_refund_id']), $order['order_no'], 'audit');
            // 事务提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
    /**
     * 确认收货并退款
     */
    public function receipt($data)
    {
        // 订单详情
        $order = Order::detail($this['order_id']);
        if ($data['refund_money'] > $order['pay_price']) {
            $this->error = '退款金额不能大于商品实付款金额';
            return false;
        }
        $this->transaction(function () use ($order, $data) {
            $this->refundMoney($order, $data);
        });
        return true;
    }

    private function refundMoney($order, $data){
        // 更新售后单状态
        $this->save([
            'refund_money' => $data['refund_money'],
            'is_receipt' => 1,
            'status' => 20
        ]);
        // 消减用户的实际消费金额
        // 条件：判断订单是否已结算
        if ($order['is_settled'] == true) {
            (new UserModel)->setDecUserExpend($order['user_id'], $data['refund_money']);
        }
        // 执行原路退款
        (new OrderRefundService)->execute($order, $data['refund_money']);
        // 记录售后处理进度
        (new OrderRefundLog())->save([
            'order_refund_id' => $this['order_refund_id'],
            'status' => 4,
            'desc' => '卖家退款完成，金额已返回到您的用户余额',
            'app_id' => self::$app_id,
            'create_time' => time()
        ]);
        // 发送模板消息
        (new MessageService)->refund(self::detail($this['order_refund_id']), $order['order_no'], 'receipt');
    }
    /**
     * 统计售后订单
     */
    public function getRefundOrderTotal()
    {
        $filter['is_agree'] = 0;
        return $this->where($filter)->count();
    }
    /**
     * 统计售后订单
     */
    public function getPlateOrderTotal(){
        $filter['plate_status'] = 10;
        return $this->where($filter)->count();
    }
}