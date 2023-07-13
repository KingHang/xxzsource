<?php

namespace app\api\model\plus\bargain;

use app\common\model\plugin\pricedown\Active as ActiveModel;

/**
 * 砍价模型
 */
class Active extends ActiveModel
{
    /**
     *列表
     */
    public function bargainList($param = null)
    {
        return $this->where('is_delete', '=', 0)->select();
    }

    /**
     * 获取砍价活动详情
     */
    public function getDetail($activeId)
    {
        $model = static::detail($activeId, 'product.sku');
        if (empty($model) || $model['is_delete'] == true || $model['status'] == false) {
            $this->error = '很抱歉，该砍价商品不存在或已下架';
            return false;
        }
        return $model;
    }

    /**
     * 取最近要结束的一条记录
     */
    public static function getActive()
    {
        return (new static())->where('start_time', '<', time())
            ->where('end_time', '>', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->find();
    }

    /**
     * 获取砍价商品列表
     */
    public function activityList()
    {
        return  $this->where('start_time', '<=', time())
            ->where('end_time', '>=', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }
}
