<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\Adv as AdeModel;

/**
 * 活动幻灯片
 */
class Adv extends AdeModel
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
        'enabled',
        'adv_order',
        'image_id'
    ];

    /**
     * 获取幻灯片列表
     */
    public function getList()
    {
        return $this ->with(['uploadfile'])
            ->where(['is_delete' => 0 , 'app_id' => self::$app_id , 'enabled' => 1])
            ->order(['adv_order' => 'asc', 'create_time' => 'desc'])
            ->select();
    }

}