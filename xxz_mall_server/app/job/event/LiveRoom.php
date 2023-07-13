<?php

namespace app\job\event;

use think\facade\Cache;
use app\job\model\plus\live\Room as LiveRoomModel;
use app\common\library\helper;

/**
 * 直播间行为管理
 */
class LiveRoom
{
    private $model;

    /**
     * 执行函数
     */
    public function handle($app_id)
    {
        try {
            $this->model = new LiveRoomModel();
            $cacheKey = "task_space_live_room";
            if (!Cache::has($cacheKey)) {
                // 修改已过期的直播预告
                $this->onSetIsEnd();
                // 删除直播开始后5分钟未推流的直播间，可能退出或者切换了
                $this->onSetRoomEnd();
                Cache::set($cacheKey, time(), 60);
            }
        } catch (\Throwable $e) {
            echo 'ERROR LiveRoom: ' . $e->getMessage() . PHP_EOL;
            log_write('LiveRoom TASK : ' . $app_id . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 将已过期的直播标记为已结束
     */
    private function onSetIsEnd()
    {
        $list = $this->model->getEndList();
        $roomIds = helper::getArrayColumn($list, 'room_id');
        !empty($roomIds) && $this->model->setIsEnd($roomIds);
        // 记录日志
        $this->dologs('close', [
            'roomIds' => json_encode($roomIds),
        ]);
        return true;
    }

    /**
     * 删除未开始的直播，异常
     */
    private function onSetRoomEnd()
    {
        $list = $this->model->getEndRoomList();
        $roomIds = helper::getArrayColumn($list, 'room_id');
        !empty($roomIds) && $this->model->setIsRoomEnd($roomIds);
        // 记录日志
        $this->dologs('close', [
            'roomIds' => json_encode($roomIds),
        ]);
        return true;
    }


    /**
     * 记录日志
     * @param $method
     * @param array $params
     * @return bool|int
     */
    private function dologs($method, $params = [])
    {
        $value = 'live room Task --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }

}