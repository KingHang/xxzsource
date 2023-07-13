<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_serverproject extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'project_title' => 'require',
        'project_imgs' => 'require',
        'type_id' => 'require',
        'time_account' => 'require',
        'enabled' => 'require',
        'server_hour' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'project_title.require'=>'请填写项目名称',
        'project_imgs.require' => '请上传项目图片',
        'type_id.require' => '请选择分类',
        'time_account.require' => '请填写时间酬劳',
        'enabled.require' => '请选择上下架',
        'server_hour.require' => '请填写服务时长'
    ];
}
