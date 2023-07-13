<?php
declare (strict_types = 1);

namespace app\validate\applet;

use think\Validate;

class Address extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'phone' => 'require',
//        'province_id' => 'require',
//        'city_id' =>' require',
//        'region_id' => 'require',
        'detail' => 'require',

    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '请输入姓名',
        'phone.require' => '请输入电话',
//        'province_id.require' => '请选择省',
//        'city_id.require' => '请选择城市',
//        'region_id.require' => '所在区id',
        'detail.require' => '请填写详细地址'
    ];
}
