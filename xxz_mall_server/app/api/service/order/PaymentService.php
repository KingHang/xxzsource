<?php

namespace app\api\service\order;
use app\common\library\alipay\AliPay;
use app\common\library\easywechat\AppOpen;
use app\common\library\easywechat\AppWx;
use app\common\library\easywechat\AppMp;
use app\common\library\easywechat\WxPay;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\library\helper;
use app\common\model\app\AppWx as AppWxModel;
use app\common\model\order\OrderTrade as OrderTradeModel;
use app\common\service\order\OrderService;
use app\common\model\user\User as UserModel;
use app\common\exception\BaseException;
use app\api\service\order\paysuccess\type\PayTypeSuccessFactory;
use app\common\model\user\Sms as SmsModel;
use app\common\service\qrcode\OrderService as OrderQrcode;
use app\common\model\order\Order;
use think\facade\Env;

class PaymentService
{
    /**
     * 构建订单支付参数
     */
    public static function orderPayment($user, $order, $payType)
    {
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::wechat(
                $user,
                $order['order_id'],
                $order['order_no'],
                $order['pay_price'],
                OrderTypeEnum::MASTER
            );
        }
        return [];
    }

    /**
     * 构建微信支付
     */
    public static function wechat(
        $user,
        $order_arr,
        $orderType = OrderTypeEnum::MASTER,
        $pay_source,
        $code=''
    )
    {
        // 统一下单API
        $app = null;
        if($pay_source == 'wx'){
            $app = AppWx::getWxPayApp($user['app_id']);

            $wxConfig = AppWxModel::getAppWxCache($user['app_id']);
            // 验证appid和appsecret是否填写
            if (empty($wxConfig['wxapp_id']) || empty($wxConfig['wxapp_secret'])) {
                throw new BaseException(['msg' => '请到 [后台-应用-小程序设置] 填写appid 和 appsecret']);
            }

            $data['app_id'] = $wxConfig['wxapp_id'];
            $data['code'] = $code;
            if (!empty($code)){
                $data['secret'] = $wxConfig['wxapp_secret'];
                $result = \app\common\model\user\User::getOpenId($data);
                if ($result['openid']){
                    $open_id = $result['openid'];
                }else{
                    $open_id = $user['open_id'];
                }
            }else{
                $open_id = $user['open_id'];
            }


        }else if($pay_source == 'mp'){
            $app = AppMp::getWxPayApp($user['app_id']);
            $open_id = $user['mpopen_id'];
        } else if($pay_source == 'payH5'){
            $app = AppMp::getWxPayApp($user['app_id']);
            $open_id = '';
        } else if($pay_source == 'app'){
            $app = AppOpen::getWxPayApp($user['app_id']);
            $open_id = $user['appopen_id'];
        }

        //如果订单数大于1，则创建外部交易号
        $multiple = 0;
        if(count($order_arr) > 1){
            $orderNo = OrderService::createOrderNo();
            $payPrice =  helper::number2(helper::getArrayColumnSum($order_arr, 'pay_price'));

            //记录out_trade_no跟order_id对应关系
            foreach($order_arr as $order){
                $trade_model = new OrderTradeModel;
                $trade_list = [];
                $trade_list[] = [
                    'out_trade_no' => $orderNo,
                    'order_id' => $order['order_id'],
                    'app_id' => $order['app_id']
                ];
                $trade_model->saveAll($trade_list);
            }
            $multiple = 1;
        }else{
            $orderNo = $order_arr[0]['order_no'];
            $payPrice = $order_arr[0]['pay_price'];
        }

        $WxPay = new WxPay($app);
        $payment = $WxPay->unifiedorder($orderNo, $open_id, $payPrice, $orderType, $pay_source, $multiple);
        if($pay_source == 'wx'){
            return $payment;
        }else if($pay_source == 'mp'){
            $jssdk = $app->jssdk;
            return $jssdk->bridgeConfig($payment['prepay_id']);
        }else if($pay_source == 'payH5'){
            return $payment;
        }else if($pay_source == 'app'){
            return $payment;
        }
    }


    /**
     * 构建支付宝支付
     */
    public static function alipay(
        $user,
        $order_arr,
        $orderType = OrderTypeEnum::MASTER,
        $pay_source
    )
    {
        //如果订单数大于1，则创建外部交易号
        $multiple = 0;
        if(count($order_arr) > 1){
            $orderNo = OrderService::createOrderNo();
            $payPrice =  helper::number2(helper::getArrayColumnSum($order_arr, 'pay_price'));

            //记录out_trade_no跟order_id对应关系
            foreach($order_arr as $order){
                $trade_model = new OrderTradeModel;
                $trade_list = [];
                $trade_list[] = [
                    'out_trade_no' => $orderNo,
                    'order_id' => $order['order_id'],
                    'app_id' => $order['app_id']
                ];
                $trade_model->saveAll($trade_list);
            }
            $multiple = 1;
        }else{
            $orderNo = $order_arr[0]['order_no'];
            $payPrice = $order_arr[0]['pay_price'];
        }

        $AliPay = new AliPay($pay_source);
        $payment = $AliPay->unifiedorder($orderNo, $payPrice, $orderType, $pay_source, $multiple);
        return $payment;
    }

    public static function AgentPay($param, $orderInfo, $order_type, $user)
    {
        if ($param['payType'] == 40) {
            $param['payType'] = 20;
            $param['pay_source'] = 'qr';
        }
        $qrcode = '';
        $multiple = $orderInfo['multiple'];
        $payPrice = $orderInfo['payPrice'];
        $order_id = $orderInfo['order_id'];
        $payment = [];
        if ($param['payType'] == OrderPayTypeEnum::ALIPAY) {
            $AliPay = new AliPay($param['pay_source']);
            $payment = $AliPay->unifiedorder($param['orderNo'], $payPrice, $order_type, $param['pay_source'], $multiple);

        }
        if ($param['payType'] == OrderPayTypeEnum::WECHAT) {
            $app = '';
            $open_id = '';
            if ($param['pay_source'] == 'wx') {
                $app = AppWx::getFapp();
                $session = $app->auth->session($param['code']);
                $open_id = $session['openid'];
                $app = AppWx::getFwxPayApp($user['app_id']);
//                $open_id = $user['open_id'];
            } else if ($param['pay_source'] == 'mp') {
                $app = AppMp::getWxPayApp($user['app_id']);
                $open_id = $user['mpopen_id'];
            } else if ($param['pay_source'] == 'payH5') {
                $app = AppMp::getWxPayApp($user['app_id']);
                $open_id = '';
            } else if ($param['pay_source'] == 'app') {
                $app = AppOpen::getWxPayApp($user['app_id']);
//                $open_id = $user['appopen_id'];
                $open_id = '';
            } elseif ($param['pay_source'] == 'qr') {
                $app = AppWx::getWxPayApp($user['app_id']);
                $open_id = '';
            }
            $WxPay = new WxPay($app);
            $payment = $WxPay->unifiedorder($param['orderNo'], $open_id, $payPrice, $order_type, $param['pay_source'], $multiple,2);
            if ($param['pay_source'] == 'mp') {
                $jssdk = $app->jssdk;
                $payment = $jssdk->bridgeConfig($payment['prepay_id']);
            }
            if ($param['pay_source'] == 'qr') {
                // 生产支付二维码
                // 生产二维码
                $Qrcode = new OrderQrcode(
                    $param['orderNo'],
                    $param['app_id'],
                    $payment['code_url'],
                   'wxpay'
                );
                $qrcode = $Qrcode->getImage();
            }
        }
        if ($param['payType'] == OrderPayTypeEnum::BALANCE) {
            $userDetail = UserModel::detail($orderInfo['order']['user_id']);
            if (!self::check($param,'balance_pay')) {
                return false;
            }
            if ($userDetail['balance'] < $payPrice) {
                throw new BaseException(['msg' => '用户余额不足，无法使用余额支付']);
            }
            $attach['order_type'] = $order_type;
            $attach['multiple'] = $multiple;
            // 实例化订单模型
            $PaySuccess = PayTypeSuccessFactory::getFactory($param['orderNo'], $attach);
            $status = $PaySuccess->balanceonPaySuccess(OrderPayTypeEnum::BALANCE);
            if (!$status) {
                throw new BaseException(['msg' => $PaySuccess->getError()]);
            }
        }
        $payInfo['payPrice'] = $payPrice;
        $payInfo['payment'] = $payment;
        $payInfo['multiple'] = $multiple;
        $payInfo['order_id'] = $order_id;
        $payInfo['qrcode'] = $qrcode;
        return $payInfo;
    }

    public static function UserPay($param, $orderInfo, $order_type, $user)
    {
        $multiple = $orderInfo['multiple'];
        $payPrice = $orderInfo['payPrice'];
        $order_id = $orderInfo['order_id'];
        $payment = [];
        if ($param['payType'] == OrderPayTypeEnum::ALIPAY) {
            $AliPay = new AliPay($param['pay_source']);
            $payment = $AliPay->unifiedorder($param['orderNo'], $payPrice, $order_type, $param['pay_source'], $multiple);

        }
        if ($param['payType'] == OrderPayTypeEnum::WECHAT) {
            $app = '';
            $open_id = '';
            if ($param['pay_source'] == 'wx') {
                $app = AppWx::getwxPayApp($user['app_id']);
                $wxConfig = AppWxModel::getAppWxCache($user['app_id']);
                // 验证appid和appsecret是否填写
                if (empty($wxConfig['wxapp_id']) || empty($wxConfig['wxapp_secret'])) {
                    throw new BaseException(['msg' => '请到 [后台-应用-小程序设置] 填写appid 和 appsecret']);
                }

                $data['app_id'] = $wxConfig['wxapp_id'];
                $data['code'] = $param['code'];
                $data['secret'] = $wxConfig['wxapp_secret'];
                $result = \app\common\model\user\User::getOpenId($data);
                $open_id = $result['openid'];
            } else if ($param['pay_source'] == 'mp') {
                $app = AppMp::getWxPayApp($user['app_id']);
                $open_id = $user['mpopen_id'];
            } else if ($param['pay_source'] == 'payH5') {
                $app = AppMp::getWxPayApp($user['app_id']);
                $open_id = '';
            } else if ($param['pay_source'] == 'app') {
                $app = AppOpen::getWxPayApp($user['app_id']);
                $open_id = $user['appopen_id'];
            }
            $WxPay = new WxPay($app);
            $payment = $WxPay->unifiedorder($param['orderNo'], $open_id, $payPrice, $order_type, $param['pay_source'], $multiple);
            if ($param['pay_source'] == 'mp') {
                $jssdk = $app->jssdk;
                $payment = $jssdk->bridgeConfig($payment['prepay_id']);
            }

        }
        if ($param['payType'] == OrderPayTypeEnum::BALANCE) {
            if ($user['balance'] < $payPrice) {
                throw new BaseException(['msg' => '用户余额不足，无法使用余额支付']);
            }
            $attach['order_type'] = $order_type;
            $attach['multiple'] = $multiple;
            // 实例化订单模型
            $PaySuccess = PayTypeSuccessFactory::getFactory($param['orderNo'], $attach);
            $status = $PaySuccess->balanceonPaySuccess(OrderPayTypeEnum::BALANCE);
            if (!$status) {
                throw new BaseException(['msg' => $PaySuccess->getError()]);
            }
        }
        $payInfo['payPrice'] = $payPrice;
        $payInfo['payment'] = $payment;
        $payInfo['multiple'] = $multiple;
        $payInfo['order_id'] = $order_id;
        return $payInfo;
    }

    /**
     * 验证
     */
    public static function check($data , $sence = '')
    {
        //判断验证码是否过期、是否正确
        $sms_model = new SmsModel();
        $sms_record_list = $sms_model
            ->where('mobile', '=', $data['mobile'])
            ->where('sence', '=', $sence)
            ->order(['create_time' => 'desc'])
            ->limit(1)->select();

        if (count($sms_record_list) == 0) {
            throw new BaseException(['msg' => '未查到短信发送记录']);
        }
        $sms_model = $sms_record_list[0];
        if ((time() - strtotime($sms_model['create_time'])) / 60 > 30) {
            throw new BaseException(['msg' => '短信验证码超时']);
        }
        if ($sms_model['code'] != $data['sms_code']) {
            throw new BaseException(['msg' => '验证码不正确']);
        }
        return true;
    }
}