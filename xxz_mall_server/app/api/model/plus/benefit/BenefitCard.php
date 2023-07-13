<?php

namespace app\api\model\plus\benefit;

use app\common\model\plugin\benefit\BenefitCard as BenefitCardModel;
use app\common\library\helper;
use app\common\model\purveyor\Purveyor as SupplierModel;

/**
 * 权益卡模型
 * @package app\common\model\plugin\pricedown
 */
class BenefitCard extends BenefitCardModel
{
    public function getListByIds($cardIds)
    {
        $model = $this;
        if (!empty($cardIds)) {
            $model = $model->orderRaw('field(card_id, ' . implode(',', $cardIds) . ')');
        }
        $where = array(
            'is_delete' => 0,
            'status'   => 1,
            'app_id'    => self::$app_id,
            'card_id' => $cardIds,
        );
        // 获取商品列表数据
        return $model->where($where)->with(['file','Relevance.benefit.file'])
            ->select();
    }
    /**
     * 获取首页权益卡数据
     * @param $params
     */
    public function getHomeCardList($params)
    {
        $model = $this;
        $sort = [];
        if ($params['sortType'] === 'all') {
            $sort = ['card_id' => 'desc'];
        } else if ($params['sortType'] === 'sales') {
            $sort = ['sales' => 'desc'];
        } else if ($params['sortType'] === 'price') {
            $sort = ['retail_price' => 'desc'];
        }
        $where = array(
            'is_delete' => 0,
            'status'    => 1,
            'app_id'    => self::$app_id,
        );
        return $model->where($where)
            ->with(['file','Relevance.benefit.file'])
            ->order($sort)
            ->limit($params['list_rows'])
            ->select();
    }
    /**
     * 获取服务列表
     */
    public function getCardListById($card_list)
    {
        // 商品列表
        $cardList = [];
        if (empty($card_list)) {
            return $cardList;
        }
        $card_ids = array_unique(helper::getArrayColumn($card_list, 'card_id'));
        // 获取并格式化商品数据
        $sourceData = $this->getListByIds($card_ids);
        $sourceData = helper::arrayColumn2Key($sourceData, 'card_id');
        // 供应商信息
        $supplierData = [];
        // 格式化购物车数据列表
        foreach ($card_list as $key => $item) {
            // 判断商品不存在则自动删除
            if (!isset($sourceData[$item['card_id']])) {
                unset($card_list[$key]);
                continue;
            }
            // 商品信息
            $card = clone $sourceData[$item['card_id']];

            // 购买数量
            $card['total_num'] = $item['total_num'];
            // 商品总价
            $card['total_price'] = bcmul($card['retail_price'], $item['total_num'], 2);
            // 供应商
            $card['shop_supplier_id'] = $card['shop_supplier_id'];
            $cardList[] = $card;
        }
        $supplierIds = array_unique(helper::getArrayColumn($cardList, 'shop_supplier_id'));
        foreach ($supplierIds as $supplierId) {
            $supplierData[] = [
                'shop_supplier_id' => $supplierId,
                'supplier' => SupplierModel::detail($supplierId),
                'cardList' => $this->getServerBySupplier($supplierId, $cardList)
            ];
        }
        return $supplierData;
    }
    /**
     * 按供应商归属服务
     * @param $supplierId
     * @param $productList
     * @return array
     */
    private function getServerBySupplier($supplierId, $productList){
        $result = [];
        foreach ($productList as $product){
            if($product['shop_supplier_id'] == $supplierId){
                array_push($result, $product);
            }
        }
        return $result;
    }
}