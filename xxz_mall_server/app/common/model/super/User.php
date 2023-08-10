<?php
namespace app\common\model\super;

use app\common\model\BaseModel;

/**
 * 超管后台用户模型
 */
class User extends BaseModel
{
    protected $name = 'super_user';
    protected $pk = 'super_user_id';
}