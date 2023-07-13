<?php

namespace app\job\model\plus\agent;

use app\common\model\plugin\agent\Month as AgentMonthModel;
use app\common\model\plugin\agent\Setting as AgentSettingModel;
use app\common\model\plugin\agent\User;
use app\common\model\plugin\agent\User as AgentUserModel;
use app\shop\model\plugin\agent\OrderDetail as OrderDetailModel;

/**
 * 分销商订单模型
 */
class Month extends AgentMonthModel
{
    /**
     * 获取上月未结算的分销员
     */
    public function getNoSettled($app_id){
        $basic = AgentSettingModel::getItem('basic', $app_id);
        // 大于结算日
        if(intval(date('d')) < intval($basic['month_day'])){
            return null;
        }
        if(intval(date('d')) == intval($basic['month_day']) && $basic['month_time'] < date('H:m')){
            return null;
        }
        $month = date('Y-m', strtotime('-1 month'));
//        $month = '2022-10';
        return $this->where('month', '=', $month)
            ->where('is_settled', '=', 0)
            ->where('app_id', '=', $app_id)
            ->find();
    }
    /**
     * 获取上月未结算的创业分红
     */
    public function getNoBounsSettled($app_id)
    {
        $basic = AgentSettingModel::getItem('settlement_setting', $app_id);
        // 大于结算日
        if(intval(date('d')) < intval($basic['settlement_date'])){
            return null;
        }
        if(intval(date('d')) == intval($basic['settlement_date']) && $basic['time'] < date('H:m')){
            return null;
        }
        $month = date('Y-m', strtotime('-1 month'));
//        $month = '2022-10';
        return $this->where('month', '=', $month)
            ->where('bouns_settled', '=', 0)
            ->where('app_id', '=', $app_id)
            ->whereIn('bouns_status',[1,3,4,5])
            ->find();
    }
    public function getNoBouns($id)
    {
        return $this->where('id', $id)
            ->find();
    }
    public function getSaleNoSettled($app_id)
    {
        $basic = AgentSettingModel::getItem('bouns_reward', $app_id);
        // 是否是月结
        if ($basic['settlement_way'] != 1) {
            return null;
        }
//        // 大于结算日
        if(intval(date('d')) < intval($basic['settlement_week'])){
            return null;
        }
        if(intval(date('d')) == intval($basic['settlement_week']) && $basic['time'] < date('H:m')){
            return null;
        }
        $month = date('Y-m', strtotime('-1 month'));
//        $month = '2022-10';
        return $this->where('month', '=', $month)
            ->where('sale_settled', '=', 0)
            ->where('app_id', '=', $app_id)
            ->find();
    }
    /**
     * 佣金结算
     */
    public function settledMoney($app_id,$type=0){
        $agent_user = AgentUserModel::detail($this['user_id']);
        if(!$agent_user || $agent_user['is_delete'] == 1){
            // 如果不存在，则删除
            $this->delete();
            return;
        }
        if ($type ==1){
            //计算创业分红奖励
            $status =$this->settledBounsMoney($agent_user, $app_id);
//            if ($status){
                $this->setBounsSettled();
//            }
            return $status;
        }else{
            $basic = AgentSettingModel::getItem('basic', $app_id);
            // 结算销售奖
            $this->settledAgentSaleMoney($agent_user, $app_id);
            // 结算团队奖
            $this->settledTeamMoney($agent_user, $app_id);
            // 结算级差奖励
            $this->settledGiveMoney($agent_user, $app_id, $basic, 'level');
            // 结算创业级差奖励
            $this->settledGiveMoney($agent_user, $app_id, $basic, 'level_bonus');
            // 结算平级奖励
            $this->settledGiveMoney($agent_user, $app_id, $basic, 'same');
            // 结算超越奖励
            $this->settledGiveMoney($agent_user, $app_id, $basic, 'than');
            // 结算拓展奖励
            $this->settledGiveMoney($agent_user, $app_id, $basic, 'expand_bonus');
            // 结算拓展奖励
            $this->settledGiveMoney($agent_user, $app_id, $basic, 'expand');
            // 结算区域奖
            $this->settledAreaMoney($agent_user, $app_id);
            // 发放积分
            $this->settledPoints($app_id);
            // 标记已结算
            $this->setSettled();
        }

    }
    /**
     * @param $user_id 用户id
     * @param int $type =1 自购订单跳转=2团队订单跳转=3直属团队订单跳转
     */
    public function getOrderId($user_id=0,$type=1)
    {
        $user = [];
        $user['user_id'] = $user_id;
        if ($type == 3){

            $userLevelIds = (new \app\common\model\plugin\agent\User())->getDirectUserIds($user,'0','bonus');
            $order_ids = OrderDetailModel::field('order_id,is_settled,type')->where(['type'=>'bouns'])
                ->where('user_id','in',$userLevelIds)
                ->select();
            $order_array = '';
            foreach ($order_ids as $k =>$v){
                $order_array .= $v['order_id'].',';
            }
            }if ($type == 1){
                $order_ids = OrderDetailModel::field('order_id,is_settled,type')->where(['type'=>'bouns','user_id'=>$user_id])
                    ->select();
            $order_array = '';
            foreach ($order_ids as $k =>$v){
            $order_array .= $v['order_id'].',';
        }
            }if ($type == 2){
                $team_user_ids = $this->where(['user_id'=>$user_id])->find();
                $order_ids = $team_user_ids->order_ids;
                $order_ids = trim($order_ids,',');
                $order_array = $order_ids;
        }


        return $order_array;

    }

    /**
     * 月结创业拓展奖
     * @param $app_id
     * @throws \Exception
     */
    public function settledSaleMoney($app_id)
    {
        $agent_user = AgentUserModel::detail($this['user_id']);
        if(!$agent_user || $agent_user['is_delete'] == 1){
            // 如果不存在，则删除
            $this->delete();
            return;
        }
        $this->settledBonusSaleMoney($agent_user, $app_id);
        // 标记已结算
        $this->setSaleSettled();
    }

}