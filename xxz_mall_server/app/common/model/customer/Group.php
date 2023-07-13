<?php

namespace app\common\model\customer;

use app\common\model\BaseModel;

/**
 * 用户标签模型
 */
class Group extends BaseModel
{
    protected $pk = 'group_id';
    protected $name = 'customer_group';

    /**
     * 用户标签模型初始化
     */
    public static function init()
    {
        parent::init();
    }

    /**
     * 获取详情
     */
    public static function detail($where)
    {
        $model = new static;
        $filter = ['is_delete' => 0];

        if (is_array($where)) {
            $filter = array_merge($filter, $where);
        } else {
            $filter['group_id'] = (int)$where;
        }

        return $model->where($filter)->find();
    }

    /**
     * 获取可用的会员标签列表
     */
    public static function getUsableList($appId = null)
    {
        $model = new static;
        $appId = $appId ? $appId : $model::$app_id;
        return $model->where('app_id', '=', $appId)
            ->order(['create_time' => 'asc'])
            ->select();
    }

    /**
     * 获取默认标签id
     */
    public static function getDefaultGradeId(){
        $grade = self::where('is_default', '=', 1)->find();
        return $grade['group_id'];
    }
}