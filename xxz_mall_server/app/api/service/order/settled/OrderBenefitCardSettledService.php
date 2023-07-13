<?php

namespace app\api\service\order\settled;

use app\api\model\order\Order as OrderModel;
use app\api\model\order\OrderGoods;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\model\settings\Settings as SettingModel;
use app\common\library\helper;
use app\common\service\BaseService;
use app\common\model\order\OrderTrade as OrderTradeModel;
use app\common\service\order\OrderService;
use app\common\model\order\OrderBenefit;

/**
 * 订单结算服务基类
 */
abstract class OrderBenefitCardSettledService extends BaseService
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
        'is_agent' => false,     // 商品是否开启分销,最终还是支付成功后判断分销活动是否开启
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
            $this->orderData = $this->getOrderData();
            // 订单商品总数量
            $orderTotalNum += helper::getArrayColumnSum($supplier['cardList'], 'total_num');
            // 设置订单商品总金额
            $this->setOrderTotalPrice($supplier['cardList']);
            $orderTotalPrice += $this->orderData['order_total_price'];
            // 计算订单实际付款金额
            $this->setOrderProductPayPrice($supplier['cardList']);
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
        return [
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
            'pay_source' => isset($this->params['pay_source']) ? $this->params['pay_source'] : 'wx',
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
        return $order_arr[0]['order_id'];
    }

    /**
     * 设置订单的商品总金额(不含优惠折扣)
     */
    private function setOrderTotalPrice($cardList)
    {
        // 订单商品的总金额(不含优惠券折扣)
        $this->orderData['order_total_price'] = helper::number2(helper::getArrayColumnSum($cardList, 'total_price'));
    }

    /**
     * 计算订单商品的实际付款金额
     */
    private function setOrderProductPayPrice($cardList)
    {
        // 商品总价 - 优惠抵扣
        foreach ($cardList as &$card) {
            $card['total_pay_price'] = helper::number2($card['total_price']);
        }
        $this->orderData['order_price'] = helper::number2(helper::getArrayColumnSum($cardList, 'total_pay_price'));
        // 订单实付款金额(订单金额 + 运费)
        $this->orderData['order_pay_price'] = $this->orderData['order_price'];
        return true;
    }

    /**
     * 整理订单数据(结算台初始化)
     */
    private function getOrderData()
    {
        return [
            // 支付方式
            'pay_type' => isset($this->params['pay_type']) ? $this->params['pay_type'] : OrderPayTypeEnum::WECHAT,
        ];
    }

    /**
     * 创建订单事件
     */
    private function createOrderEvent($commomOrder, $supplier)
    {
        // 新增订单记录
        $status = $this->add($commomOrder, $supplier);
        // 保存订卡项/服务信息
        $this->saveOrderCard($supplier, $status);
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
            'buyer_remark' => isset($this->params['remark']) ? $this->params['remark'] : '',
            'order_source' => $this->orderSource['source'],
            'is_agent' => $this->settledRule['is_agent'] ? 1 : 0,
            'shop_supplier_id' => $supplier['shop_supplier_id'],
            'supplier_money' => $order['supplier_money'],
            'sys_money' => $order['sys_money'],
            'app_id' => $this->app_id,
            'virtual_auto' => 1,
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
     * 保存订单商品信息
     */
    private function saveOrderCard($supplier, $status)
    {
        // 订单卡项信息
        $card_list = [];
        $order_benefit = [];
        foreach ($supplier['cardList'] as $card) {
            $day = 0;
            $verify_enddate = 0;
            switch ($card['limit_type']) {
                case 0:
                    break;
                case 1;
                    $end_time = $card['enddate'];
                    break;
                case 2:
                    $verify_enddate = strtotime('+' . $card['days'] . ' day');
                    $day = $card['days'];
                    break;
                case 3:
                    // 首次消单时计算
                    $day = $card['days'];
                    break;
            }
            // 处理卡项信息
            $card_list = array(
                'order_id' => $status,
                'product_id' => $card['card_id'],
                'product_name' => $card['card_name'],
                'image_id' => $card['image_id'],
                'content' => $card['content'],
                'product_no' => $card['card_no'],
                'product_price' => $card['retail_price'],
                'supplier_money' => $card['supplier_money'],
                'sys_money' => $card['sys_money'],
                'order_source' => 3,
                'product_type' => 0,
                'verify_limit_type' => $card['limit_type'],
                'verify_days' => $day,
                'verify_enddate' => $verify_enddate,
                'shop_supplier_id' => $card['shop_supplier_id'],
                'total_num' => $card['total_num'], // 购买数量
                'app_id'    => $this->app_id,
                'total_price' => $card['total_price'], // 商品总价(数量×单价)
                'total_pay_price' => $card['total_pay_price'], // 实际付款价(折扣和优惠后)
                'user_id' => $this->user['user_id'],
            );
            // 处理权益
            foreach ($card['Relevance'] as $Relevance) {
                $order_benefit[] = [
                    'benefit_id' => $Relevance['benefit_id'],
                    'card_id' => $Relevance['card_id'],
                    'number' => $Relevance['number']*$card['total_num'],
                    'order_id' => $status,
                    'benefit_name' =>  $Relevance['benefit']['benefit_name'],
                    'image_id' =>  $Relevance['benefit']['image_id'],
                    'remarks' =>  $Relevance['benefit']['remarks'],
                    'app_id' =>$this->app_id,
                    'surplus_number' => $Relevance['number']*$card['total_num'],
                ];
            }
        }
        $OrderProduct = new OrderGoods();
        $OrderProduct->save($card_list);
        $order_product_id = $OrderProduct->order_product_id;
        foreach ($order_benefit as &$item) {
            $item['order_product_id'] = $order_product_id;
        }
        return (new OrderBenefit())->saveAll($order_benefit);
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
            foreach ($supplier['cardList'] as &$card) {
                $card['supplier_money'] = helper::number2($card['total_pay_price'] * $supplier_percent / 100);
                $card['sys_money'] = helper::number2($card['total_pay_price'] * $sys_percent / 100);
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