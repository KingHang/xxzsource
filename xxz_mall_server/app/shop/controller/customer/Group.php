<?php

namespace app\shop\controller\customer;

use app\common\model\goods\Goods;
use app\shop\controller\Controller;
use app\shop\model\customer\Group as GroupModel;
use app\shop\model\goods\Goods as ProductModel;

/**
 * 会员等级
 */
class Group extends Controller
{
    /**
     * 标签列表
     */
    public function index()
    {
        $model = new GroupModel;
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 添加标签
     */
    public function add()
    {
        $model = new GroupModel;
        // 新增记录
        if ($model->add($this->postData())) {
            return $this->renderSuccess('添加成功');
        }
        return $this->renderError($model->getError() ?: '添加失败');
    }

    /**
     * 编辑标签
     */
    public function edit($group_id)
    {
        $model = GroupModel::detail($group_id);
        $products = [];
        if ($model && $model['good_ids']) {
            $products = ProductModel::getSelectedList($model['good_ids']);

            foreach ($products as &$product) {
                // 商品主图
                $product['product_image'] = $product['image'][0]['file_path'];
            }
        }

        if($this->request->isGet()){
            return $this->renderSuccess('', compact('model', 'products'));
        }
        // 修改记录
        if ($model->edit($this->postData())) {
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }

    /**
     * 删除标签
     */
    public function delete($group_ids)
    {
        $model = new GroupModel();
        $result = $model->onBatchDelete($group_ids);

        if (!$result) {
            return $this->renderError('已存在客户，删除失败');
        }
        return $this->renderSuccess('删除成功');
    }

}