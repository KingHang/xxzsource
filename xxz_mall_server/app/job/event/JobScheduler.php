<?php

namespace app\job\event;


use app\common\model\app\App as AppModel;

/**
 * 订单事件管理
 */
class JobScheduler
{

    /**
     * 执行函数
     */
    public function handle()
    {
        // 查找所有appid
        $appList = AppModel::getAll();
        // 涉及到应用单独配置的，循环执行
        foreach ($appList as $app){
            // 订单任务
            event('Order', $app['app_id']);
            // 活动订单
            event('Activity', $app['app_id']);
        }
        // 拼团任务
        event('AssembleBill');
        // 砍价任务
        event('BargainTask');
        // 用户优惠券
        event('UserVoucher');
        // 直播间管理
        event('LiveRoom');
        return true;
    }

}
