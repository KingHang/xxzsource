<?php

namespace app\common\service\product\factory;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\plugin\benefit\BenefitCard;

/**
 * 商品来源-服务商品扩展类
 */
class BenefitProductService extends BenefitCard
{

    /**
     * 更新商品库存销量
     */
    public function updateStockSales($card_list)
    {
        $cardData = [];
        foreach ($card_list as $card) {
            // 记录商品的销量
            $server_data = [
                'data' => ['sales' => ['inc', $card['total_num']],'stock' => ['dec' , $card['total_num']]],
                'where' => [
                    'card_id' => $card['product_id']
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
    public function backProductStock($card_list)
    {
        $cardData = [];
        foreach ($card_list as $card) {
            // 记录商品的销量
            $server_data = [
                'data' => ['sales' => ['dec', $card['total_num']],'stock' => ['inc' , $card['total_num']]],
                'where' => [
                    'card_id' => $card['product_id']
                ],
            ];
            $cardData[] = $server_data;
        }
        // 更新商品销量
        !empty($cardData) && $this->updateCard($cardData);
        return true;
    }

    /**
     * 更新商品信息
     */
    private function updateCard($data)
    {
        return (new BenefitCard)->updateAll($data);
    }
}