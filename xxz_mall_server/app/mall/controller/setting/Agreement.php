<?php
declare (strict_types = 1);

namespace app\mall\controller\setting;

use app\XxzController;
use app\mall\controller\Controller;
use think\Request;

class Agreement extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $model = new \app\common\model\setting\Agreement();
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 新增记录
        $model = new \app\common\model\setting\Agreement();
        if ($model->add($this->postData())) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    public function detail($id)
    {
        $detail = \app\common\model\setting\Agreement::detail($id);
        return $this->renderSuccess('', compact('detail'));

    }

    public function edit($id)
    {
        $model = \app\common\model\setting\Agreement::detail($id);
        // 更新记录
        if ($model->upd($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 删除记录
     */
    public function delete($id)
    {
        $model = \app\common\model\setting\Agreement::detail($id);
        if ($model->remove()) {
            return $this->renderSuccess('删除成功');
        }
        return $this->renderError($model->getError() ?:'删除失败');
    }
}
