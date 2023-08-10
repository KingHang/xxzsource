<?php

namespace app\common\service\order;

use app\common\library\helper;
use app\common\model\setting\Setting;
use app\common\model\user\User as UserModel;
use app\common\model\user\GrowthLog as GrowthLogModel;
use app\common\model\user\PointsLog as PointsLogModel;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\order\OrderSettled as OrderSettledModel;
use app\common\model\order\OrderGoods as OrderProductModel;
use Exception;

/**
 * 已完成订单结算服务类
 */
class OrderCompleteService
{
    // 订单类型
    private $orderType;

    /**
     * 订单模型类
     * @var array
     */
    private $orderModelClass = [
        OrderTypeEnum::MASTER => 'app\common\model\order\Order',
    ];

    // 模型
    private $model;

    /* @var UserModel $model */
    private $UserModel;

    /**
     * 构造方法
     */
    public function __construct($orderType = OrderTypeEnum::MASTER)
    {
        $this->orderType = $orderType;
        $this->model = $this->getOrderModel();
        $this->UserModel = new UserModel;
    }

    /**
     * 初始化订单模型类
     */
    private function getOrderModel()
    {
        $class = $this->orderModelClass[$this->orderType];
        return new $class;
    }

    /**
     * 执行订单完成后的操作
     */
    public function complete($orderList, $appId)
    {
        // 已完成订单结算
        // 条件：后台订单流程设置 - 已完成订单设置0天不允许申请售后
        //if (SettingModel::getItem('trade', $appId)['order']['refund_days'] == 0) {
            $this->settled($orderList);
        //}

        return true;
    }

    /**
     * 执行订单结算
     * @throws Exception
     */
    public function settled($orderList)
    {
        // 订单id集
        $orderIds = helper::getArrayColumn($orderList, 'order_id');
        // 累积用户实际消费金额
//         $this->setIncUserExpend($orderList);
        // 处理订单赠送的积分
        $this->setGiftPointsBonus($orderList);
        // 处理订单赠送的成长值
        $this->setGiftGrowthValueBonus($orderList);
        // 将订单设置为已结算
        $this->model->onBatchUpdate($orderIds, ['is_settled' => 1]);
        // 供应商结算
        $this->setIncSupplierMoney($orderList);
        return true;
    }

    /**
     * 供应商金额=支付金额-运费
     */
    private function setIncSupplierMoney($orderList)
    {
        // 计算并累积实际消费金额(需减去售后退款的金额)
        $supplierData = [];
        $supplierCapitalData = [];
        // 订单结算记录
        $orderSettledData = [];
        foreach ($orderList as $order) {
            if($order['purveyor_id'] == 0){
                continue;
            }
            // 供应价格+运费
            $supplierMoney = $order['supplier_money'];
            $sysMoney = $order['sys_money'];
            // B2b模式，如果有参与分销，减去分销的佣金
            // 商城设置
            $settings = Setting::getItem('store');
            $refundSupplierMoney = 0;
            $refundSysMoney = 0;
            // 减去订单退款的金额
            foreach ($order['product'] as $product) {
                if (
                    !empty($product['refund'])
                    && $product['refund']['type']['value'] == 10      // 售后类型：退货退款
                    && $product['refund']['is_agree']['value'] == 10  // 商家审核：已同意
                ) {
                    $supplierMoney -= $product['supplier_money'];
                    $sysMoney -= $product['sys_money'];
                    $refundSupplierMoney += $product['supplier_money'];
                    $refundSysMoney += $product['sys_money'];
                }
            }
            $agentMoney = 0;
            !isset($supplierData[$order['purveyor_id']]) && $supplierData[$order['purveyor_id']] = 0.00;
            $supplierMoney > 0 && $supplierData[$order['purveyor_id']] += $supplierMoney;
            $orderSettledData[] = [
                'order_id' => $order['order_id'],
                'purveyor_id' => $order['purveyor_id'],
                'order_money' => $order['order_price'],
                'pay_money' => $order['pay_price'],
                'express_money' => $order['express_price'],
                'supplier_money' => $order['supplier_money'],
                'real_supplier_money' => $supplierMoney,
                'sys_money' => $order['sys_money'],
                'real_sys_money' => $sysMoney,
                'agent_money' => $agentMoney,
                'refund_money' => $refundSupplierMoney + $refundSysMoney,
                'refund_supplier_money' => $refundSupplierMoney,
                'refund_sys_money' => $refundSysMoney,
                'app_id' => $order['app_id']
            ];
            // 商家结算记录
            $supplierCapitalData[] = [
                'purveyor_id' => $order['purveyor_id'],
                'money' => $supplierMoney,
                'describe' => '订单结算，订单号：' . $order['order_no'],
                'app_id' => $order['app_id']
            ];
        }
        // 修改平台结算金额
        (new OrderSettledModel())->saveAll($orderSettledData);
        return true;
    }
    /**
     * 处理订单赠送的积分
     */
    private function setGiftPointsBonus($orderList)
    {
        // 计算用户所得积分
        $userData = [];
        $logData = [];
        foreach ($orderList as $order) {
            // 计算用户所得积分
            $pointsBonus = $order['points_bonus'];
            if ($pointsBonus <= 0) continue;
            // 减去订单退款的积分
            foreach ($order['product'] as $product) {
                if (
                    !empty($product['refund'])
                    && $product['refund']['type']['value'] == 10      // 售后类型：退货退款
                    && $product['refund']['is_agree']['value'] == 10  // 商家审核：已同意
                ) {
                    $pointsBonus -= $product['points_bonus'];
                }
            }
            // 计算用户所得积分
            !isset($userData[$order['user_id']]) && $userData[$order['user_id']] = 0;
            $userData[$order['user_id']] += $pointsBonus;
            // 整理用户积分变动明细
            $logData[] = [
                'user_id' => $order['user_id'],
                'value' => $pointsBonus,
                'describe' => "订单赠送：{$order['order_no']}",
                'app_id' => $order['app_id'],
            ];
        }
        if (!empty($userData)) {
            // 累积到会员表记录
            $this->UserModel->onBatchIncPoints($userData);
            // 批量新增积分明细记录
            (new PointsLogModel)->onBatchAdd($logData);
        }
        return true;
    }

    /**
     * 处理订单赠送的成长值
     * @throws Exception
     */
    private function setGiftGrowthValueBonus($orderList)
    {
        // 计算用户所得成长值
        $userData = [];
        $logData = [];
        foreach ($orderList as $order) {
            // 计算用户所得成长值
            $growthValueBonus = $order['growth_value_bonus'];
            if ($growthValueBonus <= 0) continue;
            // 减去订单退款的成长值
            foreach ($order['product'] as $product) {
                if (
                    !empty($product['refund'])
                    && $product['refund']['type']['value'] == 10      // 售后类型：退货退款
                    && $product['refund']['is_agree']['value'] == 10  // 商家审核：已同意
                ) {
                    $growthValueBonus -= $product['growth_value_bonus'];
                }
            }
            // 计算用户所得成长值
            !isset($userData[$order['user_id']]) && $userData[$order['user_id']] = 0;
            $userData[$order['user_id']] += $growthValueBonus;
            // 整理用户成长值变动明细
            $logData[] = [
                'user_id' => $order['user_id'],
                'value' => $growthValueBonus,
                'describe' => "订单赠送：{$order['order_no']}",
                'app_id' => $order['app_id'],
            ];
        }
        if (!empty($userData)) {
            // 累积到会员表记录
            $this->UserModel->onBatchIncGrowthValue($userData);
            // 批量新增成长值明细记录
            (new GrowthLogModel)->onBatchAdd($logData);
        }
        return true;
    }

    /**
     * 处理订单商品赠送CFP等等
     * @param $orderList
     * @return bool
     */
    public function setGiftcertBonus($orderList)
    {
        // 计算用户所得CFP等等
        $optData = [];
        $log_list = [];
        $product_list = [];
        $userData = [];
        foreach ($orderList as $order) {
            $remark = '商品CFP赠送-' . $order['order_no'];
            // 获取会员信息
            !isset($userData[$order['user_id']]) && $userData[$order['user_id']] = UserModel::detail($order['user_id']);
            // 商品的CFP赠送记录
            foreach ($order['product'] as $product) {
                // 计算用户所得CFP等等
                $amount = 0;
                if (!empty($product['refund']) && $product['refund']['type']['value'] == 10 && $product['refund']['is_agree']['value'] == 10) continue;
                // 有赠送
                if ($product['is_gift'] && $product['gift_amount'] > 0) {
                    // 计算发放CFP数量
                    if (in_array($product['gift_stages'],[1,0])) {
                        //不分期直接全部发放
                        $amount = ($product['gift_amount'] * $product['total_num']);
                    } else {

                        $remark .=  "(";
                        $remark .= $product['already_stages']+1;
                        $remark .= "/ " . $product['gift_stages'] . ")";
                        // 获取分期CFP平均数
                        $gift_amount = number_format( $product['gift_amount']/$product['gift_stages'], 5, '.', '');
                        if ($product['already_stages']+1 < $product['gift_stages']) {
                            // 非最后一期
                            $amount = $gift_amount * $product['total_num'];
                        } else {
                            // 最后一期发放剩余全部
                            $amount = $product['gift_amount'] - ($gift_amount * $product['total_num'] * $product['already_stages']);
                        }
                    }
                    $remark .= $product['product_name'];
                    if ($amount > 0) {
                        // 整理发放数量及信息
                        $optData[] = [
                            'mobile' => $userData[$order['user_id']]['mobile'],
                            'amount' => $amount,
                            'user_id' => $order['user_id'],
                            'remark' => $remark
                        ];
                        // 发放日志
                        $log_list[] = [
                            'order_id' => $product['order_id'],
                            'order_product_id' => $product['order_product_id'],
                            'amount' => $amount,
                            'stages_number' => $product['already_stages'] +1,
                            'app_id' => $product['app_id'],
                            'create_time' => time(),
                            'update_time' => time(),
                        ];
                        // 修改商品发放次数
                        $product_list[] = [
                            'where' => [
                                'order_product_id' => $product['order_product_id'],
                            ],
                            'data' => ['already_stages' => $product['already_stages'] +1],
                        ];
                    }

                }
            }
        }

        if (!empty($optData)) {
            // 累积到用户CFP记录
            foreach ($optData as $item)
            {
                $this->UserModel->giftcertAmountToken($item['mobile'], $item['amount'], $item['user_id'] , $item['remark']);
            }
//            if ($this->UserModel->onBatchIncGiftcertAmount($userData)) {
//                // 修改发放期数
                (new OrderProductModel())->updateAll($product_list);
//                // 插入发放日志
                (new LogModel())->saveAll($log_list);
//            }
        }

        return true;
    }

    /**
     * 累积用户实际消费金额
     */
    private function setIncUserExpend($orderList)
    {
        // 计算并累积实际消费金额(需减去售后退款的金额)
        $userData = [];
        foreach ($orderList as $order) {
            // 订单实际支付金额
            $expendMoney = $order['pay_price'];
            // 减去订单退款的金额
            foreach ($order['product'] as $product) {
                if (
                    !empty($product['refund'])
                    && $product['refund']['type']['value'] == 10      // 售后类型：退货退款
                    && $product['refund']['is_agree']['value'] == 10  // 商家审核：已同意
                ) {
                    $expendMoney -= $product['refund']['refund_money'];
                }
            }
            !isset($userData[$order['user_id']]) && $userData[$order['user_id']] = 0.00;
            $expendMoney > 0 && $userData[$order['user_id']] += $expendMoney;
        }
        // 累积到会员表记录
        $this->UserModel->onBatchIncExpendMoney($userData);
        return true;
    }

}