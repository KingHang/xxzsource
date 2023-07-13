<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_show extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'content' => 'require',
        'images' => 'require',
        'is_open' => 'require',
        'province' => 'require',
        'city' => 'require',
        'area' => 'require',
        'address' => 'require',
        'lng' => 'require',
        'lat' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'content.require' => '请填写服务秀内容',
        'images.require' => '请上传图片',
        'is_open.require' => '请选择是否打开位置',
        'province.require' => '请选择省',
        'city.require' => '请选择市',
        'area.require' => '请选择区',
        'address.require' => '请填写详细地址',
        'lng.require' => '经度不能为空',
        'lat.require' => '纬度不能为空',


    ];
}
