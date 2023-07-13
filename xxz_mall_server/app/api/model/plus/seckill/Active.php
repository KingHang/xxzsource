<?php

namespace app\api\model\plus\seckill;

use app\common\model\plugin\flashsell\Active as ActiveModel;
/**
 * 秒杀活动模型
 */
class Active extends ActiveModel
{
    public function checkOrderTime($data)
    {
        $result = ['code' => 0];
        if ($data['start_time'] > time()) {
            $result = ['code' => 10, 10 => '该活动未开始'];
        }
        if ($data['end_time'] < time()) {
            $result = ['code' => 20, 20 => '该活动已结束'];
        }
        $now_start_time = strtotime(date('Y-m-d') . ' ' . $data['day_start_time']);
        $now_end_time = strtotime(date('Y-m-d') . ' ' . $data['day_end_time']);
        if ($now_start_time > time()) {
            $result = ['code' => 30, 30 => '该活动今天未开始'];
        }
        if ($now_end_time < time()) {
            $result = ['code' => 40, 40 => '该活动今天已结束'];
        }
        return $result;
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
     * 获取秒杀活动
     */
    public function activityList()
    {
        return $this->where('start_time', '<=', time())
            ->where('end_time', '>', time())
            ->where('status', '=', 1)
            ->where('is_delete', '=', 0)
            ->select();
    }
}
