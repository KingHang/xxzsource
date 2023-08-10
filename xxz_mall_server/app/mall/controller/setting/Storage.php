<?php

namespace app\mall\controller\setting;

use app\mall\controller\Controller;
use app\mall\model\setting\Setting as SettingModel;

/**
 * 存储控制器
 */
class Storage extends Controller
{
    /**
     * 存储设置
     */
    public function index()
    {
        if($this->request->isGet()){
            return $this->fetchData();
        }
        $model = new SettingModel;
        $data = $this->postData();
        $arr['default'] = $data['radio'];
        $arr['engine'] = $data['engine'];
        if ($model->edit('storage', $arr)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 获取存储配置
     */
    public function fetchData()
    {
        $key = 'storage';
        $vars['values'] = SettingModel::getItem($key);
        return $this->renderSuccess('', compact('vars'));
    }

}
