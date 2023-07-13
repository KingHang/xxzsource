<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_single extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'user_id' => 'require',
        'name' => 'require',
        'mobile' => 'require',
        'province' => 'require',
        'city' => 'require',
        'address' => 'require',
        'start_time' => 'require',
        'final_time' => 'require',
//        'content' => 'require',
        'project_id' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'user_id.require' =>'请选择会员',
        'name.require' =>'请填写名称',
        'mobile.require' => '请填写手机号',
        'province.require' => '请选择省',
        'city.require' => '请选择市',
        'address.require' => '请填写详细地址',
        'start_time.require' => '请填写开始时间',
        'final_time.require' => '请填写结束时间',
//        'content.require' => '请填写服务描述',
        'project_id.require' => '请选择服务项目'
    ];
}
