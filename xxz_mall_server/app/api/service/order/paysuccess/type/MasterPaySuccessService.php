<?php

namespace app\api\service\order\paysuccess\type;

use app\api\model\user\User as UserModel;
use app\api\model\order\Order as OrderModel;
use app\common\model\order\OrderTrade as OrderTradeModel;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;
use app\common\service\BaseService;
use app\common\service\product\factory\ProductFactory;
use app\api\model\plugin\live\Room as RoomModel;
use app\api\model\order\OrderGoods as OrderProductModel;
/**
 * 订单支付成功服务类
 */
class MasterPaySuccessService extends BaseService
{
    // 订单模型
    public $model;
    // 当前用户信息
    private $user;
    // 交易号
    private $orderNo;
    // 交易附带信息
    private $attach;
    /**
     * 构造函数
     */
    public function __construct($orderNo, $attach = [])
    {
        $this->orderNo = $orderNo;
        $this->attach = $attach;
    }

    /**
     * 订单支付成功业务处理
     */
    public function onPaySuccess($payType, $payData = [])
    {
        if($payData == [] || ($this->attach && $this->attach['multiple'] == 0)){
            // 实例化订单模型
            $this->model = OrderModel::getPayDetail($this->orderNo);
            // 获取用户信息
            $this->user = UserModel::detail($this->model['user_id']);
            $this->paySuccess($payType, $payData);
        }else{
            $orderList = OrderTradeModel::detailWithOrder(['out_trade_no' => $this->orderNo, 'pay_status' => 10]);
            foreach ($orderList as $order){
                $this->model = $order;
                // 获取用户信息
                $this->user = UserModel::detail($this->model['user_id']);
                $this->paySuccess($payType, $payData);
            }
            //更新trade为已支付
            (new OrderTradeModel())->where('out_trade_no', '=', $this->orderNo)->save([
                'pay_status' => 20,
                'pay_time' => time()
            ]);
        }
        if($this->getError() != ''){
            return false;
        }
        return true;
    }

    /**
     * 订单支付成功业务处理
     */
    public function balanceonPaySuccess($payType, $payData = [])
    {
        if ($this->attach['multiple'] == 0) {
            // 实例化订单模型
            $this->model = OrderModel::getPayDetail($this->orderNo);
            // 获取用户信息
            $this->user = UserModel::detail($this->model['user_id']);
            $this->paySuccess($payType, $payData);
        } else {
            $orderList = OrderTradeModel::detailWithOrder(['out_trade_no' => $this->orderNo, 'pay_status' => 10]);
            foreach ($orderList as $order) {
                $this->model = $order;
                // 获取用户信息
                $this->user = UserModel::detail($this->model['user_id']);
                $this->paySuccess($payType, $payData);
            }
            //更新trade为已支付
            (new OrderTradeModel())->where('out_trade_no', '=', $this->orderNo)->save([
                'pay_status' => 20,
                'pay_time' => time()
            ]);
        }
        if ($this->getError() != '') {
            return false;
        }
        return true;
    }

    /**
     * 订单支付成功业务处理
     */
    public function paySuccess($payType, $payData = [])
    {
        // 更新付款状态
        $status = $this->updatePayStatus($payType, $payData);
        // 订单支付成功行为
        if ($status == true) {
            // 获取订单详情
            $detail = OrderModel::getUserOrderDetail($this->model['order_id'], $this->user['user_id']);
            event('PaySuccess', $detail);
        }
    }
    /**
     * 更新付款状态
     */
    private function updatePayStatus($payType, $payData = [])
    {
        // 验证余额支付时用户余额是否满足
        if ($payType == OrderPayTypeEnum::BALANCE) {
            if ($this->user['balance'] < $this->model['pay_price']) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false; 
            }
        }
        // 事务处理
        $this->model->transaction(function () use ($payType, $payData) {
            // 更新订单状态
            $this->updateOrderInfo($payType, $payData);
            // 累积用户总消费金额
            $this->user->setIncPayMoney($this->model['pay_price']);
            // 记录订单支付信息
            $this->updatePayInfo($payType);
            //更新直播销量
            $this->updateLiveSales();
        });
        return true;
    }
    //更新直播销量
    public function updateLiveSales(){
        // 如果是直播，则更新直播间销售数量
        if($this->model['room_id'] > 0){
            $sales_num = (new OrderProductModel())->where('order_id','=',$this->model['order_id'])->sum('total_num');
        (new RoomModel())->where('room_id', '=', $this->model['room_id'])->inc('sales_num',$sales_num)->update();
        }
    }
    /**
     * 更新订单记录
     */
    private function updateOrderInfo($payType, $payData)
    {
        // 更新商品库存、销量
        ProductFactory::getFactory($this->model['order_source'])->updateStockSales($this->model['product']);
        // 整理订单信息
        $order = [
            'pay_type' => $payType,
            'pay_status' => 20,
            'pay_time' => time()
        ];
        if (isset($payData['attach'])) {
            $attach = json_decode($payData['attach'], true);
            if (isset($attach['pay_source'])) {
                $order['pay_source'] = $attach['pay_source'];
            }
            if (isset($attach['method'])) {
                $order['pay_method'] = $attach['method'];
            }
        }

        if ($payType == OrderPayTypeEnum::WECHAT || $payType == OrderPayTypeEnum::ALIPAY) {
            $order['transaction_id'] = $payData['transaction_id'];
        }
        // 更新订单状态
        return $this->model->save($order);
    }

    /**
     * 记录订单支付信息
     */
    private function updatePayInfo($payType)
    {
        // 余额支付
        if ($payType == OrderPayTypeEnum::BALANCE) {
            // 更新用户余额
            (new UserModel())->where('user_id', '=', $this->user['user_id'])
                ->dec('balance', $this->model['pay_price'])
                ->update();
            BalanceLogModel::add(BalanceLogSceneEnum::CONSUME, [
                'user_id' => $this->user['user_id'],
                'money' => -$this->model['pay_price'],
            ], ['order_no' => $this->model['order_no']]);
        }
    }

    /**
     * 返回app_id，大于0则存在订单信息
     * $pay_status 兼容支付宝支付
     */
    public function isExist($pay_status = 10){
        // 订单信息
        $app_id = 0;
        if($this->attach['multiple'] == 0){
            $params = ['order_no' => $this->orderNo, 'is_delete' => 0];
            if($pay_status == 10){
                $params['pay_status'] = 10;
            }
            $order = OrderModel::detail($params, []);
            !empty($order) && $app_id = $order['app_id'];
        }else{
            $params = ['out_trade_no' => $this->orderNo];
            if($pay_status == 10){
                $params['pay_status'] = 10;
            }
            $trade = OrderTradeModel::detail($params, ['orderList']);
            !empty($trade) && $app_id = $trade['app_id'];
        }
        return $app_id;
    }
}