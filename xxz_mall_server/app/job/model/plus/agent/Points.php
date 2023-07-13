<?php

namespace app\job\model\plus\agent;

use app\common\library\helper;
use app\common\model\plugin\agent\PointsYear as AgentPointsYearModel;
use app\common\model\plugin\agent\Setting as AgentSettingModel;
use app\common\model\order\Order as OrderModel;
use app\common\model\plugin\agent\User;
use app\common\model\plugin\agent\Points as AgentPointsModel;
/**
 * 分销商订单模型
 */
class Points extends AgentPointsYearModel
{
    /**
     * 获取上月未结算的分销员
     */
    public function getNoSettled($app_id){
        $basic = AgentSettingModel::getItem('basic', $app_id);
        // 大于结算日
        if(intval(date('d')) < intval($basic['month_day'])){
            //return false;
        }
        if(intval(date('d')) == intval($basic['month_day']) && $basic['month_time'] < date('H:m')){
            //return false;
        }
        // 并且是1月
        if(date('m') != '01'){
            //return false;
        }
        $year = date('Y', strtotime('-1 year'));
        return $this->where('year', '=', $year)
            ->where('is_settled', '=', 0)
            ->where('app_id', '=', $app_id)
            ->limit(50)
            ->select();
    }

    /**
     * 佣金结算
     */
    public function settledMoney($list){
        // 查找分红的总额
        $total_money = (new OrderModel())->getTotalPayMoney($list[0]['year']);
        $settings = AgentSettingModel::getItem('reward_points', $list[0]['app_id']);
        $send_money = helper::number2($settings['percent'] * $total_money / 100);
        // 所有积分
        $total_points = $this->getTotalPoints($list[0]['year']);
        foreach ($list as $item){
            // 发放分红
            $money = helper::number2($send_money * $item['exchangepurch'] / $total_points);
            User::grantMoney($item['user_id'], $money, '积分年度分红');
            // 标记为已结算
            $item->save([
                'is_settled' => 1
            ]);
            // 积分余额减少
            (new User())->where('user_id', '=', $item['user_id'])->dec('exchangepurch', $item['exchangepurch'])->update();
            // 写入积分记录
            (new AgentPointsModel())->save([
                'user_id' => $item['user_id'],
                'value' => $item['exchangepurch'],
                'describe' => '积分年度分红',
                'app_id' => $item['app_id']
            ]);
        }
    }

    private function getTotalPoints($year){
        return $this->where('year', '=', $year)->sum('exchangepurch');
    }
}