<?php

namespace app\job\model\plus\bargain;

use app\common\model\plugin\pricedown\Task as TaskModel;


/**
 * 砍价任务模型
 */
class Task extends TaskModel
{
    /**
     * 获取已过期但未结束的砍价任务
     */
    public function getEndList()
    {
        return $this->where('end_time', '<=', time())
            ->where('status', '=', 0)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 将砍价任务标记为砍价失败(批量)
     */
    public function setIsEnd($taskIds)
    {
        return $this->where('bargain_task_id' , 'in', $taskIds)->data([
            'status' => 2
        ])->update();
    }

}