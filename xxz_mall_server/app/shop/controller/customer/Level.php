<?php

namespace app\shop\controller\customer;

use app\shop\controller\Controller;
use app\shop\model\customer\Level as LevelModel;

/**
 * 会员等级
 */
class Level extends Controller
{
    /**
     * 会员等级列表
     */
    public function index()
    {
        $model = new LevelModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 添加等级
     */
    public function add()
    {
        $model = new LevelModel;
        // 新增记录
        if ($model->add($this->postData())) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError('添加失败');
    }

    /**
     * 编辑会员等级
     */
    public function edit($level_id)
    {
        $model = LevelModel::detail($level_id);
        if($this->request->isGet()){
            return $this->renderSuccess('', compact('model'));
        }
        // 修改记录
        if ($model->edit($this->postData())) {
            return $this->renderSuccess();
        }
        return $this->renderError();
    }

    /**
     * 删除会员等级
     */
    public function delete($level_id)
    {
        // 会员等级详情
        $model = LevelModel::detail($level_id);
        if (!$model->setDelete()) {
            return $this->renderError('已存在客户，删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}