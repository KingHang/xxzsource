<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\ActivityHostRember as ActivityHostRemberModel;

/**
 * 活动幻灯片
 */
class ActivityHostRember extends ActivityHostRemberModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'update_time',
        'create_time',
    ];

    /**
     * 验证主办方成员是否有操作指定活动权限
     * @param $user_id
     * @param $host_id
     * @return bool
     */
    public function checkUserActivityAuth($user_id,$host_id)
    {
        return !!$this->where(['user_id' => $user_id , 'host_id' => $host_id ,'is_delete' => 0])->value('id');
    }
    // 获取会员所属主办方id集合
    public function checkUserAuth($user_id)
    {
        return $this->where(['user_id' => $user_id , 'is_delete' => 0])->value('GROUP_CONCAT(host_id)');
    }

}