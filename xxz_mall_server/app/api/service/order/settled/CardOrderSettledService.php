<?php

namespace app\api\service\order\settled;

use app\common\enum\order\OrderSourceEnum;
use app\api\model\order\Order as OrderModel;

/**
 * 分销商卡项订单结算服务类
 */
class CardOrderSettledService extends OrderCardSettledService
{
    /**
     * 构造函数
     */
    public function __construct($user, $supplierData, $params)
    {
        parent::__construct($user, $supplierData, $params);
        //订单来源
        $this->orderSource = [
            'source' => OrderSourceEnum::CARD,
            'activity_id' => 0
        ];
        //自身构造,差异化规则
    }

    /**
     * 验证订单商品的状态
     */
    public function validateProductList()
    {
        return true;
    }
}