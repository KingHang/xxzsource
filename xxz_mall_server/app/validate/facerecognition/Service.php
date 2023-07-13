<?php
declare (strict_types = 1);

namespace app\validate\facerecognition;

use think\Validate;

/**
 * @method getError()
 * @method check($postData)
 */
class Service extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'server_name' => ['require'],
        'server_imgs' => ['require']
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'server_name.require' => '服务名称必填',
        'server_imgs.require' => '服务图片至少上传一张'
    ];
}
