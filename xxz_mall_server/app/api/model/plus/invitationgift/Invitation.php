<?php

namespace app\api\model\plus\invitationgift;

use app\common\model\plugin\invitationgift\Invitation as InvitationModel;
use app\api\model\user\User;
use app\common\exception\BaseException;

/**
 * 邀请有礼模型
 */
class Invitation extends InvitationModel
{
    /**
     *获取活动
     */
    public function getDatas($user_id)
    {
        $where = [
            'is_delete' => 0,
            'status' => 0
        ];
        $model = new User;
        $count = $model->getCountInv($user_id);
        $data = $this->with(['Reward'])->where($where)->find();
        $InvitationPartake = new Partake();
        $data['prize'] = $InvitationPartake->getUserPrizes($user_id, $data['invitation_gift_id']);
        $data['dif'] = -1;
        if (time() < $data['start_time']['value']) {
            throw new BaseException(['msg' => '活动尚未开始']);
        }
        if (time() > $data['end_time']['value']) {
            throw new BaseException(['msg' => '活动已结束']);
        }
        foreach ($data['Reward'] as $val) {
            if ($val['invitation_num'] > $count) {
                $data['dif'] = $val['invitation_num'] - $count;
                break;
            }

        }
        return $data;
    }
}