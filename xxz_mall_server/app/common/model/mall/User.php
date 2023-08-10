<?php

namespace app\common\model\mall;

use app\common\model\BaseModel;

/**
 * 商家用户模型
 */
class User extends BaseModel
{
    protected $name = 'mall_user';
    protected $pk = 'mall_user_id';

    /**
     * 关联应用表
     */
    public function app()
    {
        return $this->belongsTo('app\\common\\model\\app\\App', 'app_id', 'app_id');
    }

    /**
     * 关联用户角色表表
     */
    public function role()
    {
        return $this->belongsToMany('app\\common\\model\\auth\\Role', 'app\\common\\model\\auth\\UserRole');
    }

    public function userRole()
    {
        return $this->hasMany('app\\common\\model\\mall\\UserRole', 'mall_user_id', 'mall_user_id');
    }

    /**
     * 验证用户名是否重复
     */
    public static function checkExist($user_name)
    {
        return !!static::withoutGlobalScope()
            ->where('user_name', '=', $user_name)
            ->value('mall_user_id');
    }

    /**
     * 商家用户详情
     */
    public static function detail($where, $with = [])
    {
        !is_array($where) && $where = ['mall_user_id' => (int)$where];
        return (new static())->where(array_merge(['is_delete' => 0], $where))->with($with)->find();
    }

    /**
     * 保存登录状态
     */
    public function loginState($user)
    {
        $app = $user['app'];
        // 保存登录状态
        $session = array(
            'user' => [
                'mall_user_id' => $user['mall_user_id'],
                'user_name' => $user['user_name'],
                'app_id' => $user['app_id']
            ],
            'app' => $app->toArray(),
            'is_login' => true,
        );
        session('xxzmall_store', $session);
    }
}