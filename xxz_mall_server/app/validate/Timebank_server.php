<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_server extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require|max:255',
        'mobile' => 'require|mobile|number|length:11',
        'gender' => 'number|require',
        'org_id' => 'require',
        'idcard' => 'require|number|length:18',
        'card_obverse' => 'require',
        'card_reverse' => 'require',
        'user_id'=> 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '请填写名称',
        'mobile.number' => '手机号必须是数字',
        'mobile.length' => '请输入正确的手机号格式',
        'idcard.number' => '请输入正确格式的身份证',
        'idcard.length' => '请输入正确格式的身份证',
        'org_id.require' => '请选择机构组织',
        'mobile.require' => '请填写手机号',
        'gender.require' => '请选择性别',
        'card_obverse' => '请上传身份证正面照',
        'card_reverse' => '请上传身份证反面照',
        'user_id.require' => '请选择会员'
    ];
}
