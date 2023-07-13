<?php

namespace app\api\controller;

use app\api\model\settings\Settings as SettingModel;
use app\common\model\app\AppOpen as AppOpenModel;
use app\common\model\settings\Region as RegionModel;
use app\XxzController;

/**
 * 页面控制器
 */
class Settings extends Controller
{

    // app分享
    public function appShare()
    {
        // 分享设置
        $appshare = SettingModel::getItem('appshare');
        // logo
        $logo = AppOpenModel::detail()['logo'];
        return $this->renderSuccess('', compact('appshare', 'logo'));
    }

    /**
     * 获取省市区
     */
    public function getRegion(){
        $regionData = RegionModel::getRegionForApi();
        return $this->renderSuccess('', compact('regionData'));
    }

    /**
     * 根据首字母获取城市
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCityList()
    {
        // 获取热门城市
        $hotCity = RegionModel::getHotCity();
        // 恒聚首字母分组获取城市
        $cityData = RegionModel::getCityList();
        return $this->renderSuccess('', compact('hotCity','cityData'));
    }

    public function getRegionIdByName($region_name)
    {
        return $this->renderSuccess('', RegionModel::getIdLikeName($region_name,2,0,'Like'));
    }
    public function agreementList()
    {
        $model = new \app\common\model\settings\Agreement();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}
