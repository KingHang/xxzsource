<?php

namespace app\job\controller;


use app\api\model\order\Order as OrderModel;
use app\common\library\alipay\AliPay;
use app\common\library\easywechat\WxPay;
use app\common\model\plugin\agent\OrderDetail;
use app\job\model\plus\agent\Month as AgentMonthModel;
/**
 * 微信支付回调
 */
class Notify
{
    /**
     * 微信支付回调
     */
    public function wxpay()
    {
        // 微信支付组件：验证异步通知
        $WxPay = new WxPay(false);
        $WxPay->notify();
    }

    /**
     * 支付宝支付回调（同步）
     */
    public function alipay_return()
    {
        $AliPay = new AliPay($_POST['pay_source']);
        $url = $AliPay->return();
        if($url){
            return redirect($url);
        }
    }

    /**
     * 支付宝支付回调（异步）
     */
    public function alipay_notify()
    {
        $AliPay = new AliPay(isset($_POST['pay_source']) ? $_POST['pay_source'] : 0);
        $AliPay->notify();
    }

    /**
     * 测试结算
     */
    public function settled()
    {
        $detail = OrderModel::getUserOrderDetail(1, 24622);
//        OrderDetail::addBouns($detail);
        // 获取一个未结算的用户
        $model = new AgentMonthModel();
        $detail = $model->getNoBounsSettled(10001);
//        var_dump($detail);die;
        if (empty($detail)) return;
        $detail->settledMoney(10001);
    }
}
