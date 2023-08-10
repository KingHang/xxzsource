<?php

namespace app\mall\controller\data;

use app\mall\controller\Controller;
use app\mall\model\user\Grade;
use app\mall\model\user\User as UserModel;

/**
 * 用户数据控制器
 */
class User extends Controller
{
    /**
     * 商品列表
     */
    public function lists()
    {
        $model = new UserModel();
        $params = $this->postData();
        $list = $model->getList($params['nickName'], $params['grade_id'], '', $params['sex'], $params);
        $GradeModel = new Grade();
        $grade = $GradeModel->getLists();
        return $this->renderSuccess('', compact('list', 'grade'));
    }

}
