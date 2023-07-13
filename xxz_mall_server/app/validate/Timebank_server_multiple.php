<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Timebank_server_multiple extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require|max:255',
        'logo' => 'require',
        'is_line' => 'require',
        'timestart' => 'require',
        'timeend' => 'require',
        'org_id' => 'require',
        'protype_id'=> 'require',
        'time_account'=> 'require',
        'periodicity'=> 'require',
        'audit'=> 'require',
        'localsign'=> 'require',
        'isgps'=> 'require',
        'istime'=> 'require',
        'signup_time_start'=> 'require',
        'signup_time_end'=> 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '请填写项目名称',
        'logo.number' => '请输入项目封面',
        'is_line.number' => '请选择服务形式',
        'timestart.length' => '请输入服务开始时间',
        'timeend.require' => '请选择服务结束时间',
        'org_id.require' => '请选择组织',
        'protype_id.require' => '请选择项目分类id',
        'time_account.require' => '请输入时间酬劳',
        'signup_time_start.require' => '请输入报名开始时间',
        'signup_time_end.require' => '请输入报名结束时间',
        'periodicity.require' => '请选择是否周期报名',
        'audit.require' => '请选择是否报名审核',
        'localsign.require' => '请选择是否现场报名',
        'isgps.require' => '请选择是否gps定位',
        'istime.require' => '请选择是否倒计时'
    ];
}
