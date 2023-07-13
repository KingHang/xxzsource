<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Projectvalues extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'value_value' => 'require',
        'project_id'=> 'require',
        'pid'=> 'require',
        'server_hour'=> 'require',
        'time_account'=> 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'value_value.require' =>'请填写规格名称',
        'project_id.require'=> '项目id不能为空',
        'pid.require'=> 'pid不能为空',
        'server_hour.require'=> '服务时长必填',
        'time_account.require'=> '时间酬劳必填',

    ];
}
