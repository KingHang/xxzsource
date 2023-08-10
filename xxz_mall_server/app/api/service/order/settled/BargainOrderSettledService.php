<?php

namespace app\api\service\order\settled;

use app\common\enum\order\OrderSourceEnum;
use app\common\model\setting\Setting as SettingModel;

/**
 * 砍价订单结算服务类
 */
class BargainOrderSettledService extends OrderSettledService
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
            'source' => OrderSourceEnum::BARGAIN,
            'activity_id' => $supplierData[0]['productList'][0]['activity_id']
        ];
        $this->config = SettingModel::getItem('bargain');
        // 自身构造,差异化规则
        $this->settledRule = array_merge($this->settledRule, [
            'is_coupon' => $this->config['is_coupon'],
            'is_agent' => false,
            'is_use_points' => $this->config['is_point'],
            'is_user_grade' => false,     // 会员等级折扣
        ]);
    }

    /**
     * 验证订单商品的状态
     */
    public function validateProductList()
    {
        // 判断活动是否开启
        /*if (!$this->config['is_open']) {
            $this->error = "活动未开启";
            return false;
        }*/
        foreach ($this->supplierData[0]['productList'] as $product) {
            // 判断商品是否下架
            if ($product['product_status']['value'] != 10) {
                $this->error = "很抱歉，商品已下架";
                return false;
            }
            // 判断商品库存
            if ($product['total_num'] > $product['bargain_sku']['bargain_stock']) {
                $this->error = "很抱歉，商品库存不足";
                return false;
            }
        }
        return true;
    }
}