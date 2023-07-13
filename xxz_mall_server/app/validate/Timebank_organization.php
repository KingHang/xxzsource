<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_organization extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require|max:255',
        'social_code' => 'require',
        'social_code_img' => 'require',
        'logo' => 'require',
        'charge_mobile' => 'require|number|length:11',
        'charge_name' => 'require',
        'card_obverse' => 'require',
        'card_reverse' => 'require',
//        'country' => 'require',
        'province' => 'require',
        'city' => 'require',
        'address'=> 'require',
        'qrcode'=> 'require',
        'door'=> 'require',
        'area'=> 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '请填写名称',
        'social_code.require' => '请填写营业执照注册号',
        'social_code_img.require' => '请上传营业执照',
        'logo.require' => '请上传组织logo',
        'charge_name.require' => '请填写负责人姓名',
        'charge_mobile.require' => '请填写负责人手机号',
        'charge_mobile.length' => '请填写正确负责人手机号格式',
        'charge_mobile.number' => '请填写正确负责人手机号格式',
        'card_obverse.number' => '请上传身份证正面照',
        'card_reverse.number' => '请上传身份证反面照',
        'province.require' => '请选择省',
        'city.require' => '请选择市区',
        'address.require' => '请填写详细地址',
        'qrcode.require' => '请上传二维码',
        'area.require' => '请选择区',
        'door.require' => '请输入门牌号',
    ];
}
