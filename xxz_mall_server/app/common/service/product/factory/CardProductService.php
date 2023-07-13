<?php

namespace app\common\service\product\factory;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\server\ServerSpecSku as serverSpecSkuModel;
use app\common\model\facerecognition\Carditem as carditemModel;

/**
 * 商品来源-服务商品扩展类
 */
class CardProductService extends carditemModel
{
    /**
     * 更新商品库存 (针对下单减库存的商品)
     */
    public function updateProductStock($card_list)
    {
        return true;
    }

    /**
     * 更新商品库存销量（订单付款后）
     */
    public function updateStockSales($card_list)
    {
        $cardData = [];
        foreach ($card_list as $card) {
            // 记录商品的销量
            $server_data = [
                'data' => ['sales' => ['inc', $card['total_num']]],
                'where' => [
                    'id' => $card['card_id']
                ],
            ];
            $cardData[] = $server_data;
        }
        // 更新商品销量
        !empty($cardData) && $this->updateCard($cardData);

        return true;
    }

    /**
     * 回退商品库存
     */
    public function backProductStock($serverList, $isPayOrder = false)
    {
        return true;
    }

    /**
     * 更新商品信息
     */
    private function updateCard($data)
    {
        return (new CarditemModel)->updateAll($data);
    }
}