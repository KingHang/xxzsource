<?php

namespace app\api\model\store;

use app\common\model\store\Store as StoreModel;

/**
 * 商家门店模型
 */
class Store extends StoreModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

}