<?php
declare (strict_types = 1);

namespace app\mall\controller\promote;

use app\XxzController;
use think\facade\Request;
use think\Response;

class ArticleCategory extends XxzController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $model = new \app\mall\model\promote\ArticleCategory();
        $list = $model->typeList();
        return $this->renderSuccess('数据获取成功', $list);
    }

    /**
     * 显示创建资源表单页.
     *
     */
    public function create()
    {
        $params = Request::post();
        try {
            $model =  new \app\mall\model\promote\ArticleCategory();
            $list = $model->addType($params);
            return $this->renderSuccess('添加成功', $list);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            dump($e->getError());
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return Response
     */
    public function edit($category_id)
    {
        $model = new \app\mall\model\promote\ArticleCategory();
        $list = $model->typeDesc();
        return $this->renderSuccess('数据获取成功', $list);
    }

    /**
     * 保存更新的资源
     *

     */
    public function update()
    {
        $model = new \app\mall\model\promote\ArticleCategory();
        $params = $this->request->post();
        $list = $model->typeUpd($params);
        if ($list) {
            return $this->renderSuccess('更新成功', $list);
        } else {
            return $this->renderError('更新失败', $list);
        }
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return Response
     */
    public function delete($category_id, $is_delete = '')
    {
        $model = new \app\mall\model\promote\ArticleCategory();
        $list = $model->typeDelete($category_id, $is_delete);
        if ($list) {
            return $this->renderSuccess('操作成功', $list);
        } else {
            return $this->renderError('操作失败', $list);
        }
    }

    public function typelist()
    {
        $organ = new \app\mall\model\promote\ArticleCategory();
        $where = [];
        $where[] = ["is_delete", '=', "0"];
        $data = $organ::field()->where($where)->select()->toArray();
        return $this->renderSuccess('', $data);
    }
}
