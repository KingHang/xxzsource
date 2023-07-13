<?php

namespace app\api\model\supplier;

use app\common\model\settings\Settings;
use app\common\model\purveyor\Apply as ApplyModel;
use app\common\model\user\User as UserModel;
use app\common\model\user\Sms as SmsModel;
use app\api\model\supplier\Category as CategoryModel;
/**
 * 供应商申请模型类
 */
class Apply extends ApplyModel
{
   /**
     * 添加
     */
    public function add($data)
    {
        // 开启事务
        $this->startTrans();
        try {
            //是否需要短信验证
            $sms_open = Settings::getItem('store')['sms_open'];
            if($sms_open == 1){
                $code = $this->verifyCode($data);
                if (!$code) {
                    $this->error = '验证码错误或失效';
                    return false;
                }
            }
            $num = $this->getStoreName($data['store_name']);
            if ($num > 0) {
                $this->error = '店铺名已存在';
                return false;
            }
            $isApply = $this->isApply($data['user_id']);
            if ($isApply > 0) {
                $this->error = '已经申请开店';
                return false;
            }
            $mobile = $this->getMobile($data['mobile']);
            if ($mobile > 0) {
                $this->error = '手机号码已存在';
                return false;
            }
            //获取保证金
            $CategoryInfo = CategoryModel::detail($data['category_id']);
            $data['deposit_money'] = $CategoryInfo['deposit_money'];
            // 添加供应商
            $data['password'] = salt_hash($data['password']);
            $this->save($data);
            // 更改用户为供应商
            UserModel::updateType($data['user_id'], 2);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
    //验证验证码
    public function verifyCode($data)
    {   
        $model = new SmsModel();
        $code = $model->where('mobile', '=', $data['mobile'])->order('sms_id desc')->where('create_time','>',time()-10*60)->value('code');
        if($code&&$code==$data['code']){
            return true;
        }else{
            return false;
        }
    }
    //判断店铺名
    public function getStoreName($store_name)
    {
        return $this->where('store_name', '=', $store_name)->where('status','in','0,1,3')->count();
    }
    //判断用户是否申请
    public function isApply($user_id){
    	return $this->where('user_id', '=', $user_id)->where('status','in','0,1,3')->count();
    }
    //判断手机号是否存在
    public function getMobile($mobile){
    	return $this->where('mobile', '=', $mobile)->where('status','in','0,1,3')->count();
    }
}
