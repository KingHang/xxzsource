<?php

namespace app\mall\controller\setting;

use app\mall\controller\Controller;
use app\mall\model\setting\Setting as SettingModel;

/**
 * 交易设置控制器
 */
class Trade extends Controller
{
    /**
     * 交易设置
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        $data = $this->request->param();

        $arr = [
            'order' => [
                'close_days' => $data['close_days'],
                'receive_days' => $data['receive_days'],
                'refund_days' => $data['refund_days']
            ],
            'freight_rule' => $data['freight_rule'],
        ];
        if ($model->edit('trade', $arr)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 获取交易设置
     */
    public function fetchData()
    {
        $vars['values'] = SettingModel::getItem('trade');
        return $this->renderSuccess('', compact('vars'));
    }

}
