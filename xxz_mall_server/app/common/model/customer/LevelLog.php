<?php

namespace app\common\model\customer;

use app\common\model\BaseModel;
use app\common\enum\user\grade\ChangeTypeEnum;

/**
 * 用户会员等级变更记录模型
 */
class LevelLog extends BaseModel
{
    protected $name = 'customer_level_log';
    protected $pk = 'level_log_id';
    protected $updateTime = false;

}