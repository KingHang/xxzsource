<?php

namespace app\common\model\goods;

use app\common\model\BaseModel;

/**
 * 服务项目规格关系模型
 */
class ServerSpecRel extends BaseModel
{
    protected $name = 'timebank_serverproject_spec_rel';
    protected $pk = 'id';
    protected $updateTime = false;

    /**
     * 关联规格组
     */
    public function spec()
    {
        return $this->belongsTo('Spec');
    }
}
