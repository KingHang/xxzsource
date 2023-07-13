<?php

namespace app\api\model\plus\invitationgift;

use app\common\model\plugin\invitationgift\Partake as PartakeModel;
use app\api\model\user\User;
use app\api\model\user\PointsLog;
use app\api\model\plus\coupon\UserVoucher;
use app\api\model\plus\invitationgift\Invitation;

/**
 * 领取记录表
 */
class Partake extends PartakeModel
{
    /**
     * 领奖
     */
    public function getPrize($invitation_reward_id, $invitation_gift_id, $user_id, $reward)
    {
        $InvitationModel = new Invitation();
        $Invitation = $InvitationModel::find($invitation_gift_id);
        $data = [
            'invitation_gift_id' => $invitation_gift_id,
            'invitation_reward_id' => $invitation_reward_id,
            'user_id' => $user_id,
            'name' => $Invitation['name'],
            'app_id' => self::$app_id,
        ];
        // 开启事务
        $this->startTrans();
        try {
            // 添加领取记录
            $this->save($data);
            // 修改用户积分
            if ($reward['is_point'] == 1) {
                $user = new User();
                $user->setIncPoints($reward['exchangepurch'], '邀请有礼奖励',6);

            }
            //添加优惠券
            if ($reward['is_coupon'] == 1) {
                $UserCouponModel = new UserVoucher;
                $UserCouponModel->addUserCoupon($reward['coupon_ids'], $user_id);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }

    }

    /**
     * 判断用户是否领过奖品
     */
    public function checkReward($invitation_reward_id, $invitation_gift_id, $user_id)
    {
        $where = [
            'invitation_reward_id' => $invitation_reward_id,
            'invitation_gift_id' => $invitation_gift_id,
            'user_id' => $user_id,
        ];
        return $this->where($where)->find();
    }

    /**
     * 获取用户已领取的奖励
     */
    public function getUserPrizes($user_id, $invitation_gift_id)
    {
        $where = [
            'user_id' => $user_id,
            'invitation_gift_id' => $invitation_gift_id,
        ];
        return $this->with(['reward'])->where($where)->select();
    }
}