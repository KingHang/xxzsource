<?php

namespace app\mall\model\user;

use app\common\model\user\PointsLog as PointsLogModel;

/**
 * 用户积分变动明细模型
 */
class PointsLog extends PointsLogModel
{
    /**
     * 获取积分明细列表
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

    /**
     * 设置查询条件
     * @param $query
     */
    private function setQueryWhere($query)
    {
        // 设置默认的检索数据
        $params = $this->setQueryDefaultValue($query, [
            'user_id' => 0,
            'search' => '',
            'start_time' => '',
            'end_time' => '',
        ]);
        // 用户ID
        $params['user_id'] > 0 && $this->where('log.user_id', '=', $params['user_id']);
        // 用户昵称
        !empty($params['search']) && $this->where('user.nickName', 'like', "%{$params['search']}%");
        // 起始时间
        !empty($params['start_time']) && $this->where('log.create_time', '>=', strtotime($params['start_time']));
        // 截止时间
        !empty($params['end_time']) && $this->where('log.create_time', '<', strtotime($params['end_time']) + 86400);
    }
}
