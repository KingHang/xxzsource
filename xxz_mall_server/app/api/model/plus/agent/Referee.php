<?php

namespace app\api\model\plus\agent;

use app\common\model\plugin\agent\Referee as RefereeModel;
use app\common\model\plugin\agent\User as AgentUserModel;
use app\common\model\plugin\agent\Grade as AgentGradeModel;
use app\common\model\plugin\agent\RefereeLog as RefereeLogModel;

/**
 * 分销商推荐关系模型
 * @property int|mixed agent_id
 * @property mixed user_id
 * @property string app_id
 * @property mixed create_time
 */
class Referee extends RefereeModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [];

    /**
     * 创建推荐关系
     */
    public static function createRelation($user_id, $referee_id , $type = 0)
    {
        $saveReferee = true;
        // 自分享
        if ($user_id == $referee_id) {
            return false;
        }
        // # 记录一级推荐关系
        // 判断当前用户是否已存在推荐关系
        if (self::isExistReferee($user_id)) {
            if ($type == 0) {
                return false;
            } else {
                $saveReferee = false;
            }
        }
        // 判断推荐人是否为分销商
        if (!User::isAgentUser($referee_id)) {
            return false;
        }
        if ($saveReferee) {
            // 新增关系记录
            $model = new self;
            $model->add($referee_id, $user_id, 1);
            // # 记录二级推荐关系
            // 二级分销商id
            $referee_2_id = self::getRefereeUserId($referee_id, 1, true);
            // 新增关系记录
            $referee_2_id > 0 && $model->add($referee_2_id, $user_id, 2);
            // # 记录三级推荐关系
            // 三级分销商id
            $referee_3_id = self::getRefereeUserId($referee_id, 2, true);
            // 新增关系记录
            $referee_3_id > 0 && $model->add($referee_3_id, $user_id, 3);
            (new AgentUserModel)->getRefereeUserList(['user_id' => $user_id],1);
            event('AgentUserGrade', $referee_id);
        }
        $user = AgentUserModel::detail($user_id);
        $upgradeGrade = AgentGradeModel::getDefaultGradeId();
        (new AgentUserModel())->saveRefereeMoney($user, $upgradeGrade);
        return true;
    }

    /**
     * 新增关系记录
     */
    private function add($agent_id, $user_id, $level = 1)
    {
        // 新增推荐关系
        $app_id = self::$app_id;
        $create_time = time();
        $this->insert(compact('agent_id', 'user_id', 'level', 'app_id', 'create_time'));
        // 记录分销商成员数量
        User::setMemberInc($agent_id, $level);
        if ($level == 1) {
            // 添加变更记录
            (new RefereeLogModel())->add([
                'user_id' => $user_id,
                'new_referee_id' => $agent_id
            ]);
        }
        return true;
    }

    /**
     * 是否已存在推荐关系
     */
    private static function isExistReferee($user_id)
    {
        return !!(new static())->where(['user_id' => $user_id])->find();
    }
}