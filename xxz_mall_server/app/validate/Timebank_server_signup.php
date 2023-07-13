<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_server_signup extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'server_id' => 'require',
        'name' => 'require',
        'mobile' => 'require',
        'mult_id' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'server_id.require' => '请选择服务服务者',
        'name.require' => '请填写姓名',
        'mobile.require' => '电话不能为空',
        'mult_id.require' => '请选择报名哪个服务',
    ];
}
