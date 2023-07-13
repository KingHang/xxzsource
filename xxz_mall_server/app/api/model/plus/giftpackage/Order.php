<?php

namespace app\api\model\plus\giftpackage;

use app\api\service\order\PaymentService;
use app\api\service\order\paysuccess\type\GiftPaySuccessService;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\plugin\giftpackage\Order as OrderModel;
use app\common\model\plugin\giftpackage\GiftPackage as GiftPackageModel;

/**
 * 礼包购模型
 */
class Order extends OrderModel
{

    /**
     * 创建礼包购订单
     * 返回订单id
     */
    public function createOrder($user, $gift_package_id, $params)
    {
        $detail = GiftPackageModel::detail($gift_package_id);
        if ($detail['is_times'] == 1) {
            $where = [
                'user_id' => $user['user_id'],
                'gift_package_id' => $gift_package_id,
            ];
            //统计购买数量
            $count = $this->where($where)->count('order_id');
            //判断购买次数
            if ($count >= $detail['times']) {
                $this->error = '已超过限购数量';
                return false;
            }
        }
        //判断是否符合等级
        if ($detail['is_grade'] == 1 && !in_array($user['grade_id'], explode(',', $detail['grade_ids']))) {
            return false;
        }
        //写入order表
        $status = $this->save([
            'gift_package_id' => $gift_package_id,
            'order_no' => $this->orderNo(),
            'total_price' => $detail['money'],
            'order_price' => $detail['money'],
            'pay_price' => $detail['money'],
            'user_id' => $user['user_id'],
            'app_id' => self::$app_id
        ]);
        // 余额支付标记订单已支付
        if ($status && $params['pay_type'] == OrderPayTypeEnum::BALANCE) {
            $this->onPaymentByBalance($this['order_no']);
        }

        return $status;
    }

    /**
     * 余额支付标记订单已支付
     */
    public function onPaymentByBalance($orderNo)
    {
        // 获取订单详情
        $PaySuccess = new GiftPaySuccessService($orderNo);
        // 发起余额支付
        $status = $PaySuccess->onPaySuccess(OrderPayTypeEnum::BALANCE);

        return $status;
    }

    /**
     * 待支付订单详情
     */
    public static function getPayDetail($orderNo)
    {
        $model = new static();
        return $model->where(['order_no' => $orderNo, 'pay_status' => 10, 'is_delete' => 0])->with(['user'])->find();
    }

    /**
     * 构建支付请求的参数
     */
    public static function onOrderPayment($user, $order, $payType, $pay_source ,$code ='')
    {
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::onPaymentByWechat($user, $order, $pay_source,$code);
        }
        return [];
    }

    /**
     *
     * 构建微信支付请求
     */
    protected static function onPaymentByWechat($user, $order, $pay_source ,$code='')
    {
        return PaymentService::wechat(
            $user,
            $order['order_id'],
            $order['order_no'],
            $order['pay_price'],
            $code,
            OrderTypeEnum::GIFT,
            $pay_source
        );
    }
}