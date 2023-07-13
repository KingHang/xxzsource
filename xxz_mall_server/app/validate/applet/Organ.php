<?php
declare (strict_types = 1);

namespace app\validate\applet;

use think\Validate;

class Organ extends Validate
{
    protected $rule = [
        'name' => 'require|max:255',
        'social_code_img' => 'require',
        'logo' => 'require',
        'charge_mobile' => 'require|number|length:11',
        'charge_name' => 'require',
        'card_obverse' => 'require',
        'card_reverse' => 'require',
        'idcard' => 'require',
        'organ_type' =>'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '请填写组织名称',
        'social_code_img.require' => '请上传营业执照',
        'charge_name.require' => '请填写负责人姓名',
        'charge_mobile.require' => '请填写负责人手机号',
        'charge_mobile.length' => '请填写正确负责人手机号格式',
        'charge_mobile.number' => '请填写正确负责人手机号格式',
        'card_obverse.number' => '请上传身份证正面照',
        'card_reverse.number' => '请上传身份证反面照',
        'organ_type.require' => '请选择组织类型',
        'idcard.require' => '请输入身份证号',
    ];
}
