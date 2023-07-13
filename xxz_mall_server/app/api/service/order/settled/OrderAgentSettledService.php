<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderGoods;
use app\api\model\order\OrderAddress as OrderAddress;
use app\api\model\plus\coupon\UserVoucher as UserCouponModel;
use app\api\model\plus\coupon\UserVoucher;
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
use app\common\service\delivery\ExpressService;
use app\common\service\BaseService;
use app\common\service\product\factory\ProductFactory;
use app\api\model\shop\FullReduce as FullReduceModel;
use app\api\service\fullreduce\FullDeductService;
use app\api\model\order\Cart as CartModel;
use app\common\model\plugin\agent\UserCoupon as AgentUserCouponModel;
use app\common\model\order\OrderTrade as OrderTradeModel;
use app\common\service\order\OrderService;

/**
 * 订单结算服务基类
 */
abstract class OrderAgentSettledService extends BaseService
{
    /* $model OrderModel 订单模型 */
    public $model;

    // 当前应用id
    protected $app_id;

    protected $user;

    // 订单结算商品列表
    protected $supplierData = [];

    protected $params;

    protected $agentUser;
    /**
     * 订单结算的规则
     * 主商品默认规则
     */
    protected $settledRule = [
        'is_coupon' => true,        // 优惠券抵扣
        'is_use_points' => true,        // 是否使用积分抵扣
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
     * 构造函数
     */
    public function __construct($user, $supplierData, $params, $agentUser)
    {
        $this->model = new OrderModel;
        $this->app_id = OrderModel::$app_id;
        $this->user = $user;
        $this->supplierData = $supplierData;
        $this->params = $params;
        $this->agentUser = $agentUser;
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
        $orderGradeTotalPrice = 0;
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
            $orderGradeTotalPrice += $this->orderData['order_grade_total_price'];
            $this->setOrderTotalPv($supplier['productList']);
            $orderTotalPv += $this->orderData['order_total_pv'];
            if ($this->settledRule['is_coupon']) {
                // 当前用户可用的优惠券列表
                $couponList = $this->getUserCouponList($this->orderData['order_total_price'], $supplier['shop_supplier_id']);
                foreach ($couponList as $i => $coupon) {
                    if (!$this->checkCouponCanUse($coupon, $supplier['productList'])) {
                        unset($couponList[$i]);
                    }
                }
                // 计算优惠券抵扣,如果没有选择，则默认为第一个，折扣最多的
                $this->orderData['coupon_id'] = 0;
                $this->orderData['couponList'] = $couponList;
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
            // 计算订单商品的实际付款金额
            $this->setOrderProductPayPrice($supplier['productList']);
            // 处理配送方式
            if ($this->commonOrderData['deliver'] == DeliveryTypeEnum::EXPRESS) {
                $this->setOrderExpress($supplier['productList']);
                $expressPrice += $this->orderData['express_price'];
            } elseif ($this->commonOrderData['deliver'] == DeliveryTypeEnum::EXTRACT) {
                $this->orderData['store_id'] > 0 && $this->orderData['extract_store'] = StoreModel::detail($this->params['store_id']);
            }
            // 计算订单最终金额
            $this->setOrderPayPrice($supplier['productList']);
            $orderPayPrice += $this->orderData['order_pay_price'];
            $supplier['orderData'] = $this->orderData;
        }
        // 平台优惠券
        $couponList = $this->getUserCouponList($orderTotalPrice, 0);
        // 计算优惠券抵扣,如果没有选择，则默认为第一个，折扣最多的
        $coupon_id = 0;
        if ($this->params['coupon_id'] > 0) {
            $coupon_id = $this->params['coupon_id'];
        } else if ($this->params['coupon_id'] == -1) {
            // 传-1取最高的抵扣
            if (count($couponList) > 0) {
                $coupon_id = current($couponList)['user_coupon_id'];
            }
        }
        $this->setOrderSysCouponMoney($coupon_id, $couponList);
        //分销商优惠券
        $agentCouponList = (new UserVoucher())->getAgentCoupon($this->agentUser, $orderPayPrice, 0);

        // 计算优惠券抵扣,如果没有选择，则默认为第一个，折扣最多的
        $agent_coupon_id = 0;
        if ($this->params['agent_coupon_id'] > 0) {
            $agent_coupon_id = $this->params['agent_coupon_id'];
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
            'order_grade_total_price' => helper::number2($orderGradeTotalPrice),//会员折扣
            'order_total_pv' => helper::number2($orderTotalPv),        // 商品pv
            'order_pay_price' => helper::number2($orderPayPrice),        // 商品总价,最终支付
            'coupon_list' => $couponList,
            'coupon_id_sys' => $coupon_id,
            'coupon_money_sys' => 0,
            'points_money' => $totalPointsMoney,
            'points_num' => $totalPoints,
            'expressPrice' => $expressPrice,
            'agentCouponList' => $agentCouponList,
            'coupon_id_agent' => $agent_coupon_id,
            'coupon_money_agent' => 0,
        ], $this->settledRule, $this->commonOrderData);
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
        $orderType = (new CartModel($this->user))->getAgentOrderTypeList($this->params['product_list']);
        return [
            // 默认地址
            'address' => $this->params['address'],
            // 是否存在收货地址
            'exist_address' => $this->user['address_id'] > 0,
            // 是否允许使用积分抵扣
            'is_allow_points' => true,
            // 是否使用积分抵扣
            'is_use_points' => $this->params['is_use_points'],
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
            'pay_source' => isset($this->params['pay_source']) ? $this->params['pay_source'] : '',
            // 系统设置
            'setting' => [
                'points_name' => $pointsSetting['points_name'],      // 积分名称
            ],
            'store_id' => $this->params['store_id'],
            'distance' => isset($this->params['distance']) ? $this->params['distance'] : 0,
            'real_product' => $orderType && in_array('1', $orderType) ? 1 : 0,
            'time_product' => $orderType && in_array('3', $orderType) ? 1 : 0,
            'contact' => isset($this->params['contact']) ? $this->params['contact'] : '',
            'extract_store' => StoreModel::detail($this->params['store_id']),
            'deliver' => $this->params['delivery'],
            'remark' => isset($this->params['remark']) ? $this->params['remark'] : '',
        ];
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
        //拆单
        $order_arr = [];
        $orderList = (new CartModel($this->user))->getAgentOrderTypeList($this->params['product_list']);
        if (count($orderList) <= 0) {
            $this->error = '商品不能为空';
            return false;
        }
        foreach ($orderList as $orderType) {
            $total_price = 0;
            $order_price = 0;
            $pay_price = 0;
            $order_grade_money = 0;
            $order_pv = 0;
            $coupon_money = 0;
            $coupon_money_sys = 0;
            $coupon_money_agent = 0;
            $points_money = 0;
            $points_num = 0;
            $points_bonus = 0;
            $growth_value_bonus = 0;
            $supplier_money = 0;
            $sys_money = 0;
            $fullreduce_money = 0;
            $productList = [];
            $deliver = 0;
            foreach ($order['supplierList'][0]['productList'] as $product) {
                $order['orderData']['is_use_points'] == 0 && $product['points_money'] = 0;
                $order['orderData']['is_use_points'] == 0 && $product['points_num'] = 0;
                if ($orderType == $product['product_type']) {
                    $total_price += $product['total_price'];
                    $order_price += $product['total_pay_price'];
                    $order_grade_money += $product['grade_total_money'];
                    $order_pv += $product['total_pv'];
                    $coupon_money += $product['coupon_money'];
                    $coupon_money_sys += $product['coupon_money_sys'];
                    $coupon_money_agent += $product['coupon_money_agent'];
                    $points_money += $product['points_money'];
                    $points_num += $product['points_num'];
                    $points_bonus += $product['points_bonus'];
                    $growth_value_bonus += $product['growth_value_bonus'];
                    $supplier_money += $product['supplier_money'];
                    $sys_money += $product['sys_money'];
                    $fullreduce_money += $product['fullreduce_money'];
                    $productList[] = $product;
                }
            }
            $pay_price = $order_price;
            if ($orderType == 1) {
                if ($order['orderData']['deliver'] == DeliveryTypeEnum::EXPRESS) {
                    $pay_price = $pay_price + $order['supplierList'][0]['orderData']['express_price'];
                }
                $deliver = $order['orderData']['deliver'];
            } else {
                $deliver = 30;
            }
            $supplier = [
                'total_price' => $total_price,
                'order_price' => $order_price,
                'pay_price' => $pay_price,
                'order_grade_money' => $order_grade_money,
                'order_pv' => $order_pv,
                'coupon_money' => $coupon_money,
                'coupon_money_sys' => $coupon_money_sys,
                'coupon_money_agent' => $coupon_money_agent,
                'points_money' => $points_money,
                'points_num' => $points_num,
                'points_bonus' => $points_bonus,
                'growth_value_bonus' => $growth_value_bonus,
                'supplier_money' => $supplier_money,
                'sys_money' => $sys_money,
                'fullreduce_money' => $fullreduce_money,
                'productList' => $productList,
                'shop_supplier_id' => $this->params['shop_supplier_id'],
                'deliver' => $deliver,
                'productType' => $orderType,
            ];
            $this->model = new OrderModel;
            $this->model->transaction(function () use ($order, $supplier) {
                // 创建订单事件
                $this->createOrderEvent($order, $supplier);
            });
            array_push($order_arr, $this->model);

        }
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

    /**
     * 设置订单的商品总金额(不含优惠折扣)
     */
    private function setOrderTotalPrice($productList)
    {
        // 订单商品的总金额(不含优惠券折扣)
        $this->orderData['order_total_price'] = helper::number2(helper::getArrayColumnSum($productList, 'total_price'));
        $this->orderData['order_grade_total_price'] = helper::number2(helper::getArrayColumnSum($productList, 'grade_total_money'));
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
        $CouponMoney = new ProductDeductService('coupon_money', 'total_price');
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
            // 减去满减金额
            if ($this->settledRule['is_reduce'] && $this->orderData['reduce']) {
                $value = helper::bcsub($value, $product['fullreduce_money']);
            }
            $product['total_pay_price'] = helper::number2($value);
        }

        return true;
    }

    /**
     * 整理订单数据(结算台初始化)
     */
    private function getOrderData($shop_supplier_id)
    {
        return [
            // 默认地址
            'address' => $this->params['address'],
            // 是否存在收货地址
            'exist_address' => 0,
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
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
            //门店id
            'store_id' => $this->params['store_id'],
            //优惠券id
            'coupon_id' => 0,
            //优惠金额
            'coupon_money' => 0,
            'reduce' => '',
        ];
    }

    /**
     * 订单配送-快递配送
     */
    private function setOrderExpress($productList)
    {
        // 设置默认数据：配送费用
        helper::setDataAttribute($productList, [
            'express_price' => 0,
        ], true);
        // 当前用户收货城市id
        $cityId = isset($this->params['address']) && $this->params['address'] && $this->params['address']['city_id'] > 0 ? $this->params['address']['city_id'] : '';

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
        // 判断购买者是否存在
        if (!$this->user) {
            $this->error = "购买者不能为空";
            return false;
        }
        if ($order['orderData']['deliver'] == 10 && $order['orderData']['real_product'] == 1) {
            if (!$order['orderData']['address']['name'] || !$order['orderData']['address']['phone'] || !$order['orderData']['address']['region_id'] || !$order['orderData']['address']['detail']) {
                $this->error = "请填写地址";
                return false;
            }
        }
        return true;
    }

    /**
     * 创建订单事件
     */
    private function createOrderEvent($commomOrder, $supplier)
    {
        // 新增订单记录
        $status = $this->add($commomOrder, $supplier);
        if ($supplier['deliver'] == DeliveryTypeEnum::EXPRESS) {
            // 记录收货地址
            $this->saveOrderAddress($status);
        } else {
            // 记录自提信息
            $this->saveOrderExtract($commomOrder['orderData']['contact']['name'], $commomOrder['orderData']['contact']['phone']);
        }
        // 保存订单商品信息
        $this->saveOrderProduct($supplier, $status);

        // 更新商品库存 (针对下单减库存的商品)
        ProductFactory::getFactory($this->orderSource['source'])->updateProductStock($supplier['productList']);
        // 设置平台优惠券使用状态
        UserCouponModel::setIsUse($this->params['coupon_id']);
        // 设置分销商优惠券使用状态
        AgentUserCouponModel::setIsUse($this->params['agent_coupon_id'], $this->agentUser);
        // 积分兑换扣除用户积分
        if ($commomOrder['orderData']['force_points']) {
            $describe = "用户积分兑换消费：{$this->model['order_no']}";
            $this->user->setIncPoints(-$commomOrder['orderData']['points_num'], $describe);
        } else {
            // 积分抵扣情况下扣除用户积分
            if ($commomOrder['orderData']['is_allow_points'] && $commomOrder['orderData']['is_use_points'] && $commomOrder['orderData']['points_num'] > 0) {
                $describe = "用户消费：{$this->model['order_no']}";
                $this->user->setIncPoints(-$commomOrder['orderData']['points_num'], $describe);
            }
        }
        return $status;
    }

    /**
     * 新增订单记录
     */
    private function add($commomOrder, $supplier)
    {
        $order = $commomOrder['supplierList'][0]['orderData'];
        // 订单数据
        $data = [
            'user_id' => $this->user['user_id'],
            'order_no' => $this->model->orderNo(),
            'total_price' => $supplier['total_price'],
            'order_price' => $supplier['order_price'],
            'order_grade_money' => $supplier['order_grade_money'],
            'total_pv' => $supplier['order_pv'],
            'coupon_id' => $order['coupon_id'],
            'coupon_money' => $supplier['coupon_money'],
            'coupon_id_sys' => $order['coupon_id_sys'],
            'coupon_money_sys' => $supplier['coupon_money_sys'],
            'coupon_id_agent' => $order['coupon_id_sys'],
            'coupon_money_agent' => $supplier['coupon_money_agent'],
            'points_money' => $supplier['points_money'],
            'points_num' => $supplier['points_num'],
            'pay_price' => $supplier['pay_price'],
            'delivery_type' => $supplier['deliver'],
//            'pay_type' => $commomOrder['pay_type'],
//            'pay_source' => $commomOrder['pay_source'],
            'buyer_remark' => $commomOrder['orderData']['remark'],
            'order_source' => $this->orderSource['source'],
            'points_bonus' => $supplier['points_bonus'],
            'growth_value_bonus' => $supplier['growth_value_bonus'],
            'is_agent' => $this->settledRule['is_agent'] ? 1 : 0,
            'shop_supplier_id' => $supplier['shop_supplier_id'],
            'supplier_money' => $supplier['supplier_money'],
            'sys_money' => $supplier['sys_money'],
            'app_id' => $this->app_id,
            'agent_id' => $this->agentUser['user_id'],
            'virtual_auto' => $supplier['productType'] == 1 ? 0 : 1,
            'team_id' => isset($this->params['team_id']) ? $this->params['team_id'] : 0
        ];
        if ($supplier['deliver'] == DeliveryTypeEnum::EXPRESS) {
            $data['express_price'] = $order['express_price'];
        } elseif ($supplier['deliver'] == DeliveryTypeEnum::EXTRACT) {
            $data['extract_store_id'] = $commomOrder['orderData']['extract_store']['store_id'];
            $data['verify_code'] = $this->model->getVerifyCode(0,1);
        }
        // 结束支付时间
        if ($this->orderSource['source'] == OrderSourceEnum::SECKILL) {
            //如果是秒杀
            $config = SettingModel::getItem('seckill');
            $closeMinters = $config['order_close'];
            $data['pay_end_time'] = time() + ((int)$closeMinters * 60);
        } else {
            //随主订单配置
            $config = SettingModel::getItem('trade');
            $closeDays = $config['order']['close_days'];
            $data['pay_end_time'] = time() + ((int)$closeDays * 86400);
        }
        // 如果是满减
        if (isset($order['reduce']) && $order['reduce']) {
            $data['fullreduce_money'] = $supplier['fullreduce_money'];
            $data['fullreduce_remark'] = $order['reduce']['active_name'];
        }
        UserCouponModel::setIsUse($order['coupon_id']);
        // 保存订单记录
        $this->model->save($data);
        return $this->model['order_id'];
    }

    /**
     * 记录收货地址
     */
    private function saveOrderAddress($order_id)
    {
        $model = new OrderAddress();
        $address = $this->params['address'];
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
                'product_attr' => $product['product_sku']['product_attr'],
                'content' => $product['content'],
                'product_no' => $product['product_sku']['product_no'],
                'product_price' => $product['product_sku']['product_price'],
                'line_price' => $product['product_sku']['line_price'],
                'pv' => $product['pv'],
                'product_weight' => $product['product_sku']['product_weight'],
                'is_user_grade' => (int)$product['is_user_grade'],
                'grade_ratio' => $product['grade_ratio'],
                'grade_product_price' => isset($product['grade_product_price']) ? $product['grade_product_price'] : 0,
                'grade_total_money' => $product['grade_total_money'],
                'coupon_money' => isset($product['coupon_money']) ? $product['coupon_money'] : 0,
                'points_money' => isset($product['points_money']) ? $product['points_money'] : 0,
                'points_num' => isset($product['points_num']) ? $product['points_num'] : 0,
                'points_bonus' => isset($product['points_bonus']) ? $product['points_bonus'] : 0,
                'growth_value_bonus' => isset($product['growth_value_bonus']) ? $product['growth_value_bonus'] : 0,
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
                'fullreduce_money' => isset($product['fullreduce_money']) ? $product['fullreduce_money'] : 0,
                'virtual_content' => $product['virtual_content'],
                'virtual_auto' => $product['virtual_auto'],
                'product_type' => $product['product_type'],
                'verify_num' => intval($product['verify_num']) * $product['total_num'],
                'verify_limit_type' => $product['verify_limit_type'],
                'verify_days' => $product['verify_days'],
                'store_ids' => $product['store_ids'],
                'verify_enddate' => $product['verify_limit_type'] == 1 ? strtotime($product['verify_enddate']) : 0,
            ];
            // 计次商品插入核销码
            if ($product['product_type'] == 3) {
                $item['verify_code'] = $this->model->getVerifyCode($i++);
            }
            $productList[] = $item;
        }

        $model = new OrderGoods();
        return $model->saveAll($productList);
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
                if (!$product['is_points_discount']) continue;
                // 积分抵扣比例
                $deductionRatio = helper::bcdiv($setting['discount']['max_money_ratio'], 100);
                // 最多可抵扣的金额
                $maxPointsMoney = helper::bcmul($product['total_price'], $deductionRatio);
                // 最多可抵扣的积分数量
                $product['max_points_num'] = helper::bcdiv($maxPointsMoney, $setting['discount']['discount_ratio'], 0);
                // 如果超过商品最大抵扣数量
                if ($product['max_points_discount'] != -1 && $product['max_points_num'] > $product['max_points_discount']) {
                    $product['max_points_num'] = $product['max_points_discount'];
                }

            }
        }
        return true;
    }


    /**
     * 计算订单积分赠送数量
     */
    private function setOrderPointsBonus()
    {
        // 初始化商品积分赠送数量
        foreach ($this->supplierData as &$supplier) {
            foreach ($supplier['productList'] as $product) {
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
        if (!$this->user) {
            return false;
        }
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
                if ($product['alone_grade_type'] == 10) {
                    // 折扣比例
                    $discountRatio = helper::bcdiv($product['alone_grade_equity'][$this->user['grade_id']], 100);
                } else {
                    $alone_grade_type = 20;
                    $discountRatio = helper::bcdiv($product['alone_grade_equity'][$this->user['grade_id']], $product['product_price'], 2);
                }
            } else {
                // 折扣比例
                $discountRatio = helper::bcdiv($this->user['grade']['equity'], 100);
            }

            if ($discountRatio < 1) {
                // 会员折扣后的商品总金额
                if ($alone_grade_type == 20) {
                    // 固定金额
                    $gradeTotalPrice = $product['alone_grade_equity'][$this->user['grade_id']] * $product['total_num'];
                    $grade_product_price = $product['alone_grade_equity'][$this->user['grade_id']];
                } else {
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
    private function setOrderSysCouponMoney($couponId, $couponList)
    {
        // 设置默认数据：订单信息
        helper::setDataAttribute($this->commonOrderData, [
            'coupon_id_sys' => 0,       // 用户优惠券id
            'coupon_money_sys' => 0,    // 优惠券抵扣金额
        ], false);
        $productList = [];
        foreach ($this->supplierData as &$supplier) {
            foreach ($supplier['productList'] as $product) {
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
            $product['coupon_money_sys'] = $completed[$key]['coupon_money_sys'] / 100;
        }
        // 统计供应商的分配额度
        foreach ($this->supplierData as &$supplier) {
            $supplier['orderData']['coupon_id_sys'] = $couponId;
            $supplier['orderData']['coupon_money_sys'] = helper::getArrayColumnSum($supplier['productList'], 'coupon_money_sys');
        }
        // 记录订单优惠券信息
        $this->commonOrderData['coupon_id_sys'] = $couponId;
        $this->commonOrderData['coupon_money_sys'] = helper::number2($CouponMoney->getActualReducedMoney() / 100);
        return true;
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

    /**
     * 获取所有支付价格
     */
    private function setOrderFinalPrice()
    {
        $config = SettingModel::getItem('store');
        $sys_percent = intval($config['commission_rate']);
        $supplier_percent = 100 - $sys_percent;
        foreach ($this->supplierData as &$supplier) {
            $coupon_money_sys = helper::getArrayColumnSum($supplier['productList'], 'coupon_money_sys');
            $supplier['orderData']['order_pay_price'] -= $coupon_money_sys;
            $coupon_money_agent = helper::getArrayColumnSum($supplier['productList'], 'coupon_money_agent');
            $supplier['orderData']['order_pay_price'] -= $coupon_money_agent;
            // 供应商结算金额，包括运费
            $supplier['orderData']['supplier_money'] = helper::number2($supplier['orderData']['order_price'] * $supplier_percent / 100 + $supplier['orderData']['express_price']);
            // 平台分佣金额
            $supplier['orderData']['sys_money'] = helper::number2($supplier['orderData']['order_price'] * $sys_percent / 100);
            // 产品价格
            // 结算金额不包括运费
            foreach ($supplier['productList'] as &$product) {
                $product['total_pay_price'] -= $product['coupon_money_sys'];
                $product['total_pay_price'] -= $product['coupon_money_agent'];
                $product['supplier_money'] = helper::number2($product['total_pay_price'] * $supplier_percent / 100);
                $product['sys_money'] = helper::number2($product['total_pay_price'] * $sys_percent / 100);
            }
        }
        $price = 0;
        foreach ($this->supplierData as &$supplier) {
            $price += $supplier['orderData']['order_pay_price'];
        }
        return $price;
    }

    /**
     * 检查优惠券是否可以使用
     */
    private function checkCouponCanUse($coupon, $productList)
    {
        // 0无限制
        if ($coupon['free_limit'] == 1) {
            //不可与促销同时,目前只有满减
            if ($this->orderData['reduce']) {
                return false;
            }
        } else if ($coupon['free_limit'] == 2) {
            //不可与等级优惠同时
            foreach ($productList as $product) {
                if ($product['is_user_grade']) {
                    return false;
                }
            }
        } else if ($coupon['free_limit'] == 3) {
            //不可与促销和等级同时
            if ($this->orderData['reduce']) {
                return false;
            }
            foreach ($productList as $product) {
                if ($product['is_user_grade']) {
                    return false;
                }
            }
        }
        // 是否限制商品使用
        if ($coupon['apply_range'] == 20) {
            $product_ids = explode(',', $coupon['product_ids']);
            foreach ($productList as $product) {
                if (!in_array($product['product_id'], $product_ids)) {
                    return false;
                }
            }
        }
        if ($coupon['apply_range'] == 30) {
            $product_ids = explode(',', $coupon['product_ids']);
            foreach ($productList as $product) {
                if (in_array($product['product_id'], $product_ids)) {
                    return false;
                }
            }
        }
        return true;
    }
}