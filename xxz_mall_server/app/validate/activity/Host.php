<?php
declare (strict_types = 1);

namespace app\validate\activity;

use think\Validate;

class Host extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'logo' => 'require',
        'qrcode' => 'require',
        'membername' => 'require',
        'membermobile' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '主办方名称不能为空',
        'logo.require' => '主办方logo不能为空',
        'qrcode.require' => '客服二维码不能为空',
        'membername.require' =>'联系人姓名不能为空',
        'membermobile.require' =>'联系人电话不能为空'
    ];
}
