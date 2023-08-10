<?php

namespace app\mall\model\goods;

use app\common\model\goods\Goods as ProductModel;
use app\mall\service\ProductService;
use app\common\library\helper;

/**
 * 商品模型
 */
class Goods extends ProductModel
{
    /**
     * 添加商品
     */
    public function add(array $data)
    {
        if (!isset($data['image']) || empty($data['image'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $data['alone_grade_equity'] = isset($data['alone_grade_equity']) ? json_decode($data['alone_grade_equity'], true) : '';
        $data['grade_ids'] = implode(',', $data['grade_ids']);
        $data['audit_status'] = 10;
        $data['app_id'] = $data['sku']['app_id'] = self::$app_id;
        $data = $this->setData($data);
        // 开启事务
        $this->startTrans();
        try {
            // 添加商品
            $this->save($data);
            // 商品规格
            $this->addProductSpec($data);
            // 商品图片
            $this->addProductImages($data['image']);
            // 商品详情图片
            if ($data['is_picture'] == 1) {
                $this->addProductContentImages($data['contentImage']);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
    public function setData($data)
    {
        if (isset($data['params_value'])){
            $data['category_params_value'] = json_encode($data['params_value']);
            return $data;
        }
        return $data;
    }

    /**
     * 添加商品图片
     */
    private function addProductImages($images)
    {
        $this->image()->delete();
        $data = array_map(function ($images) {
            return [
                'image_id' => isset($images['file_id']) ? $images['file_id'] : $images['image_id'],
                'app_id' => self::$app_id
            ];
        }, $images);
        return $this->image()->saveAll($data);
    }

    /**
     * 添加商品详情图片
     */
    private function addProductContentImages($images)
    {
        $this->contentImage()->delete();
        $data = array_map(function ($images) {
            return [
                'image_id' => isset($images['file_id']) ? $images['file_id'] : $images['image_id'],
                'image_type' => 1,
                'app_id' => self::$app_id
            ];
        }, $images);
        return $this->contentImage()->saveAll($data);
    }

    /**
     * 编辑商品
     */
    public function edit($data)
    {
        if (!isset($data['image']) || empty($data['image'])) {
            $this->error = '请上传商品图片';
            return false;
        }
        $data['spec_type'] = isset($data['spec_type']) ? $data['spec_type'] : $this['spec_type'];
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $data['alone_grade_equity'] = isset($data['alone_grade_equity']) ? json_decode($data['alone_grade_equity'], true) : '';
        $data['grade_ids'] = implode(',', $data['grade_ids']);
        $data['app_id'] = $data['sku']['app_id'] = self::$app_id;
        $data['goods_id'] = $data['product_id'];
        $productSkuIdList = helper::getArrayColumn(($this['sku']), 'goods_sku_id');
        return $this->transaction(function () use ($data, $productSkuIdList) {
            // 审核商品
            if ($data['submit_type'] == 'edit') {
                unset($data['audit_status']);
            }
            $data = $this->setData($data);
            $this->save($data);
            // 商品规格
            $this->addProductSpec($data, true, $productSkuIdList);
            // 商品图片
            $this->addProductImages($data['image']);
            // 商品详情图片
            if ($data['is_picture'] == 1) {
                $this->addProductContentImages($data['contentImage']);
            }
            // 店铺商品总销量
            $this->reSupplierTotalSales($this['purveyor_id']);
            return true;
        });
    }

    /**
     * 添加商品规格
     */
    private function addProductSpec($data, $isUpdate = false, $productSkuIdList = [])
    {
        // 更新模式: 先删除所有规格
        $model = new GoodsSku;
        $isUpdate && $model->removeAll($this['goods_id']);
        $stock = 0;//总库存
        $product_price = 0;//价格
        $line_price = 0;
        // 添加规格数据
        if ($data['spec_type'] == '10') {
            // 删除多规格遗留数据
            $isUpdate && $model->removeSkuBySpec($this['goods_id']);
            // 单规格
            $this->sku()->save($data['sku']);
            $stock = $data['sku']['stock_num'];
            $product_price = $data['sku']['product_price'];
            $line_price = $data['sku']['line_price'];
        } else if ($data['spec_type'] == '20') {
            // 添加商品与规格关系记录
            $model->addProductSpecRel($this['goods_id'], $data['spec_many']['spec_attr']);
            // 添加商品sku
            $model->addSkuList($this['goods_id'], $data['spec_many']['spec_list'], $productSkuIdList);
            $product_price = $data['spec_many']['spec_list'][0]['spec_form']['product_price'];
            foreach ($data['spec_many']['spec_list'] as $item) {
                $stock += $item['spec_form']['stock_num'];
                if ($item['spec_form']['product_price'] < $product_price) {
                    $product_price = $item['spec_form']['product_price'];
                }
                if ($item['spec_form']['line_price'] < $line_price) {
                    $line_price = $item['spec_form']['line_price'];
                }
            }
        }
        $this->save([
            'product_stock' => $stock,
            'product_price' => $product_price,
            'line_price' => $line_price
        ]);
    }

    /**
     * 修改商品状态
     */
    public function setStatus($state)
    {
        return $this->allowField(true)->save(['product_status' => $state ? 10 : 20]) !== false;
    }

    /**
     * 产品上下架
     * @param $product_id
     * @param $type
     * @return Goods|bool
     */
    public function operateProduct($product_id, $type)
    {
        $arr = explode(',', $product_id);

        if (!$product_id || empty($arr)) return false;

        return $this->where('goods_id', 'in', $arr)->update(['product_status' => $type == 1 ? 10 : 20]);
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        if (ProductService::checkSpecLocked($this, 'delete')) {
            $this->error = '当前商品正在参与其他活动，不允许删除';
            return false;
        }
        //  回收站，和未审核通过的直接删
        if ($this['product_status']['value'] == 30 || $this['audit_status'] != 10) {
            return $this->save(['is_delete' => 1]);
        } else {
            return $this->save(['product_status' => 30]);
        }
    }


    /**
     * 获取商品告急数量总数
     */
    public function getProductStockTotal()
    {

        return $this->where('is_delete', '=', 0)->where('product_stock', '<', 20)->count();
    }

    public function getProductId($search)
    {
        $res = $this->where('product_name', 'like', '%' . $search . '%')->select()->toArray();
        return array_column($res, 'product_id');
    }

    public function setAudit($status)
    {
        return $this->save([
            'audit_status' => $status
        ]);
    }

    /**
     * 设置是否允许抵扣
     * @param $product_id
     * @param int $type 1-允许抵扣，2-不允许抵扣
     * @return Goods
     */
    public function setDeduct($product_id, $type)
    {
        $list = explode(',', $product_id);
        return $this->where('goods_id', 'in', $list)->update(['is_deduct' => $type == 2 ? 1 : 0]);
    }

    /**
     * 商品抵扣设置
     * @param $data
     * @return bool
     */
    public function setDeductSetting($data)
    {
        $res = $this->where('goods_id', '=', $data['product_id'])->update([
            'deduct_type'             => $data['deduct_type'],
            'customize_deduct'        => $data['customize_deduct'],
            'deduct_money_setting'    => $data['deduct_money_setting'],
            'deduct_discount_setting' => $data['deduct_discount_setting']
        ]);
        return $res !== false;
    }

    /**
     * 获取数量
     */
    public function getCount($type)
    {
        $model = $this;
        // 销售中
        if ($type == 'sell') {
            $model = $model->where('audit_status', '=', 10);
            $model = $model->where('product_status', '=', 10);
        }
        //仓库中
        if ($type == 'lower') {
            $model = $model->where('audit_status', '=', 10);
            $model = $model->where('product_status', '=', 20);
        }
        // 回收站
        if ($type == 'recovery') {
            $model = $model->where('product_status', '=', 30);
        }
        //待审核
        if ($type == 'audit') {
            $model = $model->where('audit_status', '=', 0);
        }
        //未通过
        if ($type == 'no_audit') {
            $model = $model->where('audit_status', '=', 20);
        }

        return $model->where('is_delete', '=', 0)
            ->count();
    }

    /**
     * 查询指定商品
     * @param $value
     */
    public function getProduct($value)
    {
        return $this->with(['image.file'])->where('product_id', 'in', $value)->hidden(['content'])->select();
    }
}
