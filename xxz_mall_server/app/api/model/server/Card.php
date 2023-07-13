<?php

namespace app\api\model\server;

use app\common\model\facerecognition\Carditem as CarditemModel;
use app\common\model\facerecognition\CarditemMid as CarditemMidModel;
use app\common\library\helper;
use app\common\model\purveyor\Purveyor as SupplierModel;
use app\api\model\store\Store;

/**
 * 服务模型
 * Class Server
 * @package app\faceRecognition\model\server
 */
class Card extends CarditemModel
{
    /**
     * 获取商品列表
     */
    public function getList($param, $userInfo = false)
    {
//        $param['store_id'] = 10001;
        if (!isset($param['store_id']) || empty($param['store_id'])) {
            $this->error = '请选择门店';
            return false;
        }
        return $this->getCardList($param);
    }
    /**
     * 获取商品列表
     */
    public function getCardList($params)
    {
        $model = $this;
        $where = array(
            'Carditem.is_delete' => 0,
            'Carditem.on_shelf'   => 1,
            'Carditem.app_id'    => self::$app_id,
            'Carditem.status'    => 10,
        );
        if (!empty($params['search'])) {
            $model->where('Carditem.title', 'like',   "%" . strtolower($params['search']) . "%");
        }
        // 处理卡项门店显示中间表
        $bindsql = (new CarditemMidModel())->alias('CarditemMid')->field('CarditemMid.carditem_id')
            ->where(array('CarditemMid.store_id' => $params['store_id'] , 'CarditemMid.mid_is_disabled' => 1 ))->fetchSql()->count();

        $model = $model->alias('Carditem')->where($where)->whereRaw('(Carditem.is_on_shelf_store = 1 or (Carditem.is_on_shelf_store = 2 AND (' . $bindsql . ' AND Carditem.id = CarditemMid.carditem_id) > 0))');
        $list = $model->with(['CarditemServer' => function($query) {
                    $query->with(['serverMid' => function($query) {
                        $where = array(
                            'is_delete' => 0,
                            'is_disabled'   => 1,
                            'status'    => 10,
                        );
                        $query->where($where);
                        $query->field('server_id,server_name,server_price,server_desc,server_min');
                        $query->with(['serverimage.file']);
                    }]);
                    $query->field('server_id,card_id,disposable');
                },
            ])
            ->field('Carditem.id,Carditem.id as card_id,Carditem.title,Carditem.retail_price,Carditem.background_image,valid_period')
            ->order(['sort', 'id' => 'desc'])
            ->paginate($params);
        return $this->getCardData($list);
    }
    /**
     * 处理卡项返回参数格式
     * @param $data
     * @param bool $is_list
     * @param array $params
     * @return array|mixed
     */
    public function getCardData($data,$is_list = true,$params = []){
        if (empty($data)) {
            return array();
        }
        $data = !$is_list ? [$data] : $data;

        foreach ($data as &$detail) {
            // 处理服务
            $server = array();
            if (!empty($detail['CarditemServer'])) {
                foreach ($detail['CarditemServer'] as &$item){
                    if (isset($item['serverMid'][0]) && !empty($item['serverMid'][0])) {
                        $serverItem = $item['serverMid'][0];
                        $serverItem['disposable'] = $item['disposable'];
                        $serverItem['total_price'] = $serverItem['server_price'] *$item['disposable'];
                        $serverItem['total_num'] = $item['disposable'] ;
                        $serverItem['file_path'] = '';
                        $serverItem['image_id'] = 0;
                        if (isset($serverItem['serverimage'][0]['file_path'])) {
                            $serverItem['file_path'] = $serverItem['serverimage'][0]['file_path'];
                            $serverItem['image_id'] = $serverItem['serverimage'][0]['image_id'];
                        }
                        unset($serverItem['serverimage']);
                        $server[] = $serverItem->toArray();
                    }
                }
            }
            unset($detail['CarditemServer']);
            $detail['cardServer'] = $server;
            // 处理门店列表
            $store = array();
            if (!empty($detail['CarditemMid'])) {
                foreach ($detail['CarditemMid'] as &$item){
                    if (isset($item['storeList'][0]) && !empty($item['storeList'][0])) {
                        $store[] = $item['storeList'][0];
                    }
                }
            }
            unset($detail['CarditemMid']);
            $detail['store_list'] = $store;
        }

        return !$is_list ? $data[0] : $data;
    }

    /**
     * 合并重复购买的卡项
     * @param $card_list
     * @return array
     */
    public function mergeCardList($card_list)
    {
        $list = [];
        $card_list_arr = [];
        if (!empty($card_list)) {
            foreach ($card_list as $item) {
                if (isset($card_list_arr[$item['card_id']])) {
                    $card_list_arr[$item['card_id']]['total_num'] += $item['total_num'];
                } else {
                    $card_list_arr[$item['card_id']] = $item;
                }
            }
        }
        return $card_list_arr;
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
        $card_list = $this->mergeCardList($card_list);
        // 购物车中所有商品id集
        $card_ids = array_unique(helper::getArrayColumn($card_list, 'card_id'));
//        $card_ids = array(1);
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
            // 获取支持门店集合
            $store_ids = [];
            if ($card['is_on_shelf_store'] == 1) {
                // 全部门店
                $store_ids = (new Store())->getStoreIds(true);
            } elseif ($card['is_on_shelf_store'] == 2) {
                // 指定门店
                $store_ids = (new CarditemMid())->getStoreIds($card['id']);
            }
            $card['store_ids'] = !empty($store_ids) ? implode(',',$store_ids) : '';
            // 购买数量
            $card['total_num'] = $item['total_num'];
            // 商品总价
            $card['total_price'] = bcmul($card['retail_price'], $item['total_num'], 2);
            // 供应商
            $card['shop_supplier_id'] = $card['shop_supplier_id'];
            $card['supplier_price'] = bcmul($card['supplier_price'], $item['total_num'], 2);
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
    /**
     * 根据商品id集获取商品列表
     */
    public function getListByIds($cardIds)
    {
        $model = $this;
        if (!empty($cardIds)) {
            $model = $model->orderRaw('field(id, ' . implode(',', $cardIds) . ')');
        }
        $where = array(
            'is_delete' => 0,
            'on_shelf'   => 1,
            'app_id'    => self::$app_id,
            'status'    => 10,
            'id' => $cardIds,
        );
        // 获取商品列表数据
        $data = $model->where($where)->with(['CarditemServer' => function($query) {
                $query->with(['serverMid' => function($query) {
                    $where = array(
                        'is_delete' => 0,
                        'is_disabled'   => 1,
                        'status'    => 10,
                    );
                    $query->where($where);
                    $query->field('server_id,server_name,server_price,server_desc,server_min,spec_type,server_no,line_price,server_min');
                    $query->with(['serverimage.file']);
                }]);
                $query->field('server_id,card_id,disposable');
            },
            ])
            ->select();
        $data = $this->getCardData($data);
        foreach ($data as &$item) {
            $item['card_id'] = $item['id'];
            $item['server_num'] = count($item['cardServer']);
        }
        return $data;
    }
}