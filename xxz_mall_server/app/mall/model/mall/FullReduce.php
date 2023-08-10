<?php

namespace app\mall\model\mall;

use app\common\model\mall\FullReduce as FullReduceModel;

/**
 * 满减模型
 */
class FullReduce extends FullReduceModel
{
    /**
     * 获取列表记录
     */
    public function getList($data)
    {
        return $this->alias('reduce')->field(['reduce.*,purveyor.name as supplier_name'])
            ->join('purveyor', 'reduce.purveyor_id = purveyor.purveyor_id', 'left')
            ->where('reduce.is_delete', '=', 0)
            ->order(['reduce.create_time' => 'asc'])
            ->paginate($data);
    }

    /**
     * 新增记录
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
        return $this->save($data);
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

}