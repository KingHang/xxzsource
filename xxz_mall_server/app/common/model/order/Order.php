<?php

namespace app\common\model\order;

use app\api\controller\plus\assemble\Bill;
use app\common\enum\order\OrderSourceEnum;
use app\common\model\BaseModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\enum\order\OrderTypeEnum;
use app\common\enum\order\OrderPayTypeEnum;
use app\common\library\helper;
use app\common\service\order\OrderService;
use app\common\service\order\OrderCompleteService;
use app\common\model\store\Order as StoreOrderModel;
use app\common\model\order\OrderGoods;
use app\common\service\message\MessageService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\facade\Filesystem;
use app\common\model\settings\Express as ExpressModel;

/**
 * 订单模型模型
 */
class Order extends BaseModel
{
    protected $pk = 'order_id';
    protected $name = 'order';

    /**
     * 追加字段
     * @var string[]
     */
    protected $append = [
        'state_text',
        'order_source_text',
        'pay_time_text',
        'delivery_time_text',
        'receipt_time_text',
        'cancel_time_text'
    ];

    /**
     * 订单商品列表
     */
    public function product()
    {
        return $this->hasMany('app\\common\\model\\order\\OrderGoods', 'order_id', 'order_id')->hidden(['content']);
    }


    /**
     * 关联订单收货地址表
     */
    public function address()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderAddress');
    }

    /**
     * 关联自提订单联系方式
     */
    public function extract()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderExtract');
    }

    /**
     * 关联物流公司表
     */
    public function express()
    {
        return $this->belongsTo('app\\common\\model\\settings\\Express', 'express_id', 'express_id');
    }

    /**
     * 关联自提门店表
     */
    public function extractStore()
    {
        return $this->belongsTo('app\\common\\model\\store\\Store', 'extract_store_id', 'store_id');
    }

    /**
     * 关联门店店员表
     */
    public function extractClerk()
    {
        return $this->belongsTo('app\\common\\model\\store\\Clerk', 'extract_clerk_id');
    }

    /**
     * 关联用户表
     */
    public function user()
    {
        return $this->belongsTo('app\\common\\model\\user\\User', 'user_id', 'user_id');
    }

    /**
     * 关联用户表
     */
    public function room()
    {
        return $this->belongsTo('app\\common\\model\\plus\\live\\Room', 'room_id', 'room_id');
    }

    /**
     * 关联供应商表
     */
    public function supplier()
    {
        return $this->belongsTo('app\\common\\model\\purveyor\\Purveyor', 'shop_supplier_id', 'shop_supplier_id')->field(['shop_supplier_id', 'name', 'user_id']);
    }
    /**
     * 订单卡项表
     */
    public function orderCarditem()
    {
        return $this->hasMany('app\\common\\model\\order\\FaceOrdeCarditem', 'order_id', 'order_id');
    }
    /**
     * 关联拼团
     */
    public function billuser()
    {
        return $this->belongsTo('app\\common\\model\\plus\\assemble\\BillUser', 'order_id', 'order_id');
    }
    /**
     * 订单状态文字描述
     * @param $value
     * @param $data
     * @return string
     */
    public function getStateTextAttr($value, $data)
    {
        if (!isset($data['order_status']) || !isset($data['pay_status']) || !isset($data['order_source']) || !isset($data['delivery_status']) || !isset($data['receipt_status'])) {
            return false;
        }
        // 订单状态
        if (in_array($data['order_status'], [20,21, 30, 40])) {
            $orderStatus = [20 => '已关闭', 30 => '已完成', 40 => '退换货', 21 => '待取消'];
            return $orderStatus[$data['order_status']];
        }
        // 付款状态
        if ($data['pay_status'] == 10) {
            return '待付款';
        }
        // 拼团状态
        if ($data['order_source'] == OrderSourceEnum::ASSEMBLE) {
            $assemble_status = $this->getAssembleStatus($data);
            if ($assemble_status != '') {
                return $assemble_status;
            }
        }
        // 发货状态
        if ($data['delivery_status'] == 10) {
            return $data['send_type'] == 1 ? '部分发货' : '待发货';
        }
        if ($data['receipt_status'] == 10) {
            return '待收货';
        }
        return false;
    }

    /**
     *  拼团订单状态
     */
    private function getAssembleStatus($data)
    {
        // 发货状态
        if ($data['assemble_status'] == 10) {
            return '拼团中';
        }
        if ($data['assemble_status'] == 20 && $data['delivery_status'] == 10) {
            return '拼团成功，待发货';
        }
        if ($data['assemble_status'] == 30) {
            return '拼团失败';
        }
        return '';
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getPayTypeAttr($value, $data)
    {
        if ($data['pay_method'] == 1) {
            return ['text' => OrderPayTypeEnum::data()[$value]['name'], 'value' => $value];
        } else {
            return ['text' => '店员代付', 'value' => 20];
        }

    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getPayTimeTextAttr($value, $data)
    {
        return isset($data['pay_time']) && is_numeric($data['pay_time']) && $data['pay_time'] > 0 ? date('Y-m-d H:i:s', $data['pay_time']) : '';
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getCancelTimeTextAttr($value, $data)
    {
        return isset($data['cancel_time']) && is_numeric($data['cancel_time']) && $data['cancel_time'] > 0 ? date('Y-m-d H:i:s', $data['cancel_time']) : '';
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getDeliveryTimeTextAttr($data)
    {
        return $data['delivery_time'] ? date('Y-m-d H:i:s', $data['delivery_time']) : '';
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getReceiptTimeTextAttr($data)
    {
        return $data['receipt_time'] ? date('Y-m-d H:i:s', $data['receipt_time']) : '';
    }

    /**
     * 订单来源
     * @param $value
     * @return array
     */
    public function getOrderSourceTextAttr($value, $data)
    {
        if (empty($data)) {
            return false;
        }
        return OrderSourceEnum::data()[$data['order_source']]['name'];
    }

    /**
     * 付款状态
     * @param $value
     * @return array
     */
    public function getPayStatusAttr($value)
    {
        return ['text' => OrderPayStatusEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 改价金额（差价）
     * @param $value
     * @return array
     */
    public function getUpdatePriceAttr($value)
    {
        return [
            'symbol' => $value < 0 ? '-' : '+',
            'value' => sprintf('%.2f', abs($value))
        ];
    }

    /**
     * 发货状态
     * @param $value
     * @return array
     */
    public function getDeliveryStatusAttr($value)
    {
        $status = [10 => '待发货', 20 => '已发货'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 收货状态
     * @param $value
     * @return array
     */
    public function getReceiptStatusAttr($value)
    {
        $status = [10 => '待收货', 20 => '已收货'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 收货状态
     * @param $value
     * @return array
     */
    public function getOrderStatusAttr($value)
    {
        $status = [10 => '进行中', 20 => '已取消', 21 => '待取消', 30 => '已完成', 40 => '退换货'];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 配送方式
     * @param $value
     * @return array
     */
    public function getDeliveryTypeAttr($value)
    {
        return ['text' => DeliveryTypeEnum::data()[$value]['name'], 'value' => $value];
    }

    /**
     * 订单详情
     * @param $where
     * @param string[] $with
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function detail($where, $with = ['user', 'address', 'product' => ['image', 'refund', 'express','OrderTravelers'], 'extract', 'express', 'extractStore.logo', 'extractClerk', 'supplier','orderCarditem.Carditem.CarditemServer.server'])
    {
        is_array($where) ? $filter = $where : $filter['order_id'] = (int)$where;
        return (new static())->with($with)->where($filter)->find();
    }

    /**
     * 订单详情
     */
    public static function detailByNo($order_no, $with = ['user', 'address', 'product' => ['image', 'refund'], 'extract', 'express', 'extractStore.logo', 'extractClerk', 'supplier'])
    {
        return (new static())->with($with)->where('order_no', '=', $order_no)->find();
    }

    /**
     * 批量获取订单列表
     */
    public function getListByIds($orderIds, $with = [])
    {
        $data = $this->getListByInArray('order_id', $orderIds, $with);
        return helper::arrayColumn2Key($data, 'order_id');
    }

    /**
     * 批量更新订单
     */
    public function onBatchUpdate($orderIds, $data)
    {
        return $this->where('order_id', 'in', $orderIds)->save($data);
    }

    /**
     * 批量获取订单列表
     */
    private function getListByInArray($field, $data, $with = [])
    {
        return $this->with($with)
            ->where($field, 'in', $data)
            ->where('is_delete', '=', 0)
            ->select();
    }

    /**
     * 生成订单号
     */
    public function orderNo()
    {
        return OrderService::createOrderNo();
    }
    public function getVerifyCode($number = 0 , $type = 0)
    {
        $max_id = $type == 0 ?(new OrderGoods())->getMaxId() : $this->getMaxId();
        $max_id += $number;
        $len = strlen($max_id);
        if ($len <= 9) {
            // 不足九位补齐
            $diff = 9 - $len;
            for ($i=0;$i<$diff;$i++)
            {
                $max_id .= rand(0,9);
            }
            return $max_id;
        } else {
            // 超出八位截取
            return substr($max_id,$len - 9);
        }
    }
    public function getMaxId()
    {
        return $this->max('order_id');
    }
    /**
     * 确认核销（自提订单）
     */
    public function verificationOrder($extractClerkId)
    {
        if (
            $this['pay_status']['value'] != 20
            || $this['delivery_type']['value'] != DeliveryTypeEnum::EXTRACT
            || $this['delivery_status']['value'] == 20
            || in_array($this['order_status']['value'], [20, 21])
        ) {
            $this->error = '该订单不满足核销条件';
            return false;
        }
        return $this->transaction(function () use ($extractClerkId) {
            // 更新订单状态：已发货、已收货
            $status = $this->save([
                'extract_clerk_id' => $extractClerkId,  // 核销员
                'delivery_status' => 20,
                'delivery_time' => time(),
                'receipt_status' => 20,
                'receipt_time' => time(),
                'order_status' => 30
            ]);
            // 新增订单核销记录
            StoreOrderModel::add(
                $this['order_id'],
                $this['extract_store_id'],
                $this['extract_clerk_id'],
                $this['shop_supplier_id'],
                OrderTypeEnum::MASTER
            );
            // 执行订单完成后的操作
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$this], static::$app_id);
            return $status;
        });
    }


    /**
     * 获取已付款订单总数 (可指定某天)
     */
    public function getOrderData($startDate = null, $endDate = null, $type, $shop_supplier_id = 0)
    {
        $model = $this;

        !is_null($startDate) && $model = $model->where('pay_time', '>=', strtotime($startDate));

        if (is_null($endDate)) {
            !is_null($startDate) && $model = $model->where('pay_time', '<', strtotime($startDate) + 86400);
        } else {
            $model = $model->where('pay_time', '<', strtotime($endDate) + 86400);
        }

        if ($shop_supplier_id > 0) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }

        $model = $model->where('is_delete', '=', 0)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20);


        if ($type == 'order_total') {
            // 订单数量
            return $model->count();
        } else if ($type == 'order_total_price') {
            // 订单总金额
            return $model->sum('pay_price');
        } else if ($type == 'order_user_total') {
            // 支付用户数
            return count($model->distinct(true)->column('user_id'));
        }
        return 0;
    }

    /**
     * 修改订单价格
     */
    public function updatePrice($data)
    {
        if ($this['pay_status']['value'] != 10) {
            $this->error = '该订单不合法';
            return false;
        }
        if ($this['order_source'] != 10) {
            $this->error = '该订单不合法';
            return false;
        }
        // 实际付款金额
        $payPrice = bcadd($data['update_price'], $data['update_express_price'], 2);
        if ($payPrice <= 0) {
            $this->error = '订单实付款价格不能为0.00元';
            return false;
        }
        return $this->save([
                'order_no' => $this->orderNo(), // 修改订单号, 否则微信支付提示重复
                'order_price' => $data['update_price'],
                'pay_price' => $payPrice,
                'update_price' => helper::bcsub($data['update_price'], helper::bcsub($this['total_price'], $this['coupon_money'])),
                'express_price' => $data['update_express_price']
            ]) !== false;
    }

    /**
     * 所有支付金额
     */
    public function getTotalPayMoney($year){
        $startTime = strtotime($year.'-01-01 00:00:00');
        $endTime = strtotime($year.'-12-31 23:59:59');
        return $this->where('pay_status', '=', 20)
            ->where('order_status', 'in', [10,30])
            ->whereBetweenTime('create_time', $startTime, $endTime)
            ->sum('pay_price');
    }
    public function checkOrderStatus($order_id)
    {
        return !!$this->where(['order_status' => [10 , 30], 'pay_status' => 20 , 'order_id' => $order_id])->value('order_id');
    }
    /**
     * 确认发货(单独订单)
     */
    public function delivery($data)
    {
        // 转义为订单列表
        $orderList = [$this];
        // 验证订单是否满足发货条件
        if (!$this->verifyDelivery($orderList)) {
            return false;
        }
        // 整理更新的数据
        $updateList = [[
            'order_id' => $this['order_id'],
            'express_id' => $data['express_id'],
            'express_no' => $data['express_no']
        ]];
        // 更新订单发货状态
        if ($status = $this->updateToDelivery($updateList)) {
            // 获取已发货的订单
            $completed = self::detail($this['order_id'], ['user', 'address', 'product', 'express']);
            // 发送消息通知
            $this->sendDeliveryMessage([$completed]);
        }
        return $status;
    }

    /**
     * 确认发货后发送消息通知
     */
    private function sendDeliveryMessage($orderList)
    {
        // 实例化消息通知服务类
        $Service = new MessageService;
        foreach ($orderList as $item) {
            // 发送消息通知
            $Service->delivery($item, OrderTypeEnum::MASTER);
        }
        return true;
    }

    /**
     * 更新订单发货状态(批量)
     */
    private function updateToDelivery($orderList)
    {
        $data = [];
        foreach ($orderList as $item) {
            $data[] = [
                'order_id' => $item['order_id'],
                'express_no' => $item['express_no'],
                'express_id' => $item['express_id'],
                'delivery_status' => 20,
                'delivery_time' => time(),
            ];
        }
        return $this->saveAll($data);
    }

    /**
     * 验证订单是否满足发货条件
     */
    private function verifyDelivery($orderList)
    {
        foreach ($orderList as $order) {
            if (
                $order['pay_status']['value'] != 20
                || $order['delivery_type']['value'] != DeliveryTypeEnum::EXPRESS
                || $order['delivery_status']['value'] != 10
                || $order['order_status']['value'] != 10
            ) {
                $this->error = "订单号[{$order['order_no']}] 不满足发货条件!";
                return false;
            }
        }
        return true;
    }

    /**
     * 批量发货
     */
    public function batchDelivery($fileInfo, $shop_supplier_id)
    {
        try {
            $saveName = Filesystem::disk('public')->putFile('', $fileInfo);
            $savePath = public_path() . "uploads/{$saveName}";
            //载入excel表格
            $inputFileType = IOFactory::identify($savePath); //传入Excel路径
            $reader = IOFactory::createReader($inputFileType);
            $PHPExcel = $reader->load($savePath);
            $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
            // 遍历并记录订单信息
            $list = [];
            $orderList = [];
            foreach ($sheet->toArray() as $key => $val) {
                if ($key > 0) {
                    if ($val[19] && $val[20]) {
                        // 查找发货公司是否存在
                        $express = ExpressModel::findByName(trim($val[19]));
                        $order = self::detail(['order_no' => trim($val[0])], ['user', 'address', 'product', 'express']);
                        if ($express && $order) {
                            if ($order['shop_supplier_id'] == $shop_supplier_id) {
                                $list[] = [
                                    'data' => [
                                        'express_no' => trim($val[20]),
                                        'express_id' => $express['express_id'],
                                        'delivery_status' => 20,
                                        'delivery_time' => time(),
                                    ],
                                    'where' => [
                                        'order_id' => $order['order_id']
                                    ],
                                ];
                            }
                            array_push($orderList, $order);
                        }
                    }
                }
            }
            if (count($list) > 0) {
                $this->updateAll($list);
                // 发送消息通知
                $this->sendDeliveryMessage($orderList);
            }
            unlink($savePath);
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 获取赠送CFP待释放订单
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getToBeReleasedOrderList()
    {
        return $this->alias('o')->with(['product'])
            ->join('order_product og', 'og.order_id=o.order_id')
            ->where('o.receipt_status' , '=' , 20)
            ->where('og.already_stages < og.gift_stages')
            ->where('og.gift_stages > 1')
            ->select();
    }
}