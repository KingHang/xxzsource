<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\common\model\admin\TradeAccess;
use app\XxzController;
use think\Request;
use think\Response;
use think\response\Json;

class Trade extends XxzController
{
    /**
     * 显示资源列表
     *
     * @return Response
     */
    public function index()
    {
        return $this->renderSuccess('请求成功', \app\admin\model\Trade::getALL());
    }

    /**
     * 添加行业
     */
    public function add()
    {
        $model = new \app\admin\model\Trade();
        $res = $model->add($this->postData());
        // 新增记录
        if ($res['status'] == '1') {
            return $this->renderError($res['msg']);
        }
        return $this->renderSuccess('添加成功');
    }

    /**
     * 编辑行业
     * @param $trade_id
     * @return Json
     */
    public function edit($trade_id)
    {
        // 分类详情
        $model = \app\admin\model\Trade::detail(['trade_id' => $trade_id]);
        // get请求
        if ($this->request->isGet()) {
            return $this->renderSuccess('success',(new TradeAccess())->editInfo($trade_id));
        }
        $res = $model->edit($this->postData());
        // 更新记录
        if ($res['status'] == '1') {
            return $this->renderError($res['msg']);
        }
        return $this->renderSuccess('更新成功');
    }

    /**
     * 删除行业
     * @param $trade_id
     * @return Json
     */
    public function delete($trade_id)
    {
        $model = \app\admin\model\Trade::detail(['trade_id' => $trade_id]);

        if (!$model) {
            return $this->renderError('删除失败,行业不存在');
        }

        $res = $model->remove();

        if ($res['status'] == '1') {
            return $this->renderError($res['msg']);
        }

        return $this->renderSuccess('删除成功');
    }
}
