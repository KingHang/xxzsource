<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderGoods;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\model\setting\Setting as SettingModel;
use app\common\model\store\Store as StoreModel;
use app\api\service\user\UserService;
use app\common\library\helper;
use app\common\service\BaseService;
use app\common\model\order\OrderTrade as OrderTradeModel;
use app\common\service\order\OrderService;
use app\common\model\order\VerifyServerOrder;

/**
 * 订单结算服务基类
 */
abstract class OrderServerSettledService extends BaseService
{
    /* $model OrderModel 订单模型 */
    public $model;

    // 当前应用id
    protected $app_id;

    protected $user;

    // 订单结算商品列表
    protected $supplierData = [];

    protected $params;

    /**
     * 订单结算的规则
     * 主商品默认规则
     */
    protected $settledRule = [
        'is_coupon' => true,        // 优惠券抵扣
        'is_use_points' => true,        // 是否使用积分抵扣
        'force_points' => false,     // 强制使用积分，积分兑换
        'is_user_grade' => true,     // 会员等级折扣
        'is_agent' => false,     // 商品是否开启分销,最终还是支付成功后判断分销活动是否开启
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
    public function __construct($user, $supplierData, $params)
    {
        $this->model = new OrderModel;
        $this->app_id = OrderModel::$app_id;
        $this->user = $user;
        $this->supplierData = $supplierData;
        $this->params = $params;
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
        $orderPayPrice = 0;
        $this->commonOrderData = $this->getCommonOrderData();
        // 供应商
        foreach ($this->supplierData as &$supplier) {
            // 整理订单数据
            $this->orderData = $this->getOrderData($supplier['shop_supplier_id']);
            // 订单商品总数量
            $orderTotalNum += helper::getArrayColumnSum($supplier['serverList'], 'total_num');
            // 设置订单商品总金额
            $this->setOrderTotalPrice($supplier['serverList']);
            $orderTotalPrice += $this->orderData['order_total_price'];
            // 计算订单实际付款金额
            $this->setOrderProductPayPrice($supplier['serverList']);
            $orderPayPrice += $this->orderData['order_pay_price'];
            $supplier['orderData'] = $this->orderData;
        }
        //最终价格
        $orderPayPrice = $this->setOrderFinalPrice();
        //订单数据
        $this->commonOrderData = array_merge([
            'order_total_num' => $orderTotalNum,        // 商品总数量
            'order_total_price' => helper::number2($orderTotalPrice),        // 商品总价
            'order_pay_price' => helper::number2($orderPayPrice),        // 商品总价,最终支付
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
        return [
            // 默认地址
            'address' => '',
            // 是否存在收货地址
            'exist_address' => 0,
            // 是否允许使用积分抵扣
            'is_allow_points' => true,
            // 是否使用积分抵扣
            'is_use_points' => true,
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
            'pay_source' => isset($this->params['pay_source']) ? $this->params['pay_source'] : '',
            // 系统设置
            'setting' => [
                'points_name' => $pointsSetting['points_name'],      // 积分名称
            ],
            'store_id' => $this->params['store_id'],
            'distance' => isset($this->params['distance']) ? $this->params['distance'] : 0,
            'contact' => isset($this->params['contact']) ? $this->params['contact'] : '',
            'extract_store' => StoreModel::detail($this->params['store_id']),
            'remark' => $this->params['remark'],
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
        if (!$this->validateOrderForm($order)) {
            return false;
        }
        $order_arr = [];
        // 创建新的订单
        foreach ($order['supplierList'] as $supplier) {
            $this->model = new OrderModel;
            $this->model->transaction(function () use ($order, $supplier) {
                // 创建订单事件
                $this->createOrderEvent($order['orderData'], $supplier);
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
    private function setOrderTotalPrice($serverList)
    {
        // 订单商品的总金额(不含优惠券折扣)
        $this->orderData['order_total_price'] = helper::number2(helper::getArrayColumnSum($serverList, 'total_price'));
    }

    /**
     * 计算订单商品的实际付款金额
     */
    private function setOrderProductPayPrice($serverList)
    {
        // 商品总价 - 优惠抵扣
        foreach ($serverList as &$server) {
            $server['total_pay_price'] = helper::number2($server['total_price']);
        }
        $this->orderData['order_price'] = helper::number2(helper::getArrayColumnSum($serverList, 'total_pay_price'));
        // 订单实付款金额(订单金额 + 运费)
        $this->orderData['order_pay_price'] = $this->orderData['order_price'];
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
        $delivery = $deliveryType[0];

        return [
            // 配送类型
            'delivery' => $delivery,
            // 默认地址
            'address' => '',
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
            'is_use_points' => true,
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
            // 系统设置
            'setting' => [
                'delivery' => $deliveryType,     // 支持的配送方式
                'points_name' => $pointsSetting['points_name'],      // 积分名称
            ],
            // 记忆的自提联系方式
            //'last_extract' => UserService::getLastExtract($this->user['user_id']),
            'deliverySetting' => $deliveryType,
            //门店id
            'store_id' => 0,
            //优惠券id
            'coupon_id' => 0,
            //优惠金额
            'coupon_money' => 0
        ];
    }

    /**
     * 表单验证 (订单提交)
     */
    private function validateOrderForm($order)
    {
        // 判断购买者是否存在
        if (!$this->user) {
            $this->error = "购买者不能为空";
            return false;
        }
        if (!$order['orderData']['contact'] || $order['orderData']['contact']['name'] = '' || $order['orderData']['contact']['phone'] = '') {
            $this->error = "联系人和电话不能为空";
            return false;
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
        // 记录联系人信息
        $this->saveOrderExtract($commomOrder['contact']['name'], $commomOrder['contact']['phone']);
        // 保存订单商品信息
        $this->saveOrderProduct($supplier, $status);
        // 新增服务商品消单记录
//        $this->saveVerifyServer($purveyor,$status);
        return $status;
    }

    /**
     * 新增订单记录
     */
    private function add($commomOrder, $supplier)
    {
        $order = $supplier['orderData'];
        // 订单数据
        $data = [
            'user_id' => $this->user['user_id'],
            'order_no' => $this->model->orderNo(),
            'total_price' => $order['order_total_price'],
            'order_price' => $order['order_price'],
            'pay_price' => $order['order_pay_price'],
            'delivery_type' => 30,
            'pay_type' => $commomOrder['pay_type'],
            'pay_source' => $commomOrder['pay_source'],
            'buyer_remark' => $commomOrder['remark'],
            'order_source' => $this->orderSource['source'],
            'is_agent' => 0,
            'purveyor_id' => $supplier['shop_supplier_id'],
            'supplier_money' => $order['supplier_money'],
            'sys_money' => $order['sys_money'],
            'app_id' => $this->app_id,
            'virtual_auto' => 1,
            'total_pv' => $order['order_pay_price'],
            'team_id' => isset($this->params['team_id']) ? $this->params['team_id'] : 0
        ];
        //随主订单配置
        $config = SettingModel::getItem('trade');
        $closeDays = $config['order']['close_days'];
        $data['pay_end_time'] = time() + ((int)$closeDays * 86400);
        // 保存订单记录
        $this->model->save($data);
        return $this->model['order_id'];
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
        // 核销记录
        $verify_list = [];
        // 订单商品列表
        $serverList = [];
        $i = 0;
        foreach ($supplier['serverList'] as $server) {
            $verifycode = $this->model->getVerifyCode($i++);
            $item = [
                'order_id' => $status,
                'user_id' => $this->user['user_id'],
                'app_id' => $this->app_id,
                'product_id' => $server['server_id'],
                'product_name' => $server['server_name'],
                'image_id' => isset($server['server_sku']['image']['file_id']) ? $server['server_sku']['image']['file_id']: $server['image'][0]['image_id'], // 图片id
                'is_agent' => 0,
                'spec_type' => $server['spec_type'], // 产品规格(10单规格 20多规格)
                'spec_sku_id' => $server['server_sku']['spec_sku_id'], // sku标识
                'product_sku_id' => $server['server_sku']['server_sku_id'], // 商品规格id
                'product_attr' => $server['server_sku']['product_attr'], // 商品规格信息
                'content' => $server['server_desc'] ? $server['server_desc'] : '', // 商品详情
                'product_no' => $server['server_sku']['server_no'], // 商品编码
                'product_price' => $server['server_sku']['server_price'], // 商品价格(单价)
                'line_price' => $server['server_sku']['line_price'], // 商品划线价
                'product_weight' => $server['server_sku']['server_weight'], // 商品重量(Kg)
                'total_num' => $server['total_num'], // 购买数量
                'total_price' => $server['total_price'], // 商品总价(数量×单价)
                'total_pay_price' => $server['total_pay_price'], // 实际付款价(折扣和优惠后)
                'supplier_money' => $server['supplier_money'], // 供应商金额
                'order_source' => 1, // 标记商品表信息来源表 0：goods,1:server
                'category_id' => $server['category_id'],
                'server_min' => $server['server_min'],
                'verify_code' => $verifycode, // 核销码
                'store_ids' => $server['store_ids'],
                'verify_num' => 1,
                'total_pv' => $server['total_price'],
                'pv' => $server['server_sku']['server_price'],
            ];
            $verify = [
                'exchange_order_id' => $status, //订单id
                'user_id' => $this->user['user_id'], // 下单会员id
                'app_id' => $this->app_id, // 应用id
                'verify_code' => $verifycode, // 核销码（后台人工核销使用）
                'verify_num' => $server['total_num'], // 可使用次数
                'verify_limit_type' => 0, // 0永久有效 1指定日期有效 2购买n天后有效 3首次使用后n天有效  暂时只有0
                'server_id' => $server['server_id'], // 服务id
                'server_sku_id' => $server['server_sku_id'], // 服务skuid
            ];
            $verify_list[] = $verify;
            $serverList[] = $item;
        }
        $verifyModel = new VerifyServerOrder();
        $verifyModel->saveAll($verify_list);
        $model = new OrderGoods();
        return $model->saveAll($serverList);
    }
    /**
     * 新增服务商品核销记录
     * @param $supplier
     * @param $order_id
     * @return \think\Collection
     * @throws \Exception
     */
    private function saveVerifyServer($supplier, $order_id)
    {
        // 格式核销信息
        $verify_list = [];
        foreach ($supplier['serverList'] as $server) {
            $verifycode = '8' . $this->random(8, true);
            $item = [
                'exchange_order_id' => $order_id, //订单id
                'user_id' => $this->user['user_id'], // 下单会员id
                'app_id' => $this->app_id, // 应用id
                'verify_code' => $verifycode, // 核销码（后台人工核销使用）
                'verify_num' => $server['total_num'], // 可使用次数
                'verify_limit_type' => 0, // 0永久有效 1指定日期有效 2购买n天后有效 3首次使用后n天有效  暂时只有0
                'server_id' => $server['server_id'], // 服务id
                'server_sku_id' => $server['server_sku_id'], // 服务skuid
            ];
            $verify_list[] = $item;
        }
        $verifyModel = new VerifyServerOrder();
        return $verifyModel->saveAll($verify_list);
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
            // 供应商结算金额，包括运费
            $supplier['orderData']['supplier_money'] = helper::number2($supplier['orderData']['order_price'] * $supplier_percent / 100);
            // 平台分佣金额
            $supplier['orderData']['sys_money'] = helper::number2($supplier['orderData']['order_price'] * $sys_percent / 100);
            // 产品价格
            // 结算金额不包括运费
            foreach ($supplier['serverList'] as &$server) {
                $server['supplier_money'] = helper::number2($server['total_pay_price'] * $supplier_percent / 100);
                $server['sys_money'] = helper::number2($server['total_pay_price'] * $sys_percent / 100);
            }
        }
        $price = 0;
        foreach ($this->supplierData as &$supplier) {
            $price += $supplier['orderData']['order_pay_price'];
        }
        return $price;
    }
    /**
     * 生产随机字符串
     * @param $length
     * @param bool $numeric
     * @return string
     */
    private function random($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }
}