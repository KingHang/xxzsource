<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\ActivityOrderDetail as ActivityOrderDetailModel;

/**
 * 活动幻灯片
 */
class ActivityOrderDetail extends ActivityOrderDetailModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'create_time',
        'image_id'
    ];

}