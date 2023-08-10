<?php

namespace app\mall\model\goods;

use app\common\model\goods\GoodsSku as ProductSkuModel;

/**
 * 商品规格模型
 */
class GoodsSku extends ProductSkuModel
{
    /**
     * 批量添加商品sku记录
     */
    public function addSkuList($product_id, $spec_list, $productSkuIdList)
    {
        $updateData = [];
        $saveData = [];
        foreach ($spec_list as $item) {
            $data = array_merge($item['spec_form'], [
                'spec_sku_id' => $item['spec_sku_id'],
                'goods_id' => $product_id,
                'app_id' => self::$app_id,
            ]);
            if(isset($item['goods_sku_id']) && $item['goods_sku_id'] > 0){
                $index = 0;
                foreach($productSkuIdList as $skuId){
                    if($skuId == $item['goods_sku_id']){
                        array_splice($productSkuIdList, $index, 1);
                        break;
                    }
                    $index++;
                }
                $updateData[] = [
                    'data' => $data,
                    'where' => [
                        'goods_sku_id' => $item['goods_sku_id'],
                    ],
                ];
            }else{
                $saveData[] = $data;
            }
        }
        count($updateData) > 0 && $this->updateAll($updateData);
        count($saveData) > 0 && $this->saveAll($saveData);
        count($productSkuIdList) > 0 && $this->where('goods_sku_id', 'in', $productSkuIdList)->delete();
    }

    /**
     * 添加商品规格关系记录
     */
    public function addProductSpecRel($product_id, $spec_attr)
    {
        $data = [];
        $model = new GoodsSpecRel;
        array_map(function ($val) use (&$data, $product_id, $model) {
            array_map(function ($item) use (&$val, &$data, $product_id, $model) {
                $detail = $model->where('goods_id', '=', $product_id)
                    ->where('spec_id', '=', $val['group_id'])
                    ->where('spec_value_id', '=', $item['item_id'])->find();
                if(!$detail){
                    $data[] = [
                        'goods_id' => $product_id,
                        'spec_id' => $val['group_id'],
                        'spec_value_id' => $item['item_id'],
                        'app_id' => self::$app_id,
                    ];
                }
            }, $val['spec_items']);
        }, $spec_attr);

        count($data) > 0 && $model->saveAll($data);
    }

    /**
     * 移除指定商品的所有sku
     */
    public function removeAll($product_id)
    {
        $model = new GoodsSpecRel;
        return $model->where('goods_id','=', $product_id)->delete();
    }

    /**
     * 移除指定商品的所有sku
     */
    public function removeSkuBySpec($product_id)
    {
        $model = new self;
        return $model->where('goods_id','=', $product_id)
            ->where('spec_sku_id', '<>', 0)
            ->delete();
    }
}
