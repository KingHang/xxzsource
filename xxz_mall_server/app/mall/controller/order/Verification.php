<?php

namespace app\mall\controller\order;

use app\common\model\order\VerifyGoodsLog;
use app\common\model\order\VerifyServerLog;
use app\mall\controller\Controller;
use app\mall\model\order\OrderGoods as OrderProductModel;
use app\mall\model\store\Clerk as ClerkModel;
use app\mall\model\store\Store as StoreModel;

/**
 * 订单操作
 * @package app\mall\controller\order
 */
class Verification extends Controller
{
    public function index ()
    {
        $param = $this->postData();
        if ($param['search_type'] == 1) {
            // 计次核销
            $list = (new OrderProductModel())->getVerifList($param);
        } elseif ($param['search_type'] == 2) {
            // 服务核销
        }elseif ($param['search_type'] == 3) {
            // 上门自提核销
        } else {
            return $this->renderError('操作失败，不存在的操作类型');
        }
        return $this->renderSuccess('', $list);
    }

    /**
     * 计次商品核销
     * @param $verify_code
     * @param $type
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setOrderVerify($verify_code , $type)
    {
        $param = $this->postData();
        $OrderProductModel = new OrderProductModel();
        // 获取消单订单详情
        $order = $OrderProductModel->getVerifyOrderDetail($verify_code,$param['type']);
//        return $this->renderSuccess('订单核销成功',$order);
        $ClerkModel = ClerkModel::detail($param['user_id']);
        if (!$ClerkModel->checkUser($order['store_ids'] ? explode(',' , $order['store_ids']) : [] , $order['product_type'] == 3 ? 1 : 2)) {
            return $this->renderError($ClerkModel->getError());
        }
        // 确认核销
        if ($order->verificationOrder($ClerkModel,$param['number'])) {
            return $this->renderSuccess('订单核销成功', []);
        }
        return $this->renderError($order->getError() ?:'核销失败');
    }

    public function logList()
    {
        $param = $this->postData();
        if ($param['search_type'] == 1) {
            // 计次核销记录列表
            $model = new VerifyGoodsLog;
            $list = $model->getVerifLogsList(0, $param['search'], $param);
        } elseif ($param['search_type'] == 2) {
            // 服务核销
        }elseif ($param['search_type'] == 3) {
            // 上门自提核销
        } else {
            return $this->renderError('操作失败，不存在的操作类型');
        }
        // 门店列表
        $store_list = (new StoreModel)->getShopStoreList();
        return $this->renderSuccess('', compact('list', 'store_list'));
    }
}