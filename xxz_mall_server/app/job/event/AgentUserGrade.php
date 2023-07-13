<?php

namespace app\job\event;

use app\common\model\plugin\agent\Grade as GradeModel;
use app\common\model\plugin\agent\User as AgentUserModel;
use app\common\model\user\User as UserModel;
use app\common\model\plugin\agent\Referee as RefereeModel;
use app\common\model\order\OrderGoods as OrderProductModel;
/**
 * 用户等级事件管理
 */
class AgentUserGrade
{
    /**
     * 执行函数
     */
    public function handle($userId)
    {
        // 设置用户的会员等级
        $this->setGrade($userId);
        return true;
    }

    /**
     * 设置等级
     */
    private function setGrade($userId)
    {
        log_write('分销商升级$user_id='.$userId);
        // 用户模型
        $user = AgentUserModel::details($userId);
        if(!$user){
            return false;
        }
        $weight = 0;
        if (!empty($user['grade'])) {
            $weight = $user['grade']['weight'];
        }
        // 获取所有等级
        $list = GradeModel::getUsableList($user['app_id'] , $weight);

        if ($list->isEmpty()) {
            return false;
        }
        // 遍历等级，根据升级条件 查询满足消费金额的用户列表，并且他的等级小于该等级
        $upgradeGrade = null;
        foreach ($list as $grade) {
            if($grade['is_default'] == 1){
                continue;
            }
            $is_upgrade = $this->checkCanUpdate($user, $grade);
            if($is_upgrade){
                $upgradeGrade = $grade;
                if ($is_upgrade == 'qrcode' && !empty($upgradeGrade['certificate_bg'])){
                    $upgradeGrade['qrcode'] = 1;
                }
                continue;
            }
        }
        if($upgradeGrade){
            $this->dologs('setAgentUserGrade', [
                'user_id' => $user['user_id'],
                'grade_id' => $upgradeGrade['grade_id'],
            ]);
            // 修改会员的等级
            (new AgentUserModel())->upgradeGrade($user, $upgradeGrade);
        }
    }

    /**
     * 查询满足会员等级升级条件的用户列表
     */
    public function checkCanUpdate($user, $grade)
    {
        $real_user = AgentUserModel::detail($user['user_id']);
        // 推广金额
        if($grade['open_agent_money'] == 1 && ($user['money'] + $user['freeze_money'] + $user['total_money']) < $grade['agent_money']){
           return false;
        }
        // 自购金额
        if($grade['open_buy_money'] == 1 && $real_user['user']['expend_money'] < $grade['buy_money']){
            return false;
        }
        // 客户数
        if($grade['open_customer'] == 1 && (new RefereeModel())->getRefereeUserNum($user['user_id'] , 1) < $grade['customer']){
            return false;
        }
        // 分销商数
        if($grade['open_agent_user'] == 1 && AgentUserModel::agentCount($user['user_id']) < $grade['agent_user']){
            return false;
        }
        // 加盟费(暂无入口)
        if ($grade['open_join_money'] == 1 && $grade['open_join_money'] > 0) {
            return false;
        }
        // 指定等级及以上分销商数
        if ($grade['open_grade_agent_user'] == 1 && AgentUserModel::agentCountWithGrade($user['user_id'] , $grade['join_grade']) < $grade['join_grade_user']) {
            return false;
        }
        // 购买指定商品
        if ($grade['open_buy_product'] == 1) {
            $product_ids = $grade['product_ids'] ? unserialize($grade['product_ids']) : [];
            if (empty($product_ids)) {
                return false;
            } elseif ((new OrderProductModel())->getProductData(null,null,'pay',0,$product_ids,$user['user_id']) == 0){
                return false;
            }
            return 'qrcode';//升级生成证书
        }
        return true;
    }

    /**
     * 记录日志
     */
    private function dologs($method, $params = [])
    {
        $value = 'UserGrade --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }
}
