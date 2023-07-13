<?php

namespace app\api\model\user;

use app\common\model\user\PointsLog as PointsLogModel;
use think\db\exception\DbException;
use think\Paginator;

/**
 * 积分变动明细模型
 */
class PointsLog extends PointsLogModel
{
    /**
     * 获取积分明细列表
     * @param $userId
     * @param $data
     * @return Paginator
     * @throws DbException
     */
    public function getList($userId, $data)
    {
        $model = $this;
        if (isset($data['month'])) {
            $month = $data['month'] ? $data['month'] : date('Y-m');
            $beginTime = date('Y-m-01 00:00:00', strtotime($month));
            $endTime = date('Y-m-01 00:00:00', strtotime("{$month} +1 month"));
            $model = $model->where('create_time', '>=', strtotime($beginTime));
            $model = $model->where('create_time', '<', strtotime($endTime));
        }
        $limit = isset($data['limit']) && $data['limit'] ? $data['limit'] : 30;
        // 获取列表数据
        return $model->where('user_id', '=', $userId)
            ->order(['create_time' => 'desc'])
            ->paginate($limit);
    }
}
