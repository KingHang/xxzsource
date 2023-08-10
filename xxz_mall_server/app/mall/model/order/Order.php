<?php

namespace app\mall\model\order;

use app\common\model\order\Order as OrderModel;
use app\common\model\order\OrderGoods as OrderProductModel;
use app\common\enum\order\OrderTypeEnum;
use app\common\service\message\MessageService;
use app\common\service\order\OrderRefundService;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\service\product\factory\ProductFactory;
use app\common\model\plugin\voucher\UserCoupon as UserCouponModel;
use app\common\model\user\User as UserModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\mall\service\order\ExportService;

/**
 * 订单模型
 */
class Order extends OrderModel
{
    /**
     * 订单列表
     */
    public function getList($dataType, $data = null)
    {
        $model = $this;
        // 检索查询条件
        $model = $model->setWhere($model, $data);
        // 获取数据列表
        return $model->with(['product' => ['image', 'refund'], 'user'])
            ->alias('order')
            ->field('order.*')
            ->join('user', 'user.user_id = order.user_id', 'left')
            ->join('order_address', 'order_address.order_id = order.order_id', 'left')
            ->where($this->transferDataType($dataType))
            ->order(['order.create_time' => 'desc'])
            ->paginate($data);
    }

    /**
     * 获取订单总数
     */
    public function getCount($type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order_status'] = 10;
                break;
            case 'delivery';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'received';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'cancel';
                $filter['order_status'] = 21;
                break;

        }
        return $this->where($filter)->count();
    }

    /**
     * 订单列表(全部)
     */
    public function getListAll($dataType, $query = [])
    {
        $model = $this;
        // 检索查询条件
        $model = $model->setWhere($model, $query);
        // 获取数据列表
        return $model->with(['product.image', 'address', 'user', 'extract', 'extract_store'])
            ->alias('order')
            ->field('order.*')
            ->join('user', 'user.user_id = order.user_id')
            ->join('order_address', 'order_address.order_id = order.order_id', 'left')
            ->where($this->transferDataType($dataType))
            ->where('order.is_delete', '=', 0)
            ->order(['order.create_time' => 'desc'])
            ->select();
    }

    /**
     * 订单导出
     */
    public function exportList($dataType, $query)
    {
        // 获取订单列表
        $list = $this->getListAll($dataType, $query);
        // 导出excel文件
        return (new Exportservice)->orderList($list);
    }

    /**
     * 根据条件获取订单统计
     * @param $dataType
     * @param null $data
     * @param int $type 类型：0-数量，1-金额
     * @return int
     */
    public function getOrderStatistics($dataType, $data = null, $type = 0)
    {
        $model = $this;
        // 检索查询条件
        $model = $model->setWhere($model, $data);
        // 关联查询
        $model = $model->alias('order')
            ->join('user', 'user.user_id = order.user_id', 'left')
            ->join('order_address', 'order_address.order_id = order.order_id', 'left')
            ->where($this->transferDataType($dataType));
        // 获取统计数据
        if ($type) {
            // 金额
            return $model->sum('order.pay_price');
        } else {
            // 数量
            return $model->count('order.order_id');
        }
    }

    /**
     * 设置检索查询条件
     */
    private function setWhere($model, $data)
    {
        //搜索订单号
        if (isset($data['order_no']) && $data['order_no'] != '') {
            $model = $model->where('order.order_no', 'like', '%' . trim($data['order_no']) . '%');
        }
        //按时间筛选
        if (isset($data['time_type']) && $data['time_type'] != ''
            && isset($data['create_time']) && $data['create_time'] != '') {
            $sta_time = array_shift($data['create_time']);
            $end_time = array_pop($data['create_time']);
            switch ($data['time_type']) {
                case '1':
                    $model = $model->whereBetweenTime('order.create_time', $sta_time, $end_time);
                    break;
                case '2':
                    $model = $model->whereBetweenTime('order.pay_time', $sta_time, $end_time);
                    break;
                case '3':
                    $model = $model->whereBetweenTime('order.delivery_time', $sta_time, $end_time);
                    break;
                case '4':
                    $model = $model->whereBetweenTime('order.receipt_time', $sta_time, $end_time);
                    break;
            }
        }
        //按订单信息筛选
        if (isset($data['fields']) && $data['fields'] != ''
            && isset($data['search']) && $data['search'] != '') {
            switch ($data['fields']) {
                case '1':
                    $model = $model->where('order.order_no', 'like', '%' . trim($data['search']) . '%');
                    break;
                case '2':
                    $model = $model->where('order.user_id', '=', trim($data['search']));
                    break;
                case '3':
                    $model = $model->where('user.nickName|user.mobile|user.realname', 'like', '%' . trim($data['search']) . '%');
                    break;
                case '4':
                    $model = $model->where('order_address.name|order_address.phone', 'like', '%' . trim($data['search']) . '%');
                    break;
                case '5':
                    $model = $model->where('order_address.detail', 'like', '%' . trim($data['search']) . '%');
                    break;
                case '6':
                    $model = $model->where('order.express_no', 'like', '%' . trim($data['search']) . '%');
                    break;
                case '7':
                    $goodsModel = (new OrderProductModel())->where('product_name', 'like', '%' . trim($data['search']) . '%')
                        ->distinct(true)
                        ->field('order_id')
                        ->buildSql();
                    $model = $model->join([$goodsModel => 'goods'], 'goods.order_id = order.order_id');
            }
        }
        //搜索自提门店
        if (isset($data['store_id']) && $data['store_id'] != '') {
            $model = $model->where('order.extract_store_id', '=', $data['store_id']);
        }
        //搜索订单来源
        if (isset($data['order_source']) && $data['order_source'] != '') {
            $model = $model->where('order.order_source', '=', $data['order_source']);
        }
        //搜索配送方式
        if (isset($data['style_id']) && $data['style_id'] != '') {
            $model = $model->where('order.delivery_type', '=', $data['style_id']);
        }
        //查询某个人自购金额来源的订单
        if (isset($data['style_id']) && $data['style_id'] != '') {
            $model = $model->where('order.delivery_type', '=', $data['style_id']);
        }
        return $model;
    }

    /**
     * 转义数据类型条件
     */
    private function transferDataType($dataType)
    {
        $filter = [];
        // 订单数据类型
        switch ($dataType) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order_status'] = 10;
                break;
            case 'delivery';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'received';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'comment';
                $filter['is_comment'] = 0;
                $filter['order_status'] = 30;
                break;
            case 'complete';
                $filter['is_comment'] = 1;
                $filter['order_status'] = 30;
                break;
            case 'cancel';
                $filter['order_status'] = 21;
                break;
        }
        return $filter;
    }

    /**
     * 确认发货(单独订单)
     */
    public function delivery($data)
    {
        // 转义为订单列表
        $orderList = [$this];

        // 验证订单是否满足发货条件
        if (!$this->verifyDelivery($orderList)) {
            return false;
        }

        // 判断发货类型
        $sendType = isset($data['send_type']) ? $data['send_type'] : 0;
        $orderProductIds = isset($data['order_product_ids']) ? $data['order_product_ids'] : [];

        if ($sendType && empty($orderProductIds)) {
            $this->error = "部分发货请选择商品";
            return false;
        }

        // 区分部分发货还是全部发货
        if ($sendType) return $this->partDelivery($data);

        // 整理更新的数据
        $updateList = [[
            'order_id' => $this['order_id'],
            'express_id' => $data['express_id'],
            'express_no' => $data['express_no']
        ]];

        // 更新订单发货状态
        if ($status = $this->updateToDelivery($updateList)) {
            // 获取已发货的订单
            $completed = self::detail($this['order_id'], ['user', 'address', 'product', 'express']);
            // 发送消息通知
            $this->sendDeliveryMessage([$completed]);
        }

        return $status;
    }

    /**
     * 部分发货(单独订单)
     * @param $data
     * @return bool
     */
    public function partDelivery($data)
    {
        // 获取订单详情
        $orderInfo = self::detail($this['order_id'], ['user', 'address', 'product', 'express']);

        // 发货数量
        $allNum = 0;
        $sendNum = 0;
        $sendData = [];

        $time = time();

        //开始执行
        $this->startTrans();
        try {
            if ($orderInfo['product']) {
                foreach ($orderInfo['product'] as $product) {
                    $allNum += 1;

                    // 已发货跳过
                    if ($product['send_type']) {
                        $sendNum += 1;
                        continue;
                    }

                    // 需要发货的
                    if (in_array($product['order_goods_id'], $data['order_product_ids'])) {
                        $sendNum += 1;
                        $sendData[] = [
                            'order_goods_id' => $product['order_goods_id'],
                            'send_type'        => 1,
                            'express_id'       => $data['express_id'],
                            'express_no'       => $data['express_no'],
                            'delivery_time'    => $time
                        ];
                    }
                }
            }

            if ($sendNum > 0 && $allNum == $sendNum) {
                // 更改订单抓状态
                $this->where('order_id', '=', $this['order_id'])->update(['delivery_status' => 20, 'delivery_time' => $time, 'send_type' => 1]);
                // 发送消息通知
                $this->sendDeliveryMessage([$orderInfo]);
            } else {
                // 标记部分发货
                $this->where('order_id', '=', $this['order_id'])->update(['send_type' => 1]);
            }

            // 处理订单商品
            (new OrderProductModel())->saveAll($sendData);

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 确认发货后发送消息通知
     */
    private function sendDeliveryMessage($orderList)
    {
        // 实例化消息通知服务类
        $Service = new MessageService;
        foreach ($orderList as $item) {
            // 发送消息通知
            $Service->delivery($item, OrderTypeEnum::MASTER);
        }
        return true;
    }

    /**
     * 更新订单发货状态(批量)
     */
    private function updateToDelivery($orderList)
    {
        $data = [];
        foreach ($orderList as $item) {
            $data[] = [
                'order_id' => $item['order_id'],
                'express_no' => $item['express_no'],
                'express_id' => $item['express_id'],
                'delivery_status' => 20,
                'delivery_time' => time(),
            ];
        }
        return $this->saveAll($data);
    }

    /**
     * 更新订单备注
     * @param $data
     * @return bool
     */
    public function updateOrderRemark($data)
    {
        if (!isset($data['order_remark']) || !$data['order_remark']) {
            $this->error = '订单备注不能为空';
            return false;
        }
        return $this->save(['order_remark' => $data['order_remark']]);
    }

    /**
     * 验证订单是否满足发货条件
     */
    private function verifyDelivery($orderList)
    {
        foreach ($orderList as $order) {
            if (
                $order['pay_status']['value'] != 20
                || $order['delivery_type']['value'] != DeliveryTypeEnum::EXPRESS
                || $order['delivery_status']['value'] != 10
            ) {
                $this->error = "订单号[{$order['order_no']}] 不满足发货条件!";
                return false;
            }
        }
        return true;
    }

    /**
     * 审核：用户取消订单
     */
    public function confirmCancel($data)
    {
        // 判断订单是否有效
        if ($this['pay_status']['value'] != 20) {
            $this->error = '该订单不合法';
            return false;
        }
        // 订单取消事件
        $status = $this->transaction(function () use ($data) {
            if ($data['is_cancel'] == true) {
                // 执行退款操作
                (new OrderRefundService)->execute($this);
                // 回退商品库存
                ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], true);
                // 回退用户优惠券
                $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
                $this['coupon_id_sys'] > 0 && UserCouponModel::setIsUse($this['coupon_id_sys'], false);
                // 回退用户积分
                $user = UserModel::detail($this['user_id']);
                $describe = "订单取消：{$this['order_no']}";
                $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe, 3);
                // 回退用户通证
                $this['deduct_num'] > 0 && $user->giftcertAmountToken($user['mobile'], $this['deduct_num'], $user['user_id'], '订单取消：商品CFP退回');
            }
            // 更新订单状态
            return $this->save(['order_status' => $data['is_cancel'] ? 20 : 10]);
        });
        return $status;
    }

    /**
     * 获取待处理订单
     */
    public function getReviewOrderTotal()
    {
        $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
        $filter['delivery_status'] = 10;
        $filter['order_status'] = 10;
        return $this->where($filter)->count();
    }

    /**
     * 获取某天的总销售额
     * 结束时间不传则查一天
     */
    public function getOrderTotalPrice($startDate = null, $endDate = null)
    {
        $model = $this;
        $model = $model->where('pay_time', '>=', strtotime($startDate));
        if (is_null($endDate)) {
            $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }
        return $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('is_delete', '=', 0)
            ->sum('pay_price');
    }

    /**
     * 获取某天的客单价
     * 结束时间不传则查一天
     */
    public function getOrderPerPrice($startDate = null, $endDate = null)
    {
        $model = $this;
        $model = $model->where('pay_time', '>=', strtotime($startDate));
        if (is_null($endDate)) {
            $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }
        return $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('is_delete', '=', 0)
            ->avg('pay_price');
    }

    /**
     * 获取某天的下单用户数
     */
    public function getPayOrderUserTotal($day)
    {
        $startTime = strtotime($day);
        $userIds = $this->distinct(true)
            ->where('pay_time', '>=', $startTime)
            ->where('pay_time', '<', $startTime + 86400)
            ->where('pay_status', '=', 20)
            ->where('is_delete', '=', 0)
            ->column('user_id');
        return count($userIds);
    }

    /**
     * 获取兑换记录
     * @param $param array
     * @return \think\Paginator
     */
    public function getExchange($param)
    {
        $model = $this;
        if (isset($param['order_status']) && $param['order_status'] > -1) {
            $model = $model->where('order.order_status', '=', $param['order_status']);
        }
        if (isset($param['nickName']) && !empty($param['nickName'])) {
            $model = $model->where('user.nickName', 'like', '%' . trim($param['nickName']) . '%');
        }

        return $model->with(['user'])->alias('order')
            ->join('user', 'user.user_id = order.user_id')
            ->where('order.order_source', '=', 20)
            ->where('order.is_delete', '=', 0)
            ->order(['order.create_time' => 'desc'])
            ->paginate($param);
    }

    /**
     * 获取平台的总销售额
     */
    public function getTotalMoney($type = 'all', $is_settled = -1)
    {
        $model = $this;
        $model = $model->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('is_delete', '=', 0);
        if($is_settled == 0){
            $model = $model->where('is_settled', '=', 0);
        }
        if($type == 'all'){
            return $model->sum('pay_price');
        } else if($type == 'purveyor'){
            return $model->sum('supplier_money');
        } else if($type == 'sys'){
            return $model->sum('sys_money');
        }
        return 0;
    }

    /**
     * 获取视频订单
     */
    public function getAgentLiveOrder($params)
    {
        $model = $this;
        if(isset($params['order_no']) && !empty($params['order_no'])){
            $model = $model->where('order.order_no', 'like', '%' . trim($params['order_no']) . '%');
        }
        if(isset($params['room_name']) && !empty($params['room_name'])){
            $model = $model->where('room.name', 'like', '%' . trim($params['room_name']) . '%');
        }
        if(isset($params['nickName']) && !empty($params['nickName'])){
            $model = $model->where('user.nickName', 'like', '%' . trim($params['real_name']) . '%');
        }
        if(isset($params['supplier_name']) && !empty($params['supplier_name'])){
            $model = $model->where('purveyor.name', 'like', '%' . trim($params['supplier_name']) . '%');
        }
        return $model->alias('order')->field(['order.*'])->with(['product.image','user','room.user','supplier'])
            ->join('live_room room', 'room.room_id = order.room_id','left')
            ->join('purveyor purveyor', 'purveyor.purveyor_id = room.purveyor_id','left')
            ->join('user user', 'user.user_id = room.user_id','left')
            ->where('order.room_id', '>', 0)
            ->where('order.is_delete', '=', 0)
            ->order(['order.create_time' => 'desc'])
            ->paginate($params);
    }
}
