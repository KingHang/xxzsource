<?php

namespace app\common\model\home;

use app\common\model\BaseModel;

/**
 * 用户信息模型
 */
class FormInfo extends BaseModel
{
    //表名
    protected $name = 'form_info';
    //主键字段名
    protected $pk = 'app_id';

    /**
     * 获取用户信息
     */
    public static function detail()
    {
        //全局有app_id，不用加
        $model = self::find();

        return $model;
    }
}
