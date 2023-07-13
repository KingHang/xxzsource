<?php

namespace app\api\controller\order;

use app\api\model\order\Cart as CartModel;
use app\api\model\order\Order as OrderModel;
use app\api\service\order\settled\MasterOrderSettledService;
use app\api\controller\Controller;
use app\api\model\settings\Message as MessageModel;
use app\api\service\pay\PayService;
use app\common\enum\order\OrderTypeEnum;
use app\common\library\helper;
use app\api\model\settings\Settings as SettingModel;
use app\api\service\order\PaymentService;
use app\common\model\order\OrderTrade as OrderTradeModel;
/**
 * 普通订单
 */
class Order extends Controller
{
    /**
     * 订单确认
     */
    public function buy()
    {
        // 立即购买：获取订单商品列表
        $params = json_decode($this->postData()['params'], true);
        $supplierData = OrderModel::getOrderProductListByNow($params);
        $user = $this->getUser();
        // 实例化订单service
        $orderService = new MasterOrderSettledService($user, $supplierData, $params);
        // 获取订单信息
        $orderInfo = $orderService->settlement();
        // 订单结算提交
        if ($orderService->hasError()) {
            return $this->renderError($orderService->getError());
        }
        if ($this->request->isGet()) {
            // 如果来源是小程序, 则获取小程序订阅消息id.获取支付成功,发货通知.
            $template_arr = MessageModel::getMessageByNameArr($params['pay_source'], ['order_pay_user', 'order_delivery_user']);
             //是否显示店铺信息
            $store_open=SettingModel::getStoreOpen();
            // 是否开启支付宝支付
            $show_alipay = PayService::isAlipayOpen($params['pay_source'], $user['app_id']);
            $balance = $user['balance'];
            return $this->renderSuccess('', compact('orderInfo', 'template_arr', 'store_open', 'show_alipay' , 'balance'));
        }
        // 创建订单
        $order_arr = $orderService->createOrder($orderInfo);
        if(!$order_arr){
            return $this->renderError($orderService->getError() ?: '订单创建失败');
        }
        // 构建支付请求
//        $payment = OrderModel::onOrderPayment($user, $order_arr, $params['pay_type'], $params['pay_source']);

        // 返回结算信息
        return $this->renderSuccess(['success' => '支付成功', 'error' => '订单未支付'], [
            'order_id' => helper::getArrayColumn($order_arr, 'order_id'),   // 订单id
//            'pay_type' => $params['pay_type'],  // 支付方式
//            'payment' => $payment,               // 微信支付参数
            'order_type' => OrderTypeEnum::MASTER, //订单类型
        ]);
    }

    /**
     * 订单确认-立即购买
     */
    public function cart()
    {
        // 立即购买：获取订单商品列表
        if ($this->request->isGet()) {
            $params = json_decode($this->postData()['params'], true);
        }else{
            $params = json_decode($this->postData()['params'], true);
        }
        $user = $this->getUser();
        // 商品结算信息
        $CartModel = new CartModel($user);
        // 购物车商品列表
        $supplierData = $CartModel->getList($params['cart_ids'],'',$is_show_gift=1,$params);
        // 实例化订单service
        $orderService = new MasterOrderSettledService($user, $supplierData, $params);
        // 获取订单信息
        $orderInfo = $orderService->settlement();
        if ($this->request->isGet()) {
            // 如果来源是小程序, 则获取小程序订阅消息id.获取支付成功,发货通知.
            $template_arr = MessageModel::getMessageByNameArr($params['pay_source'], ['order_pay_user', 'order_delivery_user']);
             //是否显示店铺信息
            $store_open=SettingModel::getStoreOpen();
            // 是否开启支付宝支付
            $show_alipay = PayService::isAlipayOpen($params['pay_source'], $user['app_id']);
            $balance = $user['balance'];
            return $this->renderSuccess('', compact('orderInfo', 'template_arr','store_open', 'show_alipay', 'balance'));
        }
        // 订单结算提交
        if ($orderService->hasError()) {
            return $this->renderError($orderService->getError());
        }
        // 创建订单
        $order_arr = $orderService->createOrder($orderInfo);
        if(!$order_arr){
            return $this->renderError($orderService->getError() ?: '订单创建失败');
        }
        // 移出购物车中已下单的商品
        $CartModel->clearAll($params['cart_ids']);
        // 构建支付请求
//        $payment = OrderModel::onOrderPayment($user, $order_arr, $params['pay_type'], $params['pay_source']);
        // 返回结算信息
        return $this->renderSuccess('', [
            'order_id' => helper::getArrayColumn($order_arr, 'order_id'),   // 订单id
//            'pay_type' => $params['pay_type'],  // 支付方式
//            'payment' => $payment,               // 微信支付参数
            'order_type' => OrderTypeEnum::MASTER, //订单类型
        ]);
    }

    /**
     * 订单付款
     */
    public function pay()
    {
        $param = $this->postData();
        $model = new OrderModel();
        $payDetail = $model->getPay($param['orderNo'], $this->getUser());
        if ($this->request->isGet()) {
            return $this->renderSuccess('', [
                'payInfo' => $payDetail,   // 支付详情
            ]);
        }
        $payInfo = PaymentService::UserPay($param, $payDetail, OrderTypeEnum::MASTER, $this->getUser());
        return $this->renderSuccess('', [
            'payInfo' => $payInfo,   // 支付详情
            'order_id' => $payInfo['order_id'],   // 订单id
            'pay_type' => $param['payType'],  // 支付方式
            'payment' => $payInfo['payment'],  // 支付参数
            'order_type' => OrderTypeEnum::MASTER, //订单类型
        ]);
    }

    /**
     * 支付成功详情信息
     */
    public function paySuccess($orderNo, $multiple)
    {
        if ($multiple == 1) {
            $orderIds = OrderTradeModel::where('out_trade_no', '=', $orderNo)
                ->column('order_id');
            $order_num = count($orderIds);
            $order = [
                'pay_price' => 0,
                'order_num' => $order_num,
                'order_no' => ''
            ];
            $model = '';
            foreach ($orderIds as $id) {
                $model = OrderModel::detail($id);
                $order['pay_price'] += $model['pay_price'];
            }
            $order['order_no'] = $model ? $model['order_no'] : '';
        } else {
            $orderDetail = OrderModel::detail(['order_no' => $orderNo]);
            $order = [
                'pay_price' => $orderDetail['pay_price'],
                'order_num' => 1,
                'order_no' => $orderNo
            ];
        }
        return $this->renderSuccess('', compact('order'));
    }
}