<?php

namespace app\shop\controller\app;

use app\shop\controller\Controller;
use app\shop\model\app\AppByte as AppByteModel;

/**
 * 字节小程序设置
 */
class Appbyte extends Controller
{
    /**
     * 修改
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new AppByteModel;
        $data = $this->postData();
        unset($data['data']);
        if ($model->edit($data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 获取字节小程序设置
     */
    public function fetchData()
    {
        // 当前微信小程序信息
        $data = AppByteModel::detail();
        return $this->renderSuccess('', compact('data'));
    }

}
