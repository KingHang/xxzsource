<?php

namespace app\api\service\order\settled;

use app\common\enum\order\OrderSourceEnum;
use app\api\model\order\Order as OrderModel;

/**
 * 分销商卡项订单结算服务类
 */
class BenefitCardOrderSettledService extends OrderBenefitCardSettledService
{
    /**
     * 构造函数
     */
    public function __construct($user, $supplierData, $params)
    {
        parent::__construct($user, $supplierData, $params);
        //订单来源
        $this->orderSource = [
            'source' => OrderSourceEnum::BENEFIT,
        ];
        //自身构造,差异化规则
    }

    public function validateProductList()
    {
        foreach ($this->supplierData as $supplier) {
            foreach ($supplier['cardList'] as $product) {
                // 判断商品是否下架
                if ($product['status'] != 1) {
                    $this->error = "很抱歉，权益卡 [{$product['card_name']}] 已下架";
                    return false;
                }
                // 判断商品库存
                if ($product['total_num'] > $product['stock']) {
                    $this->error = "很抱歉，权益卡 [{$product['card_name']}] 库存不足";
                    return false;
                }
            }
        }
        return true;
    }
}