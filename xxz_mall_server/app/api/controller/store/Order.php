<?php

namespace app\api\controller\store;

use app\api\controller\Controller;
use app\api\model\setting\Setting as SettingModel;
use app\api\model\store\Clerk as ClerkModel;
use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderGoods as OrderProductModel;

/**
 * 自提订单管理
 */
class Order extends Controller
{
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        parent::initialize();
        $this->user = $this->getUser();   // 用户信息
    }

    /**
     * 核销订单详情
     */
    public function detail($order_no)
    {
        // 订单详情
        $model = OrderModel::detailByNo($order_no);
        // 验证是否为该门店的核销员
        $clerkModel = ClerkModel::detail(['user_id' => $this->user['user_id']]);
        if (!$clerkModel->checkUser($model['extract_store_id'])) {
            return $this->renderError($clerkModel->getError());
        }
        return $this->renderSuccess('', [
            'order' => $model,  // 订单详情
            'clerkModel' => $clerkModel,
            'setting' => [
                // 积分名称
                'points_name' => SettingModel::getPointsName(),
            ],
        ]);
    }

    /**
     * 确认核销
     */
    public function extract($order_id)
    {
        // 订单详情
        $order = OrderModel::detail($order_id);
        // 验证是否为该门店的核销员
        $ClerkModel = ClerkModel::detail(['user_id' => $this->user['user_id']]);
        if (!$ClerkModel->checkUser($order['extract_store_id'])) {
            return $this->renderError($ClerkModel->getError());
        }
        // 确认核销
        if ($order->verificationOrder($ClerkModel['clerk_id'])) {
            return $this->renderSuccess('订单核销成功', []);
        }
        return $this->renderError($order->getError() ?:'核销失败');
    }

    /**
     * 计次商品/服务核销
     * @param $verify_code
     * @param $type
     * @return \think\response\Json
     * @throws \app\common\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setOrderVerify($verify_code , $type)
    {
        $OrderProductModel = new OrderProductModel();
        // 获取消单订单详情
        $order = $OrderProductModel->getVerifyOrderDetail($verify_code,$type);
//        return $this->renderSuccess('订单核销成功',$order);
        $ClerkModel = ClerkModel::detail(['user_id' => $this->user['user_id']]);
        if (!$ClerkModel->checkUser($order['store_ids'] ? explode(',' , $order['store_ids']) : [] , $order['product_type'] == 3 ? 1 : 2)) {
            return $this->renderError($ClerkModel->getError());
        }
        // 确认核销
        if ($order->verificationOrder($ClerkModel)) {
            return $this->renderSuccess('订单核销成功', []);
        }
        return $this->renderError($order->getError() ?:'核销失败');
    }
}