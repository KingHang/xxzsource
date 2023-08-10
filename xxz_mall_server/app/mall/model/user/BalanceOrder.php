<?php

namespace app\mall\model\user;

use app\common\model\user\BalanceOrder as BalanceOrderModel;

/**
 * 充值模型
 */
class BalanceOrder extends BalanceOrderModel
{
    /**
     * 列表
     * @param $params
     * @return array
     */
    public function getList($params)
    {
        $model = $this->alias('order')->field('order.*');
        //用户昵称
        $data['search'] = isset($params['search']) && $params['search'] ? trim($params['search']) : '';
        !empty($data['search']) && $model = $model->where('user.nickName|user.mobile|user.realname', 'like', "%{$data['search']}%");
        //搜索时间段
        if (isset($params['value1']) && $params['value1'] != '') {
            $sta_time = array_shift($params['value1']);
            $end_time = array_pop($params['value1']);
            $model = $model->whereBetweenTime('order.create_time', $sta_time, $end_time);
        }
        $list = $model->with(['user'])
            ->join('user', 'user.user_id = order.user_id')
            ->order(['order.create_time' => 'desc'])
            ->paginate($params);
        foreach ($list as $key => &$value) {
            $value['snapshot'] = json_decode($value['snapshot'],true);
        }
        return $list;
    }
}
