<?php
declare (strict_types = 1);

namespace app\validate\activity;

use think\Validate;

/**
 * @method check($postData)
 * @method getError()
 */
class Activity extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' =>'require',
        'image_id' =>'require',
        'activity_time_start' => 'require',
        'activity_time_end' => 'require',
        'category_id' => 'require',
        'group_id' => 'require',
        'signup_time_start' => 'require',
        'signup_time_end' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require'=> '活动主题不能为空',
        'image_id.require' => '图片不能为空',
        'activity_time_start.require' =>'请输入活动开始时间',
        'activity_time_end.require' => '请输入活动结束时间',
        'category_id.require' => '请选择分类',
        'group_id.require' => '请选择组',
        'signup_time_start.require'=>'请输入报名开始时间',
        'signup_time_end.require'=>'请输入报名结束时间'
    ];
}
