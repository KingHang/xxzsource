<?php
declare (strict_types = 1);

namespace app\validate\activity;

use think\Validate;

class Adv extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'advname' => 'require',
        'image_id' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [

        'advname.require' => '请输入幻灯片',
        'image_id.require' => '请选择图片',
    ];
}
