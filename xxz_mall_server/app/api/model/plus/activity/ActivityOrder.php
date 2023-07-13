<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\ActivityOrder AS ActivityOrderModel;
use app\api\model\plus\activity\ActivityOrderDetail as ActivityOrderDetailModel;
use app\api\model\plus\activity\ActivityLog as ActivityLogModel;
use app\api\service\order\paysuccess\type\ActivityPaySuccessService;
use app\api\service\order\PaymentService;
use app\api\model\plus\activity\Activity as ActivityModel;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;
use app\common\model\purveyor\Purveyor as SupplierModel;
use app\common\model\settings\Settings as SettingModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\library\helper;
use think\validate;
use think\facade\Cache;
use app\common\service\order\OrderRefundService;

/**
 * 活动报名提醒模型
 */
class ActivityOrder extends ActivityOrderModel
{
    public $model;
    protected $type = [
        'pay_time' => 'timestamp:Y-m-d H:i:s',
        'create_time' => 'timestamp:Y-m-d H:i:s'
    ];

    /**
     * 获取订单预览信息
     * @param $data
     * @param $user
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getActivityOrder($data,$user)
    {
        $order = array();
        // 获取活动信息
        $activity = (new ActivityModel())->detail($data['activity_id'],$user['user_id']);

        $activity_expand = $this->checkActivity($activity,$data,$user['user_id']);
        if (!$activity_expand) {
            return false;
        }
        Cache::set('activity_order_sign_field_' . $user['user_id']. '_' .$data['activity_id'], json_encode($activity_expand['sign_field'])); // 缓存报名参数
        Cache::set('activity_order_charge_' . $user['user_id'].'_' .$data['activity_id'], json_encode($activity_expand['charge'])); // 缓存选中的票种
        // 格式化报名信息
        $sign_info = '';
        if (isset($data['name']) && !empty($data['name'])) {
            $sign_info = $data['name'];
        }
        if (isset($data['mobile']) && !empty($data['mobile'])) {
            $Validate = new Validate();
            if (!empty($data['mobile']) && !$Validate->regex($data['mobile'], '/^1\d{10}$/') ) {
                $this->error = '手机号格式不正确';
                return false;
            }
            $sign_info .= "(" . $data['mobile'] . ")";
        }
        // 获取供应商信息
        $supplier = SupplierModel::detail($activity['shop_supplier_id']);
        if ($supplier) {
            $supplier_name = $supplier['name'];
        } else {
            $supplier_name = SettingModel::getItem('store')['name'];;
        }
        // 格式返回参数
        $order['total_price'] = $activity_expand['price'];
        $order['sign_info'] = $sign_info;
        $order['activity_id'] = $activity['id'];
        $order['activity_time_format'] = date('m/d H:i', strtotime($activity['activity_time_start'])) . ' - ' . date('m/d H:i' , strtotime($activity['activity_time_end']));
        $order['charge'] = $activity_expand['charge'];
        $order['name'] = $activity['name'];
        $order['file_path'] = $activity['file_path'];
        $order['address'] = $activity['address'];
        $order['region'] = $activity['region'];
        $order['sign_field'] = $activity_expand['sign_field'];
        $order['supplier_name'] = $supplier_name;
        $order['shop_supplier_id'] = $activity['shop_supplier_id'];
        $order['balance'] = $user['balance']; // 会员月
        return $order;
    }

    /**插入订单
     * @param $user
     * @param $params
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function createOrder ($user,$params)
    {
        $source = isset($params['source']) ? $params['source'] : 0; // 0:自主下单报名 ,1现场报名
        // 获取活动信息
        $activity = (new ActivityModel())->detail($params['activity_id'],$user['user_id']);
        // 验证活动是否满足下单条件
        $activity_expand = $this->checkActivity($activity,$params,$user['user_id']);
        if (!$activity_expand) {
            return false;
        }

        // 结束支付时间
        $config = SettingModel::getItem('trade');
        $closeDays = $config['order']['close_days'];
        $pay_end_time = time() + ((int)$closeDays * 86400);

        // 获取抽成比例
        $sys_config = SettingModel::getItem('store');
        $sys_percent = intval($sys_config['commission_rate']);
        $supplier_percent = 100 - $sys_percent;
        // 计算平台结算金额
        $sys_money = helper::number2($activity_expand['price'] * $sys_percent/100);
        // 计算商家结算金额
        $supplier_money = helper::number2($activity_expand['price'] * $supplier_percent/100);
        // 初始订单信息
        $price = helper::number2($activity_expand['price']);
        $order = array(
            'verify_come' => $source == 0 ? 2 : 0,
            'order_no' => $this->orderNo(),
            'user_id' => $user['user_id'],
            'total_price' => $price,
            'order_price' => $price,
            'pay_price' => $price,
            'pay_type' => isset($params['pay_type']) && $price > 0  ? $params['pay_type'] : OrderPayTypeEnum::BALANCE, // 支付方式
            'pay_source' => $params['pay_source'], // 支付来源 mp，wx，app
            'pay_status' => 10,
            'pay_end_time' => $pay_end_time,
            'shop_supplier_id' => $activity['shop_supplier_id'],
            'order_status' => 10,
            'app_id' => self::$app_id,
            'sys_money' => $sys_money,
            'supplier_money' => $supplier_money,
        );
        if ($order['pay_type'] == OrderPayTypeEnum::BALANCE) {
            // 验证余额支付时用户余额是否满足
            if ($user['balance'] < $order['pay_price']) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false;
            }
        }
        $this->model = $this;
        $this->transaction(function () use ($activity, $activity_expand,$order) {
            // 创建订单事件
            $this->model->createOrderEvent($activity, $activity_expand,$order);
            if ($order['pay_type'] == OrderPayTypeEnum::BALANCE) {
                $this->model->onPaymentByBalance( $this->model['order_no']);
            }

        });
        // 0：元订单直接支付
        if ($order['pay_price'] == 0) {
            // 获取订单详情
            $PaySuccess = new ActivityPaySuccessService($this->model['order_no']);
            // 发起余额支付
            $status = $PaySuccess->onPaySuccess($order['pay_type']);
            if (!$status) {
                $this->error = $PaySuccess->getError();
            }
        }
        $log_id = 0;
        // 现场报名 返回报名记录id
        if ($source == 1) {
            $log_info = (new ActivityLogModel())->getInfoWithOrder($this->model['order_id']);
            $log_id = isset($log_info['id']) ? $log_info['id'] : 0;
        }
        // 获取返回参数
        $return = [
            'log_id' => $log_id,
            'order_id' => $this->model['order_id'],
            'order_no' => $this->model['order_no'],
            'pay_price' => $this->model['pay_price'],
            'status' => $activity['is_audit'] == 1 ? 0 : 1,
            'is_paySuccess' => $order['pay_price'] == 0 || $order['pay_type'] == OrderPayTypeEnum::BALANCE ? 20 :10
        ];
        return $return;
    }

    /**
     * 插入订单数据
     * @param $activity
     * @param $data
     * @param $order
     * @return mixed
     */
    public function createOrderEvent($activity,$data,$order)
    {
        $this->model->save($order);
        $order_id = $this->model['order_id'];
        // 录入订单活动信息
        $order_activity = array(
            'activity_id' => $activity['id'],
            'order_id' => $order_id,
            'user_id' => $order['user_id'],
            'name' => $activity['name'],
            'image_id' => $activity['image_id'],
            'sign_field' => serialize($data['sign_field']),
            'charge' => serialize($data['charge']),
            'content' => $activity['content'],
            'total_num' => 1,
            'total_price' => $order['total_price'],
            'total_pay_price' => $order['pay_price'],
            'supplier_money' => $order['supplier_money'],
            'sys_money' => $order['sys_money'],
            'app_id' => self::$app_id,
            'signup_time_start' => strtotime($activity['signup_time_start']),
            'signup_time_end' => strtotime($activity['signup_time_end']),
            'activity_time_start' => strtotime($activity['activity_time_start']),
            'activity_time_end' => strtotime($activity['activity_time_end']),
            'sponsor' => $activity['sponsor'],
            'province' => $activity['province'],
            'city' => $activity['city'],
            'area' => $activity['area'],
            'address' => $activity['address'],
            'longitude' => $activity['longitude'],
            'latitude' => $activity['latitude'],
            'charge_type' => $activity['charge_type']['value'],
            'type'  => $activity['type']['value'],
            'is_audit' => $activity['is_audit'],
            'host_id' => $activity['host_id'],
        );
        (new ActivityOrderDetailModel())->save($order_activity);
        return $order_id;
    }
    /**
     * 余额支付标记订单已支付
     */
    public function onPaymentByBalance($orderNo)
    {
        // 获取订单详情
        $PaySuccess = new ActivityPaySuccessService($orderNo);
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
        return $model->where(['order_no' => $orderNo, 'pay_status' => 10 , 'order_status' => 10])->with(['activity'])->find();
    }
    /**
     * 活动下单 参数验证
     * @param $activity
     * @param $data
     * @param $user_id
     * @return array|bool
     */
    private function checkActivity($activity,$data,$user_id)
    {
        $return = array();
        if (!$activity) {
            $this->error = '活动不存在';
            return false;
        }
        // 验证报名时间
        if (strtotime($activity['signup_time_start']) > time()) {
            $this->error = '活动未开始';
            return false;
        }
        if (strtotime($activity['signup_time_end']) < time()) {
            $this->error = '活动已结束';
            return false;
        }

        // 验证限购
        if ($activity['limit_buy'] > 0) {
            $count = (new ActivityLog())->getUserByNumber($user_id,$activity['id']);
            if ($count >= $activity['limit_buy']) {
                $this->error = '当前活动限购' . $activity['limit_buy'] . "次";
                return false;
            }

        }

        // 验证票种
        // 收费活动 计算金额
        $price = 0.00; // 订单金额
        $charge = array(); // 选中的票种
        if ($activity['charge_type']['value'] == 1) {
            $charge_id = array_unique(helper::getArrayColumn($activity['charge'], 'id'));
            if (!isset($data['charge_id']) || !in_array($data['charge_id'],$charge_id)) {
                $this->error = '票种不合法';
                return false;
            }
            $price = $activity['charge'][$data['charge_id']]['price'];
            $charge = $activity['charge'][$data['charge_id']];
        }
        // 验证码报名参数
        $sign_field = [];
        if ($activity['sign_field']) {
            $sign_field = json_decode($data['sign_field'],true);
            foreach ($activity['sign_field'] as $key=>$item) {
                if (isset($item['tp_must']) && $item['tp_must'] == 1 && empty($sign_field[$key]['value'])) {
                    $this->error = isset($item['placeholder']) && $item['placeholder'] != '' ? $item['placeholder'] : $item['tp_name'] . '必填';
                    return false;
                }
            }
        }

        $return['price'] = $price;
        $return['charge'] = $charge;
        $return['sign_field'] = $sign_field;

        return $return;
    }

    /**
     * 构建支付请求的参数
     */
    public static function onOrderPayment($user, $order_arr, $payType, $pay_source,$code='')
    {
        //如果来源是h5,首次不处理，payH5再处理
        if($pay_source == 'h5'){
            return [];
        }
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::onPaymentByWechat($user, $order_arr, $pay_source,$code);
        }
        if ($payType == OrderPayTypeEnum::ALIPAY) {
            return self::onPaymentByAlipay($user, $order_arr, $pay_source);
        }
        return [];
    }

    /**
     * 订单详情
     * @param $order_id
     * @param $user_id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function OrderDetail($order_id,$user_id)
    {
        $info = $this->field('create_time,order_no,order_id,user_id,pay_price,pay_type,pay_time,shop_supplier_id,update_time')->with([
            'activity' => function($query) {
                $query->with('image');
                $query->field('type,id,name,order_id,activity_time_start,signup_time_start,signup_time_end,activity_time_end,province,city,area,address,sign_field,charge_type,charge,total_pay_price,longitude,latitude');
            },
        ])->where(array('user_id' => $user_id , 'order_id' => $order_id))->find();
        if (empty($info)) {
            $this->error = '订单不存在';
        }
        if (empty($info['activity'])) {
            $this->error = '订单异常';
        }

        // 处理返回参数
        $info['pay_type_name'] = $info['activity'][0]['charge_type']['value'] == 1 ? $info['pay_type']['text'] : '免费';
        // 获取供应商信息
        $supplier = SupplierModel::detail($info['shop_supplier_id']);
        if ($supplier) {
            $info['shop_supplier_name'] = $supplier['name'];
        } else {
            $info['shop_supplier_name'] = SettingModel::getItem('store')['name'];;
        }
        return $info;
    }
    /**
     * 构建微信支付请求
     */
    protected static function onPaymentByWechat($user, $order_arr, $pay_source,$code='')
    {
        return PaymentService::wechat(
            $user,
            $order_arr,
            OrderTypeEnum::ACTIVITY,
            $pay_source,
            $code
        );
    }

    /**
     * 构建支付宝请求
     */
    protected static function onPaymentByAlipay($user, $order_arr, $pay_source)
    {
        return PaymentService::alipay(
            $user,
            $order_arr,
            OrderTypeEnum::ACTIVITY,
            $pay_source
        );
    }

    /**
     * 执行订单退款
     */
    public function refund($log_info)
    {
        // 获取订单信息
        $order = $this->OrderDetail($log_info['order_id'],$log_info['user_id']);
        if (empty($order)) {
            return false;
        }
        // 订单取消事件
        $status = $order->transaction(function () use ($order,$log_info) {
            // 订单支付金额大于0 原路退回
            if ($order['pay_price'] > 0) {
                $model = new OrderRefundService();
                // 执行退款操作
                $model->execute($order,$order['pay_price'],BalanceLogSceneEnum::ACTIVITY);
            }
            // 更新报名记录状态
            $log_info->save(['status' => 2]);
            // 更新订单状态
            return $order->save(['order_status' => 40]);
        });
        return $status;
    }
}
