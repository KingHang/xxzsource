<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_notice extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require',
        'content'=> 'require',
        'cover_img'=> 'require',
        'enabled' =>'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require' =>'请填写标题',
        'content.require'=> '请填写制度介绍',
        'cover_img'=> '请上传封面',
        'enabled' => '请选择状态'
    ];
}
