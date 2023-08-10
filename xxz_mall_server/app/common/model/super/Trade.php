<?php

declare (strict_types = 1);

namespace app\common\model\super;

use think\Model;

/**
 * @mixin Model
 */
class Trade extends Model
{
    protected $name = 'trade';

    protected $pk = 'trade_id';

    /**
     * 获取所有行业
     */
    public static function getTradeAll()
    {
        $model = new static;
        return $model->order(['sort' => 'desc', 'create_time' => 'desc'])->select();
    }
}
