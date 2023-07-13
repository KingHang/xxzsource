<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\plus\bargain\Task;

/**
 * 个人砍价控制器
 */
class Bargain extends Controller
{
    // 当前用户
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUser();   // 用户信息

    }

    /**
     *个人砍价列表
     */
    public function lists()
    {
        $model = new Task();
        $list = $model->getList($this->user['user_id'], $this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}