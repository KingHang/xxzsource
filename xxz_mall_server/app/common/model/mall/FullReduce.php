<?php

namespace app\common\model\mall;

use app\common\model\BaseModel;

/**
 * 满减模型
 */
class FullReduce extends BaseModel
{
    protected $pk = 'fullreduce_id';
    protected $name = 'mall_fullreduce';

    /**
     * 获取详情
     */
    public static function detail($fullreduce_id)
    {
        return (new static())->find($fullreduce_id);
    }

    /**
     * 列表
     */
    public function getAll($shop_supplier_id)
    {
        return $this->where('is_delete', '=', '0')
            ->where('purveyor_id', '=', $shop_supplier_id)
            ->order(['create_time' => 'asc'])
            ->select();
    }
}