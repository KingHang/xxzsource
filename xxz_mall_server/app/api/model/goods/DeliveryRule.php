<?php

namespace app\api\model\goods;

use app\common\model\setting\DeliveryRule as DeliveryRuleModel;
/**
 * 配送模板区域及运费模型
 */
class DeliveryRule extends DeliveryRuleModel
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