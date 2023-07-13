<?php

namespace app\api\service\order\paysuccess\type;

use app\api\model\user\User as UserModel;
use app\api\model\supplier\DepositOrder as DepositOrderModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum;
use app\common\model\user\BalanceLog as BalanceLogModel;
use app\common\service\BaseService;
use app\common\model\purveyor\User as SupplierUserModel;
use app\common\model\purveyor\Purveyor as SupplierModel;
/**
 * 押金支付成功服务类
 */
class CashPaySuccessService extends BaseService
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
        $this->model = DepositOrderModel::getPayDetail($orderNo);
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
        // 订单支付成功行为
        if ($status == true) {
            //更新用户类型
            UserModel::updateType($this->user['user_id'], 2);
        }
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
            // 更新申请状态和添加账号
            $this->updateSupplier($this->user['user_id']);
        });
        return true;
    }
    //更新申请状态和添加账号
    public function updateSupplier($user_id){
        $supplier_user = (new SupplierUserModel())->where(['user_id'=>$user_id])->find();
        $supplier = SupplierModel::detail($supplier_user['shop_supplier_id']);
        if($supplier_user){
            //更新
            $supplier->save([
                'status' => 0,
                'deposit_money' => $this->model['pay_price']
            ]);
        }
    }

    /**
     * 更新订单记录
     */
    private function updateOrderInfo($payType, $payData)
    {
        // 整理订单信息
        $pay_source = '';
        if (isset($payData['attach'])) {
            $attach = json_decode($payData['attach'], true);
            $pay_source = isset($attach['pay_source']) ?$attach['pay_source']:'';
        }

        $order = [
            'pay_type' => $payType,
            'pay_status' => 20,
            'pay_time' => time(),
            'pay_source' => $pay_source
        ];
        if ($payType == OrderPayTypeEnum::WECHAT) {
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
            ], ['描述' => '支付开店押金']);
        }
    }

}