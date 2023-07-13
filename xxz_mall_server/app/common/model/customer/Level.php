<?php

namespace app\common\model\customer;

use app\common\model\BaseModel;

/**
 * 用户等级模型
 */
class Level extends BaseModel
{
    protected $pk = 'level_id';
    protected $name = 'customer_level';

    /**
     * 用户等级模型初始化
     */
    public static function init()
    {
        parent::init();
    }

    /**
     * 获取详情
     */
    public static function detail($level_id)
    {
        return self::find($level_id);
    }

    /**
     * 获取可用的会员等级列表
     */
    public static function getUsableList($appId = null)
    {
        $model = new static;
        $appId = $appId ? $appId : $model::$app_id;
        return $model->where('app_id', '=', $appId)
            ->order(['level' => 'asc', 'create_time' => 'asc'])
            ->select();
    }

    /**
     * 获取默认等级id
     */
    public static function getDefaultGradeId(){
        $grade = self::where('is_default', '=', 1)->find();
        return $grade['level_id'];
    }
}