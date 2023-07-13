<?php
declare (strict_types = 1);

namespace app\validate\facerecognition;

use think\Validate;

/**
 * @method check($postData)
 * @method getError()
 */
class Recognition extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name' => 'require',
        'store_id' => 'require',
        'logo' => 'require',
        'equipment_id' => 'require',
        'type' => 'require',
        'status' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'name.require' => '请输入通道名称',
        'store_id.require' => '请选择门店',
        'logo.require' => '请选择图标',
        'equipment_id.require' => '请选择设备',
        'type.require' => '请选择通道类型',
        'status.require' => '请选择是否启用',
    ];
}
