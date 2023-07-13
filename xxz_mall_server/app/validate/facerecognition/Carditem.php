<?php
declare (strict_types = 1);

namespace app\validate\facerecognition;

use think\Validate;

/**
 * @method check($postData)
 * @method getError()
 */
class Carditem extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require',
        'retail_price' => 'require',
        'valid_period' => 'require',
//        'shop_type' => 'require',
        'is_online' => 'require',
        'protocol_status' => 'require',
        'card_background' => 'require',
        'on_shelf' => 'require',
        'particulars' => 'require',
//        'sales' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require' => '请填写标题',
        'retail_price.require' => '请填写零售价',
        'valid_period.require' => '请选择有效期',
//        'shop_type.require' => '请选择适用门店',
        'is_online.require' => '请选择是否网店销售',
        'protocol_status.require' => '请选择是否显示卡协议',
        'card_background.require' => '请选择卡片背景图',
        'on_shelf.require' => '请选择是否上架'
    ];
}
