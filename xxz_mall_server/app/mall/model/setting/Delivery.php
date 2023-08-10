<?php

namespace app\mall\model\setting;

use app\common\model\setting\DeliveryRule as DeliveryRuleModel;
use app\mall\model\goods\Goods as ProductModel;
use app\common\model\setting\Delivery as DeliveryModel;

/**
 * 配送模板模型
 */
class Delivery extends DeliveryModel
{
    /**
     * 添加新记录
     */
    public function add($data)
    {
        if (!isset($data['rule']) || empty($data['rule'])) {
            $this->error = '请选择可配送区域';
            return false;
        }

        $delivery = [
            'name' => $data['name'],
            'method' => $data['radio'],
            'sort' => $data['sort'],
            'purveyor_id' => 10001,
            'app_id' => self::$app_id
        ];

        if ($this->save($delivery)) {
            return $this->createDeliveryRule($data['rule'], 'add');
        }
        return false;
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
        if (!isset($data['rule']) || empty($data['rule'])) {
            $this->error = '请选择可配送区域';
            return false;
        }

        $delivery = [
            'name' => $data['name'],
            'method' => $data['radio'],
            'sort' => $data['sort'],
        ];

        if ($this->save($delivery)) {
            return $this->createDeliveryRule($data['rule'],'edit');
        }
        return false;
    }

    /**
     * 添加模板区域及运费
     */
    private function createDeliveryRule($data, $scene = 'add')
    {
        $save = [];
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $save[] = [
                'region' => join(',', array_values($data[$i]['citys'])),
                'first' => $data[$i]['first'],
                'first_fee' => $data[$i]['first_fee'],
                'additional' => $data[$i]['additional'],
                'additional_fee' => $data[$i]['additional_fee'],
                'app_id' => self::$app_id
            ];
        }
        if($scene == 'edit'){
            (new DeliveryRuleModel())->where('delivery_id', '=', $this['delivery_id'])->delete();
        }
        return $this->rule()->saveAll($save);
    }

    /**
     * 表单验证
     */
    private function onValidate($data)
    {
        if (!isset($data['rule']) || empty($data['rule'])) {
            $this->error = '请选择可配送区域';
            return false;
        }
        foreach ($data['rule']['first'] as $value) {
            if ((int)$value <= 0 || (int)$value != $value) {
                $this->error = '首件或首重必须是正整数';
                return false;
            }
        }
        foreach ($data['rule']['additional'] as $value) {
            if ((int)$value <= 0 || (int)$value != $value) {
                $this->error = '续件或续重必须是正整数';
                return false;
            }
        }
        return true;
    }

    /**
     * 获取配送区域及运费设置项
     */
    public function getFormList()
    {
        // 所有地区
        $regions = Region::getCacheAll();
        $list = [];
        foreach ($this['rule'] as $rule) {
            $citys = explode(',', $rule['region']);
            $province = [];
            foreach ($citys as $cityId) {
                if (!isset($regions[$cityId])) continue;
                !in_array($regions[$cityId]['pid'], $province) && $province[] = $regions[$cityId]['pid'];
            }
            $list[] = [
                'first' => $rule['first'],
                'first_fee' => $rule['first_fee'],
                'additional' => $rule['additional'],
                'additional_fee' => $rule['additional_fee'],
                'province' => $province,
                'citys' => $citys,
            ];
        }
        return $list;
    }


    /**
     * 删除记录
     */
    public function remove()
    {
        // 验证运费模板是否被商品使用
        if (!$this->checkIsUseProduct($this['delivery_id'])) {
            return false;
        }
        // 删除运费模板
        $this->rule()->delete();
        return $this->delete();
    }

    /**
     * 验证运费模板是否被商品使用
     */
    private function checkIsUseProduct($deliveryId)
    {
        // 判断是否存在商品
        $productCount = (new ProductModel)->where('delivery_id', '=', $deliveryId)
            ->where('is_delete', '=', 0)
            ->count();
        if ($productCount > 0) {
            $this->error = '该模板被' . $productCount . '个商品使用，不允许删除';
            return false;
        }
        return true;
    }

}
