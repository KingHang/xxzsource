<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\api\model\user\BalanceOrder as BalanceOrderModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\user\Sms as SmsModel;
use app\api\model\user\UserWeb as UserModel;

/**
 * h5 web用户管理
 */
class Userweb extends Controller
{

    /**
     * 用户自动登录,默认微信小程序
     */
    public function login()
    {
        $model = new UserModel;
        $user_id = $model->login($this->request->post());
        if($user_id == 0){
            return $this->renderError($model->getError() ?:'登录失败');
        }
        return $this->renderSuccess('',[
            'user_id' => $user_id,
            'token' => $model->getToken()
        ]);
    }

    /**
     * 短信登录
     */
    public function sendCode($mobile)
    {
        $model = new SmsModel();
        if($model->send($mobile, 'login')){
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError() ?:'发送失败');
    }

    public function bindMobile(){
        $model = new UserModel;
        if($model->bindMobile($this->getUser(), $this->request->post())){
            return $this->renderSuccess('绑定成功');
        }
        return $this->renderError($model->getError() ?:'绑定失败');
    }

    public function payH5($order_id, $order_type = OrderTypeEnum::MASTER){
        $order = OrderModel::detail($order_id);
        $user = $order['user'];
        $model = null;
        $payment = null;
        $return_Url = '';
        if($order_type == OrderTypeEnum::MASTER){
            // 订单详情
            $model = OrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 构建支付请求
            $payment = OrderModel::onOrderPayment($user, [$model], OrderPayTypeEnum::WECHAT, 'payH5');
            $return_Url = urlencode(base_url()."h5/pages/index/order/myorder");
        }else if($order_type == OrderTypeEnum::BALANCE){
            // 订单详情
            $model = BalanceOrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 构建支付请求
            $payment = BalanceOrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::WECHAT, 'payH5');
            $return_Url = urlencode(base_url()."h5/pages/user/my-wallet/my-wallet");
        }

        return $this->renderSuccess('',[
            'order' => $model,  // 订单详情
            'mweb_url' => $payment['mweb_url'],
            'return_Url' => $return_Url
        ]);
    }

    /**
     * h5下支付宝支付
     */
    public function alipayH5($order_id, $order_type = OrderTypeEnum::MASTER, $pay_source = 'payH5'){
        $order = OrderModel::detail($order_id);
        $user = $order['user'];
        $payment = [];
        if($order_type == OrderTypeEnum::MASTER){
            // 订单详情
            $model = OrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 构建支付请求
            $payment = OrderModel::onOrderPayment($user, [$model], OrderPayTypeEnum::ALIPAY, $pay_source);
        }else if($order_type == OrderTypeEnum::BALANCE){
            // 订单详情
            $model = BalanceOrderModel::getUserOrderDetail($order_id, $user['user_id']);
            // 构建支付请求
            $payment = BalanceOrderModel::onOrderPayment($user, $model, OrderPayTypeEnum::ALIPAY, $pay_source);
        }
        return $this->renderSuccess('',[
            'payment' => $payment,
        ]);
    }
    /**
     * 立即支付
     */
    public function pay($order_id, $payType = OrderPayTypeEnum::WECHAT, $pay_source = 'wx')
    {
        $code = $this->request->post('code');
        $user = $this->getUser();
        // 获取订单详情
        $order_arr = [];
        $order_id_arr = explode(',' , $order_id);
        foreach ($order_id_arr as $item) {
            $model = OrderModel::getUserOrderDetail($item, $user['user_id']);
            // 订单支付事件
            if (!$model->onPay($payType)) {
                return $this->renderError($model->getError() ?: '订单支付失败');
            }
            $order_arr[] = $model;
        }
        // 构建微信支付请求
        $payment = $model->onOrderPayment($user, $order_arr, $payType, $pay_source,$code);
        // 支付状态提醒
        return $this->renderSuccess('', [
            'order_id' => $order_id,   // 订单id
            'pay_type' => $payType,             // 支付方式
            'payment' => $payment,            // 微信支付参数
            'model' => $order_arr
        ]);
    }
}
