<?php

namespace app\shop\controller\customer;

use app\shop\controller\Controller;
use app\shop\model\customer\User as UserModel;
use app\shop\model\customer\Card as CardModel;
use app\shop\model\customer\Level;
use app\shop\model\order\Order;

/**
 * 用户管理
 */
class User extends Controller
{
    /**
     * 商户列表
     */
    public function index()
    {
        $list = UserModel::getList($this->postData());
        $LevelModel = new Level();
        $level = $LevelModel->getLists();
        return $this->renderSuccess('', compact('list', 'level'));
    }


    /**
     * 删除用户
     */
    public function delete($customer_ids)
    {
        $model = new UserModel();
        $result = $model->onBatchDelete($customer_ids);
        if ($result) {
            return $this->renderSuccess('删除成功');
        }
        return $this->renderError($model->getError() ?: '删除失败');
    }

    public function setBlack($customer_ids, $type)
    {
        $model = new UserModel();
        $result = $model->onBatchSetBlack($customer_ids, $type);
        // 修改记录
        if ($result) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }


    /**
     * 添加客户
     */
    public function add()
    {
        $model = new UserModel;
        // 新增记录
        if ($model->add($this->request->param())) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 用户充值
     */
    public function recharge($customer_id, $source)
    {
        // 用户详情
        $model = UserModel::detail($customer_id);

        if ($model->recharge($this->store['user']['user_name'], $source, $this->postData('params'))) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 等级改用户
     */
    public function edit($customer_id)
    {
        // 用户详情
        $model = UserModel::detail($customer_id);
        if ($this->request->isGet()) {
            return $this->renderSuccess('', compact('model'));
        }
        // 修改记录
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }

    public function editLevel($customer_ids, $level_id)
    {
        $model = new UserModel();
        $result = $model->onBatchUpdateLevel($customer_ids, $level_id);
        // 修改记录
        if ($result) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }

    public function editGroup($customer_ids, $group_id)
    {
        $model = new UserModel();
        $result = $model->onBatchUpdateGroup($customer_ids, $group_id);
        // 修改记录
        if ($result) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }

    /**
     * 客户导出
     */
    public function export($dataType)
    {
        $model = new UserModel();
        return $model->exportList($dataType, $this->postData());
    }

    public function getCardList()
    {
        $model = new CardModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }




    public function saveCustomerinfo(){
        $model = new UserModel();
        $result = $model->savaInfo();
        if ($result) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }
}
