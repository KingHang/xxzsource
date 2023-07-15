<?php

namespace app\shop\controller\finance;

use app\shop\controller\Controller;
use app\common\library\helper;
use app\shop\model\order\Order as OrderModel;
use app\shop\model\purveyor\Purveyor as SupplierModel;
use app\shop\model\plugin\agent\User as AgentUserModel;
/**
 * 提现
 */
class Finance extends Controller
{
    /**
     * 首页概况
     */
    public function index()
    {
        // 平台统计数据
        $tj_data = [
            'total_money' => helper::number2((new OrderModel())->getTotalMoney('all')),
            'supplier_money' => helper::number2((new OrderModel())->getTotalMoney('supplier')),
            'sys_money' => helper::number2((new OrderModel())->getTotalMoney('sys')),
        ];
        // 供应商统计数据
        $supplier_data = [
            'total_money' => helper::number2((new SupplierModel())->getTotalMoney('total_money')),
            'money' => helper::number2((new SupplierModel())->getTotalMoney('money')),
            'nosettled_money' => helper::number2((new OrderModel())->getTotalMoney('supplier', 0)),
            'freeze_money' => helper::number2((new SupplierModel())->getTotalMoney('freeze_money')),
            'cash_money' => helper::number2((new SupplierModel())->getTotalMoney('cash_money')),
            'deposit_money' => helper::number2((new SupplierModel())->getTotalMoney('deposit_money')),
        ];
        // 分销商统计数据
        $agent_data = [
        ];
        return $this->renderSuccess('', compact( 'tj_data', 'supplier_data', 'agent_data'));
    }
}