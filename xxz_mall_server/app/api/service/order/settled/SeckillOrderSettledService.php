<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\api\model\plus\seckill\Active as ActiveModel;
use app\api\model\plus\seckill\Product as ProductModel;
use app\common\enum\order\OrderSourceEnum;
use app\common\model\settings\Settings as SettingModel;

/**
 * 秒杀订单结算服务类
 */
class SeckillOrderSettledService extends OrderSettledService
{
    private $config;

    /**
     * 构造函数
     */
    public function __construct($user, $supplierData, $params)
    {

        parent::__construct($user, $supplierData, $params);
        // 订单来源
        $this->orderSource = [
            'source' => OrderSourceEnum::SECKILL,
            'activity_id' => $supplierData[0]['productList'][0]['activity_id']
        ];
        $this->config = SettingModel::getItem('seckill');
        // 自身构造,差异化规则
        $this->settledRule = array_merge($this->settledRule, [
            'is_coupon' => $this->config['is_coupon'],
            'is_agent' => $this->config['is_agent'],
            'is_use_points' => $this->config['is_point'],
            'is_user_grade' => false,     // 会员等级折扣
        ]);
    }

    /**
     * 验证订单商品的状态
     */
    public function validateProductList()
    {
        foreach ($this->supplierData[0]['productList'] as $product) {
            // 判断商品是否下架
            if ($product['product_status']['value'] != 10) {
                $this->error = "很抱歉，秒杀商品已下架";
                return false;
            }
            // 判断商品库存
            if ($product['total_num'] > $product['seckill_sku']['seckill_stock']) {
                $this->error = "很抱歉，秒杀商品库存不足";
                return false;
            }
            //是否在秒杀时间段
            $seckill_model = ProductModel::detail($product['seckill_sku']['seckill_product_id'], ['active']);
            $res = (new ActiveModel())->checkOrderTime($seckill_model['active']);
            if ($res['code'] != 0) {
                $this->error = $res[$res['code']];
                return false;
            }
            // 是否超过购买数
            $hasNum = OrderModel::getPlusOrderNum($this->user['user_id'], $product['product_source_id'],OrderSourceEnum::SECKILL);
            if($product['seckill_product']['limit_num'] > 0 && ($hasNum + $product['total_num'] > $product['seckill_product']['limit_num'])){
                $this->error = "很抱歉，你已购买或超过此商品最大秒杀数量";
                return false;
            }
        }
        return true;
    }
}