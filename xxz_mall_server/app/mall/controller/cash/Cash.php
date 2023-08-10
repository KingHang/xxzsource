<?php

namespace app\mall\controller\cash;

use app\mall\controller\Controller;
use app\common\library\helper;
use app\mall\model\order\Order as OrderModel;
use app\mall\model\purveyor\Purveyor as SupplierModel;
/**
 * 提现
 */
class Cash extends Controller
{
    /**
     * 首页概况
     */
    public function index()
    {
        // 平台统计数据
        $tj_data = [
            'total_money' => helper::number2((new OrderModel())->getTotalMoney('all')),
            'supplier_money' => helper::number2((new OrderModel())->getTotalMoney('purveyor')),
            'sys_money' => helper::number2((new OrderModel())->getTotalMoney('sys')),
        ];

        // 分销商统计数据
        $agent_data = [
            'all_money' => 0,
            'money' => 0,
            'freeze_money' => 0,
            'total_money' => 0,
        ];
        return $this->renderSuccess('', compact( 'tj_data', 'agent_data'));
    }
}
