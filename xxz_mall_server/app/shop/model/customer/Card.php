<?php

namespace app\shop\model\customer;

use app\common\model\customer\Card as CardModel;

/**
 * 用户会员等级模型
 */
class Card extends CardModel
{
    /**
     * 获取列表记录
     */
    public function getList($data)
    {
        return $this->order(['create_time' => 'asc'])
            ->paginate($data, false, [
                'query' => request()->request()
            ]);
    }

    /**
     * 获取列表记录
     */
    public function getLists()
    {
        return $this->field('group_id, group_name')
            ->order(['create_time' => 'asc'])
            ->select();
    }
}