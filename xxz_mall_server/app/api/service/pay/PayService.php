<?php

namespace app\api\service\pay;


use app\common\enum\settings\SettingEnum;
use app\common\model\app\AppOpen as AppOpenModel;
use app\common\model\settings\Settings;

class PayService
{
    public static function isAlipayOpen($pay_source, $app_id)
    {
        // 是否开启支付宝支付
        $h5_alipay = $pay_source == 'h5' && self::isH5AlipayOpen($app_id);
        $app_alipay = $pay_source == 'app' && self::isAppAlipayOpen($app_id);
        return $h5_alipay || $app_alipay;
    }

    /**
     * 是否开启h5支付宝支付
     */
    private static function isH5AlipayOpen($app_id){
        return Settings::getItem(SettingEnum::H5ALIPAY, $app_id)['is_open'];
    }

    /**
     * 是否开启app支付宝支付
     */
    private static function isAppAlipayOpen($app_id){
        $config = AppOpenModel::getAppOpenCache($app_id);
        return $config['is_alipay'] === 1;
    }
}