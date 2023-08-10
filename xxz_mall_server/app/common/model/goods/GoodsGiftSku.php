<?php
declare (strict_types = 1);

namespace app\common\model\goods;

use think\Model;

/**
 * @mixin \think\Model
 */
class GoodsGiftSku extends Model
{
    protected $pk = 'gift_goods_sku_id';
    protected $name = 'goods_gift_sku';
    public function product()
    {
        return $this->hasOne('app\\common\\model\\goods\\goods', 'goods_id', 'gift_goods_id');
    }
    /**
     * 关联商品规格关系表
     */
    public function specRel()
    {
        return $this->belongsToMany('SpecValue', 'GoodsSpecRel')->order(['id' => 'asc']);
    }
}
