<?php

namespace app\shop\model\shop;

use app\common\library\sms\Driver as SmsDriver;
use app\common\model\BaseModel;
use app\common\model\settings\Settings as SettingModel;

/**
 * 短信模型
 */
class Sms extends BaseModel
{
    protected $pk = 'sms_id';
    protected $name = 'sms';

    /**
     * 短信发送
     * @param int $type    1:登录,2:注册,3:重置密码,  默认 1
     * @return bool
     */
    public function send($mobile, $type = 1)
    {
        $sence = '';
        $app_id = 0;
		
        if (empty($mobile)) {
            $this->error = '手机号码不能为空';
            return false;
        }

        if ($type == 2) {
            $sence = 'smsregister';
        } elseif ($type == 3) {
            $sence = 'smsresetpassword';
        } elseif ($type == 1) {
            $sence = 'smslogin';
        } else {
            $this->error = '参数错误';
            return false;
        }

        $user = (new User)->withoutGlobalScope()->where('mobile', '=', $mobile)->find();

        if ($user) {

            if ($type == 2) {
                $this->error = '该手机号已存在';
                return false;
            }
            
            $app_id = $user->app_id;

        } else {
            if ($type != 2) {
                $this->error = '该手机号未注册';
                return false;
            }

        }
   
//        $smsConfig = SettingModel::getItem('sms', $app_id);
        $smsConfig = SettingModel::getItem('sms', 10001);
        $send_template = $smsConfig['engine'][$smsConfig['default']]['login_template'] ;

        
//        if ($app_id < 1 || empty($send_template)) {
//            $config = SettingModel::getSysConfig();
//
//            if (!$config['sms_open']) {
//                $this->error = '短信未开启';
//                return false;
//            }
//
//            if (empty($config['login_template'])) {
//                $this->error = '短信未开启';
//                return false;
//            }
//
//            $smsConfig['engine'][$smsConfig['default']]['AccessKeyId'] = $config['AccessKeyId'];
//            $smsConfig['engine'][$smsConfig['default']]['AccessKeySecret'] = $config['AccessKeySecret'];
//            $smsConfig['engine'][$smsConfig['default']]['sign'] = $config['sign'];
//            $smsConfig['engine'][$smsConfig['default']]['login_template'] = $config['login_template'];
//            $send_template = $config['login_template'];
//            $app_id = 0;
//        }
     
        $code = str_pad(mt_rand(100000, 999999), 6, "0", STR_PAD_BOTH);
        $SmsDriver = new SmsDriver($smsConfig);
        $send_data = [
            'code' => $code,
        ];
        // 短信模板
        $flag = $SmsDriver->sendSms($mobile, $send_template, $send_data);
        $flag = true;
        if ($flag) {
            $this->save([
                'mobile' => $mobile,
                'code' => $code,
                'sence' => $sence,
                'app_id' => $app_id,
            ]);
        }

        return $flag;
    }
}
