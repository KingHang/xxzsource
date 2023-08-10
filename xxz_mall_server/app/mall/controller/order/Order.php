<?php

namespace app\mall\controller\order;

use app\XxzController;
use app\mall\controller\Controller;
use app\mall\model\order\Order as OrderModel;
use app\mall\model\order\OrderGoods as OrderProductModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\mall\model\setting\Express as ExpressModel;
use app\mall\model\store\Clerk as ShopClerkModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

/**
 * 订单控制器
 */
class Order extends Controller
{
    /**
     * 订单列表
     * @param string $dataType
     * @return Json
     */
    public function index($dataType = 'all')
    {
        // 订单列表
        $model = new OrderModel();
        $list = $model->getList($dataType, $this->postData());
        $order_count = [
            'order_count' => [
                'payment' => $model->getCount('payment'),
                'delivery' => $model->getCount('delivery'),
                'received' => $model->getCount('received'),
                'cancel' => $model->getCount('cancel'),
            ]
        ];
        $orderStatistics['orderNum'] = $model->getOrderStatistics($dataType, $this->postData());
        $orderStatistics['totalOrderAmount'] = $model->getOrderStatistics($dataType, $this->postData(), 1);
        $ex_style = DeliveryTypeEnum::data();
        return $this->renderSuccess('', compact('list', 'ex_style', 'order_count', 'orderStatistics'));
    }

    /**
     * 订单详情
     * @param $order_id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function detail($order_id)
    {
        // 订单详情
        $detail = OrderModel::detail($order_id);
        if (isset($detail['pay_time']) && $detail['pay_time'] != '') {
            $detail['pay_time'] = date('Y-m-d H:i:s', $detail['pay_time']);
        }
        if (isset($detail['delivery_time']) && $detail['delivery_time'] != '') {
            $detail['delivery_time'] = date('Y-m-d H:i:s', $detail['delivery_time']);
        }
        $detail['buy_remark'] = json_decode($detail['buy_remark'],1);
        // 物流公司列表
        $model = new ExpressModel();
        $expressList = $model->getAll();
        // 门店店员列表
        $shopClerkList = (new ShopClerkModel)->getAll(true);
        return $this->renderSuccess('', compact('detail', 'expressList', 'shopClerkList'));
    }

    /**
     * 确认发货
     * @param $order_id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function delivery($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->delivery($this->postData('param'))) {
            return $this->renderSuccess('发货成功');
        }
        return $this->renderError('发货失败');
    }

    /**
     * 修改订单价格
     * @param $order_id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function updatePrice($order_id)
    {
        $model = OrderModel::detail($order_id);
        if ($model->updatePrice($this->postData('order'))) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError() ?: '修改失败');
    }

    /**
     * 获取物流信息
     * @param $order_id
     * @param int $product_id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function express($order_id, $product_id = 0)
    {
        if ($product_id) {
            // 订单商品信息
            $order = (new OrderProductModel())->getUserOrderProductDetail($order_id, $product_id);
        } else {
            // 订单信息
            $order = OrderModel::detail($order_id);
        }
        if (!$order['express_no']) {
            return $this->renderError('没有物流信息');
        }
        // 获取物流信息
        $model = $order['express'];
        $express = $model->dynamic($model['express_name'], $model['express_code'], $order['express_no']);
        if ($express === false) {
            return $this->renderError($model->getError());
        }
        return $this->renderSuccess('', compact('express'));
    }

    /**
     * 订单改地址
     * @param $order_id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function updateAddress($order_id)
    {
        // 订单信息
        $order = OrderModel::detail($order_id);
        if ($order['delivery_type']==10&&$order['delivery_status']==20) {
            return $this->renderError('订单已发货不允许修改');
        }
        // 获取物流信息
        $model = $order['address'];
        if (!$model->updateAddress($this->postData())) {
            return $this->renderError($model->getError());
        }
        return $this->renderSuccess('修改成功');
    }

    /**
     * 订单备注
     * @param $order_id
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function remark($order_id)
    {
        // 订单信息
        $order = OrderModel::detail($order_id);
        if (!$order->updateOrderRemark($this->postData())) {
            return $this->renderError($order->getError());
        }
        return $this->renderSuccess('操作成功');
    }
}
