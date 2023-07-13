<?php

namespace app\api\model\plus\live;

use app\common\model\plugin\live\Plan as PlanModel;
/**
 * 礼物模型
 */
class Plan extends PlanModel
{

    /**
     * 获取充值套餐列表
     */
    public function getList()
    {
        return $this->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }
}