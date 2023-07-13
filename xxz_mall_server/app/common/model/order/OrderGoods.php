<?php

namespace app\common\model\order;

use app\api\model\supplier\Purveyor as SupplierModel;
use app\common\model\BaseModel;
use app\common\model\order\VerifyServerOrder as VerifyServerOrderModel;
use app\common\model\order\VerifyServerLog as VerifyServerLogModel;
use app\common\model\order\VerifyProductLog as VerifyProductLogModel;
use app\common\model\order\Order as OrderModel;
use app\common\model\store\Store as StoreModel;
use app\common\model\order\OrderTravelers;
use app\common\model\order\OrderBenefit;

/**
 * 订单商品模型
 */
class OrderGoods extends BaseModel
{
    protected $name = 'order_goods';
    protected $pk = 'order_product_id';
    /**
     * 追加字段
     * @var string[]
     */
    protected $append = [
        'time_verify_text',
        'delivery_time_text',
        'surplus_num',
        'total_verify_num', //
        'time_text',
        'store_list',
    ];

    /**
     * 获取核销次数总数
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getTotalVerifyNumAttr($value,$data)
    {
        return $data['verify_num'] * $data['total_num'];
    }
    public function getStoreListAttr($value,$data)
    {
        $list = [];
        if (isset($data['store_ids'])) {
            if (in_array($data['product_type'],[1,2]) || (in_array($data['product_type'],[3,4]) && $data['store_ids'] == '')) {
                $where = ['status' => 1 , 'is_delete' => 0 ];
                // 判断店铺类型  自营店铺获取全部门店  供应商获取供应商门店
                if (isset($data['shop_supplier_id']) && $data['shop_supplier_id'] > 0 && !(new SupplierModel())->checkSupplierType($data['shop_supplier_id'])) {
                    $where['shop_supplier_id'] = $data['shop_supplier_id'];
                }
                $list = (new StoreModel)->where($where)->select();
            } else {
                $list = (new StoreModel)->where('store_id', 'in', $data['store_ids'])->select();
            }
        }
        return $list;
    }
    /**
     * 剩余次数
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getSurplusNumAttr($value,$data)
    {
       if ($data['product_type'] == 4) {
           $surplus_num = isset($data['status']) && $data['status'] == 0 ? 1 : 0;
       } else {
           $surplus_num = $data['verify_num'] * $data['total_num'] - $data['already_verify'];
       }
        return $surplus_num;
    }

    /**
     * 发货时间
     * @param $value
     * @param $data
     * @return string
     */
    public function getDeliveryTimeTextAttr($value, $data)
    {
        return $data['delivery_time'] ? date('Y-m-d H:i:s', $data['delivery_time']) : '';
    }

    /**
     * 计次商品有效期及剩余次数
     * @param $value
     * @return array
     */
    public function getTimeVerifyTextAttr($value,$data)
    {
        $time_verify_text = [];
        // 格式化有效期
        if (in_array($data['product_type'],[3,4]) || in_array($data['order_source'] , [1,2,3])) {
            $time_verify_text = [
                'text' => '永久有效',
                'status' => 1, // 状态  0:未生效 1：有效期内,2已过期，3，已使用
                'status_text' => '有效期内',
                'value' => $data['verify_enddate']
            ];
            // 验证订单状态 如果待支付，已取消，取消中，售后中 状态为待生效
            if (!(new OrderModel())->checkOrderStatus($data['order_id'])) {
                $time_verify_text['status'] = 0;
                $time_verify_text['status_text'] = '未生效';
                return $time_verify_text;
            }
            // 权益卡重新获取状态
            if (isset($data['order_source']) && $data['order_source'] == 3) {
                $numberArr = (new OrderBenefit())->getBenefitNumber($data['order_product_id']);
                $verify_num = isset($numberArr['verify_num']) ? $numberArr['verify_num'] : 0;
                $already_verify = isset($numberArr['already_verify']) ? $numberArr['already_verify'] : 0;
            } else {
                $verify_num = $data['verify_num'] * $data['total_num'];
                $already_verify = $data['already_verify'];
                // 计次商品 不限次数
                if ($data['product_type'] == 3 && $data['verify_num'] == 0) {
                    $verify_num = $data['already_verify'] + 1;
                } elseif ($data['product_type'] == 4){
                    $verify_num = 1;
                    $already_verify = isset($data['status']) && $data['status'] == 1 ? 1 : 0;
                }
            }
            if ($data['verify_limit_type'] == 0) {
                $time_verify_text['text'] = "永久有效";
                $time_verify_text['status_text'] = $verify_num > $already_verify ?'有效期内' : '已使用';
                $time_verify_text['status'] = $verify_num > $already_verify ?  1 : 3;
            } elseif ($data['verify_limit_type'] == 1) {
                $time_verify_text['text'] = date('Y-m-d', $data['verify_enddate']) . '前有效';
                $time_verify_text['status_text'] = $verify_num > $already_verify ? $data['verify_enddate'] < time() ? '已过期' : '有效期内' : '已使用';
                $time_verify_text['status'] = $verify_num > $already_verify ? $data['verify_enddate'] < time() ? 2 : 1 : 3;
            } elseif ($data['verify_limit_type'] == 2) {
                $time_verify_text['status_text'] = $verify_num > $already_verify ? $data['verify_enddate'] > 0 ?  $data['verify_enddate'] < time() ? '已过期' : '有效期内' : '有效期内' : '已使用';
                $time_verify_text['status'] = $verify_num > $already_verify ? $data['verify_enddate'] > 0 ?  $data['verify_enddate'] < time() ? 2 : 1 : 1 : 3;
                $time_verify_text['text'] = $data['verify_enddate'] > 0 ? date('Y-m-d', $data['verify_enddate']) . '前有效' : '购买后' . $data['verify_days'] . '天内有效';
            } elseif ($data['verify_limit_type'] == 3) {
                $time_verify_text['status_text'] = $verify_num > $already_verify ? $data['verify_enddate'] > 0 ?  $data['verify_enddate'] < time() ? '已过期' : '有效期内' : '有效期内' : '已使用';
                $time_verify_text['status'] = $verify_num > $already_verify ? $data['verify_enddate'] > 0 ?  $data['verify_enddate'] < time() ? 2 : 1 : 1 : 3;
                $time_verify_text['text'] = $data['verify_enddate'] > 0 ? date('Y-m-d', $data['verify_enddate']) . '前有效' : '首次使用后' . $data['verify_days'] . '天内有效';
            }

        }
        return $time_verify_text;
    }
    public function getTimeTextAttr($value, $data)
    {
        $text = '';
        if (isset($data['verify_limit_type'])) {
            if ($data['verify_limit_type'] == 0) {
                $text = "永久有效";
            } elseif ($data['verify_limit_type'] == 1) {
                $text = date('Y-m-d', $data['verify_enddate']) . '前有效';
            } elseif ($data['verify_limit_type'] == 2) {
                $text = '购买后' . $data['verify_days'] . '天内有效';
            } elseif ($data['verify_limit_type'] == 3) {
                $text = '首次使用后' . $data['verify_days'] . '天内有效';
            }
        }
        return $text;
    }
    /**
     * 关联物流公司表
     */
    public function express()
    {
        return $this->belongsTo('app\\common\\model\\settings\\Express', 'express_id', 'express_id');
    }
    /**
     * 关联物权益卡权益表
     */
    public function OrderBenefit()
    {
        return $this->hasMany('app\\common\\model\\order\\OrderBenefit', 'order_product_id', 'order_product_id');
    }
    /**
     * 旅游商品出行人
    */
    public function OrderTravelers()
    {
        return $this->hasMany('app\\common\\model\\order\\OrderTravelers', 'order_product_id', 'order_product_id');
    }
    /**
     * 权益卡兑换旅游商品出行人
     */
    public function OrderBenefitTravelers()
    {
        return $this->hasMany('app\\common\\model\\order\\OrderTravelers', 'card_order_product_id', 'order_product_id');
    }
    /**
     * 订单商品列表
     * @return \think\model\relation\BelongsTo
     */
    public function image()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id');
    }
    public function benefit(){
        return $this->hasOne('app\\common\\model\\plus\\benefit\\Benefit', 'benefit_id', 'benefit_id');
    }
    /**
     * 关联商品表
     * @return \think\model\relation\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('app\\common\\model\\product\\Product');
    }

    /**
     * 关联商品sku表
     * @return \think\model\relation\BelongsTo
     */
    public function sku()
    {
        return $this->belongsTo('app\\common\\model\\product\\ProductSku', 'spec_sku_id', 'spec_sku_id');
    }

    /**
     * 关联订单主表
     * @return \think\model\relation\BelongsTo
     */
    public function orderM()
    {
        return $this->belongsTo('Order','order_id','order_id');
    }

    /**
     * 售后单记录表
     * @return \think\model\relation\HasOne
     */
    public function refund()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderRefund');
    }

    /**
     * 关联分销商
     * @return \think\model\relation\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo('app\\common\\model\\agent\\Apply', 'agent_user_id', 'user_id');
    }
    /**
     * 售后单记录表
     * @return \think\model\relation\HasOne
     */
    public function order()
    {
        return $this->hasOne('app\\common\\model\\order\\Order' , 'order_id' , 'order_id');
    }
    /**
     * 订单商品详情
     */
    public static function detail($where)
    {
        return (new static())->with(['image', 'refund', 'orderM'])->find($where);
    }

    /**
     * 获取商品数据 (可指定某天)
     */
    public function getProductData($startDate = null, $endDate = null, $type, $shop_supplier_id , $product_id = [], $user_id = 0)
    {
        $model = $this;
        if($shop_supplier_id > 0){
            $model = $model->where('order.shop_supplier_id', '=', $shop_supplier_id);
        }
        if (!empty($product_id)) {
            $model = $model->whereIn('order_product.product_id',  $product_id);
        }
        if ($user_id > 0) {
            $model = $model->where('order.user_id',  $user_id);
        }
        $model = $model->alias('order_product')
            ->join('order order', 'order_product.order_id = order.order_id','left');

        $model = $model->where('order.create_time', '>=', strtotime($startDate));
        if (empty($product_id)) {
            if(is_null($endDate)){
                $model = $model->where('order.create_time', '<', strtotime($startDate) + 86400);
            }else{
                $model = $model->where('order.create_time', '<', strtotime($endDate) + 86400);
            }
        }
        if($type == 'no_pay'){
            // 未支付
            return $model->where('order.pay_status', '=', 10)->sum('order_product.total_num');
        }else if($type == 'pay'){
            // 已支付
            return $model->where('order.pay_status', '=', 20)->sum('order_product.total_num');
        }
        return 0;
    }

    /**
     * 获取指定商品销量（d/today:天，w/week:周，m/month:月，y/year:年，yesterday：昨天，last week：上周，last month：上个月，last year:去年）
     * @param string $data_type
     * @param $product_id
     * @param $order_source 0:商品，1：服务
     * @return float|int
     */
    public function getDataSalesVolume($data_type = 'week',$product_id,$order_source = '0')
    {
        $model = $this;
        // 使用日期表达式
        switch (strtolower($data_type)) {
            case 'today':
            case 'd':
                $start_time = strtotime(date("Y-m-d",time()));
                $model->where('order.create_time' , '>' , $start_time);
                break;
            case 'week':
            case 'w':
                $start_time = strtotime('-7 day');
                $model->where('order.create_time' , '>' , $start_time);
                break;
            case 'month':
            case 'm':
                $start_time = mktime(0,0,0,1,date('m'),date('Y'));
                $model->where('order.create_time' , '>' , $start_time);
                break;
            case 'year':
            case 'y':
                $start_time = mktime(0,0,0,1,1,date('Y'));
                $model->where('order.create_time' , '>' , $start_time);
                break;
            case 'yesterday':
                $start_time = strtotime(date("Y-m-d",time()));
                $end_time = mktime(0,0,0,date('d')+1,date('m'),date('Y'));
                $model = $model->whereBetweenTime('order.create_time',$start_time,$end_time);
                break;
            case 'last week':
                $start_time = strtotime('-14 day');
                $end_time = strtotime('-7 day');
                $model = $model->whereBetweenTime('order.create_time',$start_time,$end_time);
                break;
            case 'last month':
                $start_time = strtotime('-30 day');
                $end_time = time();
                $model = $model->whereBetweenTime('order.create_time',$start_time,$end_time);
                break;
            case 'last year':
                $start_time = mktime(0,0,0,1,1,date('Y')-1);
                $end_time = mktime(0,0,0,1,1,date('Y'));
                $model = $model->whereBetweenTime('order.create_time',$start_time,$end_time);
                break;
            default:
                return 0;
        }
        return $model->alias('order_product')
            ->join('order order', 'order_product.order_id = order.order_id','left')
            ->where(array('order.pay_status' => 20 , 'order_product.product_id' => $product_id , 'order_product.order_source' => $order_source))
//            ->fetchSql()
            ->sum('order_product.total_num');
    }

    /**
     * 更新计次商品有效期
     * @param $order_product
     * @param int $verify_limit_type
     * @return bool
     */
    public function setTimesProduct($order_product,$verify_limit_type = 0)
    {
        if (empty($order_product) || !in_array($verify_limit_type,[2,3])) {
            return false;
        }

        $data = [];
        foreach ($order_product as $product) {
            if (in_array($product['product_type'],[3,4]) && $product['verify_limit_type'] == $verify_limit_type) {
                $data[] = [
                    'data' => ['verify_enddate' => strtotime('+' . $product['verify_days'] . ' day')],
                    'where' => [
                        'order_product_id' => $product['order_product_id'],
                    ],
                ];
            }
        }
        try{
            // 更新计次商品到期时间
            !empty($data) && $this->updateOrderProduct($data);
        }catch (\Exception $e){
           return false;
        }
    }

    /**
     * 更新订单商品信息
     * @param $data
     * @return mixed
     */
    private function updateOrderProduct($data)
    {
        return $this->updateAll($data);
    }

    /**
     * 获取最大订单商品id
     * @return mixe
     *
     */
    public function getMaxId()
    {
        return $this->max('order_product_id');
    }

    /**
     * 获取核销订单商品信息
     * @param $verify_code
     * @param int $type
     * @return array|bool|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getVerifyOrderDetail($verify_code,$type = 0)
    {
        // 判断必传项
        if ($verify_code == '' || $type == 0) {
            $this->error = '参数错误';
            return false;
        }
        if (!in_array($type , [1,2,3])) {
            $this->error = '无效商品类型';
            return false;
        }

        return $this->alias('p')->field("p.*,ot.id,ot.status as t_status")
            ->with(['order'])
            ->join('order_travelers ot' , 'ot.order_product_id = p.order_product_id','left')
            ->where(function ($query) use ($type,$verify_code) {
                if ($type == 1) {
                    $query->where('p.verify_code', '=', $verify_code);
                    $query->where('p.product_type', '=', 3);
                } elseif ($type == 2) {
                    $query->where('p.verify_code', '=', $verify_code);
                    $query->whereIn('p.order_source', [1,2]);
                } else {
                    $query->where('ot.verify_code', '=', $verify_code);
                    $query->where('p.product_type', '=', 4);
                }
            })
            ->order('verify_num * total_num - already_verify desc')->find();

    }

    /**
     * 计次商品/服务核销
     * @param $ClerkModel
     * @return bool|mixed
     */
    public function verificationOrder($ClerkModel,$number = 1,$verify_type = 0)
    {
        // 验证订单状态
        if (
            $this['order']['pay_status']['value'] != 20
            || in_array($this['order_status']['value'], [20, 21, 40])
        ) {
            $this->error = '该订单不满足核销条件';
            return false;
        }
        if ($this['product_type'] == 4) {
            //验证剩余核销次数
            if ($this['t_status'] == 1) {
                $this->error = '该商品已核销';
                return false;
            }
            if ($this['t_status'] == 2) {
                $this->error = '该商品已失效';
                return false;
            }
        } else {
            // 验证使用状态
            if ($this['verify_num'] > 0 && $this['already_verify'] >= $this['total_num'] * $this['verify_num']) {
                $this->error = '该订单不满足核销条件';
                return false;
            }
            //验证剩余核销次数
            if ($this['surplus_num'] < $number) {
                $this->error = '剩余次数不足';
                return false;
            }
            // 判断状态
            if ($this['time_verify_text']['status'] == 2) {
                $this->error = '商品已过期';
                return false;
            }
            if ($this['time_verify_text']['status'] == 3) {
                $this->error = '商品已使用';
                return false;
            }
        }

        return $this->transaction(function () use ($ClerkModel,$number,$verify_type) {
            if ($this['product_type'] == 4) {
                $status = (new OrderTravelers())->where('id','=',$this['id'])->update([
                    'status' => 1
                ]);
                // 更新计次商品有效期
                $this['verify_enddate'] == 0 && $this->setTimesProduct([$this],4);
                // 新增订单核销记录
                (new VerifyProductLogModel())->save([
                    'order_product_id' => $this['id'],
                    'verify_num' => $number,
                    'verify_date' => time(),
                    'app_id' => self::$app_id,
                    'store_id' => $ClerkModel['store_id'],
                    'clerk_id' => $ClerkModel['clerk_id'],
                    'type' => 1,
                    'verify_type' => $verify_type
                ]);
            } else {
                // 更新商品使用数量
                $status = $this->save([
                    'already_verify' => $this['already_verify'] + $number
                ]);
                // 更新使用次数
                if ($this['product_type'] == 3) {
                    // 更新计次商品有效期
                    $this['verify_enddate'] == 0 && $this->setTimesProduct([$this],3);
                    // 新增订单核销记录
                    (new VerifyProductLogModel())->save([
                        'order_product_id' => $this['order_product_id'],
                        'verify_num' => $number,
                        'verify_date' => time(),
                        'app_id' => self::$app_id,
                        'store_id' => $ClerkModel['store_id'],
                        'clerk_id' => $ClerkModel['clerk_id'],
                        'verify_type' => $verify_type
                    ]);
                } else {
                    // 获取核销记录
                    $verif = (new VerifyServerOrderModel())->getdetailForOrder($this['verify_code'] , $this['order_id']);
                    // 消单日志
                    $verif_log = array(
                        'verify_id' => $verif['id'],
                        'verify_num'    => $number, // 核销次数
                        'verify_date' => time(),
                        'app_id' => self::$app_id,
                        'store_id' => $ClerkModel['store_id'], //门店id
                        'clerk_id' => $ClerkModel['clerk_id'],
                        'verify_type' => $verify_type
                    );
                    // 更新服务剩余次数和使用次数
                    $verif_data = array(
                        'verify_num' => $verif['verify_num']-$number,
                        'used_num' => $verif['used_num']+$number,
                    );
                    // 如果是首次使用N天后失效更新失效时间
                    $limt_time = array();
                    if ($verif['verify_limit_type'] === 3 && $verif['end_time'] === 0) {
                        $limt_time['start_time'] = time();
                        $limt_time['end_time'] = strtotime('+' . $verif['verify_days'] . ' day');
                    }
                    // 更新服务使用次数
                    $verif->where('id','=',$verif['id'])->update($verif_data);
                    // 更新消单记录
                    (new VerifyServerLogModel())->save($verif_log);
                    // 更新卡项使用时间
                    if (!empty($limt_time)) {
                        $this->where(array('card_id' => $verif['card_id'] , 'exchange_order_id' => $verif['exchange_order_id']))->update($limt_time);
                    }
                }
            }
            return $status;
        });
    }
}