<?php
declare (strict_types = 1);

namespace app\api\model\plus\articlepromotion;

use think\Model;
use app\common\model\plugin\articlepromotion\Contribute as ContributeModel;

/**
 * @mixin \think\Model
 */
class Contribute extends ContributeModel
{
    public function getList($params,$user)
    {
        $model = $this;
        !empty($params['contribute_status']) && $model = $model->where('contribute_status', '=', $params['contribute_status']);
        return $model
            ->where('user_id', '=', $user['user_id'])
            ->order('create_time','desc')
            ->paginate($params);
    }
}
