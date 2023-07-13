<?php

namespace app\common\service\product\factory;

use app\common\enum\order\OrderSourceEnum;

/**
 * 商品辅助工厂类
 */
class ProductFactory
{
    public static function getFactory($type = OrderSourceEnum::MASTER)
    {
        switch ($type) {
            case OrderSourceEnum::MASTER:
                return new MasterProductService();
                break;
            case OrderSourceEnum::POINTS;
                return new PointsProductService();
                break;
            case OrderSourceEnum::SECKILL:
                return new SeckillProductService();
                break;
            case OrderSourceEnum::BARGAIN:
                return new BargainProductService();
                break;
            case OrderSourceEnum::ASSEMBLE:
                return new AssembleProductService();
                break;
            case OrderSourceEnum::AGENT:
                return new MasterProductService();
                break;
            case OrderSourceEnum::SERVER:
                return new ServerProductService();
                break;
            case OrderSourceEnum::CARD:
                return new CardProductService();
                break;
            case OrderSourceEnum::BENEFIT:
                return new BenefitProductService();
                break;
        }
    }
}