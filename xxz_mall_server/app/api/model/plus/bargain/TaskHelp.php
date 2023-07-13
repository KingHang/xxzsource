<?php

namespace app\api\model\plus\bargain;

use app\common\model\plugin\pricedown\TaskHelp as TaskHelpModel;

/**
 * 砍价任务助力记录模型
 */
class TaskHelp extends TaskHelpModel
{
    /**
     * 隐藏的字段
     */
    protected $hidden = [
        'app_id',
        'create_time',
    ];

    /**
     * 获取助力列表记录
     */
    public static function getListByTaskId($bargain_task_id)
    {
        // 获取列表数据
        return (new static())->with(['user'])
            ->where('bargain_task_id', '=', $bargain_task_id)
            ->order(['create_time' => 'desc'])
            ->select();
    }

    /**
     * 新增记录
     */
    public function add($task, $userId, $cutMoney, $isCreater = false)
    {
        return $this->save([
            'bargain_task_id' => $task['bargain_task_id'],
            'bargain_activity_id' => $task['bargain_activity_id'],
            'user_id' => $userId,
            'cut_money' => $cutMoney,
            'is_creater' => $isCreater,
            'app_id' => static::$app_id,
        ]);
    }

}