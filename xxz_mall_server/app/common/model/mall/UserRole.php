<?php

namespace app\common\model\mall;


use app\common\model\BaseModel;

/**
 * 应用模型
 */
class UserRole extends BaseModel
{
    protected $name = 'mall_user_role';
    protected $pk = 'id';

    /**
     * 关联角色
     * @return \think\model\relation\BelongsTo
     */
    public function role(){
        return $this->belongsTo('Role', 'role_id', 'role_id');
    }
}
