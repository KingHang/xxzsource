<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\Order as OrderModel;
use app\api\model\settings\Settings as SettingModel;
use app\api\service\pay\PayService;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\exception\BaseException;
use app\common\model\plugin\agent\OrderDetail;
use app\common\model\plugin\groupsell\BillUser;
use app\common\service\qrcode\ExtractService;
use app\common\model\purveyor\Service as ServiceModel;
use app\common\model\purveyor\User as SupplierUserModel;
use app\api\model\order\OrderGoods;
use app\common\model\order\VerifyProductLog;
use app\common\model\order\VerifyServerLog;
use app\timebank\model\Timebank_basicsetup;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

/**
 * 我的订单
 */
class Order extends Controller
{
    // user
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
     * 我的订单列表
     */
    public function lists($dataType)
    {
        $data = $this->postData();
        $model = new OrderModel;
        $list = $model->getList($this->user['user_id'], $dataType, $data);
        $show_alipay = PayService::isAlipayOpen($data['pay_source'], $this->user['app_id']);
        return $this->renderSuccess('', compact('list', 'show_alipay'));
    }

    /**
     * 订单详情信息
     */
    public function detail($order_id, $pay_source = '')
    {
        // 订单详情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 剩余支付时间
        if ($model['pay_status']['value'] == 10 && $model['order_status']['value'] != 20) {
            $model['pay_end_time'] = $this->formatPayEndTime($model['pay_end_time'] - time());
        } else {
            $model['pay_end_time'] = '';
        }
        // 获取物流信息第一条
        $model['expressInfo'] = ['status' => '已发货', 'context' => '暂无物流'];
        if ($model['express_no']) {
            $express = $model['express'];
            $expressList = $express->dynamic($express['express_name'], $express['express_code'], $model['express_no']);
            $model['expressInfo'] = isset($expressList['list']) && !empty($expressList['list']) ? $expressList['list'][0] : $model['expressInfo'];
        }
        $model['expressInfo'] = (object)$model['expressInfo'];
        // 该订单是否允许申请售后
        $model['isAllowRefund'] = $model->isAllowRefund();
        $model['supplier']['supplier_user_id'] = (new SupplierUserModel())->where('shop_supplier_id', '=', $model['shop_supplier_id'])->value('supplier_user_id');
        // 通证设置
        $tokenSetting = (new Timebank_basicsetup())->basicsetupDesc(1);
        return $this->renderSuccess('', [
            'balance' => $this->user['balance'],
            'order' => $model,  // 订单详情
            'setting' => [
                // 积分名称
                'points_name' => SettingModel::getPointsName(),
                // 通证名称
                'token_name' => $tokenSetting['token_name'],
                //是否开启客服
                'service_open' => SettingModel::getSysConfig()['service_open'],
                //店铺客服信息
                'mp_service' => ServiceModel::detail($model['shop_supplier_id']),
            ],
            'show_alipay' => PayService::isAlipayOpen($pay_source, $model['app_id'])
        ]);
    }

    /**
     * 支付成功详情信息
     */
    public function paySuccess($order_id)
    {
        $order_arr = explode(',', $order_id);
        $order = [
            'pay_price' => 0,
            'points_bonus' => 0,
            'list' => array()
        ];
        $is_card = 0;
        foreach ($order_arr as $id) {
            $model = OrderModel::getUserOrderDetail($id, $this->user['user_id']);
            $order['pay_price'] += $model['pay_price'];
            $order['points_bonus'] += $model['points_bonus'];
            $order['list'][] = array(
                'order_id' => $id,
                'extractStore' => $model['extractStore'],
                'express' => $model['express'],
                'address' => $model['address'],
                'delivery_type' => $model['delivery_type'],
            );
            if (!empty($model['product'])) {
                foreach ($model['product'] as $product) {
                    if (in_array($product['product_type'],[3,4])) {
                        $is_card ++;
                    }
                }
            }
        }
        return $this->renderSuccess('', compact('order','is_card'));
    }

    /**
     * 获取物流信息
     */
    public function express($order_id,$source='')
    {
        // 订单信息
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id'],$source);
        if (!$order['express_no']) {
            return $this->renderError('没有物流信息');
        }
        // 获取物流信息
        $model = $order['express'];
        $express = $model->dynamic($model['express_name'], $model['express_code'], $order['express_no']);
        if ($express === false) {
            return $this->renderError($model->getError());
        }
        $productInfo['product_num'] = count($order['product']);
        $productInfo['product'] = $order['product'][0];
        return $this->renderSuccess('', compact('express', 'productInfo', 'order'));
    }

    /**
     * 部分发货获取物流信息
     * @param $order_id
     * @param $product_id
     * @return Json
     * @throws BaseException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function productExpress($order_id, $product_id)
    {
        // 获取订单商品信息
        $product = (new OrderGoods)->getUserOrderProductDetail($order_id, $product_id);

        if (!$product['express_no']) return $this->renderError('没有物流信息');

        // 获取物流信息
        $model = $product['express'];

        $express = $model->dynamic($model['express_name'], $model['express_code'], $product['express_no']);

        if ($express === false) return $this->renderError($model->getError());

        return $this->renderSuccess('', compact('express', 'product'));
    }

    /**
     * 取消订单
     */
    public function cancel($order_id)
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
//        OrderDetail::addBouns($model);die;
        if ($model->cancel($this->user)) {
            return $this->renderSuccess('订单取消成功');
        }
        return $this->renderError($model->getError() ?: '订单取消失败');
    }

    /**
     * 确认收货
     */
    public function receipt($order_id,$source='')
    {
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id'],$source);
        if ($model->receipt()) {
            return $this->renderSuccess('收货成功');
        }
        return $this->renderError($model->getError() ?: '收货失败');
    }
    public function getPayOrder($order_id)
    {
        $order = [
            'order_no' => '',
            'pay_price' => 0
        ];
        $order_id_arr = explode(',',$order_id);
        foreach ($order_id_arr as $item) {
            $Detail = OrderModel::getUserOrderDetail($item, $this->user['user_id']);
            $order['order_no'] = $Detail['order_no'];
            $order['pay_price'] += $Detail['pay_price'];
        }
        // 返回结算信息
        return $this->renderSuccess('', [
            'order' => $order,   // 订单号
            'balance' => $this->user['balance'],
        ]);
    }
    /**
     * 立即支付
     */
    public function pay($order_id, $payType = OrderPayTypeEnum::WECHAT, $pay_source = 'wx')
    {
        $code = $code = $this->request->post('code');
        // 获取订单详情
        $model = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 订单支付事件
        if (!$model->onPay($payType)) {
            return $this->renderError($model->getError() ?: '订单支付失败');
        }
        // 构建微信支付请求
        $payment = $model->onOrderPayment($this->user, [$model], $payType, $pay_source,$code);
        // 支付状态提醒
        return $this->renderSuccess('', [
            'order_id' => $model['order_id'],   // 订单id
            'pay_type' => $payType,             // 支付方式
            'payment' => $payment               // 微信支付参数
        ], ['success' => '支付成功', 'error' => '订单未支付']);
    }

    /**
     * 获取订单核销二维码
     */
    public function qrcode($order_id, $source)
    {
        // 订单详情
        $order = OrderModel::getUserOrderDetail($order_id, $this->user['user_id']);
        // 判断是否为待核销订单
        if (!$order->checkExtractOrder($order)) {
            return $this->renderError($order->getError());
        }
        $Qrcode = new ExtractService(
            $this->app_id,
            $this->user,
            $order_id,
            $source,
            $order['order_no']
        );
        return $this->renderSuccess('', [
            'qrcode' => $Qrcode->getImage(),
        ]);
    }

    private function formatPayEndTime($leftTime)
    {
        if ($leftTime <= 0) {
            return '';
        }

        $str = '';
        $day = floor($leftTime / 86400);
        $hour = floor(($leftTime - $day * 86400) / 3600);
        $min = floor((($leftTime - $day * 86400) - $hour * 3600) / 60);

        if ($day > 0) $str .= $day . '天';
        if ($hour > 0) $str .= $hour . '小时';
        if ($min > 0) $str .= $min . '分钟';
        return $str;
    }

    /**
     * 获取计次/服务核销信息
     * @param $order_product_id
     * @return Json
     */
    public function getOrderVerifyInfo()
    {
        $post = $this->postData();
        $product_type = isset($post['product_type']) && $post['product_type'] > 0 ? $post['product_type'] : 3;
        $order_product_id = isset($post['order_product_id']) ? $post['order_product_id'] : 0;
        $verify_code = isset($post['verify_code']) ? $post['verify_code'] : '';
        $productModel = new OrderGoods();
        $info = $productModel->productDetail($order_product_id,$this->user['user_id'],$product_type,$verify_code);
        if ($info) {
            return $this->renderSuccess('', $info);
        }
        return $this->renderError($productModel->getError() ?: '请求失败');
    }

    /**
     * 获取核销记录
    */
    public function getOrderVerifyLog($order_product_id)
    {
        $post = $this->postData();
        $product_type = isset($post['product_type']) ? $post['product_type'] : 3;
        $mdoel = (new OrderGoods());
        $info = $mdoel->productDetail($order_product_id,$this->user['user_id'],$product_type);
        if (!$info) {
            return $this->renderError($mdoel->getError() ?: '请求失败');
        }
        $log_list = [
            'product_name' => $info['product_name'],
            'product_id' => $info['product_id'],
            'order_id' => $info['order_id'],
            'order_product_id' => $info['order_product_id'],
            'product_type' => $info['product_type'],
            'order_source' => $info['order_source'],
        ];
        if (in_array($info['product_type'],[3,4])) {
            // 计次/旅游商品核销记录
            $log_list['log'] = (new VerifyProductLog)->getLogList($info['opt_id'],$info['product_type']);
        } else {
            // 服务核销记录
            $log_list['log'] = (new VerifyServerLog)->getLogList($info['order_id'] , $info['verify_code']);
        }
        return $this->renderSuccess('', $log_list);
    }

    /**
     * 我的权益卡
    */
    public function getMyBenefitCardList()
    {
        $mdoel = (new OrderGoods());
        $post = $this->postData();
        $list = $mdoel->getMyBenefitCardList($post,$this->user['user_id']);
        return $this->renderSuccess('', $list);
    }

    /**
     * 我的旅游卡详情
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getMyBenefitCardDetail()
    {
        $mdoel = (new OrderGoods());
        $post = $this->postData();
        $detail = $mdoel->getMyBenefitCardDetail($post,$this->user['user_id']);
        return $this->renderSuccess('', $detail);
    }
}