<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\setting\Express as ExpressModel;
use app\api\model\order\OrderGoods as OrderProductModel;
use app\api\model\order\OrderRefund as OrderRefundModel;
use app\api\model\order\OrderRefundLog as OrderRefundLogModel;
use app\api\model\setting\Message as MessageModel;
use app\common\enum\order\OrderRefundLogEnum;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

/**
 * 订单售后服务
 */
class Refund extends Controller
{
    // $user
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        $this->user = $this->getUser();   // 用户信息
    }

    /**
     * 用户售后单列表
     */
    public function lists($state = -1)
    {
        $model = new OrderRefundModel;
        $list = $model->getList($this->user['user_id'], $state, $this->postData());
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 申请售后
     */
    public function apply($order_product_id, $platform = 'wx')
    {
        // 订单商品详情
        $detail = OrderProductModel::detail($order_product_id);
        if (isset($product['refund']) && !empty($detail['refund'])) {
            return $this->renderError('当前商品已申请售后');
        }
        if ($this->request->isGet()) {
            // 如果来源是小程序, 则获取小程序订阅消息id.获取售后通知.
            $template_arr = MessageModel::getMessageByNameArr($platform, ['order_refund_user']);
            return $this->renderSuccess('', compact('detail', 'template_arr'));
        }
        // 新增售后单记录
        $model = new OrderRefundModel;
        if ($model->apply($this->user, $detail, $this->request->post())) {
            return $this->renderSuccess('提交成功');
        }
        return $this->renderError($model->getError() ?: '提交失败');
    }
    /**
     * 申请平台介入售后
     */
    public function plateapply($order_refund_id)
    {
        // 订单详情
        $detail = OrderRefundModel::detail($order_refund_id);
        if(!in_array($detail['status']['value'],[0,10]) || $detail['plate_status']['value'] != 0){
             return $this->renderError('当前状态不允许申请');
        }
        // 新增记录
        $model = new OrderRefundModel;
        if ($model->plateapply($order_refund_id)) {
            return $this->renderSuccess('提交成功');
        }
        return $this->renderError($model->getError() ?: '提交失败');
    }

    /**
     * 售后单详情
     * @param $order_refund_id
     * @param string $platform
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function detail($order_refund_id, $platform = '')
    {
        $data = $this->request->get();
        // 售后单详情
        $detail = OrderRefundModel::detail(!isset($data['source']) ? [
            'user_id' => $this->user['user_id'],
            'order_refund_id' => $order_refund_id
        ] : [
            'order_refund_id' => $order_refund_id
        ]);

        if (empty($detail)) {
            return $this->renderError('售后单不存在');
        }

        $line = OrderRefundLogEnum::data($detail['type']['value']);

        // 处理进度
        $list = (new OrderRefundLogModel())->getList($order_refund_id);
        $currentStatus = !empty($list) ? $list[0]['status'] : [];//当前进度
        $current = !empty($currentStatus) && isset($currentStatus['value']) ? strval($currentStatus['value']) : '0';

        // 物流公司列表
        $model = new ExpressModel();
        $expressList = $model->getAll();

        // 如果来源是小程序, 则获取小程序订阅消息id.获取售后通知.
        $template_arr = MessageModel::getMessageByNameArr($platform, ['order_refund_user']);
        return $this->renderSuccess('', compact('detail', 'line', 'current', 'expressList', 'list', 'template_arr'));
    }

    /**
     * 用户发货
     */
    public function delivery($order_refund_id)
    {
        $data = $this->request->post();
        // 售后单详情
        $model = OrderRefundModel::detail(!isset($data['source']) ? [
            'user_id' => $this->user['user_id'],
            'order_refund_id' => $order_refund_id
        ] : [
            'order_refund_id' => $order_refund_id
        ]);
        if ($model->delivery($this->postData())) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '提交失败');
    }
    /**
     * 取消申请
     */
    public function cancel($order_refund_id)
    {
        // 售后单详情
        $data = $this->request->post();
        // 售后单详情
        $model = OrderRefundModel::detail(!isset($data['source']) ? [
            'user_id' => $this->user['user_id'],
            'order_refund_id' => $order_refund_id
        ] : [
            'order_refund_id' => $order_refund_id
        ]);
        if ($model->cancel($data)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '提交失败');
    }
}