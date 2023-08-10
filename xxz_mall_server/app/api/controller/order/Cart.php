<?php

namespace app\api\controller\order;

use app\api\controller\Controller;
use app\api\model\order\Cart as CartModel;
use app\api\model\setting\Setting as SettingModel;

/**
 * 购物车控制器
 */
class Cart extends Controller
{
    private $user;

    // $model
    private $model;

    /**
     * 构造方法
     */
    public function initialize()
    {
        $this->user = $this->getUser();
        $this->model = new CartModel($this->user);
    }


    /**
     * 购物车列表
     */
    public function lists()
    {
        // 请求参数
        $param = $this->request->param();
        $cartIds = isset($param['cart_ids']) ? $param['cart_ids'] : '';
        // 购物车商品列表
        $productList = $this->model->getList($cartIds, 1);
        // 购物车商品总数量
        $cartcount = $this->model->getProductNum();
        //是否显示店铺信息
        $store_open=SettingModel::getStoreOpen();
        return $this->renderSuccess('', compact('productList', 'cartcount', 'store_open'));
    }

    /**
     * 加入购物车
     * @param int $product_id 商品id
     * @param int $product_num 商品数量
     * @param string $product_sku_id 商品sku索引
     */
    public function add()
    {
        $data = $this->request->param();
        $product_id = $data['product_id'];
        $product_num = $data['total_num'];
        $product_sku_id = $data['product_sku_id'];
        $model = $this->model;
        if (!$model->add($product_id, $product_num, $product_sku_id)) {
            return $this->renderError($model->getError() ?:'加入购物车失败');
        }
        // 购物车商品总数量
        $totalNum = $model->getProductNum();
        return $this->renderSuccess('加入购物车成功', ['cart_total_num' => $totalNum]);
    }

    /**
     * 减少购物车商品数量
     * @param $product_id
     * @param $product_sku_id
     * @return array
     */
    public function sub($product_id, $product_sku_id)
    {
        $this->model->sub($product_id, $product_sku_id);
        return $this->renderSuccess('');
    }

    /**
     * 删除购物车中指定商品
     * @param $product_sku_id (支持字符串ID集)
     * @return array
     */
    public function delete($product_sku_id)
    {
        $this->model->delete($product_sku_id);
        return $this->renderSuccess('删除成功');
    }
}