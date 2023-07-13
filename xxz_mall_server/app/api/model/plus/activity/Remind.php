<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\Remind as RemindModel;

/**
 * 活动报名提醒模型
 */
class Remind extends RemindModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'app_id',
        'update_time',
        'create_time',
    ];

    public function add($activity_id,$user_id)
    {
        // 验证是否已设置
        if ($this->where(array('user_id' => $user_id , 'activity_id' => $activity_id))->count() > 0) {
            $this->error = '您已经设置过了，不能重复设置';
            return false;
        }
        $this->startTrans();
        try {
            $data = array(
                'user_id' => $user_id,
                'activity_id' => $activity_id,
                'app_id' => self::$app_id
            );
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}