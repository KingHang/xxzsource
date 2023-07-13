<?php

namespace app\common\model\user;

use app\common\model\BaseModel;
use think\Db;
/**
 * 登录日志模型
 */
class LoginLog extends BaseModel
{
    protected $name = 'user_login_log';
    protected $pk = 'log_id';
    
    /**
     * 更新登录日志
     */
    public static function saveLoginLog($user){
        $model = new static;
        //获取登录ip
        $ip = request()->ip();
        //ip转换为数值
        $ip = ip2Long($ip);
        $time = time();
        $lastLog = self::getLastLog($user['user_id']);

        if (!empty($lastLog)) {
            $lastLog = $lastLog->toArray();
            //判断连续登录和最大连续登录
            if ($lastLog['createtime'] < \fast\Date::unixtime('day')) {
                //超过一天为登录中断连续登陆 未超过连续登陆天数+1
                $lastLog['successions'] = $lastLog['createtime'] < \fast\Date::unixtime('day', -1) ? 1 : $lastLog['successions'] + 1;
                //获取最大连续登陆天数
                $lastLog['maxsuccessions'] = max($lastLog['successions'], $lastLog['maxsuccessions']);
            }

            $lastLog['prevtime'] = $lastLog['createtime'];
            $lastLog['loginip'] = $ip;
            if ($lastLog['prevtime'] == $lastLog['createtime']) {
                $lastLog['createtime'] = $time +1;
            } else {
                $lastLog['createtime'] = $time;
            }
        } else {
            $lastLog = array();
            $lastLog['loginip'] = $ip; // 登录ip
            $lastLog['createtime'] = $time; // 登录时间
            $lastLog['successions'] = 1; // 连续登陆天数
            $lastLog['maxsuccessions'] = 1; // 最大连续登陆天数
        }
        $lastLog['user_id'] = $user['user_id'];
        $lastLog['token'] = $user['token'];
        $lastLog['app_id']  = self::$app_id;
        // 开启事务
        $model->startTrans();
        try {
            $model->save($lastLog);
            $model->commit();
            return true;
        } catch (\Exception $e) {
            log_write($e->getMessage());
            $model->rollback();
            return false;
        }
    }
    /**
     * 获取最后一条登录日志
     */
    public static function getLastLog($user_id){
        return self::order('createtime DESC')->field('successions,maxsuccessions,prevtime,loginip,createtime')->where(array('user_id'=>$user_id))->find();
    }

}