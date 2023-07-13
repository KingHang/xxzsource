<?php

namespace app\shop\controller\order;

use app\common\model\order\VerifyProductLog;
use app\common\model\order\VerifyServerLog;
use app\shop\controller\Controller;
use app\shop\model\order\OrderGoods as OrderProductModel;
use app\shop\model\store\Clerk as ClerkModel;
use app\shop\model\store\Store as StoreModel;

/**
 * 订单操作
 * @package app\shop\controller\order
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
            // 旅游商品核销
            $list = (new OrderProductModel())->getVerifList($param);
        }elseif ($param['search_type'] == 3) {
            // 服务核销记录
            
        } elseif ($param['search_type'] == 10) {
            // 全部
            $list = (new OrderProductModel())->getVerifList(['search'=>$param['search'],'search_type'=>2]);
            $list1 = (new OrderProductModel())->getVerifList(['search'=>$param['search'],'search_type'=>1]);
            $list = array_merge_multiple($list,$list1);
        }else {
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
        $check_type = 2;
        if ($order['product_type'] == 3) {
            $check_type = 1;
        } elseif ($order['product_type'] == 4) {
            $check_type = 3;
        }
        if (!$ClerkModel->checkUser($order['store_ids'] ? explode(',' , $order['store_ids']) : [] , $check_type)) {
            return $this->renderError($ClerkModel->getError());
        }
        // 确认核销
        if ($order->verificationOrder($ClerkModel,$param['number'],1)) {
            return $this->renderSuccess('订单核销成功', []);
        }
        return $this->renderError($order->getError() ?:'核销失败');
    }

    /**
     * 核销记录
     * @return \think\response\Json
     */
    public function logList()
    {
        $param = $this->postData();
        if ($param['search_type'] == 1) {
            // 计次核销记录列表
            $model = new VerifyProductLog;
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