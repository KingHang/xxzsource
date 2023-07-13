<?php

namespace app\api\controller\store;

use app\api\controller\Controller;
use app\api\model\store\Store as StoreModel;
use app\common\library\easywechat\AppMp;


/**
 * 门店列表
 */
class Store extends Controller
{
    /**
     * 门店列表
     */
    public function lists($longitude = '', $latitude = '', $url = '', $shop_supplier_id = 0,$keyword='')
    {
        $model = new StoreModel;
        $list = $model->getList(true, $longitude, $latitude, false, $shop_supplier_id,$keyword);
        $signPackage = '';
        if($url != ''){
            $app = AppMp::getApp($this->app_id);
            $app->jssdk->setUrl($url);
            $signPackage = $app->jssdk->buildConfig(array('getLocation', 'openLocation'), false);
        }
        return $this->renderSuccess('', compact('list', 'signPackage'));
    }

    //可选门店列表
    public function list()
    {
        $param = $this->postData();
        $model = new StoreModel;
        $list = $model->getStoreList('',0,$param);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 门店详情
     */
    public function detail($store_id)
    {
        $detail = StoreModel::detail($store_id);
        return $this->renderSuccess('', compact('detail'));
    }

}