<?php

namespace app\shop\model\user;

use app\common\model\user\GrowthLog as GrowthLogModel;

/**
 * 用户成长值变动明细模型
 */
class GrowthLog extends GrowthLogModel
{
    /**
     * 获取成长值明细列表
     * @param array $query
     * @return array
     */
    public function getList($query = [])
    {
        $model = $this;
        // 搜索用户信息
        if (isset($query['search']) && $query['search'] != '') {
            $model = $model->where('user.nickName|user.mobile|user.realname', 'like', '%' . trim($query['search']) . '%');
        }
        // 搜索时间段
        if (isset($query['value1']) && $query['value1'] != '') {
            $sta_time = array_shift($query['value1']);
            $end_time = array_pop($query['value1']);
            $model = $model->whereBetweenTime('log.create_time', $sta_time, $end_time);
        }
        // 获取列表数据
        return $model->with(['user'])
            ->alias('log')
            ->field('log.*')
            ->join('user', 'user.user_id = log.user_id')
            ->order(['log.create_time' => 'desc'])
            ->paginate($query);
    }
}
