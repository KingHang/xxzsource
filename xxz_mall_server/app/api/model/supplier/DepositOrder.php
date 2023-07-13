<?php

namespace app\api\model\supplier;

use app\common\model\purveyor\DepositOrder as DepositOrderModel;
use app\api\service\order\paysuccess\type\CashPaySuccessService;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayTypeEnum;
use app\api\service\order\PaymentService;
/**
 * 押金订单模型类
 */
class DepositOrder extends DepositOrderModel
{   

   /**
     * 创建订单
     */
    public function createOrder($user, $supplier, $pay_type,$pay_source)
    {
        if($supplier['status'] != 20){
            $this->error = '您已支付保证金';
            return false;
        }
        $price = $supplier['category']['deposit_money'];
        // 余额支付标记订单已支付
        if ($pay_type == OrderPayTypeEnum::BALANCE) {
            // 验证余额支付时用户余额是否满足
            if ($user['balance'] < $price) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false;
            }
        }
        $orderInfo = $this->where('user_id', '=', $user['user_id'])
            ->where('pay_status', '=', 10)->find();
        if($orderInfo){
            $order_no =  $this->orderNo();
            $status = $this->where('order_id', $orderInfo['order_id'])->update(['order_no' =>$order_no]);
            $this['order_id'] = $orderInfo['order_id'];
            $this['order_no'] = $order_no;
            $this['pay_price'] = $price;
        }else{
             // 获取订单数据
            $data = [
                'order_no' => $this->orderNo(),
                'user_id' => $user['user_id'],
                'pay_price' => $price,
                'app_id' => self::$app_id,
                'pay_source' => $pay_source
            ];
            $status = $this->save($data);
        }
       
        // 余额支付标记订单已支付
        if ($status && $pay_type == OrderPayTypeEnum::BALANCE) {
            $this->onPaymentByBalance($this['order_no']);
        }

        return $this['order_id'];
    }

    /**
     * 待支付订单详情
     */
    public static function getPayDetail($orderNo)
    {
        $model = new static();
        return $model->where(['order_no' => $orderNo, 'pay_status' => 10])->with(['user'])->find();
    }
    /**
     * 余额支付标记订单已支付
     */
    public function onPaymentByBalance($orderNo)
    {
        // 获取订单详情
        $PaySuccess = new CashPaySuccessService($orderNo);
        // 发起余额支付
        $status = $PaySuccess->onPaySuccess(OrderPayTypeEnum::BALANCE);
        if (!$status) {
            $this->error = $PaySuccess->getError();
        }
        return $status;
    }
    /**
     * 构建支付请求的参数
     */
    public static function onOrderPayment($user, $order, $payType, $pay_source)
    {
        //如果来源是h5,首次不处理，payH5再处理
        if($pay_source == 'h5'){
            return [];
        }
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::onPaymentByWechat($user, $order, $pay_source);
        }
        return [];
    }
    /**
     * 构建微信支付请求
     */
    protected static function onPaymentByWechat($user, $order, $pay_source)
    {
        return PaymentService::wechat(
            $user,
            [$order],
            OrderTypeEnum::CASH,
            $pay_source
        );
    }
    
}
