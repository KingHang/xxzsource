<?php

namespace app\api\service\order\paysuccess\type;

use app\api\model\user\User as UserModel;
use app\api\model\plugin\activity\ActivityOrder;
use app\api\model\plugin\activity\ActivityLog;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\service\BaseService;
/**
 * 活动报名支付成功服务类
 */
class ActivityPaySuccessService extends BaseService
{
    // 订单模型
    public $model;

    // 当前用户信息
    private $user;

    /**
     * 构造函数
     */
    public function __construct($orderNo)
    {
        // 实例化订单模型
        $this->model = ActivityOrder::getPayDetail($orderNo);
        // 获取用户信息
        $this->user = UserModel::detail($this->model['user_id']);
    }

    /**
     * 返回app_id，大于0则存在订单信息
     */
    public function isExist(){
        if($this->model){
            return $this->model['app_id'];
        }
        return 0;
    }
    /**
     * 订单支付成功业务处理
     */
    public function onPaySuccess($payType, $payData = [])
    {
        if (empty($this->model)) {
            $this->error = '未找到该订单信息';
            return false;
        }
        // 更新付款状态
        $status = $this->updatePayStatus($payType, $payData);

        return $status;
    }

    /**
     * 更新付款状态
     */
    private function updatePayStatus($payType, $payData = [])
    {
        // 事务处理
        $this->model->transaction(function () use ($payType, $payData) {
            // 更新订单状态
            $this->updateOrderInfo($payType, $payData);
            // 记录订单支付信息
            $this->updatePayInfo($payType);
            // 更新活动报名日志
            $this->updateActivityLog();
            // 累积用户总消费金额
            $this->user->setIncPayMoney($this->model['pay_price']);
        });
        return true;
    }
    public function updateActivityLog()
    {
        $activity_log = [
            'order_id' => $this->model['order_id'],
            'user_id'   => $this->model['user_id'],
            'app_id' => $this->model['app_id'],
        ];
        foreach ($this->model['activity'] as $key=>$activity) {
            $charge = !empty($activity['charge']) ? $activity['charge'] : array();
            $sign_field = !empty($activity['sign_field']) ? $activity['sign_field'] : array();
            $activity_log['name'] = isset($sign_field['need_name']['value']) ? $sign_field['need_name']['value'] : '';
            $activity_log['mobile'] = isset($sign_field['need_mobile']['value']) ? $sign_field['need_mobile']['value'] : '';
            if (!empty($sign_field)) {
                foreach($sign_field as $k=>$v) {
                    if ($v['custom'] == 0) {
                        unset($sign_field[$k]);
                    }
                }
            }
            $activity_log['activity_id'] = $activity['activity_id'];
            $activity_log['order_detail_id'] = $activity['id'];
            $activity_log['charge'] = serialize($charge);
            $activity_log['sign_field'] = serialize($sign_field);
            $activity_log['verify_code'] = $this->random(7, true);
            $activity_log['status'] = $activity['is_audit'] == 1 ? 0 : 1;
            $activity_log['charge_type'] = $activity['charge_type']['value'];

            (new ActivityLog())->save($activity_log);
        }

    }
    /**
     * 更新订单记录
     */
    private function updateOrderInfo($payType, $payData)
    {
        // 整理订单信息
        $order = [
            'pay_type' => $payType,
            'pay_status' => 20,
            'pay_time' => time(),
            'order_status' => 30 , // 活动订单支付成功后直接完成
        ];
        // 整理订单信息
        if (isset($payData['attach'])) {
            $attach = json_decode($payData['attach'], true);
            $order['pay_source'] = isset($attach['pay_source']) ?$attach['pay_source']:'';
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
            // 余额日志
            BalanceLogModel::add(BalanceLogSceneEnum::CONSUME, [
                'user_id' => $this->user['user_id'],
                'money' => -$this->model['pay_price'],
            ], ['描述' => '支付活动报名']);
        }
    }
    /**
     * 生产随机字符串
     * @param $length
     * @param bool $numeric
     * @return string
     */
    public function random($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

}