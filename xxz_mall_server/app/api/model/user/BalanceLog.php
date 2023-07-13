<?php

namespace app\api\model\user;

use app\common\model\user\BalanceLog as BalanceLogModel;
use think\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Paginator;

/**
 * 用户余额变动明细模型
 */
class BalanceLog extends BalanceLogModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
    ];

    /**
     * 获取账单明细列表
     * @param $userId
     * @param array $data
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getTop10($userId, $data = [])
    {
        $model = $this;
        if (isset($data['month'])) {
            $month = $data['month'] ? $data['month'] : date('Y-m');
            $beginTime = date('Y-m-01 00:00:00', strtotime($month));
            $endTime = date('Y-m-01 00:00:00', strtotime("{$month} +1 month"));
            $model = $model->where('create_time', '>=', strtotime($beginTime));
            $model = $model->where('create_time', '<', strtotime($endTime));
        }
        // 获取列表数据
        return $model->where('user_id', '=', $userId)
            ->order(['create_time' => 'desc'])
            ->limit(10)
            ->select();
    }

    /**
     * 获取账单明细列表
     * @param $userId
     * @param $type
     * @param array $data
     * @return Paginator
     * @throws DbException
     */
    public function getList($userId, $type, $data = [])
    {
        $model = $this;
        if (isset($data['month'])) {
            $month = $data['month'] ? $data['month'] : date('Y-m');
            $beginTime = date('Y-m-01 00:00:00', strtotime($month));
            $endTime = date('Y-m-01 00:00:00', strtotime("{$month} +1 month"));
            $model = $model->where('create_time', '>=', strtotime($beginTime));
            $model = $model->where('create_time', '<', strtotime($endTime));
        }
        if ($type == 'rechange') {
            $model = $model->where('scene', '=', 10);
        }
        // 获取列表数据
        return $model->where('user_id', '=', $userId)
            ->order(['create_time' => 'desc'])
            ->paginate(30);
    }
}
