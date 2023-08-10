<?php

namespace app\mall\model\auth;

use app\common\model\mall\UserRole as UserRoleModel;


/**
 * 角色模型
 */
class UserRole extends UserRoleModel
{

    public function getUserRole($where)
    {
        return $this->where($where)->column('role_id');

    }

    /**
     * 获取指定管理员的所有角色id
     * @param $mall_user_id
     * @return array
     */
    public static function getRoleIds($mall_user_id)
    {
        return (new self)->where('mall_user_id', '=', $mall_user_id)->column('role_id');
    }

    /**
     * 获取角色下的用户
     */
    public static  function getUserRoleCount($role_id){
        $model = new static();
        return $model->alias('userRole')
            ->join('mall_user', 'userRole.mall_user_id = mall_user.mall_user_id', 'left')
            ->where('userRole.role_id', '=', $role_id)
            ->where('mall_user.is_delete', '=', 0)
            ->count();
    }
}
