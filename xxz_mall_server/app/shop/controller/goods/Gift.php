<?php
declare (strict_types = 1);

namespace app\shop\controller\goods;

use app\common\model\goods\GoodsGift;
use app\XxzController;
use app\shop\controller\Controller;
use think\Request;

class Gift extends XxzController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = $this->postData();
        $list = (new GoodsGift())->getList($params);
        $exclude_ids =[];
        foreach ($list as $k =>$v)
        {
            array_push($exclude_ids,$v['product_id']);
        }
        return $this->renderSuccess('',compact('list','exclude_ids'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $params = $this->postData();
        $model =(new GoodsGift());
        if ($model->add($params)) {
            return $this->renderSuccess('添加成功');
        }else{
            return $this->renderError($model->getError(), '添加失败');
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
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
    public function edit($product_gift_id)
    {
        return $this->renderSuccess('success',(new GoodsGift())->detail($product_gift_id));
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
    {
        $params = $this->postData();
        $model =(new GoodsGift());
        if ($model->upd($params)) {
            return $this->renderSuccess('修改成功');
        }else{
            return $this->renderError($model->getError(), '修改失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
        $product_ids = $this->postData('product_ids');
        if ((new GoodsGift())->del($product_ids)) {
            return $this->renderSuccess('删除成功');
        }else{
            return $this->renderError('删除失败');
        }
    }
    public function open()
    {
        $data = $this->postData();
        if ((new GoodsGift())->open($data)) {
            return $this->renderSuccess('操作成功');
        }else{
            return $this->renderError('操作失败');
        }
    }
}
