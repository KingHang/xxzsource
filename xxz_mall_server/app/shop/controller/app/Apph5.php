<?php

namespace app\shop\controller\app;

use app\shop\controller\Controller;
use app\shop\model\settings\Settings as SettingModel;
use app\common\enum\settings\SettingEnum;
/**
 * 支付宝支付设置
 */
class Apph5 extends Controller
{
    /**
     * 支付宝支付设置
     */
    public function pay()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        if ($model->edit(SettingEnum::H5ALIPAY, $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 获取支付宝支付
     */
    public function fetchData()
    {
        $data = SettingModel::getItem(SettingEnum::H5ALIPAY);
        return $this->renderSuccess('', compact('data'));
    }

}
