<?php

namespace app\shop\service\statistics;

use app\shop\model\purveyor\Purveyor as SupplierModel;
use app\shop\model\purveyor\Apply as SupplierApplyModel;
use app\shop\model\purveyor\DepositRefund as DepositRefundModel;
/**
 * 供应商数据概况
 */
class SupplierService
{
    /**
     * 获取数据概况
     */
    public function getData()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $data = [
            // 累积供应商数
            'supplier_total' => [
                'today' => number_format($this->getSupplierData(null, $today, 'supplier_total')),
                'yesterday' => number_format($this->getSupplierData(null, $yesterday, 'supplier_total'))
            ],
            // 新增供应商数
            'supplier_add' => [
                'today' => number_format($this->getSupplierData($today, null, 'supplier_add')),
                'yesterday' => number_format($this->getSupplierData($yesterday, null, 'user_add'))
            ],
            // 新申请供应商数
            'supplier_apply' => [
                'today' => number_format($this->getSupplierApplyData($today, null, 'apply_total')),
                'yesterday' => number_format($this->getSupplierApplyData($yesterday, null, 'apply_total'))
            ],
            // 供应商退出数
            'supplier_refund' => [
                'today' => number_format($this->getDepositRefundData($today, null, 'supplier_refund')),
                'yesterday' => number_format($this->getDepositRefundData($yesterday, null, 'supplier_refund'))
            ],
            // 供应商退出申请数
            'supplier_refund_apply' => [
                'today' => number_format($this->getDepositRefundData($today, null, 'supplier_refund_apply')),
                'yesterday' => number_format($this->getDepositRefundData($yesterday, null, 'supplier_refund_apply'))
            ],
        ];
        return $data;
    }

    /**
     * 通过时间段查询用户数据
     */
    public function getDataByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = [
                'day' => $day,
                'new_num' => $this->getSupplierData($day, null, 'supplier_add'),
                'refund_num' => $this->getDepositRefundData($day, null, 'supplier_refund')
            ];
        }
        return $data;
    }
    /**
     * 获取用户数据
     */
    private function getSupplierData($startDate = null, $endDate = null, $type)
    {
        return (new SupplierModel)->getSupplierData($startDate, $endDate, $type);
    }

    private function getSupplierApplyData($startDate = null, $endDate = null, $type){
        return (new SupplierApplyModel)->getApplyData($startDate, $endDate, $type);
    }
    /**
     * 获取订单数据
     */
    private function getDepositRefundData($startDate = null, $endDate = null, $type)
    {
        return (new DepositRefundModel())->getRefundData($startDate, $endDate, $type);
    }
}