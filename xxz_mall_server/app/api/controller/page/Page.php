<?php

namespace app\api\controller\page;

use app\api\controller\Controller;
use app\api\model\home\FormInfo as FormInfoModel;

class Page extends Controller
{
    public function saveInfo()
    {
        $model = new FormInfoModel();
        $model->add($this->postData());

        // 接收post数据
        if (!$model->add($this->postData())) {
            return $this->renderError($model->getError() ?:'提交失败');
        }
        return $this->renderSuccess('提交成功');
    }
}