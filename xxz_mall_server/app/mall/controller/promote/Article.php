<?php
declare (strict_types = 1);

namespace app\mall\controller\promote;

use app\XxzController;
use think\Request;

class Article extends XxzController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $model = new \app\mall\model\promote\Article();
        return $this ->renderSuccess('数据获取成功',$model -> getList($this->postData()));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $model = new \app\common\model\promote\Article();
        if ($model -> serverAdd($this->postData())){
            return $this ->renderSuccess('添加成功');
        }else{
            return $this ->renderError($model->getError(),'添加失败');
        }

    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function update($article_id)
    {
        // 详情
        $model = \app\mall\model\promote\Article::detail($article_id);
//        dump($model);die;
        // 更新记录
        if ($model->edit($this->postData())) {
            return $this->renderSuccess('更新成功');
        }
        return $this->renderError($model->getError() ?: '更新失败');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($article_id)
    {
        return $this->renderSuccess('success',\app\mall\model\promote\Article::detail($article_id));
    }

    public function handle($article_id,$param,$req)
    {
        $model = new \app\common\model\promote\Article();
        if ($model->recognitionHandle($article_id,$param,$req)){
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失败',$model->getError());
    }
}
