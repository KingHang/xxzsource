<?php

namespace app\mall\controller\authority;

use app\mall\controller\Controller;
use app\mall\model\mall\LoginLog as LoginLogModel;
/**
 * 管理员登录日志
 */
class Loginlog extends Controller
{
    /**
     * 登录日志
     */
    public function index()
    {
        $model = new LoginLogModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}