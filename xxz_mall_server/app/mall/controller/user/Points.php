<?php

namespace app\mall\controller\user;

use app\mall\controller\Controller;
use app\mall\model\setting\Setting as SettingModel;
use app\mall\model\user\PointsLog as PointsLogModel;
use app\mall\model\user\GrowthLog as GrowthLogModel;

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

    /**
     * 成长值设置
     */
    public function grow()
    {
        if ($this->request->isGet()) {
            $values = SettingModel::getItem('grow');
            return $this->renderSuccess('', compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit('grow', $this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 成长值明细
     */
    public function growLog()
    {
        // 成长值明细列表
        $model = new GrowthLogModel;
        $list = $model->getList($this->request->param());
        return $this->renderSuccess('', compact('list'));
    }
}
