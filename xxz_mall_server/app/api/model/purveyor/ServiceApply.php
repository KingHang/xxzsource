<?php

namespace app\api\model\purveyor;
use app\common\model\purveyor\ServiceApply as ServiceApplyModel;
/**
 * 供应商服务保障申请模型
 */
class ServiceApply extends ServiceApplyModel
{
    /**
     * 获取列表数据
     */
    public function getList($shop_supplier_id)
    {
        $model = $this;
        // 查询列表数据
        $list = $model->alias('a')
            ->where('a.status','=',1)
            ->where('purveyor_id','=',$shop_supplier_id)
            ->join('purveyor_service_security s','s.service_security_id=a.service_security_id')
            ->field('name,describe')
            ->order(['a.create_time' => 'asc'])
            ->select();
        return $list;    
    }
   
}