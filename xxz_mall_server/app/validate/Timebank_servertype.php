<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_servertype extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'type_name' => 'require',
        'type_img' => 'require',
        'sort' => 'require',
        'status' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'type_name.require'=>'请填写分类名称',
        'type_img.require' => '请上传分类图片',
        'sort.require' => '请填写序号'
    ];
}
