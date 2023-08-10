<?php

namespace app\api\model\goods;

use app\common\model\goods\GoodsSku as ProductSkuModel;

/**
 * 商品sku模型
 */
class GoodsSku extends ProductSkuModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time'
    ];

}