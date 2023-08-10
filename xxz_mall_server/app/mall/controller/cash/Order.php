<?php

namespace app\mall\controller\cash;

use app\mall\controller\Controller;
use app\mall\model\order\OrderSettled as OrderSettledModel;
use app\mall\model\order\Order as OrderModel;
/**
 * 提现
 */
class Order extends Controller
{
    /**
     * 订单列表
     */
    public function index()
    {
        $model = new OrderSettledModel;
        //列表数据
        $list = $model->getList($this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 详情
     */
    public function detail($settled_id){
        $model = OrderSettledModel::detail($settled_id);
        $order = OrderModel::detail($model['order_id']);
        return $this->renderSuccess('', compact('model', 'order'));
    }
}
