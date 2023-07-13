<?php

namespace app\api\model\order;

use app\common\model\order\OrderRefund as OrderRefundModel;
use app\common\model\order\Order as OrderModel;
use app\common\model\plugin\agent\OrderDetail as OrderDetailModel;

/**
 * 售后单模型
 */
class OrderRefund extends OrderRefundModel
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
     * 追加字段
     * @var array
     */
    protected $append = [
        'state_text',   // 售后单状态文字描述
    ];

    /**
     * 售后单状态文字描述
     */
    public function getStateTextAttr($value, $data)
    {
        // 已完成
        if ($data['status'] == 20) {
            $text = [10 => '已同意退货并已退款', 20 => '已同意换货', '30' => '仅退款并已退款'];
            return $text[$data['type']];
        }
        // 已取消
        if ($data['status'] == 30) {
            return '已取消';
        }
        // 已拒绝
        if ($data['status'] == 10) {
            return $data['type'] == 10 ? '已拒绝退货退款' : '已拒绝换货';
        }
        // 进行中
        if ($data['status'] == 0) {
            if ($data['is_agree'] == 0) {
                return '等待审核中';
            }
            if ($data['type'] == 10) {
                return $data['is_user_send'] ? '已发货，待平台确认' : '已同意退货，请及时发货';
            }
        }
        return $value;
    }

    /**
     * 获取用户售后单列表
     */
    public function getList($user_id, $state = -1, $param)
    {
        $model = $this;
        $state > -1 && $model = $this->where('status', '=', $state);
        if(isset($params['shop_supplier_id'])&&$params['shop_supplier_id']){
            $model = $model->where('shop_supplier_id','=',$params['shop_supplier_id']);
        }
        return $model->with(['order_master', 'orderproduct.image'])
            ->where('user_id', '=', $user_id)
            ->order(['create_time' => 'desc'])
            ->paginate($param);
    }

    /**
     * 用户发货
     */
    public function delivery($data)
    {
        if (
            $this['type']['value'] != 10
            || $this['is_agree']['value'] != 10
            || $this['is_user_send'] != 0
        ) {
            $this->error = '当前售后单不合法，不允许该操作';
            return false;
        }
        if ($data['express_id'] <= 0) {
            $this->error = '请选择物流公司';
            return false;
        }
        if (empty($data['express_no'])) {
            $this->error = '请填写物流单号';
            return false;
        }
        // 记录售后处理进度
        (new OrderRefundLog())->save([
            'order_refund_id' => $this['order_refund_id'],
            'status' => 2,
            'desc' => '商品已寄回，等待卖家收货确认',
            'app_id' => self::$app_id,
            'create_time' => time()
        ]);
        return $this->save([
            'is_user_send' => 1,
            'send_time' => time(),
            'express_id' => (int)$data['express_id'],
            'express_no' => $data['express_no'],
        ]);
    }

    /**
     * 新增售后单记录
     */
    public function apply($user, $product, $data)
    {
        // 验证是否存在已结束佣金 存在不能取消
        if ((new OrderDetailModel())->checkOrderSettled($product['order_id'])) {
            $this->error = '订单存在已结算记录，不能取消';
            return false;
        }
        // 兼容分销小程序
        $user_id = $user['user_id'];
        if (isset($data['source']) && $data['source'] == 'agent') {
            $user_id = $data['user_id'];
        }
        $this->startTrans();
        try {
            // 更改订单状态为售后
            $orderInfo = new OrderModel();
            $shop_supplier_id = $orderInfo->where(['order_id'=>$product['order_id']])->value('
                shop_supplier_id');
            $orderInfo->where(['order_id'=>$product['order_id']])->update(['order_status' => 40]);
            // 新增售后单记录
            $this->save([
                'order_product_id' => $data['order_product_id'],
                'order_id' => $product['order_id'],
                'user_id' => $user_id,
                'type' => $data['type'],
                'apply_desc' => $data['content'],
                'is_agree' => 0,
                'status' => 0,
                'app_id' => self::$app_id,
                'shop_supplier_id'=>$shop_supplier_id
            ]);
            // 记录凭证图片关系
            if (isset($data['images']) && !empty($data['images'])) {
                $this->saveImages($this['order_refund_id'], $data['images']);
            }
            // 分销订单失效
            (new OrderDetailModel())->setInvalid($this['order_id']);
            // 记录售后处理进度
            (new OrderRefundLog())->save([
                'order_refund_id' => $this['order_refund_id'],
                'status' => 0,
                'desc' => '您的退款申请已提交，等待审核中',
                'app_id' => self::$app_id,
                'create_time' => time()
            ]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
    //新增平台介入
    public function plateapply($order_refund_id){

       return $this->where(['order_refund_id'=>$order_refund_id])->update(['plate_status'=>10]);
    }
    /**
     * 记录售后单图片
     */
    private function saveImages($order_refund_id, $images)
    {
        $images_ids = [];
        foreach (json_decode($images, true) as $val) {
            $images_ids[] = $val['file_id'];
        }
        // 生成评价图片数据
        $data = [];
        foreach ($images_ids as $image_id) {
            $data[] = [
                'order_refund_id' => $order_refund_id,
                'image_id' => $image_id,
                'app_id' => self::$app_id
            ];
        }
        return !empty($data) && (new OrderRefundImage)->saveAll($data);
    }

    /**
     *
     */
    public function cancel($data)
    {
        $this->startTrans();
        try {
            $this->save(['status'=>30]);
            // 记录售后处理进度
            (new OrderRefundLog())->save([
                'order_refund_id' => $this['order_refund_id'],
                'status' => 0,
                'desc' => '用户取消售后申请',
                'app_id' => self::$app_id,
                'create_time' => time()
            ]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

}