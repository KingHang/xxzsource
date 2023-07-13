<?php

namespace app\common\service\product\factory;

use app\common\enum\product\DeductStockTypeEnum;
use app\common\model\server\ServerSpecSku as serverSpecSkuModel;
use app\common\model\server\Server as ServerModel;

/**
 * 商品来源-服务商品扩展类
 */
class ServerProductService extends ServerModel
{
    /**
     * 更新商品库存 (针对下单减库存的商品)
     */
    public function updateProductStock($serverList)
    {
        return true;
    }

    /**
     * 更新商品库存销量（订单付款后）
     */
    public function updateStockSales($serverList)
    {
        $serverData = [];
        $serverSkuData = [];
        foreach ($serverList as $server) {
            // 记录商品的销量
            $server_data = [
                'data' => ['sales_actual' => ['inc', $server['total_num']]],
                'where' => [
                    'server_id' => $server['server_id']
                ],
            ];
            $serverData[] = $server_data;
        }
        // 更新商品销量
        !empty($serverData) && $this->updateServer($serverData);

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
    private function updateServer($data)
    {
        return (new ServerModel)->updateAll($data);
    }

    /**
     * 更新商品sku信息
     */
    private function updateServerSku($data)
    {
        return (new serverSpecSkuModel)->updateAll($data);
    }

}