<?php

namespace app\common\service\ztservice;

use app\api\controller\Controller;
use app\XxzController;
use app\timebank\model\Timebank_basicsetup;
use think\facade\Db;
use think\facade\Request;
use think\Response;
use think\facade\Env;

/**
 * 中台服务
 */
class ZtService
{
    /**
     * @param int $type
     * @return string
     */
    public function getAccessToken($type = 0)
    {
        $url = Env::get('database.url','')."/api/app.php?service=App.Auth.ApplyToken";
        $data['app_secret'] = Env::get('database.appsecret','');
        $data['app_key'] = Env::get('database.appkey','');
        $data['type'] = $type;
        return curlPost($url, $data);
    }

    /**
     * 获取中台服务是否上链开关
     */
    public function getOnline()
    {
        $model = new Timebank_basicsetup();
        $info = $model->basicsetupDesc(1);
        return !empty($info) && isset($info['online']) ? $info['online'] : 0;
    }

    /**
     * 中台创建用户
     * @param array $info timebank信息
     * @param string $type
     * @return mixed
     */
    public function blockchainTimebankCreate($info, $type = '')
    {
        $url = Env::get('database.url','')."/api/blockchain.php?s=Blockchain.Timebank.Create";
        $data['access_token'] = $info['access_token'];
        $data['regSource'] = 'timebank';
        $data['username'] = $info['username'];
        $data['mobile'] = $info['mobile'];
        $data['avatar'] = $info['avatar'];
        $data['gender'] = $info['gender'];
        $data['type'] = $type;
        $data['online'] = $this->getOnline();
        return curlPost($url, $data);
    }

    /**
     * 获取中台用户信息
     * @param $access_token
     * @param $mobile
     * @param string $type
     * @return mixed 中台userinfo
     */
    public function blockchainTimebankUserInfo($access_token, $mobile, $type = '')
    {
        $url = Env::get('database.url','')."/api/blockchain.php?s=Blockchain.Timebank.UserInfo";
        $data['access_token'] = $access_token;
        $data['mobile'] = $mobile;
        $data['type'] = $type;
        $data['online'] = $this->getOnline();
        return curlPost($url, $data);
    }

    /**
     * 中台减少余额
     * @param $access_token
     * @param $mobile
     * @param $amount
     * @param $orderNo
     * @param string $type
     * @return mixed
     */
    public function blockchainTimebankDeduct($access_token, $mobile, $amount, $orderNo, $type = '')
    {
        $url = Env::get('database.url','')."/api/blockchain.php?s=Blockchain.Timebank.Deduct";
        $data['access_token'] = $access_token;
        $data['mobile'] = $mobile;
        $data['amount'] = $amount;
        $data['orderNo'] = $orderNo;
        $data['type'] = $type;
        $data['online'] = $this->getOnline();
        return curlPost($url, $data);
    }

    /**
     * 中台增加余额
     * @param $access_token
     * @param $mobile
     * @param $amount
     * @param string $orderNo
     * @param string $type
     * @return mixed
     */
    public function blockchainTimebankIncrease($access_token, $mobile, $amount, $orderNo = '', $type = '')
    {
        $url = Env::get('database.url','')."/api/blockchain.php?s=Blockchain.Timebank.Increase";
        $data['access_token'] = $access_token;
        $data['mobile'] = $mobile;
        $data['amount'] = $amount;
        $data['orderNo'] = $orderNo;
        $data['type'] = $type;
        $data['online'] = $this->getOnline();
        return curlPost($url, $data);
    }

    /**
     * 中台相互快捷转账
     * @param $access_token
     * @param $fromMobile
     * @param $toMobile
     * @param $amount
     * @param $orderNo
     * @param string $type
     * @return mixed
     */
    public function blockchainTimebankTransfer($access_token, $fromMobile, $toMobile, $amount, $orderNo, $type = '')
    {
        $url = Env::get('database.url','')."/api/blockchain.php?s=Blockchain.Timebank.Transfer";
        $data['access_token'] = $access_token;
        $data['fromMobile'] = $fromMobile;
        $data['toMobile'] = $toMobile;
        $data['amount'] = $amount;
        $data['orderNo'] = $orderNo;
        $data['type'] = $type;
        $data['online'] = $this->getOnline();
        return curlPost($url, $data);
    }

    /**
     * 中台统一下单
     * @param $data
     * @return mixed
     */
    public function appPayUnifiedPay($data)
    {
        $url = Env::get('database.url','')."/api/app.php?s=App.Pay.UnifiedPay";
        return curlPost($url, $data);
    }

    /**
     * 中台微信退款
     * @param $data
     * @return mixed
     */
    public function appPayRefund($data)
    {
        $url = Env::get('database.url','')."/api/app.php?s=App.Pay.Refund";
        return curlPost($url, $data);
    }

    /**
     * 支付回调验签
     * @param $data
     * @return mixed
     */
    public function OrderCheckSign($data)
    {
        $url = Env::get('database.url','')."/api/app.php?s=App.pay.CheckSign";
        return curlPost($url, $data);
    }

    /**
     * 注册会员
     * @param $data
     * @return mixed
     */
    public function UserRegister($data)
    {
        $url = Env::get('database.url','')."/api/app.php?s=App.pubUser.Create";
        return curlPost($url, $data);
    }

    /**
     * 获取应用APPkey
     * @return string
     */
    public function getAppKey()
    {
        return $this->AppKey;
    }
}
