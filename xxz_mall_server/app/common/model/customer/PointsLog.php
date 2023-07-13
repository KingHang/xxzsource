<?php

namespace app\common\model\customer;

use app\common\model\BaseModel;

class PointsLog extends BaseModel
{
    protected $name = 'customer_points_log';
    protected $pk = 'point_log_id';
    protected $updateTime = false;

    /**
     * 关联会员记录表
     */
    public function user()
    {
        $module = self::getCalledModule() ?: 'common';
        return $this->belongsTo("app\\{$module}\\model\\customer\\Customer");
    }

    /**
     * 新增记录
     */
    public static function add($data)
    {
        $static = new static;
        $static->save(array_merge(['app_id' => $static::$app_id], $data));
    }

    /**
     * 新增记录 (批量)
     */
    public function onBatchAdd($saveData)
    {
        return $this->saveAll($saveData);
    }

}