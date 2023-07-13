<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_slide extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require',
        'img_url'=> 'require',
        'enabled' =>'require',
        'displayorder' =>'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require' =>'请填写标题',
        'img_url.require'=> '请上传图片',
        'enabled.require' => '请选择状态',
        'displayorder.require'=> '请填写序号'
    ];
}
