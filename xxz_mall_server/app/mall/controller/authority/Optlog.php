<?php

namespace app\mall\controller\authority;

use app\mall\controller\Controller;
use app\mall\model\mall\OptLog as OptLogModel;
/**
 * 管理员操作日志
 */
class Optlog extends Controller
{
    /**
     * 操作日志
     */
    public function index()
    {
        $model = new OptLogModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}