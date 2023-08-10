<?php

namespace app\common\model\goods;

use app\common\model\BaseModel;
use think\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * 商品SKU模型
 */
class GoodsSku extends BaseModel
{
    protected $name = 'goods_sku';

    protected $pk = 'goods_sku_id';
    protected $append = ['goods_id','goods_sku_id'];
    public function getProductIdAttr($value,$data)
    {
        if (isset($data['goods_id'])) {
            return $data['goods_id'];
        }
    }
    public function getProductSkuIdAttr($value,$data)
    {
        if (isset($data['goods_sku_id'])) {
            return $data['goods_sku_id'];
        }
    }
    /**
     * 规格图片
     */
    public function image()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }
    public function productgift()
    {
        return $this->hasMany('app\\common\\model\\goods\\GoodsGift','goods_sku_id','goods_sku_id');
    }

    /**
     * 获取sku信息详情
     * @param $productId
     * @param $specSkuId
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function detail($productId, $specSkuId)
    {
        return (new static())->where('goods_id', '=', $productId)
        ->where('spec_sku_id', '=', $specSkuId)->find();
    }

    /**
     * 获取sku列表
     * @param $productId
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function getSkuList($productId)
    {
        return (new static())->where('goods_id', '=', $productId)->order(['goods_sku_id' => 'asc'])->select();
    }
}
