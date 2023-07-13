<?php

namespace app\common\model\goods;

use app\common\model\BaseModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\db\Where;
use think\Model;

/**
 * 服务项目SKU模型
 */
class ServerSpecSku extends BaseModel
{
    protected $name = 'timebank_serverproject_spec_sku';
    protected $pk = 'project_sku_id';

    /**
     * 获取sku信息详情
     * @param $projectId
     * @param $specSkuId
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function detail($projectId, $specSkuId)
    {
        return static::where('project_id', '=', $projectId)->where('spec_sku_id', '=', $specSkuId)->find();
    }
}
