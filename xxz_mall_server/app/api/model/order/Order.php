<?php

namespace app\api\model\order;

use app\api\model\goods\Goods;
use app\api\model\goods\Goods as ProductModel;
use app\api\service\order\paysuccess\type\MasterPaySuccessService;
use app\api\service\order\PaymentService;
use app\api\model\setting\Setting as SettingModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderSourceEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\enum\order\OrderStatusEnum;
use app\common\exception\BaseException;
use app\common\model\plugin\groupsell\BillUser;
use app\common\model\goods\GoodsGift;
use app\common\model\goods\GoodsGiftSku;
use app\common\model\goods\GoodsSku;
use app\common\service\order\OrderCompleteService;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\helper;
use app\common\model\order\Order as OrderModel;
use app\api\service\order\checkpay\CheckPayFactory;
use app\common\service\product\factory\ProductFactory;
use app\common\model\plugin\voucher\UserCoupon as UserCouponModel;
use app\common\model\order\OrderTrade as OrderTradeModel;
use app\common\model\order\OrderExtract as OrderExtractModel;
use app\common\service\order\OrderRefundService;

/**
 * 普通订单模型
 */
class Order extends OrderModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'app_id',
        'update_time'
    ];

    /**
     * 优惠券金额
     * @param $value
     * @return array
     */
    public function getCouponMoneyAttr($value, $data)
    {
        return $data['coupon_money'] + $data['coupon_money_agent'];

    }

    /**
     * 订单支付事件
     */
    public function onPay($payType = OrderPayTypeEnum::WECHAT)
    {
        // 判断订单状态
        $checkPay = CheckPayFactory::getFactory($this['order_source']);

        if (!$checkPay->checkOrderStatus($this)) {
            $this->error = $checkPay->getError();
            return false;
        }
        // 余额支付
        if ($payType == OrderPayTypeEnum::BALANCE) {
            return $this->onPaymentByBalance($this['order_no']);
        }
        return true;
    }

    /**
     * 用户中心订单列表
     */
    public function getList($user_id, $type = 'all', $params)
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                $filter['order_status'] = 10;
                break;
            case 'delivery';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'received';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'comment';
                $filter['is_comment'] = 0;
                $filter['order_status'] = 30;
                break;
            case 'group':
                $filter['order_source'] = 30;
                $filter['assemble_status'] = 10;
                break;
            case 'refund':
                $filter['order_status'] = 40;
                break;
        }
        $model = new self;
        if(isset($params['shop_supplier_id'])&&$params['shop_supplier_id']){
            $model = $model->where('shop_supplier_id','=',$params['shop_supplier_id']);
        }else{
            // 用户查询
            $model = $model->where('user_id', '=', $user_id);
        }
        return $model->with(['product.image', 'supplier'])
            ->where($filter)
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate($params);
    }

    /**
     * 确认收货
     */
    public function receipt()
    {
        // 验证订单是否合法
        // 条件1: 订单必须已发货
        // 条件2: 订单必须未收货
        if ($this['delivery_status']['value'] != 20 || $this['receipt_status']['value'] != 10) {
            $this->error = '该订单不合法';
            return false;
        }
        return $this->transaction(function () {
            // 更新订单状态
            $status = $this->save([
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30
            ]);
            // 执行订单完成后的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$this], static::$app_id);
            return $status;
        });
    }

    /**
     * 立即购买：获取订单商品列表
     */
    public static function getOrderProductListByNow($params)
    {
        // 商品详情
        $product = ProductModel::detail($params['product_id']);
        // 商品sku信息
        $product['product_sku'] = ProductModel::getProductSku($product, $params['product_sku_id']);
        // 商品列表
        $productList = [$product->hidden(['category', 'content', 'image', 'sku'])];
        if ($product['is_open_gift']){
            $productList = self::getGiftProduct($params['product_id'],$params['product_sku_id'],$productList);
        }
        foreach ($productList as &$item) {
            $item['total_num'] = $params['product_num'];
            if (isset($item['gift_status']) && $item['gift_status']==1)
            {
                $item['total_num'] = $item['gift_num'] * intval($params['product_num']);
            }
            // 商品单价
            $item['product_price'] = $item['product_sku']['product_price'];
            // 商品购买数量
            $item['spec_sku_id'] = $item['product_sku']['spec_sku_id'];
            // 商品购买总金额
            $item['total_price'] = helper::bcmul($item['product_price'], $params['product_num']);
            // 商品pv,开启了分销才计算
            $item['pv'] = 0;
            $item['total_pv'] = 0;
        }
        $supplierData[] = [
            'shop_supplier_id' => $product['shop_supplier_id'],
            'supplier' => $product['supplier'],
            'productList' => $productList
        ];
        unset($product['supplier']);
        return $supplierData;
    }
    //获取选择的商品赠品信息
    public static function getGiftProduct($product_id,$spec_sku_id,$productList,$total_num=1)
    {
        $product_sku_id = GoodsSku::detail($product_id,$spec_sku_id);
        $product_sku_id = $product_sku_id['goods_sku_id'];
        if (GoodsGift::where(['goods_id'=>$product_id,'goods_sku_id'=>$product_sku_id])->find())
        {
            $data = GoodsGiftSku::where('goods_id',$product_id)->select();
            if ($data){
                foreach ($data as $key =>$v)
                {
                    $product = Goods::detail($v['gift_goods_id']);
                    $product['gift_num'] = $v['gift_num'];
                    $product['total_num'] = $total_num;
                    $product['gift_status'] = 1;
                    $product['product_price'] = 0;
                    if ($v['spec_sku_id']){
                        $product['product_sku'] = ProductModel::getProductSku($product, $v['spec_sku_id']);
                    }
                    $product['product_sku']['product_price'] = 0.00;
                    // 商品列表
                    array_push($productList,$product);
                }
            }

            return $productList;


        }
        return $productList;
    }

    /**
     * 获取订单总数
     */
    public function getCount($user, $type = 'all')
    {
        if ($user === false) {
            return false;
        }
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = OrderPayStatusEnum::PENDING;
                break;
            case 'delivery';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;    
            case 'received';
                $filter['pay_status'] = OrderPayStatusEnum::SUCCESS;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                break;
            case 'comment';
                $filter['order_status'] = 30;
                $filter['is_comment'] = 0;
                break;
        }
        return $this->where('user_id', '=', $user['user_id'])
            ->where('order_status', '<>', 20)
            ->where($filter)
            ->where('is_delete', '=', 0)
            ->count();
    }

    /**
     * 取消订单
     */
    public function cancel($user)
    {
        if ($this['delivery_status']['value'] == 20) {
            $this->error = '已发货订单不可取消';
            return false;
        }
        //进行中的拼团订单不能取消
        if($this['order_source'] == OrderSourceEnum::ASSEMBLE){
            if($this['assemble_status'] == 10){
                $this->error = '订单正在拼团，到期后如果订单未拼团成功将自动退款';
                return false;
            }
        }

        // 订单取消事件
        return $this->transaction(function () use ($user) {
            // 订单是否已支付
            $isPay = $this['pay_status']['value'] == OrderPayStatusEnum::SUCCESS;
            // 未付款的订单
            if ($isPay == false) {
                //主商品退回库存
                ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], $isPay);
                // 回退用户优惠券
                $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
                $this['coupon_id_sys'] > 0 && UserCouponModel::setIsUse($this['coupon_id_sys'], false);
                // 回退用户积分
                $describe = "订单取消：{$this['order_no']}";
                $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe, 3);
                // 回退用户通证
                $this['deduct_num'] > 0 && $user->giftcertAmountToken($user['mobile'], $this['deduct_num'], $user['user_id'], '订单取消：商品CFP退回');
            }
            // 更新订单状态
            return $this->save(['order_status' => $isPay ? OrderStatusEnum::APPLY_CANCEL : OrderStatusEnum::CANCELLED , 'cancel_time' => time()]);
        });
    }

    /**
     * 订单详情
     */
    public static function getUserOrderDetail($order_id, $user_id,$source='')
    {
        $model = new static();
        $where = ['order_id' => $order_id];
        $where['user_id'] = $user_id;
        $order = $model->where($where)->with(['product' => ['image', 'refund'], 'address', 'express', 'extractStore', 'supplier', 'billuser.bill','orderCarditem'])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        //区别拼团
        if ($order['order_source'] == 30 && isset($order->billuser)) {
            $order->billuser->bill->countdown = $order->billuser->bill->end_time - time();
            $assemble_bill_id = $order->billuser->groupsell_bill_id;
            $order->billuser->userList = BillUser::with(['user'])->where('groupsell_bill_id',$assemble_bill_id)->select();
        }
        return $order;
    }

    /**
     * 供应商查看订单详情
     */
    public static function getSupplierOrderDetail($order_id, $shop_supplier_id)
    {
        $model = new static();
        $order = $model->where(['order_id' => $order_id, 'shop_supplier_id' => $shop_supplier_id,])->with(['product' => ['image', 'refund'], 'address', 'express', 'extractStore', 'supplier'])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        return $order;
    }
    /**
     * 余额支付标记订单已支付
     */
    public function onPaymentByBalance($orderNo)
    {
        // 获取订单详情
        $PaySuccess = new MasterPaySuccessService($orderNo);
        // 发起余额支付
        $status = $PaySuccess->onPaySuccess(OrderPayTypeEnum::BALANCE);
        if (!$status) {
            $this->error = $PaySuccess->getError();
        }
        return $status;
    }

    /**
     * 构建微信支付请求
     */
    protected static function onPaymentByWechat($user, $order_arr, $pay_source,$code)
    {
        return PaymentService::wechat(
            $user,
            $order_arr,
            OrderTypeEnum::MASTER,
            $pay_source,
            $code
        );
    }

    /**
     * 构建支付宝请求
     */
    protected static function onPaymentByAlipay($user, $order_arr, $pay_source)
    {
        return PaymentService::alipay(
            $user,
            $order_arr,
            OrderTypeEnum::MASTER,
            $pay_source
        );
    }
    /**
     * 待支付订单详情
     */
    public static function getPayDetail($orderNo)
    {
        $model = new static();
        return $model->where(['order_no' => $orderNo, 'pay_status' => 10, 'is_delete' => 0])->with(['product', 'user'])->find();
    }

    /**
     * 构建支付请求的参数
     */
    public static function onOrderPayment($user, $order_arr, $payType, $pay_source,$code='')
    {
        //如果来源是h5,首次不处理，payH5再处理
        if($pay_source == 'h5'){
            return [];
        }
        if ($payType == OrderPayTypeEnum::WECHAT) {
            return self::onPaymentByWechat($user, $order_arr, $pay_source ,$code);
        }
        if ($payType == OrderPayTypeEnum::ALIPAY) {
            return self::onPaymentByAlipay($user, $order_arr, $pay_source);
        }
        return [];
    }

    /**
     * 判断当前订单是否允许核销
     */
    public function checkExtractOrder(&$order)
    {
        if (
            $order['pay_status']['value'] == OrderPayStatusEnum::SUCCESS
            && $order['delivery_type']['value'] == DeliveryTypeEnum::EXTRACT
            && $order['delivery_status']['value'] == 10
        ) {
            return true;
        }
        $this->setError('该订单不能被核销');
        return false;
    }

    /**
     * 当前订单是否允许申请售后
     */
    public function isAllowRefund()
    {
        // 必须是已发货的订单
        if ($this['delivery_status']['value'] != 20) {
            return false;
        }
        // 允许申请售后期限(天)
        $refundDays = SettingModel::getItem('trade')['order']['refund_days'];
        // 不允许售后
        if ($refundDays == 0) {
            return false;
        }
        // 当前时间超出允许申请售后期限
        if (
            $this['receipt_status'] == 20
            && time() > ($this['receipt_time'] + ((int)$refundDays * 86400))
        ) {
            return false;
        }
        return true;
    }

    /**
     * 获取活动订单
     * 已付款，未取消
     */
    public static function getPlusOrderNum($user_id, $product_id, $order_source)
    {
        $model = new static();
        return $model->alias('order')->where('order.user_id', '=', $user_id)
            ->join('order_goods', 'order_goods.order_id = order.order_id', 'left')
            ->where('order_goods.goods_source_id', '=', $product_id)
            ->where('order.pay_status', '=', 20)
            ->where('order.order_source', '=', $order_source)
            ->where('order.order_status', '<>', 20)
            ->sum('total_num');
    }

    /**
     * 累计成交笔数
     */
    public static function getTotalPayOrder($shop_supplier_id){
        //累积成交笔数
        return (new static())->where('shop_supplier_id', '=', $shop_supplier_id)
            ->where('pay_status', '=', 20)
            ->where('order_status', 'in', [10,30])
            ->count();
    }

    public static function getTodayPayOrder($shop_supplier_id){
        //开始
        $beginToday = mktime(0,0,0,date('m'),date('d'),date('Y'));
        //结束
        $endToday = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        //今日成交笔数
        return (new static())->where('shop_supplier_id', '=', $shop_supplier_id)
            ->where('pay_status', '=', 20)
            ->where('order_status', 'in', [10,30])
            ->whereBetweenTime('create_time', $beginToday, $endToday)
            ->count();
    }

    /**
     * 设置错误信息
     */
    protected function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在错误
     */
    public function hasError()
    {
        return !empty($this->error);
    }
     /**
     * 获取直播订单
     */
    public function getLiveOrder($params){
        $model = $this;
        if(isset($params['room_id'])&&$params['room_id']){
            $model = $model->where('room_id','=',$params['room_id']);
        }
        if(isset($params['pay_status'])&&$params['pay_status']){
            $model = $model->where('pay_status','=',$params['pay_status']);
        }
        return $model->with(['goods.image'])
            ->where('shop_supplier_id', '=', $params['shop_supplier_id'])
            ->where('room_id', '>', 0)
            ->where('is_delete', '=', 0)
            ->order(['create_time' => 'desc'])
            ->paginate($params);
    }

    /**
     * 主订单购买的数量
     * 未取消的订单
     */
    public static function getHasBuyOrderNum($user_id, $product_id)
    {
        $model = new static();
        return $model->alias('order')->where('order.user_id', '=', $user_id)
            ->join('order_goods', 'order_goods.order_id = order.order_id', 'left')
            ->where('order_goods.goods_id', '=', $product_id)
            ->where('order.order_source', '=', OrderSourceEnum::MASTER)
            ->where('order.order_status', '<>', 21)
            ->sum('total_num');
    }

    /**
     * 获取支付订单详情
     * @param $orderNo
     * @param $user
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPay($orderNo, $user)
    {
        $trade_model = new OrderTradeModel;
        $orderID = $trade_model::where('out_trade_no', '=', $orderNo)
            ->where('pay_status', '=', 10)
            ->column('order_id');
        $multiple = 0;
        if (count($orderID) > 0) {
            $payPrice = OrderModel::where('order_id', 'in', $orderID)
                ->where('pay_status', '=', 10)
//                ->where('user_id', '=', $user['user_id'])
                ->sum('pay_price');
            $multiple = 1;
            $order_id = current($orderID);
        } else {
            $payDetail = OrderModel::where('order_no', '=', $orderNo)
                ->where('pay_status', '=', 10)
//                ->where('user_id', '=', $user['user_id'])
                ->find();
            $payPrice = $payDetail['pay_price'];
            $order_id = $payDetail['order_id'];
            $orderID = [$order_id];
        }
        $product = (new OrderGoods())->alias('p')
            ->with(['image'])
            ->where('order_id', 'in', $orderID)
            ->select();
        foreach ($product as &$item) {
            $item['server_num'] = 0;
            if (isset($item['server'])) {
                $item['server_num'] = count($item['server']);
            }
        }
        $detail['multiple'] = $multiple;
        $detail['order_id'] = $multiple ? 0 : $order_id;
        $order = self::detail($order_id);
        $productNum = count($product);
        if ($multiple == 1) {
            $contact = OrderExtractModel::where('order_id', 'in', $orderID)->find();
            $address = (new OrderAddress())->where('order_id', 'in', $orderID)->find();
            $orderDetail = $this->where('order_id', 'in', $orderID)
                ->where('delivery_type', '=', 20)
                ->with(['extractStore.logo'])
                ->find();
            $store = $orderDetail['extractStore'];
        } else {
            $address = $order['address'];
            $contact = $order['extract'];
            $store = $order['extractStore'];
        }
        $detail['payPrice'] = $payPrice;
        $detail['product'] = $product;
        $detail['productNum'] = $productNum;
        $detail['order'] = $order;
        unset($order['orderCarditem']);
        $detail['orderCarditem'] = $order['orderCarditem'];
        $detail['address'] = $address;
        $detail['contact'] = $contact;
        $detail['store'] = $store;
        return $detail;
    }

    /**
     * 获取用户购买指定商品数量
     * @param $user_id
     * @param $product_ids
     * @return
     */
    public static function getUserBuyProduct($user_id, $product_ids)
    {
        $model = new static();
        return $model->alias('order')->where('order.user_id', '=', $user_id)
            ->join('order_goods', 'order_goods.order_id = order.order_id', 'left')
            ->whereIn('order_goods.goods_id', $product_ids)
            ->where('order.order_status', '<>', 20)
            ->sum('total_num');
    }

    //关闭订单
    public function closeOrder($data)
    {
        if ($this['order_status']['value'] != 10) {
            $this->error = "订单状态不对";
            return false;
        }

        // 订单取消事件
        $status = $this->transaction(function () use ($data) {
            // 执行退款操作
            $this['pay_status']['value'] == 20 && (new OrderRefundService)->execute($this);
            // 回退商品库存
            ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], true);
            // 回退用户优惠券
            $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
            // 回退用户积分
            $user = $this['user'];
            $describe = "订单取消：{$this['order_no']}";
            $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe, 3);
            // 回退用户通证
            $this['deduct_num'] > 0 && $user->giftcertAmountToken($user['mobile'], $this['deduct_num'], $user['user_id'], '订单取消：商品CFP退回');
            // 更新订单状态
            return $this->save(['order_status' => 20, 'cancel_remark' => $data['remark'],'cancel_time' => time()]);
        });
        return $status;
    }

    //获取待支付订单详情
    public function getOrderPay($orderNo, $user)
    {
        $multiple = 0;
        $payDetail = OrderModel::where('order_no', '=', $orderNo)
            ->where('pay_status', '=', 10)
            ->find();
        $payPrice = $payDetail['pay_price'];
        $order_id = $payDetail['order_id'];
        $detail['multiple'] = $multiple;
        $detail['order_id'] = $order_id;
        $order = self::detail($order_id);
        $detail['payPrice'] = $payPrice;
        $detail['order'] = $order;
        return $detail;
    }

    //取消發貨
    public function cancelDelivery()
    {
        if ($this['order_status']['value'] != 10) {
            $this->error = "订单已取消或已完成";
            return false;
        }
        return $this->save(['delivery_status' => 10]);
    }

    /**
     * 取消订单
     */
    public function cancelOrder($data)
    {
        // 判断订单是否有效
        if ($this['pay_status']['value'] != 20) {
            $this->error = '该订单不合法';
            return false;
        }
        if ($this['delivery_status']['value'] == 20) {
            $this->error = '已发货订单不可取消';
            return false;
        }
        //进行中的拼团订单不能取消
        if ($this['order_source'] == OrderSourceEnum::ASSEMBLE) {
            if ($this['assemble_status'] == 10) {
                $this->error = '订单正在拼团，到期后如果订单未拼团成功将自动退款';
                return false;
            }
        }

        $this->startTrans();
        try {
            $this->save(['order_status' => OrderStatusEnum::APPLY_CANCEL, 'cancel_remark' => isset($data['remark']) ? $data['remark'] : '','cancel_time' => time()]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 审核：用户取消订单
     */
    public function confirmCancel($data)
    {
        // 订单取消事件
        $status = $this->transaction(function () use ($data) {
            if ($data['is_cancel'] == true) {
                // 执行退款操作
                (new OrderRefundService)->execute($this);
                // 回退商品库存
                ProductFactory::getFactory($this['order_source'])->backProductStock($this['product'], true);
                // 回退用户优惠券
                $this['coupon_id'] > 0 && UserCouponModel::setIsUse($this['coupon_id'], false);
                // 回退用户积分
                $user = $this['user'];
                $describe = "订单取消：{$this['order_no']}";
                $this['points_num'] > 0 && $user->setIncPoints($this['points_num'], $describe, 3);
                // 回退用户通证
                $this['deduct_num'] > 0 && $user->giftcertAmountToken($user['mobile'], $this['deduct_num'], $user['user_id'], '订单取消：商品CFP退回');
            }
            // 更新订单状态
            return $this->save(['order_status' => $data['is_cancel'] ? 20 : 10]);
        });
        return $status;
    }

    /**
     * 检测订单是否支付
     * @param $params
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkPay($params)
    {
        $data['pay_success'] = 1;
        // 获取订单信息
        $pay_status = OrderTradeModel::where('out_trade_no', '=', $params['order_no'])->value('pay_status');
        if (!$pay_status) {
            $pay_status = $this->where('order_no', '=', $params['order_no'])->value('pay_status');
        }
        if (!$pay_status) {
            $this->setError('订单不存在');
            return false;
        }
        if ($pay_status == 20) {
            $data['pay_success'] = 2;
        }
        return $data;
    }
}
