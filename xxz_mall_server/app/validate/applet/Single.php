<?php
declare (strict_types = 1);

namespace app\validate\applet;

use think\Validate;

class Single extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [

        'start_time' => 'require',
        'final_time' => 'require',
        'project_id' => 'require',
        'content' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [

        'start_time.require' => '请填写开始时间',
        'final_time.require' => '请填写结束时间',
        'project_id.require' => '请选择服务项目',
        'content.require' => '需求描述'
    ];
}
