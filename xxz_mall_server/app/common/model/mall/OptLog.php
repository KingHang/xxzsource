<?php

namespace app\common\model\mall;

use app\common\model\BaseModel;

/**
 * 登录日志模型
 */
class OptLog extends BaseModel
{
    protected $name = 'mall_opt_log';
    protected $pk = 'opt_log_id';

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\mall\\User', 'mall_user_id', 'mall_user_id');
    }
}