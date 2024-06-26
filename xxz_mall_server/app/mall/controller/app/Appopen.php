<?php

namespace app\mall\controller\app;

use app\mall\controller\Controller;
use app\mall\model\app\AppOpen as AppOpenModel;

/**
 * app开放平台设置
 */
class Appopen extends Controller
{
    /**
     * 修改
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new AppOpenModel;
        $data = $this->postData();
        unset($data['data']);
        if ($model->edit($data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 获取微信小程序设置
     */
    public function fetchData()
    {
        // 当前app信息
        $data = AppOpenModel::detail();
        return $this->renderSuccess('', compact('data'));
    }

}
