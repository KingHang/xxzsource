<?php

namespace app\shop\controller\settings;

use app\shop\controller\Controller;
use app\shop\model\settings\Settings as SettingModel;
use app\shop\model\settings\Printer as PrinterModel;

/**
 * 打印设置
 */
class Printing extends Controller
{
    /**
     * 打印设置
     */
    public function index()
    {
        if ($this->request->isGet()) {
            return $this->fetchData();
        }
        $model = new SettingModel;
        if ($model->edit('printer', $this->postData(), 10001)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 获取打印配置
     */
    public function fetchData()
    {
        // 获取打印机列表
        $vars['printerList'] = PrinterModel::getAll(10001)->toArray();
        $vars['values'] = SettingModel::getSupplierItem('printer', 10001);
        $vars['values']['is_open'] = intval($vars['values']['is_open']);
        return $this->renderSuccess('', compact('vars'));
    }


}
