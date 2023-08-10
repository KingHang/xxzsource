<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\common\enum\order\OrderSourceEnum;
use app\common\model\setting\Setting as SettingModel;
use app\api\model\plugin\groupsell\BillUser as BillUserModel;
/**
 * 拼团商城订单结算服务类
 */
class AssemblelOrderSettledService extends OrderSettledService
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
            'source' => OrderSourceEnum::ASSEMBLE,
            'activity_id' => $supplierData[0]['productList'][0]['activity_id']
        ];
        $this->config = SettingModel::getItem('assemble');
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
        foreach ($this->supplierData[0]['productList'] as $product) {
            // 判断商品是否下架
            if ($product['product_status']['value'] != 10) {
                $this->error = "很抱歉，拼团商品已下架";
                return false;
            }
            // 判断商品库存
            if ($product['total_num'] > $product['assemble_sku']['assemble_stock']) {
                $this->error = "很抱歉，拼团商品库存不足";
                return false;
            }
            // 参与过就不要再参加了
            if($product['bill_source_id'] > 0){
                $join_count = (new BillUserModel)->where('groupsell_bill_id', '=', $product['bill_source_id'])
                    ->where('user_id', '=', $this->user['user_id'])
                    ->count();
                if($join_count > 0){
                    $this->error = "您已经参与此商品拼团，请勿重复参与";
                    return false;
                }
            }
            // 是否超过购买数
            $hasNum = OrderModel::getPlusOrderNum($this->user['user_id'], $product['product_source_id'],OrderSourceEnum::ASSEMBLE);
            if($product['assemble_product']['limit_num'] > 0 && ($hasNum + $product['total_num'] > $product['assemble_product']['limit_num'])){
                $this->error = "很抱歉，你已购买或超过此商品最大拼团数量";
                return false;
            }
        }
        return true;
    }
}