<?php

namespace app\api\model\plus\assemble;

use app\common\model\plugin\groupsell\Active as ActiveModel;

/**
 * 拼团模型
 */
class Active extends ActiveModel
{
    /**
     * 参与记录列表
     */
    public function getList($param)
    {
        $model = $this;
        if (isset($param['status']) && $param['status'] > -1) {
            $model = $model->where('status', '=', $param['status']);
        }
        if (isset($param['title']) && !empty($param['title'])) {
            $model = $model->where('title', 'like', '%' . trim($param['title']) . '%');
        }
        $res = $model->with(['file'])
            ->order('create_time', 'desc')
            ->paginate($param);

        foreach ($res as $key => $val) {
            $res[$key]['start_time'] = format_time($val['start_time']);
            $res[$key]['end_time'] = format_time($val['end_time']);
        }
        return $res;
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
     * 获取拼团商品列表
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
