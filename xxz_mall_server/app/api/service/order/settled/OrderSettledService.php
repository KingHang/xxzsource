<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderGoods;
use app\api\model\order\OrderAddress as OrderAddress;
use app\api\model\plus\coupon\UserVoucher as UserCouponModel;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\enum\order\OrderSourceEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\settings\Settings as SettingModel;
use app\api\service\points\PointsDeductService;
use app\api\service\coupon\ProductDeductService;
use app\common\model\store\Store as StoreModel;
use app\api\service\user\UserService;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\helper;
use app\common\model\user\UserAddress;
use app\common\service\delivery\ExpressService;
use app\common\service\BaseService;
use app\common\service\product\factory\ProductFactory;
use app\api\model\shop\FullReduce as FullReduceModel;
use app\api\service\fullreduce\FullDeductService;
use app\api\model\plus\agent\User as AgentUserModel;
use app\api\model\order\Cart as CartModel;
use app\common\model\plugin\agent\UserCoupon as AgentUserCouponModel;
use app\common\model\order\OrderTrade as OrderTradeModel;
use app\common\service\order\OrderService;
use app\common\model\plugin\giftcert\Product as GiftcertProductModel;
use app\common\model\plugin\giftcert\ProductSku as GiftcertProductSkuModel;
use app\timebank\model\Timebank_basicsetup;
use app\timebank\ztservice\Service;
use app\common\model\order\OrderTravelers;
use app\common\model\order\OrderBenefit;

/**
 * 订单结算服务基类
 */
abstract class OrderSettledService extends BaseService
{
    /* $model OrderModel 订单模型 */
    public $model;

    // 当前应用id
    protected $app_id;

    protected $user;

    // 订单结算商品列表
    protected $supplierData = [];

    protected $params;
    protected $cardDeductionNumber = 0;
    /**
     * 订单结算的规则
     * 主商品默认规则
     */
    protected $settledRule = [
        'is_coupon' => true,        // 优惠券抵扣
        'is_use_points' => true,        // 是否使用积分抵扣
        'is_use_deduct' => true,        // 是否使用通证抵扣
        'force_points' => false,     // 强制使用积分，积分兑换
        'is_user_grade' => true,     // 会员等级折扣
        'is_agent' => true,     // 商品是否开启分销,最终还是支付成功后判断分销活动是否开启
        'is_reduce' => true, //是否满减
    ];

    /**
     * 订单结算数据
     */
    protected $commonOrderData = [];
    /**
     * 订单结算数据
     */
    protected $orderData = [];
    /**
     * 订单来源
     */
    protected $orderSource;

    /**
     * 访问来源
     */
    protected $agentUser = [];

    /**
     * 构造函数
     */
    public function __construct($user, $supplierData, $params,$agent_user=[])
    {
        $this->model = new OrderModel;
        $this->app_id = OrderModel::$app_id;
        $this->user = $user;
        $this->supplierData = $supplierData;
        if (!isset($params['visit_source'])) {
            $params['visit_source'] = '';
        }
        $this->params = $params;
        $this->agentUser = $agent_user;
    }

    /**
     * 订单确认-结算台
     */
    public function settlement()
    {
        // 验证商品状态, 是否允许购买
        $this->validateProductList();
        $orderTotalNum = 0;
        $orderTotalPrice = 0;
        $orderTotalPv = 0;
        $orderPayPrice = 0;
        $expressPrice = 0;
        $totalPointsMoney = 0;
        $totalPoints = 0;
        $totalDeductMoney = 0;
        $totalDeductNum = 0;
        $orderGradeTotalPrice = 0;
        $benefit_card_money = 0;
        $this->commonOrderData = $this->getCommonOrderData();
        // 供应商
        foreach ($this->supplierData as &$supplier) {
            // 整理订单数据
            $this->orderData = $this->getOrderData($supplier['shop_supplier_id']);
            // 订单商品总数量
            $orderTotalNum += helper::getArrayColumnSum($supplier['productList'], 'total_num');
            // 设置订单商品会员折扣价
            $this->setOrderGrade($supplier['productList']);
            // 设置订单商品总金额(不含优惠折扣)
            $this->setOrderTotalPrice($supplier['productList']);
            $orderTotalPrice += $this->orderData['order_total_price'];
            $this->setOrderTotalPv($supplier['productList']);
            $orderTotalPv += $this->orderData['order_total_pv'];
            $orderGradeTotalPrice += $this->orderData['order_grade_total_price'];
            // 获取旅游商品数量
            $this->setBenefitNum($supplier['productList']);
            // 设置权益卡抵扣
            if(!empty($this->params['benefit'])){
                $this->setOrderBenefitCard($supplier['productList']);
            }
            $order_total_price = $this->orderData['order_total_price'];
            $order_total_price -= $this->orderData['benefit_card_money'];
            $benefit_card_money += $this->orderData['benefit_card_money'];
            // 先计算满减、自动满减，查找店铺满减
            if($this->settledRule['is_reduce']){
                $reduce = FullReduceModel::getReductList($order_total_price, $orderTotalNum, $supplier['shop_supplier_id']);
                // 设置满减
                $this->orderData['reduce'] = $reduce;
                $reduce && $this->setOrderFullreduceMoney($reduce, $supplier['productList']);
            }
            if($this->settledRule['is_coupon']) {
                // 当前用户可用的优惠券列表
                $couponList = $order_total_price > 0 ?$this->getUserCouponList($order_total_price, $supplier['shop_supplier_id']) : [];
                foreach ($couponList as $i => $coupon){
                    if(!$this->checkCouponCanUse($coupon, $supplier['productList'])){
                        unset($couponList[$i]);
                    }
                }
                // 计算优惠券抵扣,如果没有选择，则默认为第一个，折扣最多的
                $this->orderData['coupon_id'] = 0;
                $this->orderData['couponList'] = $couponList;
                // 不自动使用优惠券
                if (isset($this->params['supplier'])) {
                    $this->orderData['coupon_id'] = $this->params['supplier'][$supplier['shop_supplier_id']]['coupon_id'];
                } else {
                    if (count($couponList) > 0) {
                        $this->orderData['coupon_id'] = current($couponList)['user_coupon_id'];
                    }
                }
                $this->setOrderCouponMoney($couponList, $this->orderData['coupon_id'], $supplier['productList']);
            }

            // 计算可用积分抵扣
            $this->setOrderPoints($supplier['productList']);
            $totalPointsMoney += $this->orderData['points_money'];
            $totalPoints += $this->orderData['points_num'];

            // 计算可用通证抵扣
            if ($this->params['visit_source'] != 'agent') {
                $this->setOrderDeduct($supplier['productList']);
                $totalDeductMoney += $this->orderData['deduct_money'];
                $totalDeductNum += $this->orderData['deduct_num'];
            }

            // 计算订单商品的实际付款金额
            $this->setOrderProductPayPrice($supplier['productList']);

            // 设置默认配送方式
            if(!isset($this->params['supplier'])){
                $this->orderData['delivery'] = current(SettingModel::getItem('store')['delivery_type']);
            }else{
                $this->orderData['delivery'] = $this->params['supplier'][$supplier['shop_supplier_id']]['delivery'];
                $this->orderData['store_id'] = $this->params['supplier'][$supplier['shop_supplier_id']]['store_id'];
            }

            // 处理配送方式
            if ($this->orderData['delivery'] == DeliveryTypeEnum::EXPRESS) {
                $this->setOrderExpress($supplier['productList']);
                $expressPrice += $this->orderData['express_price'];
            } elseif ($this->orderData['delivery'] == DeliveryTypeEnum::EXTRACT) {
                $this->orderData['store_id'] > 0 && $this->orderData['extract_store'] = StoreModel::detail($this->params['supplier'][$supplier['shop_supplier_id']]['store_id']);
            }

            // 计算订单最终金额
            $this->setOrderPayPrice($supplier['productList']);
            $orderPayPrice += $this->orderData['order_pay_price'];
            $supplier['orderData'] = $this->orderData;
        }
        // 平台优惠券
        $couponList = $this->getUserCouponList($orderTotalPrice, 0);
        foreach ($couponList as $i => $coupon){
            if(!$this->checkPTCouponCanUse($coupon, $supplier['productList'])){
                unset($couponList[$i]);
            }
        }
        // 计算优惠券抵扣,如果没有选择，则默认为第一个，折扣最多的
        $coupon_id = 0;
        if($this->params['coupon_id'] > 0){
            $coupon_id = $this->params['coupon_id'];
        }else if($this->params['coupon_id'] == -1){
            // 传-1取最高的抵扣
            if(count($couponList) > 0){
                $coupon_id = current($couponList)['user_coupon_id'];
            }
        }
        $this->setOrderSysCouponMoney($coupon_id, $couponList);

        //分销商优惠券
        $agentCouponList = [];
        $agent_coupon_id = 0;
        if ($this->params['visit_source'] == 'agent') {
            $agentCouponList = (new UserCouponModel())->getAgentCoupon($this->agentUser, $orderPayPrice, 0);
            // 计算优惠券抵扣,如果没有选择，则默认为第一个，折扣最多的
            if ($this->params['agent_coupon_id'] > 0) {
                $agent_coupon_id = $this->params['agent_coupon_id'];
            }
        }
        $this->setOrderAgentCouponMoney($agent_coupon_id, $agentCouponList);

        //最终价格
        $orderPayPrice = $this->setOrderFinalPrice();
        // 计算订单积分赠送数量
        $this->setOrderPointsBonus();
        // 计算订单成长值赠送数量
        $this->setOrderGrowthValueBonus();
        //订单数据
        $this->commonOrderData = array_merge([
            'order_total_num' => $orderTotalNum,        // 商品总数量
            'order_total_price' => helper::number2($orderTotalPrice),        // 商品总价
            'order_total_pv' => helper::number2($orderTotalPv),        // 商品pv
            'order_pay_price' => helper::number2($orderPayPrice),        // 商品总价,最终支付
            'coupon_list' => $couponList,
            'coupon_id_sys' => $coupon_id,
            'coupon_money_sys' => 0,
            'points_money' => $totalPointsMoney,
            'points_num' => $totalPoints,
            'deduct_money' => $totalDeductMoney,
            'deduct_num' => $totalDeductNum,
            'expressPrice' => $expressPrice,
            // 房间id
            'room_id' => isset($this->params['room_id']) && $this->params['room_id'] > 0 ? $this->params['room_id'] : 0,
            'order_grade_total_price' => helper::number2($orderGradeTotalPrice),//会员折扣
            'agentCouponList' => $agentCouponList,
            'coupon_id_agent' => $agent_coupon_id,
            'coupon_money_agent' => 0,
            'benefit_card_money' => $benefit_card_money,
            'benefit' => isset($this->params['benefit']) ? $this->params['benefit'] : []
        ], $this->commonOrderData, $this->settledRule);
        // 返回订单数据
        return [
            'supplierList' => $this->supplierData,
            'orderData' => $this->commonOrderData
        ];
    }

    /**
     * 整理订单数据(结算台初始化),公共部分
     */
    private function getCommonOrderData()
    {
        // 积分设置
        $pointsSetting = SettingModel::getItem('exchangepurch');
        // 通证设置
        $tokenSetting = (new Timebank_basicsetup())->basicsetupDesc(1);
        if (UserAddress::where(['user_id'=>$this->user['user_id']])->find() || $this->params['visit_source'] == 'agent'){
            $address_status = true;
        }else{
            $address_status = false;
        }
        if ($this->params['visit_source'] == 'agent') {
            $orderType = (new CartModel($this->user))->getAgentOrderTypeList($this->params['product_list']);
            $this->params['address_id'] = 0;
            $address = $this->params['address'];
        } else {
            $address_id = $this->params['address_id'];
            $address = UserAddress::where(['address_id'=>$address_id])->find();
        }
        $data = [
            // 默认地址
            'address' => $address,
            // 是否存在收货地址
            'exist_address' => $address_status,
            // 是否允许使用积分抵扣
            'is_allow_points' => true,
            // 是否使用积分抵扣
            'is_use_points' => $this->params['is_use_points'],
            // 是否允许使用通证抵扣
            'is_allow_deduct' => true,
            // 是否使用通证抵扣
            'is_use_deduct' => isset($this->params['is_use_deduct']) ? $this->params['is_use_deduct'] : $this->settledRule['is_use_deduct'],
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
            'pay_source' => isset($this->params['pay_source']) ? $this->params['pay_source'] : '',
            // 系统设置
            'setting' => [
                'points_name' => $pointsSetting['points_name'],      // 积分名称
                'token_name' => $tokenSetting['token_name'],         // 通证名称
            ],
            'benefit_num' => 0,
            'notBenefitNum' => 0,
            'benefit_card_list' => [],
            'travelers' => isset($this->params['travelers']) ? $this->params['travelers'] : [], // 出行人信息
            'card_info' => isset($this->params['card_info']) ? $this->params['card_info'] : [], // 兑换权益卡信息
            'order_benefit_card_id' => 0,
        ];
        if ($this->params['visit_source'] == 'agent') {
            $data = array_merge($data, [
                'store_id' => $this->params['store_id'],
                'distance' => isset($this->params['distance']) ? $this->params['distance'] : 0,
                'real_product' => $orderType && in_array('1', $orderType) ? 1 : 0,
                'time_product' => $orderType && in_array('3', $orderType) ? 1 : 0,
                'contact' => isset($this->params['contact']) ? $this->params['contact'] : '',
                'extract_store' => StoreModel::detail($this->params['store_id']),
                'deliver' => $this->params['delivery'],
                'remark' => isset($this->params['remark']) ? $this->params['remark'] : '',
            ]);
        }
        return $data;
    }

    /**
     * 验证订单商品的状态
     * @return bool
     */
    abstract function validateProductList();

    /**
     * 创建新订单
     */
    public function createOrder($order)
    {
        // 表单验证
        if (!$this->validateOrderForm($order, $this->params)) {
            return false;
        }

        // 余额支付标记订单已支付
        if ($order['orderData']['pay_type'] == OrderPayTypeEnum::BALANCE) {
            // 验证余额支付时用户余额是否满足
            if ($this->user['balance'] < $order['orderData']['order_pay_price']) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false;
            }
        }
        $order_arr = [];
        // 按商品类型拆单
        $SplitOrder = $this->setSplitOrder($order);
        $order = $SplitOrder['order'];
        $product_type_data = $SplitOrder['product_type_data'];
        // 创建新的订单
        foreach ($order['supplierList'] as $supplier){
            foreach ($product_type_data as $product_type) {
                if (isset($supplier[$product_type])) {
                    $this->model = new OrderModel;
                    $this->model->transaction(function () use ($order, $supplier,$product_type) {
                        // 创建订单事件
                        $this->createOrderEvent($order['orderData'], $supplier[$product_type],$product_type);
                        if ($order['orderData']['pay_type'] == OrderPayTypeEnum::BALANCE) {
                            $this->model->onPaymentByBalance($this->model['order_no']);
                        }
                    });
                    array_push($order_arr, $this->model);
                }
            }
        }
        if ($this->params['visit_source'] == 'agent') {
            if (count($order_arr) > 1) {
                $orderNo = OrderService::createOrderNo();
                foreach ($order_arr as $order) {
                    $trade_model = new OrderTradeModel;
                    $trade_list = [];
                    $trade_list[] = [
                        'out_trade_no' => $orderNo,
                        'order_id' => $order['order_id'],
                        'app_id' => $order['app_id']
                    ];
                    $trade_model->saveAll($trade_list);
                }
            } else {
                $orderNo = $order_arr[0]['order_no'];
            }
            return $orderNo;
        }
        return $order_arr;
    }

    /**
     * 设置订单的商品总金额(不含优惠折扣)
     */
    private function setOrderTotalPrice($productList)
    {
        // 订单商品的总金额(不含优惠券折扣)
        $this->orderData['order_total_price'] = helper::number2(helper::getArrayColumnSum($productList, 'total_price'));
        $this->orderData['order_grade_total_price'] = helper::number2(helper::getArrayColumnSum($productList, 'grade_total_money'));

        $this->orderData['benefit_card_money'] = helper::number2(helper::getArrayColumnSum($productList, 'benefit_card_money'));
    }

    /**
     * 设置订单的商品总pv
     */
    private function setOrderTotalPv($productList)
    {
        // 订单商品的总金额(不含优惠券折扣)
        $this->orderData['order_total_pv'] = helper::number2(helper::getArrayColumnSum($productList, 'total_pv'));
    }

    /**
     * 当前用户可用的优惠券列表
     */
    private function getUserCouponList($orderTotalPrice, $shop_supplier_id)
    {
        if (!$this->user) {
            return [];
        }
        // 是否开启优惠券折扣
        if (!$this->settledRule['is_coupon']) {
            return [];
        }
        return UserCouponModel::getUserCouponList($this->user['user_id'], $orderTotalPrice, $shop_supplier_id);
    }

    /**
     * 设置订单优惠券抵扣信息
     */
    private function setOrderCouponMoney($couponList, $couponId, $productList)
    {
        // 设置默认数据：订单信息
        helper::setDataAttribute($this->orderData, [
            'coupon_id' => 0,       // 用户优惠券id
            'coupon_money' => 0,    // 优惠券抵扣金额
        ], false);
        // 设置默认数据：订单商品列表
        helper::setDataAttribute($productList, [
            'coupon_money' => 0,    // 优惠券抵扣金额
        ], true);
        // 是否开启优惠券折扣
        if (!$this->settledRule['is_coupon']) {
            return false;
        }
        // 如果没有可用的优惠券，直接返回
        if ($couponId <= 0 || empty($couponList)) {
            return true;
        }
        // 获取优惠券信息
        $couponInfo = helper::getArrayItemByColumn($couponList, 'user_coupon_id', $couponId);
        if ($couponInfo == false) {
            $this->error = '未找到优惠券信息';
            return false;
        }
        // 计算订单商品优惠券抵扣金额
        $productListTemp = helper::getArrayColumns($productList, ['total_price']);
        $CouponMoney = new ProductDeductService( 'coupon_money', 'total_price');
        $completed = $CouponMoney->setProductCouponMoney($productListTemp, $couponInfo['reduced_price']);
        // 分配订单商品优惠券抵扣金额
        foreach ($productList as $key => &$product) {
            $product['coupon_money'] = $completed[$key]['coupon_money'] / 100;
        }
        // 记录订单优惠券信息
        $this->orderData['coupon_id'] = $couponId;
        $this->orderData['coupon_money'] = helper::number2($CouponMoney->getActualReducedMoney() / 100);
        return true;
    }

    /**
     * 计算订单商品的实际付款金额
     */
    private function setOrderProductPayPrice($productList)
    {
        // 商品总价 - 优惠抵扣
        foreach ($productList as &$product) {
            // 减去优惠券抵扣金额
            $value = helper::bcsub($product['total_price'], $product['coupon_money']);
            // 减去积分抵扣金额
            if ($this->orderData['is_allow_points'] && $this->commonOrderData['is_use_points'] && !$this->settledRule['force_points']) {
                $value = helper::bcsub($value, $product['points_money']);
            }
            // 减去通证抵扣金额
            if ($this->orderData['is_allow_deduct'] && $this->commonOrderData['is_use_deduct'] && !$this->settledRule['force_points']) {
                $value = helper::bcsub($value, $product['deduct_money']);
            }
            // 减去满减金额
            if($this->settledRule['is_reduce'] && $this->orderData['reduce']){
                $value = helper::bcsub($value, $product['fullreduce_money']);
            }
            $value = helper::bcsub($value, $product['benefit_card_money']);

            $product['Split_total_pay_price'] = $product['total_pay_price'] = helper::number2($value);
        }
        return true;
    }

    /**
     * 整理订单数据(结算台初始化)
     */
    private function getOrderData($shop_supplier_id)
    {
        // 系统支持的配送方式 (后台设置)
        $deliveryType = SettingModel::getItem('store')['delivery_type'];
        // 积分设置
        $pointsSetting = SettingModel::getItem('exchangepurch');
        // 通证设置
        $tokenSetting = (new Timebank_basicsetup())->basicsetupDesc(1);
        if(isset($this->params['supplier'])){
            $delivery = $this->params['supplier'][$shop_supplier_id]['delivery'];
        }else{
            $delivery = $deliveryType[0];
        }
        if (UserAddress::where(['user_id'=>$this->user['user_id']])->find()){
            $address_status = true;
        }else{
            $address_status =false;
        }
        $address_id = $this->params['address_id'];
        $address = UserAddress::where(['address_id'=>$address_id])->find();
        return [
            // 配送类型
            'delivery' => $delivery,
            // 默认地址
            'address' => $address,
            // 是否存在收货地址
            'exist_address' => $address_status,
            // 配送费用
            'express_price' => 0.00,
            // 当前用户收货城市是否存在配送规则中
            'intra_region' => true,
            // 自提门店信息
            'extract_store' => [],
            // 是否允许使用积分抵扣
            'is_allow_points' => false,
            // 是否使用积分抵扣
            'is_use_points' => $this->params['is_use_points'],
            // 是否允许使用通证抵扣
            'is_allow_deduct' => false,
            // 是否使用通证抵扣
            'is_use_deduct' => isset($this->params['is_use_deduct']) ? $this->params['is_use_deduct'] : $this->settledRule['is_use_deduct'],
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
            // 系统设置
            'setting' => [
                'delivery' => $deliveryType,     // 支持的配送方式
                'points_name' => $pointsSetting['points_name'],      // 积分名称
                'token_name' => $tokenSetting['token_name'],         // 通证名称
            ],
            // 记忆的自提联系方式
            //'last_extract' => UserService::getLastExtract($this->user['user_id']),
            'deliverySetting' => $deliveryType,
            //门店id
            'store_id' => 0,
            //优惠券id
            'coupon_id' => 0,
            //优惠金额
            'coupon_money'=>0,
            'benefit_card_money' => 0
        ];
    }

    /**
     * 订单配送-快递配送
     */
    private function setOrderExpress($productList)
    {
        if (!$this->user) {
            $this->orderData['express_price'] = 0;
            $this->orderData['intra_region'] = true;
            return true;
        }
        // 设置默认数据：配送费用
        helper::setDataAttribute($productList, [
            'express_price' => 0,
        ], true);
        // 当前用户收货城市id
        $cityId = $this->user['address_default'] ? $this->user['address_default']['city_id'] : null;

        // 初始化配送服务类
        $ExpressService = new ExpressService(
            $this->app_id,
            $cityId,
            $productList,
            OrderTypeEnum::MASTER
        );

        // 获取不支持当前城市配送的商品
        $notInRuleProduct = $ExpressService->getNotInRuleProduct();

        // 验证商品是否在配送范围
        $this->orderData['intra_region'] = ($notInRuleProduct === false);

        if (!$this->orderData['intra_region']) {
            $notInRuleProductName = $notInRuleProduct['product_name'];
            $this->error = "很抱歉，您的收货地址不在商品 [{$notInRuleProductName}] 的配送范围内";
            return false;
        } else {
            // 计算配送金额
            $ExpressService->setExpressPrice();
        }

        // 订单总运费金额
        $this->orderData['express_price'] = helper::number2($ExpressService->getTotalFreight());
        return true;
    }

    /**
     * 设置订单的实际支付金额(含配送费)
     */
    private function setOrderPayPrice($productList)
    {
        // 订单金额(含优惠折扣)
        $this->orderData['order_price'] = helper::number2(helper::getArrayColumnSum($productList, 'total_pay_price'));
        // 订单实付款金额(订单金额 + 运费)
        $this->orderData['order_pay_price'] = helper::number2(helper::bcadd($this->orderData['order_price'], $this->orderData['express_price']));
    }

    /**
     * 表单验证 (订单提交)
     */
    private function validateOrderForm(&$order)
    {
        // 余额支付时，判断用户余额是否足够
        if ($order['orderData']['pay_type'] == OrderPayTypeEnum::BALANCE) {
            if ($this->user['balance'] < $order['orderData']['order_pay_price']) {
                $this->error = '用户余额不足，无法使用余额支付';
                return false;
            }
        }
        //如果是积分兑换，判断用户积分是否足够
        if ($this->settledRule['force_points']) {
            if ($this->user['exchangepurch'] < $order['orderData']['points_num']) {
                $this->error = '用户积分不足，无法使用积分兑换';
                return false;
            }
        }
        return true;
    }

    /**
     * 创建订单事件
     */
    private function createOrderEvent($commomOrder, $supplier,$product_type)
    {
        // 新增订单记录
        $status = $this->add($commomOrder, $supplier);
        if ($product_type != 4) {
            if ($supplier['orderData']['delivery'] == DeliveryTypeEnum::EXPRESS) {
                // 记录收货地址
                $this->saveOrderAddress($commomOrder['address'], $status);
            } else {
                // 记录自提信息
                if ($this->params['visit_source'] == 'agent') {
                    $this->saveOrderExtract($commomOrder['contact']['name'], $commomOrder['contact']['phone']);
                } else {
                    $this->saveOrderExtract($commomOrder['address']['name'], $commomOrder['address']['phone']);
                }
            }
        }

        // 保存订单商品信息
        $this->saveOrderProduct($supplier, $status);
        // 保存出行人信息
        $this->saveOrderTravelers($commomOrder,$status);
        // 更新商品库存 (针对下单减库存的商品)
        ProductFactory::getFactory($this->orderSource['source'])->updateProductStock($supplier['productList']);

        // 设置优惠券使用状态
        UserCouponModel::setIsUse($this->params['coupon_id']);

        // 设置分销商优惠券使用状态
        if ($this->params['visit_source'] == 'agent') {
            AgentUserCouponModel::setIsUse($this->params['agent_coupon_id'], $this->agentUser);
        }

        // 积分兑换扣除用户积分
        if ($commomOrder['force_points']) {
            $describe = "用户积分兑换消费：{$this->model['order_no']}";
            $this->user->setIncPoints(-$commomOrder['points_num'], $describe,10);
        } else {
            // 积分抵扣情况下扣除用户积分
            if (isset($this->params['is_use_points']) && $this->params['is_use_points'] && $commomOrder['is_allow_points'] && $commomOrder['is_use_points'] && $commomOrder['points_num'] > 0) {
                $describe = "用户消费：{$this->model['order_no']}";
                $this->user->setIncPoints(-$commomOrder['points_num'], $describe, 10);
            }
            // 通证抵扣情况下扣除用户通证
            if (isset($this->params['is_use_deduct']) && $this->params['is_use_deduct'] && $commomOrder['is_allow_deduct'] && $commomOrder['is_use_deduct'] && $commomOrder['deduct_num'] > 0 && $this->params['visit_source'] != 'agent') {
                $this->user->giftcertAmountToken($this->user['mobile'], -$commomOrder['deduct_num'], $this->user['user_id']);
            }
        }

        return $status;
    }

    /**
     * 新增订单记录
     */
    private function add($commomOrder, $supplier)
    {
        $order = $supplier['orderData'];
        // 获取团长id
        $team_id = (new AgentUserModel())->getTeamUserId($this->user);
        // 订单数据
        $data = [
            'user_id' => $this->user['user_id'],
            'order_no' => $this->model->orderNo(),
            'total_price' => $order['order_total_price'],
            'order_price' => $order['order_price'],
            'total_pv' => $order['order_total_pv'],
            'coupon_id' => $supplier['orderData']['coupon_id'],
            'coupon_money' => $supplier['orderData']['coupon_money'],
            'coupon_id_sys'=>$supplier['orderData']['coupon_id_sys'],
            'coupon_money_sys'=>$supplier['orderData']['coupon_money_sys'],
            'points_money' => isset($supplier['orderData']['points_money']) && $this->params['is_use_points'] ? $supplier['orderData']['points_money'] : 0,
            'points_num' => isset($supplier['orderData']['points_num']) && $this->params['is_use_points'] ? $supplier['orderData']['points_num'] : 0,
            'deduct_money' => isset($supplier['orderData']['deduct_money']) && isset($this->params['is_use_deduct']) && $this->params['is_use_deduct'] ? $supplier['orderData']['deduct_money'] : 0,
            'deduct_num' => isset($supplier['orderData']['deduct_num']) && isset($this->params['is_use_deduct']) && $this->params['is_use_deduct'] ? $supplier['orderData']['deduct_num'] : 0,
            'pay_price' => $order['order_pay_price'],
            'delivery_type' => $supplier['orderData']['delivery'],
            'pay_type' => $commomOrder['pay_type'],
            'pay_source' => $commomOrder['pay_source'],
            'buyer_remark' => $this->params['supplier'][$supplier['shop_supplier_id']]['remark'],
            'order_source' => $this->orderSource['source'],
            'points_bonus' => $supplier['orderData']['points_bonus'],
            'growth_value_bonus' => $supplier['orderData']['growth_value_bonus'],
            'is_agent' => $this->settledRule['is_agent']? 1:0,
            'shop_supplier_id' => $supplier['shop_supplier_id'],
            'supplier_money' => $order['supplier_money'],
            'sys_money' => $order['sys_money'],
            'app_id' => $this->app_id,
            'room_id' => $commomOrder['room_id'],
            'virtual_auto' => $order['productType'] == 1 ? $supplier['productList'][0]['virtual_auto'] : 1,
            'share_id' => isset($this->params['share_id']) && $this->params['share_id'] ? $this->params['share_id'] : 0,
            'team_id' => $team_id,
            'agent_id' => isset($this->agentUser['user_id']) ? $this->agentUser['user_id'] : 0,
            'coupon_id_agent' => isset($order['coupon_id_agent']) ? $order['coupon_id_agent'] : 0,
            'coupon_money_agent' => isset($supplier['orderData']['coupon_money_agent']) ? $supplier['orderData']['coupon_money_agent'] : 0,
            'pay_method' => 1,
            'benefit_card_money' => $order['benefit_card_money'],
            'benefit_card_id' => $order['benefit_card_id'],
        ];

        if ($supplier['orderData']['delivery'] == DeliveryTypeEnum::EXPRESS) {
            $data['express_price'] = $order['express_price'];
        } elseif ($supplier['orderData']['delivery'] == DeliveryTypeEnum::EXTRACT) {
            $data['extract_store_id'] = $order['extract_store']['store_id'];
            $data['verify_code'] = $this->model->getVerifyCode(0,1);
        }
        // 结束支付时间
        if($this->orderSource['source'] == OrderSourceEnum::SECKILL){
            //如果是秒杀
            $config = SettingModel::getItem('seckill');
            $closeMinters = $config['order_close'];
            $data['pay_end_time'] = time() + ((int)$closeMinters * 60);
        }else{
            //随主订单配置
            $config = SettingModel::getItem('trade');
            $closeDays = $config['order']['close_days'];
            $data['pay_end_time'] = time() + ((int)$closeDays * 86400);
        }
        // 如果是满减
        if(isset($order['reduce'])&&$order['reduce']){
            $data['fullreduce_money'] = $order['reduce']['reduced_price'];
            $data['fullreduce_remark'] = $order['reduce']['active_name'];
        }
        UserCouponModel::setIsUse($supplier['orderData']['coupon_id']);
        // 保存订单记录
        $this->model->save($data);
        return $this->model['order_id'];
    }

    /**
     * 记录收货地址
     */
    private function saveOrderAddress($address, $order_id)
    {
        $model = new OrderAddress();
        if ($address['region_id'] == 0 && !empty($address['district'])) {
            $address['detail'] = $address['district'] . ' ' . $address['detail'];
        }
        return $model->save([
            'order_id' => $order_id,
            'user_id' => $this->user['user_id'],
            'app_id' => $this->app_id,
            'name' => $address['name'],
            'phone' => $address['phone'],
            'province_id' => $address['province_id'],
            'city_id' => $address['city_id'],
            'region_id' => $address['region_id'],
            'detail' => $address['detail'],
        ]);
    }

    /**
     * 保存上门自提联系人
     */
    private function saveOrderExtract($linkman, $phone)
    {
        // 记忆上门自提联系人(缓存)，用于下次自动填写
        UserService::setLastExtract($this->model['user_id'], trim($linkman), trim($phone));
        // 保存上门自提联系人(数据库)
        return $this->model->extract()->save([
            'linkman' => trim($linkman),
            'phone' => trim($phone),
            'user_id' => $this->model['user_id'],
            'app_id' => $this->app_id,
        ]);
    }

    /**
     * 保存订单商品信息
     */
    private function saveOrderProduct($supplier, $status)
    {
        // 订单商品列表
        $productList = [];
        $i = 0;
        $benefit_num = 0;
        foreach ($supplier['productList'] as $product) {
            $item = [
                'order_id' => $status,
                'user_id' => $this->user['user_id'],
                'app_id' => $this->app_id,
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'image_id' => $product['image'][0]['image_id'],
                'deduct_stock_type' => $product['deduct_stock_type'],
                'spec_type' => $product['spec_type'],
                'spec_sku_id' => $product['product_sku']['spec_sku_id'],
                'product_sku_id' => $product['product_sku']['product_sku_id'],
                'product_attr' =>  isset($product['product_sku']['product_attr'])?$product['product_sku']['product_attr']:'',
                'content' => $product['content'],
                'product_no' => $product['product_sku']['product_no'],
                'product_price' => $product['product_sku']['product_price'],
                'line_price' => $product['product_sku']['line_price'],
                'pv' => $product['pv'],
                'product_weight' => $product['product_sku']['product_weight'],
                'is_user_grade' => (int)$product['is_user_grade'],
                'grade_ratio' => $product['grade_ratio'],
                'grade_product_price' => isset($product['grade_product_price'])?$product['grade_product_price']:0,
                'grade_total_money' => $product['grade_total_money'],
                'coupon_money' => isset($product['coupon_money'])?$product['coupon_money']:0,
                'points_money' => isset($product['points_money']) && $this->params['is_use_points'] ? $product['points_money'] : 0,
                'points_num' => isset($product['points_num']) && $this->params['is_use_points'] ? $product['points_num'] : 0,
                'deduct_money' => isset($product['deduct_money']) && isset($this->params['is_use_deduct']) && $this->params['is_use_deduct'] ? $product['deduct_money'] : 0,
                'deduct_num' => isset($product['deduct_num']) && isset($this->params['is_use_deduct']) && $this->params['is_use_deduct'] ? $product['deduct_num'] : 0,
                'points_bonus' => isset($product['points_bonus'])?$product['points_bonus']:0,
                'growth_value_bonus' => isset($product['growth_value_bonus'])?$product['growth_value_bonus']:0,
                'total_num' => $product['total_num'],
                'total_price' => $product['total_price'],
                'total_pv' => $product['total_pv'],
                'total_pay_price' => $product['total_pay_price'],
                'supplier_money' => $product['supplier_money'],
                'is_agent' => $product['is_agent'],
                'is_ind_agent' => $product['is_ind_agent'],
                'agent_money_type' => $product['agent_money_type'],
                'first_money' => $product['first_money'],
                'second_money' => $product['second_money'],
                'third_money' => $product['third_money'],
                'fullreduce_money' => isset($product['fullreduce_money'])?$product['fullreduce_money']:0,
                'virtual_content' => $product['virtual_content'],
                'product_type' => $product['product_type'],
                'verify_num' => intval($product['verify_num']),
                'verify_enddate' => $product['verify_limit_type'] == 1 ? strtotime($product['verify_enddate']) : 0,
                'verify_limit_type' => $product['verify_limit_type'],
                'verify_days' => $product['verify_days'],
                'store_ids' => $product['store_ids'],
                'is_gift_product' => isset($product['gift_status'])?$product['gift_status']:0,
                'benefit_id' => $product['benefit_id'],
                'benefit_card_money' => $product['benefit_card_money'] ? $product['benefit_card_money'] : 0.00,
                'card_order_product_id' => isset($this->params['benefit']['order_product_id']) ? $this->params['benefit']['order_product_id'] : 0,
                'benefit_card_id' => isset($this->params['benefit']['card_id']) ? $this->params['benefit']['card_id'] : 0,
            ];

            // 计算是否赠送CFP等等
            $giftcertProduct = (new GiftcertProductModel)->where('product_id', '=', $product['product_id'])->find();

            if ($giftcertProduct && $giftcertProduct['enable'] == 1) {
                // 获取规格数据
                $giftcertProductSku = (new GiftcertProductSkuModel)->where('product_id', '=', $product['product_id'])
                    ->where('product_sku_id', '=', $product['product_sku']['product_sku_id'])
                    ->find();

                if ($giftcertProductSku) {
                    $item['is_gift'] = 1;
                    $item['gift_stages'] = $giftcertProductSku['stages_number'];
                    $item['gift_amount'] = $giftcertProductSku['amount'];
                }
            }

            // 计次商品插入核销码
            if ($product['product_type'] == 3) {
                $item['verify_code'] = $this->model->getVerifyCode($i++);
            } elseif ($product['product_type'] == 4) {
                $benefit_num ++;
            }
            // 记录订单商品来源id
            $item['product_source_id'] = isset($product['product_source_id']) ? $product['product_source_id'] : 0;
            // 记录订单商品sku来源id
            $item['sku_source_id'] = isset($product['sku_source_id']) ? $product['sku_source_id'] : 0;
            // 记录拼团类的商品来源id
            $item['bill_source_id'] = isset($product['bill_source_id']) ? $product['bill_source_id'] : 0;
            $productList[] = $item;
        }
        //扣除权益卡使用次数
        if ($benefit_num > 0 && isset($this->params['benefit']['id']) && !empty($this->params['benefit']) && $this->params['benefit']['id'] != -1) {
            (new OrderBenefit())->setBenefitCardNum($product['benefit_id'],$this->params['benefit']['order_product_id'],$this->cardDeductionNumber);
        }
        $model = new OrderGoods();
        return $model->saveAll($productList);
    }
    /**
     * 插入出行人
    */
    private function saveOrderTravelers($commomOrder,$order_id)
    {
        $product_info = (new OrderGoods())->getOrderBenefitProduct($order_id);
        if (!$product_info) {
            return false;
        }

        foreach ($commomOrder['travelers'] as $key=>$traveler) {
            $commomOrder['travelers'][$key]['verify_code'] = $this->model->getVerifyCode($key);
        }
        // 设置默认数据
        helper::setDataAttribute($commomOrder['travelers'], [
            'order_id' => $order_id,
            'order_product_id' => $product_info['order_product_id'],
            'app_id' => $this->app_id,
            'card_order_product_id' => isset($this->params['benefit']['order_product_id']) ? $this->params['benefit']['order_product_id'] : 0
        ], true);
        $t_count = count($commomOrder['travelers']);
        // 出行人数大于可使用次数
        if ($t_count > $this->cardDeductionNumber) {
            for($i=$this->cardDeductionNumber;$i<$t_count;$i++){
                isset($commomOrder['travelers'][$i]['card_order_product_id']) && $commomOrder['travelers'][$i]['card_order_product_id'] = 0;
            }
        }
        // 保存出行人信息(循环新增出行人，循环次数为购买旅游商品数量)
        (new OrderTravelers())->saveAll($commomOrder['travelers']);
        return true;
    }
    /**
     * 计算订单可用积分抵扣
     */
    private function setOrderPoints($productList)
    {
        $this->orderData['points_money'] = 0;
        // 积分抵扣总数量
        $this->orderData['points_num'] = 0;
        // 允许积分抵扣
        $this->orderData['is_allow_points'] = false;
        // 积分商城兑换
        if (isset($this->settledRule['force_points']) && $this->settledRule['force_points']) {
            // 积分抵扣金额，商品价格-兑换金额
            $this->orderData['points_money'] = $productList[0]['points_money'];
            // 积分抵扣总数量
            $this->orderData['points_num'] = $productList[0]['points_num'];
            // 允许积分抵扣
            $this->orderData['is_allow_points'] = true;
            if ($this->user['exchangepurch'] < $productList[0]['points_num']) {
                $this->error = '积分不足，去多赚点积分吧！';
                return false;
            }
            return true;
        }
        // 积分设置
        $setting = SettingModel::getItem('exchangepurch');
        // 条件：后台开启下单使用积分抵扣
        if (!$setting['is_points'] || !$setting['is_shopping_discount']) {
            return false;
        }
        // 条件：订单金额满足[?]元
        if (helper::bccomp($setting['discount']['full_order_price'], $this->orderData['order_total_price']) === 1) {
            return false;
        }
        // 计算订单商品最多可抵扣的积分数量
        $this->setOrderProductMaxPointsNum($productList);
        // 订单最多可抵扣的积分总数量
        $maxPointsNumCount = helper::getArrayColumnSum($productList, 'max_points_num');
        // 实际可抵扣的积分数量
        $actualPointsNum = min($maxPointsNumCount, $this->user['exchangepurch']);
        if ($actualPointsNum < 1) {
            $this->orderData['points_money'] = 0;
            // 积分抵扣总数量
            $this->orderData['points_num'] = 0;
            // 允许积分抵扣
            $this->orderData['is_allow_points'] = true;
            return false;
        }
        // 计算订单商品实际抵扣的积分数量和金额
        $ProductDeduct = new PointsDeductService($productList);
        $ProductDeduct->setProductPoints($maxPointsNumCount, $actualPointsNum);
        // 积分抵扣总金额
        $orderPointsMoney = helper::getArrayColumnSum($productList, 'points_money');
        $this->orderData['points_money'] = helper::number2($orderPointsMoney);
        // 积分抵扣总数量
        $this->orderData['points_num'] = $actualPointsNum;
        // 允许积分抵扣
        $this->orderData['is_allow_points'] = true;
        return true;
    }

    /**
     * 计算订单商品最多可抵扣的积分数量
     */
    private function setOrderProductMaxPointsNum($productList)
    {
        // 积分设置
        $setting = SettingModel::getItem('exchangepurch');
        foreach ($productList as &$product) {
            // 积分兑换
            if ($this->settledRule['force_points']) {
                $product['max_points_num'] = $product['points_num'];
            } else {
                // 商品不允许积分抵扣
                if (!$product['is_points_discount'] || !$setting['discount']['discount_ratio']) continue;
                // 积分抵扣比例
                $deductionRatio = helper::bcdiv($setting['discount']['max_money_ratio'], 100);
                // 最多可抵扣的金额
                $maxPointsMoney = helper::bcmul($product['total_price'], $deductionRatio);
                // 最多可抵扣的积分数量
                $product['max_points_num'] = helper::bcdiv($maxPointsMoney, $setting['discount']['discount_ratio'], 0);
                // 如果超过商品最大抵扣数量
                if($product['max_points_discount'] != -1 && $product['max_points_num'] > $product['max_points_discount']){
                    $product['max_points_num'] = $product['max_points_discount'];
                }
            }
        }
        return true;
    }

    /**
     * 计算订单可用通证抵扣
     * @param $productList
     * @return bool
     */
    private function setOrderDeduct($productList)
    {
        $this->orderData['deduct_money'] = 0;
        // 通证抵扣总数量
        $this->orderData['deduct_num'] = 0;
        // 允许通证抵扣
        $this->orderData['is_allow_deduct'] = false;
        // 积分商城不允许通证抵扣
        if (isset($this->settledRule['force_points']) && $this->settledRule['force_points']) {
            return false;
        }
        // 抵扣设置
        $setting = SettingModel::getItem('deduct');
        // 条件：后台开启下单使用通证抵扣
        if ($setting['is_deduct']) {
            return false;
        }
        // 条件：订单金额满足[?]元
        if (helper::bccomp($setting['order_price'], $this->orderData['order_total_price']) === 1) {
            return false;
        }
        // 计算订单商品最多可抵扣的通证数量
        $this->setOrderProductMaxDeductNum($productList);
        // 订单最多可抵扣的通证总数量
        $maxDeductNumCount = helper::getArrayColumnSum($productList, 'max_deduct_num');
        // 获取用户通证账户
        $ztService = new Service();
        $blockBalance = $ztService->blockchainTimebankBalance($this->user['mobile']);
        $blockBalance = is_numeric($blockBalance) ? $blockBalance : 0;
        // 实际可抵扣的通证数量
        $actualDeductNum = min($maxDeductNumCount, $blockBalance);
        if ($actualDeductNum <= 0) {
            $this->orderData['deduct_money'] = 0;
            // 通证抵扣总数量
            $this->orderData['deduct_num'] = 0;
            // 允许通证抵扣
            $this->orderData['is_allow_deduct'] = true;
            return false;
        }
        // 计算订单商品实际抵扣的通证数量和金额
        $ProductDeduct = new PointsDeductService($productList);
        $ProductDeduct->setProductDeduct($maxDeductNumCount, $actualDeductNum);
        // 通证抵扣总金额
        $orderDeductMoney = helper::getArrayColumnSum($productList, 'deduct_money');
        $this->orderData['deduct_money'] = helper::number2($orderDeductMoney);
        // 通证抵扣总数量
        $orderDeductNum = helper::getArrayColumnSum($productList, 'deduct_num');
        $this->orderData['deduct_num'] = helper::number2($orderDeductNum);
        // 允许通证抵扣
        $this->orderData['is_allow_deduct'] = true;
        return true;
    }
    /**
     * 设置权益卡抵扣金额
    */
    public function setOrderBenefitCard($productList)
    {
        $benefit = $this->params['benefit']; // 选中权益卡信息
        if (!isset($benefit['id']) || $benefit['id'] == 0 || $benefit['id'] == -1) {
            return false;
        }
        foreach ($productList as &$product) {
            $product['benefit_card_money'] = 0;
            if ($product['product_type'] == '4') {
                // 根据剩余可抵扣次数计算抵扣金额
                $benefitCheck = [];
                if (isset($this->commonOrderData['benefit_card_list']) && !empty($this->commonOrderData['benefit_card_list'])) {
                    foreach ($this->commonOrderData['benefit_card_list'] as $benefit_card_list) {
                        if ($benefit_card_list['order_product_id'] = $benefit['order_product_id']) {
                            foreach ($benefit_card_list['OrderBenefit'] as $OrderBenefit) {
                                if ($OrderBenefit['benefit_id'] == $product['benefit_id']) {
                                    $benefitCheck = $OrderBenefit;
                                }
                            }
                        }
                    }
                }
                if (!empty($benefitCheck)) {

                    if ($benefitCheck['surplus_number'] >= $product['total_num']) {
                        $this->cardDeductionNumber = $product['total_num'];
                        // 剩余数量大于出行人数量 全部抵扣
                        $product['benefit_card_money'] = $product['total_price'];
                        $this->orderData['benefit_card_money'] += $product['total_price'];
                    } else {
                        $this->cardDeductionNumber = $benefitCheck['surplus_number'];
                        // 剩余数量小于出行人数量  部分抵扣
                        $benefit_card_money = $product['total_price']/$product['total_num']*$benefitCheck['surplus_number'];
                        $product['benefit_card_money'] = $benefit_card_money;
                        $this->orderData['benefit_card_money'] += $benefit_card_money;
                    }

                }

            }
        }
        $this->commonOrderData['order_benefit_card_id']= $benefit['id']; // 选中权益卡id
    }
    /**
     * 计算订单商品最多可抵扣的通证数量
     * @param $productList
     * @return bool
     */
    private function setOrderProductMaxDeductNum($productList)
    {
        // 抵扣设置
        $setting = SettingModel::getItem('deduct');
        foreach ($productList as &$product) {
            // 商品不允许通证抵扣
            if ($product['is_deduct'] || !$setting['deduct_money']) continue;
            // 区分商品抵扣自定义设置还是系统默认设置
            if ($product['deduct_type']) {
                // 自定义设置
                if ($product['customize_deduct']) {
                    // 抵扣比例
                    if (!$product['deduct_discount_setting']) continue;
                    // 通证抵扣比例
                    $deductionRatio = helper::bcdiv($product['deduct_discount_setting'], 100);
                    // 最多可抵扣的金额
                    $maxDeductMoney = helper::bcmul($product['total_price'], $deductionRatio);
                } else {
                    // 抵扣金额
                    if (!$product['deduct_money_setting']) continue;
                    // 最多可抵扣的金额
                    $maxDeductMoney = $product['deduct_money_setting'];
                }
            } else {
                // 通证抵扣比例
                $deductionRatio = helper::bcdiv($setting['max_money_ratio'], 100);
                // 最多可抵扣的金额
                $maxDeductMoney = helper::bcmul($product['total_price'], $deductionRatio);
            }
            // 最多可抵扣的通证数量
            $product['max_deduct_num'] = sprintf('%.5f', helper::bcdiv($maxDeductMoney, $setting['deduct_money'], 6));
        }
        return true;
    }

    /**
     * 计算订单积分赠送数量
     */
    private function setOrderPointsBonus()
    {
        // 初始化商品积分赠送数量
        foreach ($this->supplierData as &$supplier){
            foreach ($supplier['productList'] as $product){
                $product['points_bonus'] = 0;
            }
            $supplier['orderData']['points_bonus'] = 0;
        }
        // 积分设置
        $setting = SettingModel::getItem('exchangepurch');
        // 条件：后台开启开启购物送积分
        if (!$setting['is_points'] || !$setting['is_shopping_gift']) {
            return false;
        }
        // 设置商品积分赠送数量
        foreach ($this->supplierData as &$supplier) {
            foreach ($supplier['productList'] as &$product) {
                // 积分赠送比例
                $ratio = $setting['gift_ratio'] / 100;
                // 计算抵扣积分数量
                $product['points_bonus'] = !$product['is_points_gift'] ? 0 : helper::bcmul($product['total_pay_price'], $ratio, 0);
            }
            //  订单积分赠送数量
            $supplier['orderData']['points_bonus'] = helper::getArrayColumnSum($supplier['productList'], 'points_bonus');
        }
        return true;
    }

    /**
     * 计算订单成长值赠送数量
     */
    private function setOrderGrowthValueBonus()
    {
        // 初始化商品成长值赠送数量
        foreach ($this->supplierData as &$supplier) {
            foreach ($supplier['productList'] as $product) {
                $product['growth_value_bonus'] = 0;
            }
            $supplier['orderData']['growth_value_bonus'] = 0;
        }
        // 成长值设置
        $setting = SettingModel::getItem('grow');
        // 条件：后台开启开启购物送成长值
        if (!$setting['is_grow'] || !$setting['is_shopping_gift']) {
            return false;
        }
        // 设置商品成长值赠送数量
        foreach ($this->supplierData as &$supplier) {
            foreach ($supplier['productList'] as &$product) {
                // 成长值赠送比例
                $ratio = $setting['gift_ratio'] / 100;
                // 计算成长值数量
                $product['growth_value_bonus'] = helper::bcmul($product['total_pay_price'], $ratio, 0);
            }
            //  订单成长值赠送数量
            $supplier['orderData']['growth_value_bonus'] = helper::getArrayColumnSum($supplier['productList'], 'growth_value_bonus');
        }
        return true;
    }

    /**
     * 设置订单商品会员折扣价
     */
    private function setOrderGrade($productList)
    {
        if (!$this->user) {
            return false;
        }
        // 设置默认数据
        helper::setDataAttribute($productList, [
            // 标记参与会员折扣
            'is_user_grade' => false,
            // 会员等级抵扣的金额
            'grade_ratio' => 0,
            // 会员折扣的商品单价
            'grade_goods_price' => 0.00,
            // 会员折扣的总额差
            'grade_total_money' => 0.00,
        ], true);

        // 是否开启会员等级折扣
        if (!$this->settledRule['is_user_grade']) {
            return false;
        }
        // 计算抵扣金额
        foreach ($productList as &$product) {
            // 判断商品是否参与会员折扣
            if (!$product['is_enable_grade']) {
                continue;
            }
            $alone_grade_type = 10;
            // 商品单独设置了会员折扣
            if ($product['is_alone_grade'] && isset($product['alone_grade_equity'][$this->user['grade_id']])) {
                if($product['alone_grade_type'] == 10){
                    // 折扣比例
                    $discountRatio = helper::bcdiv($product['alone_grade_equity'][$this->user['grade_id']], 100);
                }else{
                    $alone_grade_type = 20;
                    $discountRatio = helper::bcdiv($product['alone_grade_equity'][$this->user['grade_id']], $product['product_price'], 2);
                }
            } else {
                // 折扣比例
                $discountRatio = helper::bcdiv($this->user['grade']['equity'], 100);
            }
            if ($discountRatio < 1) {
                // 会员折扣后的商品总金额
                if($alone_grade_type == 20){
                    // 固定金额
                    $gradeTotalPrice = $product['alone_grade_equity'][$this->user['grade_id']] * $product['total_num'];
                    $grade_product_price = $product['alone_grade_equity'][$this->user['grade_id']];
                }else{
                    $gradeTotalPrice = max(0.01, helper::bcmul($product['total_price'], $discountRatio));
                    $grade_product_price = helper::number2(helper::bcmul($product['product_price'], $discountRatio), true);
                }
                helper::setDataAttribute($product, [
                    'is_user_grade' => true,
                    'grade_ratio' => $discountRatio,
                    'grade_product_price' => $grade_product_price,
                    'grade_total_money' => helper::number2(helper::bcsub($product['total_price'], $gradeTotalPrice)),
                    'total_price' => $gradeTotalPrice,
                ], false);
            }
        }
        return true;
    }

    /**
     * 设置订单满减抵扣信息
     */
    private function setOrderFullreduceMoney($reduce, $productList)
    {
        // 计算订单商品满减抵扣金额
        $productListTemp = helper::getArrayColumns($productList, ['total_price']);
        $service = new FullDeductService;
        $completed = $service->setProductFullreduceMoney($productListTemp, $reduce['reduced_price']);
        // 分配订单商品优惠券抵扣金额
        foreach ($productList as $key => &$product) {
            $product['fullreduce_money'] = $completed[$key]['fullreduce_money'] / 100;
        }
        return true;
    }

    /**
     * 系统优惠券抵扣
     */
    private function setOrderSysCouponMoney($couponId, $couponList){
        // 设置默认数据：订单信息
        helper::setDataAttribute($this->commonOrderData, [
            'coupon_id_sys' => 0,       // 用户优惠券id
            'coupon_money_sys' => 0,    // 优惠券抵扣金额
        ], false);
        $productList = [];
        foreach ($this->supplierData as &$supplier){

            foreach ($supplier['productList'] as $product){
                array_push($productList, $product);
            }
            $supplier['orderData']['coupon_id_sys'] = 0;
            $supplier['orderData']['coupon_money_sys'] = 0;
        }
        // 设置默认数据：订单商品列表
        helper::setDataAttribute($productList, [
            'coupon_money_sys' => 0,    // 优惠券抵扣金额
        ], true);
        // 是否开启优惠券折扣
        if (!$this->settledRule['is_coupon']) {
            return false;
        }
        // 如果没有可用的优惠券，直接返回
        if ($couponId <= 0 || empty($couponList)) {
            return true;
        }
        // 获取优惠券信息
        $couponInfo = helper::getArrayItemByColumn($couponList, 'user_coupon_id', $couponId);
        if ($couponInfo == false) {
            $this->error = '未找到优惠券信息';
            return false;
        }

        // 计算订单商品优惠券抵扣金额
        $productListTemp = helper::getArrayColumns($productList, ['total_pay_price']);
        $CouponMoney = new ProductDeductService('coupon_money_sys', 'total_pay_price');
        $completed = $CouponMoney->setProductCouponMoney($productListTemp, $couponInfo['reduced_price']);
        // 分配订单商品优惠券抵扣金额
        foreach ($productList as $key => &$product) {
            if (isset($completed[$key]['coupon_money_sys'])) {
                $product['coupon_money_sys'] = $completed[$key]['coupon_money_sys'] / 100 > 0 ? $completed[$key]['coupon_money_sys'] / 100 : 0;
            }
        }
        // 统计供应商的分配额度
        foreach ($this->supplierData as &$supplier){
            $supplier['orderData']['coupon_id_sys'] = $couponId;
            $supplier['orderData']['coupon_money_sys'] = helper::getArrayColumnSum($supplier['productList'], 'coupon_money_sys') > 0 ? helper::getArrayColumnSum($supplier['productList'], 'coupon_money_sys') : 0;
        }
        // 记录订单优惠券信息
        $this->commonOrderData['coupon_id_sys'] = $couponId;
        $this->commonOrderData['coupon_money_sys'] = helper::number2($CouponMoney->getActualReducedMoney() / 100) ? helper::number2($CouponMoney->getActualReducedMoney() / 100) : 0;
        return true;
    }

    /**
     * 获取所有支付价格
     */
    private function setOrderFinalPrice(){
        $config = SettingModel::getItem('store');
        $sys_percent = intval($config['commission_rate']);
        $supplier_percent = 100 - $sys_percent;
        foreach ($this->supplierData as &$supplier){
            $coupon_money_sys = helper::getArrayColumnSum($supplier['productList'], 'coupon_money_sys');
            $supplier['orderData']['order_pay_price'] -= $coupon_money_sys;
            $coupon_money_agent = helper::getArrayColumnSum($supplier['productList'], 'coupon_money_agent');
            $supplier['orderData']['order_pay_price'] -= $coupon_money_agent;

            // 供应商结算金额，包括运费
            $supplier['orderData']['supplier_money'] = helper::number2($supplier['orderData']['order_price'] * $supplier_percent/100 + $supplier['orderData']['express_price']);
            // 平台分佣金额
            $supplier['orderData']['sys_money'] = helper::number2($supplier['orderData']['order_price'] * $sys_percent/100);
            // 产品价格
            // 结算金额不包括运费
            foreach ($supplier['productList'] as &$product){
                $product['total_pay_price'] -= $product['coupon_money_sys'];
                $product['total_pay_price'] -= $product['coupon_money_agent'];
                $product['supplier_money'] = helper::number2($product['total_pay_price'] * $supplier_percent/100);
                $product['sys_money'] = helper::number2($product['total_pay_price'] * $sys_percent/100);
            }
        }
        $price = 0;
        foreach ($this->supplierData as &$supplier){
            $price += $supplier['orderData']['order_pay_price'];
        }
        return $price;
    }

    /**
     * 检查优惠券是否可以使用
     */
    private function checkCouponCanUse($coupon, $productList){
        // 0无限制
        if($coupon['free_limit'] == 1){
            //不可与促销同时,目前只有满减
            if($this->orderData['reduce']){
                return false;
            }
        } else if($coupon['free_limit'] == 2){
            //不可与等级优惠同时
            foreach ($productList as $product){
                if($product['is_user_grade']){
                    return false;
                }
            }
        } else if($coupon['free_limit'] == 3){
            //不可与促销和等级同时
            if($this->orderData['reduce']){
                return false;
            }
            foreach ($productList as $product){
                if($product['is_user_grade']){
                    return false;
                }
            }
        }
        // 是否限制商品使用
        if($coupon['apply_range'] == 20){
            $product_ids = explode(',', $coupon['product_ids']);
            foreach ($productList as $product){
                if(!in_array($product['product_id'], $product_ids)){
                    return false;
                }else{
                    return true;
                }
            }
        }
        if($coupon['apply_range'] == 30){
            $product_ids = explode(',', $coupon['product_ids']);
            foreach ($productList as $product){
                if(in_array($product['product_id'], $product_ids)){
                    return false;
                }else{
                    return true;
                }
            }
        }
        return true;
    }
    /**
     * 检查优惠券是否可以使用
     */
    private function checkPTCouponCanUse($coupon, $productList){

        // 是否限制商品使用
        if($coupon['apply_range'] == 20){
            $product_ids = explode(',', $coupon['product_ids']);
            foreach ($productList as $product){
                if(!in_array($product['product_id'], $product_ids)){
                    return false;
                }else{
                    return true;
                }
            }
        }
        if($coupon['apply_range'] == 30){
            $product_ids = explode(',', $coupon['product_ids']);
            foreach ($productList as $product){
                if(in_array($product['product_id'], $product_ids)){
                    return false;
                }else{
                    return true;
                }
            }
        }
        return true;
    }

    public function setSplitOrder($order)
    {
        $product_type_data = [];
        foreach ($order['supplierList'] as &$supplier){
            // 获取类型集合
            $product_types = array_unique(helper::getArrayColumn($supplier['productList'], 'product_type'));
            foreach ($product_types as $key=>$product_type) {
                $supplier[$product_type]['shop_supplier_id'] = $supplier['shop_supplier_id'];
                if (!in_array($product_type,$product_type_data)) {
                    $product_type_data[] = $product_type;
                }
                $SplitOrderData = $supplier['orderData'];
                $SplitOrderData['order_pay_price'] = 0;
                $SplitOrderData['order_total_pv'] = 0;
                $SplitOrderData['points_money'] = 0;
                $SplitOrderData['points_num'] = 0;
                $SplitOrderData['coupon_money'] = 0;
                $SplitOrderData['coupon_money_sys'] = 0;
                $SplitOrderData['supplier_money'] = 0;
                $SplitOrderData['sys_money'] = 0;
                $SplitOrderData['growth_value_bonus'] = 0;
                $SplitOrderData['points_bonus'] = 0;
                $SplitOrderData['fullreduce_money'] = 0;
                $SplitOrderData['order_total_price'] = 0;
                $SplitOrderData['order_price'] = 0;
                $SplitOrderData['order_grade_money'] = 0;
                $SplitOrderData['benefit_card_id'] = 0;
                $SplitOrderData['benefit_card_money'] = 0;
                $SplitOrderData['reduce'] = [
                    'fullreduce_id' => isset($supplier['orderData']['reduce']['fullreduce_id']) ? $supplier['orderData']['reduce']['fullreduce_id'] : 0,
                    'active_name' => isset($supplier['orderData']['reduce']['active_name']) ? $supplier['orderData']['reduce']['active_name'] : '',
                    'reduced_price' => 0,
                ];
                $SplitOrderData['productType'] = $product_type;
                if ($product_type != 1) {
                    $SplitOrderData['delivery'] = 30;
                    $SplitOrderData['express_price'] = 0;
                }

                foreach ($supplier['productList'] as $product) {
                    if ($product_type == $product['product_type']) {
                        $supplier[$product_type]['productList'][] = $product;
                        $SplitOrderData['order_pay_price'] += $product['total_pay_price'] + $product['express_price'];
                        $SplitOrderData['order_total_pv'] += $product['total_pv'];
                        $SplitOrderData['points_money'] += $product['points_money'];
                        $SplitOrderData['points_num'] += $product['points_num'];
                        $SplitOrderData['coupon_money'] += $product['coupon_money'];
                        $SplitOrderData['coupon_money_sys'] += $product['coupon_money_sys'];
                        $SplitOrderData['supplier_money'] += $product['supplier_money'];
                        $SplitOrderData['sys_money'] += $product['sys_money'];
                        $SplitOrderData['growth_value_bonus'] += $product['growth_value_bonus'];
                        $SplitOrderData['points_bonus'] += $product['points_bonus'];
                        $SplitOrderData['fullreduce_money'] += $product['fullreduce_money'];
                        $SplitOrderData['order_total_price'] += $product['total_price'];
                        $SplitOrderData['order_grade_money'] += $product['grade_total_money'];
                        $SplitOrderData['benefit_card_money'] += $product['benefit_card_money'];
                        $SplitOrderData['reduce']['reduced_price'] = isset($product['fullreduce_money']) ? $product['fullreduce_money'] : 0;
                        $SplitOrderData['order_price'] += $product['Split_total_pay_price'];
                        if (isset($this->params['benefit']) && !empty($this->params['benefit']) && $product_type == 4) {
                            // 旅游商品记录抵扣权益卡id
                            $SplitOrderData['benefit_card_id'] = $this->params['benefit']['id'];
                        }
                    }

                }

                $supplier[$product_type]['orderData'] = $SplitOrderData;
                $supplier[$product_type] = $this->getSettlementPrice($supplier[$product_type]);
            }
        }
        return ['order' => $order , 'product_type_data' => $product_type_data];
    }
    private function getSettlementPrice($SplitOrderData){
        $config = SettingModel::getItem('store');
        $sys_percent = intval($config['commission_rate']);
        $supplier_percent = 100 - $sys_percent;
        // 供应商结算金额，包括运费
        $SplitOrderData['orderData']['supplier_money'] = helper::number2($SplitOrderData['orderData']['order_price'] * $supplier_percent/100 + $SplitOrderData['orderData']['express_price']);
        // 平台分佣金额
        $SplitOrderData['orderData']['sys_money'] = helper::number2($SplitOrderData['orderData']['order_price'] * $sys_percent/100);
        return $SplitOrderData;
    }

    /**
     * 系统优惠券抵扣
     */
    private function setOrderAgentCouponMoney($couponId, $couponList)
    {
        // 设置默认数据：订单信息
        helper::setDataAttribute($this->commonOrderData, [
            'coupon_id_agent' => 0,       // 用户优惠券id
            'coupon_money_agent' => 0,    // 优惠券抵扣金额
        ], false);
        $productList = [];
        foreach ($this->supplierData as &$supplier) {
            foreach ($supplier['productList'] as $product) {
                array_push($productList, $product);
            }
            $supplier['orderData']['coupon_id_agent'] = 0;
            $supplier['orderData']['coupon_money_agent'] = 0;
        }
        // 设置默认数据：订单商品列表
        helper::setDataAttribute($productList, [
            'coupon_money_agent' => 0,    // 优惠券抵扣金额
        ], true);
        // 是否开启优惠券折扣
        if (!$this->settledRule['is_coupon']) {
            return false;
        }
        // 如果没有可用的优惠券，直接返回
        if ($couponId <= 0 || empty($couponList)) {
            return true;
        }
        // 获取优惠券信息
        $couponInfo = helper::getArrayItemByColumn($couponList, 'coupon_id', $couponId);
        if ($couponInfo == false) {
            $this->error = '未找到优惠券信息';
            return false;
        }

        // 计算订单商品优惠券抵扣金额
        $productListTemp = helper::getArrayColumns($productList, ['total_pay_price']);
        $CouponMoney = new ProductDeductService('coupon_money_agent', 'total_pay_price');
        $completed = $CouponMoney->setProductCouponMoney($productListTemp, $couponInfo['reduced_price']);
        // 分配订单商品优惠券抵扣金额
        foreach ($productList as $key => &$product) {
            $product['coupon_money_agent'] = $completed[$key]['coupon_money_agent'] / 100;
        }
        // 统计供应商的分配额度
        foreach ($this->supplierData as &$supplier) {
            $supplier['orderData']['coupon_id_agent'] = $couponId;
            $supplier['orderData']['coupon_money_agent'] = helper::getArrayColumnSum($supplier['productList'], 'coupon_money_agent');
        }
        // 记录订单优惠券信息
        $this->commonOrderData['coupon_id_agent'] = $couponId;
        $this->commonOrderData['coupon_money_agent'] = helper::number2($CouponMoney->getActualReducedMoney() / 100);
        return true;
    }

    // 设置旅游商品数量
    public function setBenefitNum($productList)
    {
        $benefit_id = 0; // 商品权益集合
        $number = 0;
        $travelers_num = 0; // 出行人数量
        foreach ($productList as $product) {
            if ($product['product_type'] == 4) {
                $this->commonOrderData['benefit_num'] ++;
                $benefit_id = $product['benefit_id'];
                $number = $product['total_num'];
            } else {
                $this->commonOrderData['notBenefitNum'] ++;
            }
        }
        $number = $number == 0 ? 1 : $number;
        if ($this->commonOrderData['benefit_num'] > 1) {
            $this->error = '每单只支持一个旅游商品下单';
            return false;
        }
        // 获取支持兑换的权益卡
        if ($benefit_id > 0 &&  $this->commonOrderData['benefit_num'] == 1) {
            $this->commonOrderData['benefit_card_list'] = (new OrderGoods())->getBenefitCardList($benefit_id,$number,$this->user['user_id']);
            // 设置默认选中权益卡
            if (isset($this->params['benefit']['id']) && $this->params['benefit']['id'] == '-1') {
                return true;
            }
            if (!empty($this->commonOrderData['benefit_card_list']) && (!isset($this->params['benefit']) || empty($this->params['benefit']))) {
                $this->params['benefit']['id'] = $this->commonOrderData['benefit_card_list'][0]['product_id'];
                $this->params['benefit']['order_id'] = $this->commonOrderData['benefit_card_list'][0]['order_id'];
                $this->params['benefit']['order_product_id'] = $this->commonOrderData['benefit_card_list'][0]['order_product_id'];
                $this->params['benefit']['product_name'] = $this->commonOrderData['benefit_card_list'][0]['product_name'];
            }
        }
        return true;
    }
}