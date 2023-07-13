<?php

namespace app\common\model\goods;

use app\common\model\BaseModel;
/**
 * 商品规格关系模型
 */
class GoodsSpecRel extends BaseModel
{
    protected $name = 'goods_spec_rel';
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
