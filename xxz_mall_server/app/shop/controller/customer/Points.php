<?php

namespace app\shop\controller\customer;

use app\shop\controller\Controller;
use app\shop\model\settings\Settings as SettingModel;
use app\shop\model\user\PointsLog as PointsLogModel;

/**
 * 积分控制器
 */
class Points extends Controller
{
    /**
     * 积分设置
     */
    public function setting()
    {
        if ($this->request->isGet()) {
            $values = SettingModel::getItem('exchangepurch');
            return $this->renderSuccess('', compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit('exchangepurch', $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 积分明细
     */
    public function log()
    {
        // 积分明细列表
        $model = new PointsLogModel;
        $list = $model->getList($this->request->param());
        return $this->renderSuccess('', compact('list'));
    }
}