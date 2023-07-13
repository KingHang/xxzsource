<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\plus\invitationgift\Partake;
use app\api\model\plus\invitationgift\InvitationReward;
use app\api\model\user\User;

/**
 * 用户邀请有礼控制器
 */
class Invitation extends Controller
{
    // UserCouponModel $model
    private $model;

    // \app\api\model\User $model
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        $this->user = $this->getUser();
        $this->model = new Partake;
    }

    /**
     *领奖
     */
    public function getPrize($invitation_reward_id, $invitation_gift_id)
    {
        $invitationReward = new InvitationReward();
        $model = new User;
        $count = $model->getCountInv($this->user['user_id']);
        $reward = $invitationReward::find($invitation_reward_id);
        if (empty($reward)) {
            return $this->renderError('奖项不存在', '');
        }
        if ($count < $reward['invitation_num']) {
            return $this->renderError('未达邀请到人数', '');
        }
        if ($this->model->checkReward($invitation_reward_id, $invitation_gift_id, $this->user['user_id'])) {
            return $this->renderError('已经领过该奖品', '');
        }
        if ($this->model->getPrize($invitation_reward_id, $invitation_gift_id, $this->user['user_id'], $reward)) {
            return $this->renderSuccess('领取成功', '');
        }
        return $this->renderError('领取失败', '');
    }

}