<?php

namespace app\common\model\customer;

use app\common\model\BaseModel;

/**
 * 权益卡模型
 */
class Card extends BaseModel
{
    protected $pk = 'card_id';
    protected $name = 'customer_card';

    /**
     * 权益卡模型初始化
     */
    public static function init()
    {
        parent::init();
    }
}