<?php

namespace app\common\model\purveyor;

use app\common\model\BaseModel;

/**
 * 商家用户模型
 */
class User extends BaseModel
{
    protected $name = 'purveyor_user';
    protected $pk = 'purveyor_user_id';

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
        return $this->hasMany('app\\common\\model\\purveyor\\UserRole', 'purveyor_user_id', 'purveyor_user_id');
    }

    /**
     * 关联应用表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }

    /**
     * 验证用户名是否重复
     */
    public static function checkExist($user_name)
    {
        return !!static::withoutGlobalScope()
            ->where('user_name', '=', $user_name)
            ->value('purveyor_user_id');
    }

    /**
     * 商家用户详情
     */
    public static function detail($where, $with = [])
    {
        !is_array($where) && $where = ['purveyor_user_id' => (int)$where];
        return (new static())->where(array_merge(['is_delete' => 0], $where))->with($with)->find();
    }

    /**
     * 商家用户详情
     */
    public static function detailByUserId($user_id)
    {
        return (new static())->where('is_delete', '=', 0)
            ->where('user_id', '=', $user_id)
            ->where('purveyor_id', '>', 0)
            ->find();
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
                'purveyor_user_id' => $user['purveyor_user_id'],
                'user_name' => $user['user_name'],
                'purveyor_id' => $user['purveyor_id'],
                'app_id' => $user['app_id'],
                'user_id' => $user['user_id'],
                'org_id' => $user['org_id'],
            ],
            'app' => $app->toArray(),
            'is_login' => true,
        );
        session('xxzmall_purveyor', $session);
    }
}