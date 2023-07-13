<?php

namespace app\api\model\plus\live;

use app\api\service\order\PaymentService;
use app\api\service\order\paysuccess\type\PlanPaySuccessService;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\exception\BaseException;
use app\common\model\plugin\live\PlanOrder as PlanOrderModel;
use app\common\model\plugin\live\Plan as PlanModel;
/**
 * 礼物模型
 */
class PlanOrder extends PlanOrderModel
{

    /**
     * 创建订单
     */
    public function createOrder($user, $plan_id, $pay_type)
    {
        $plan = PlanModel::detail($plan_id);
        // 余额支付标记订单已支付
        if ($pay_type == OrderPayTypeEnum::BALANCE) {
            // 验证余额支付时用户余额是否满足
            if ($user['balance'] < $plan['money']) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false;
            }
        }
        // 获取订单数据
        $data = [
            'order_no' => 'GIFT' . $this->orderNo(),
            'user_id' => $user['user_id'],
            'plan_id' => $plan_id,
            'plan_name' => $plan['plan_name'],
            'pay_price' => $plan['money'],
            'gift_money' => $plan['gift_money'],
            'give_money' => $plan['give_money'],
            'total_money' =>  sprintf('%.2f', $plan['gift_money'] + $plan['give_money']),
            'app_id' => self::$app_id
        ];
        $status = $this->save($data);
        // 余额支付标记订单已支付
        if ($status && $pay_type == OrderPayTypeEnum::BALANCE) {
            $this->onPaymentByBalance($this['order_no']);
        }

        return $this['order_id'];
    }

    /**
     * 余额支付标记订单已支付
     */
    public function onPaymentByBalance($orderNo)
    {
        // 获取订单详情
        $PaySuccess = new PlanPaySuccessService($orderNo);
        // 发起余额支付
        $status = $PaySuccess->onPaySuccess(OrderPayTypeEnum::BALANCE);
        if (!$status) {
            $this->error = $PaySuccess->getError();
        }
        return $status;
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
     * 订单详情
     */
    public static function getUserOrderDetail($order_id, $user_id)
    {
        $model = new static();
        $order = $model->where(['order_id' => $order_id, 'user_id' => $user_id])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        return $order;
    }

    /**
     * 构建支付请求的参数
     */
    public static function onOrderPayment($user, $order, $payType, $pay_source,$code)
    {
        //如果来源是h5,首次不处理，payH5再处理
        if($pay_source == 'h5'){
            return [];
        }
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::onPaymentByWechat($user, $order, $pay_source,$code);
        }
        return [];
    }

    /**
     * 构建微信支付请求
     */
    protected static function onPaymentByWechat($user, $order, $pay_source,$code)
    {
        return PaymentService::wechat(
            $user,
            [$order],
            OrderTypeEnum::PLAN,
            $pay_source,
            $code
        );
    }
}