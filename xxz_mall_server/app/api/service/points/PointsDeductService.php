<?php

namespace app\api\service\points;

use app\common\library\helper;
use app\api\model\setting\Setting as SettingModel;

/**
 * 积分抵扣类
 */
class PointsDeductService
{
    private $productList;

    public function __construct($productList)
    {
        $this->productList = $productList;
    }

    public function setProductPoints($maxPointsNumCount, $actualPointsNum)
    {
        // 计算实际积分抵扣数量
        $this->setproductListPointsNum($maxPointsNumCount, $actualPointsNum);
        // 总抵扣数量
        $totalPointsNum = helper::getArrayColumnSum($this->productList, 'points_num');
        // 填充余数
        $this->setproductListPointsNumFill($actualPointsNum, $totalPointsNum);
        $this->setproductListPointsNumDiff($actualPointsNum, $totalPointsNum);
        // 计算实际积分抵扣金额
        $this->setproductListPointsMoney();
        return true;
    }

    /**
     * 计算实际积分抵扣数量
     */
    private function setproductListPointsNum($maxPointsNumCount, $actualPointsNum)
    {
        foreach ($this->productList as &$product) {
            if (!$product['is_points_discount']) continue;
            $product['points_num'] = floor($product['max_points_num'] / $maxPointsNumCount * $actualPointsNum);
        }
    }

    /**
     * 计算实际积分抵扣金额
     */
    private function setproductListPointsMoney()
    {
        $setting = SettingModel::getItem('exchangepurch');
        foreach ($this->productList as &$product) {
            if (!$product['is_points_discount']) continue;
            $product['points_money'] = helper::bcmul($product['points_num'], $setting['discount']['discount_ratio']);
        }
    }

    private function setproductListPointsNumFill($actualPointsNum, $totalPointsNum)
    {
        if ($totalPointsNum === 0) {
            $temReducedMoney = $actualPointsNum;
            foreach ($this->productList as &$product) {
                if (!$product['is_points_discount']) continue;
                if ($temReducedMoney === 0) break;
                $product['points_num'] = 1;
                $temReducedMoney--;
            }
        }
        return true;
    }

    private function setproductListPointsNumDiff($actualPointsNum, $totalPointsNum)
    {
        $tempDiff = $actualPointsNum - $totalPointsNum;
        foreach ($this->productList as &$product) {
            if (!$product['is_points_discount']) continue;
            if ($tempDiff < 1) break;
            $product['points_num'] = $product['points_num'] + 1;
            $tempDiff--;
        }
        return true;
    }

    /**
     * 计算通证抵扣
     * @param $maxDeductNumCount
     * @param $actualDeductNum
     * @return bool
     */
    public function setProductDeduct($maxDeductNumCount, $actualDeductNum)
    {
        // 计算实际通证抵扣数量
        $this->setProductListDeductNum($maxDeductNumCount, $actualDeductNum);
        // 计算实际通证抵扣金额
        $this->setProductListDeductMoney();
        // 矫正误差
        $this->setProductListDeductNumFill();
        return true;
    }

    /**
     * 计算实际通证抵扣数量
     * @param $maxDeductNumCount
     * @param $actualDeductNum
     */
    private function setProductListDeductNum($maxDeductNumCount, $actualDeductNum)
    {
        foreach ($this->productList as &$product) {
            if ($product['is_deduct']) continue;
            if ($product['deduct_type']) {
                if ($product['customize_deduct'] && !$product['deduct_discount_setting']) continue;
                if (!$product['customize_deduct'] && !$product['deduct_money_setting']) continue;
            }
            $product['deduct_num'] = sprintf('%.5f', helper::bcdiv($product['max_deduct_num'] * $actualDeductNum, $maxDeductNumCount, 6));
        }
    }

    /**
     * 计算实际通证抵扣金额
     */
    private function setProductListDeductMoney()
    {
        $setting = SettingModel::getItem('deduct');
        foreach ($this->productList as &$product) {
            if ($product['is_deduct']) continue;
            if ($product['deduct_type']) {
                if ($product['customize_deduct'] && !$product['deduct_discount_setting']) continue;
                if (!$product['customize_deduct'] && !$product['deduct_money_setting']) continue;
            }
            $product['deduct_money'] = floor(helper::bcmul($product['deduct_num'], $setting['deduct_money'], 3) * 100) / 100;
        }
    }

    /**
     * 矫正误差
     */
    private function setProductListDeductNumFill()
    {
        $setting = SettingModel::getItem('deduct');
        foreach ($this->productList as &$product) {
            if ($product['is_deduct']) continue;
            if ($product['deduct_type']) {
                if ($product['customize_deduct'] && !$product['deduct_discount_setting']) continue;
                if (!$product['customize_deduct'] && !$product['deduct_money_setting']) continue;
            }
            $product['deduct_num'] = helper::bcdiv($product['deduct_money'], $setting['deduct_money']);
        }
    }
}
