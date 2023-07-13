<?php

namespace app\shop\controller;

use app\shop\service\ShopService;
use app\common\model\admin\Trade as TradeModel;
use app\common\model\settings\Settings as SettingModel;

/**
 * 后台首页控制器
 */
class Index extends Controller
{
    /**
     * 后台首页
     */
    public function index()
    {
        $service = new ShopService;
        return $this->renderSuccess('', ['data' => $service->getHomeData()]);
    }

    /**
     * 登录数据
     */
    public function base()
    {
        $config = SettingModel::getSysConfig();
        $settings = [
            'shop_name' => $config['shop_name'],
            'shop_logo' => $config['shop_logo'],
            'shop_bg_img' => $config['shop_bg_img']
        ];
        return $this->renderSuccess('', compact('settings'));
    }

    /**
     * 所有行业
     */
    public function trade()
    {
        return $this->renderSuccess('', TradeModel::getTradeAll());
    }
}
