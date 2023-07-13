<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
use app\common\model\file\UploadFile;
use app\common\model\order\OrderBenefit;
/**
 * 商品订单模型
 */
class OrderTravelers  extends BaseModel
{
    protected $name = 'order_travelers';
    protected $pk = 'id';
    protected $append = ['card_file','product_file'];
    public function order()
    {
        return $this->hasOne('app\\common\\model\\order\\Order', 'order_id', 'order_id');
    }
    public function product()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderGoods', 'order_product_id', 'order_product_id');
    }
    public function cardProduct()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderGoods', 'order_product_id', 'card_order_product_id');
    }
    // 处理权益卡图片
    public function getCardFileAttr($value, $data)
    {
        return isset($data['card_image_id']) && $data['card_image_id'] > 0 ? UploadFile::detail($data['card_image_id']) : [];
    }
    // 处理权益卡图片
    public function getProductFileAttr($value, $data)
    {
        return isset($data['image_id']) && $data['image_id'] > 0 ? UploadFile::detail($data['image_id']) : 0;
    }
    /**
     * 获取权益卡使用记录
    */
    public function getList($params)
    {
        $model = $this;
        if (isset($params['time']) && $params['time']) {
            $start_time = strtotime($params['time'][0]);
            $end_time = strtotime($params['time'][1]) + 86399;
            $model = $model->where('create_time', 'between', [$start_time, $end_time]);
        }
        if (isset($params['order_product_id']) && $params['order_product_id'] > 0) {
            $model = $model->where('ot.card_order_product_id' , '=' , $params['order_product_id']);
        }
        if (isset($params['type']) && $params['type'] > 0) {
            switch ($params['type']) {
                case 1:
                    $model = $model->where('c.product_name', 'like', '%' . $params['search'] . '%');
                    break;
                case 2;
                    $model = $model->where('op.product_name', 'like', '%' . $params['search'] . '%');
                    break;
                case 3:
                    $model = $model->where('u.mobile', 'like', '%' . $params['search'] . '%');
                    break;
                case 4:
                    $model = $model->where('u.nickName', 'like', '%' . $params['search'] . '%');
                    break;
                case 5:
                    $model = $model->where('ot.name', 'like', '%' . $params['search'] . '%');
                    break;
                case 6:
                    $model = $model->where('ot.mobile', 'like', '%' . $params['search'] . '%');
                    break;
                case 7:
                    $model = $model->where('ot.id_card', 'like', '%' . $params['search'] . '%');
                    break;
                case 8:
                    $model = $model->where('o.order_no', 'like', '%' . $params['search'] . '%');
                    break;
            }
        }

        $data = $model->alias('ot')->with(['cardProduct.OrderBenefit'])
            ->field('ot.*,o.order_no,c.image_id as card_image_id,op.benefit_card_id,op.card_order_product_id as aaaa
            ,c.product_name as card_name,b.benefit_name,u.mobile as u_mobile,u.nickName
            ,op.create_time,op.image_id,op.product_name,ot.create_time,c.total_num,c.order_product_id')
            ->join('order o' , 'o.order_id = ot.order_id')
            ->join('order_product op' , 'op.order_product_id = ot.order_product_id and op.order_source = 0 AND op.product_type = 4')
            ->join('benefit b' , 'b.benefit_id = op.benefit_id')
            ->join('user u' , 'u.user_id = o.user_id' , 'left')
            ->join('order_product c' , 'c.order_product_id = op.card_order_product_id' )
            ->where('o.pay_status' , '=' , 20)
            ->where('ot.status' , '<>' , 2)
            ->where('ot.card_order_product_id' , '>' , 0)
            ->whereIn('o.order_status',[10,30])
            ->order(['ot.create_time' => 'desc'])
            ->group('ot.id')
            ->paginate($params);

        return $data;
    }

    /**
     * 获取剩余数量
    */
    public function getCardResidueNum($relevance = [] , $total_num = 0 , $order_product_id = 0)
    {
        if ($relevance) {
            foreach ($relevance as &$val) {
                $count = $this->alias('r')
                    ->join('order_product og' , 'og.order_product_id = r.order_product_id')
                    ->join('order o' , 'o.order_id = r.order_id')
                    ->where('og.benefit_id' , '=' , $val['benefit_id'])
                    ->whereIn('o.order_status',[10,30])
                    ->where('og.card_order_product_id' , '=' , $order_product_id)
                    ->count();
                $val['number'] = $total_num*$val['number'] - $count;
            }
        }
        return $relevance;
    }
    /**
     * 根据核销码获取出现人id
    */
    public function getIdByCode($verify_cod)
    {
        return $this->where('verify_code' , '=' , $verify_cod)->value('id');
    }

    /**
     * 编辑出行人信息
    */
    public function updateOrderTravelersInfo($data)
    {
        $updateDate = [];
        isset($data['name']) && $updateDate['name'] = $data['name'];
        isset($data['mobile']) && $updateDate['mobile'] = $data['mobile'];
        isset($data['id_card']) && $updateDate['id_card'] = $data['id_card'];
        if (empty($updateDate)) {
            $this->error = '操作失败';
            return false;
        } elseif (isset($updateDate['name']) && $updateDate['name'] == ''){
            $this->error = '请输入姓名';
            return false;
        } elseif (isset($updateDate['mobile']) && $updateDate['mobile'] == ''){
            $this->error = '请输入手机号';
            return false;
        } elseif (isset($updateDate['id_card']) && $updateDate['id_card'] == ''){
            $this->error = '请输入身份证号';
            return false;
        }
        $this->startTrans();
        try {
            $this->where('id','=',$data['opt_id'])->update($updateDate);
            // 事务提交
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 返回兑换权益卡权益次数
     * @param $order_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function rollBenefitCardNumber($order_id)
    {
        $travelers_ids = []; // 出行人人id
        $order_benefit_card_data = [];

        $list = $this->with(['product','cardProduct.OrderBenefit'])->where('order_id' , '=' , $order_id)->select();
        if (!empty($list)){
            foreach ($list as $item) {
                $travelers_ids[] = $item['id'];
                if (isset($item['cardProduct']['OrderBenefit'])) {
                    foreach ($item['cardProduct']['OrderBenefit'] as $OrderBenefit) {
                        if ($OrderBenefit['benefit_id'] == $item['product']['benefit_id']) {
                            // 返还兑换权益卡使用次数
                            $order_benefit_card_data[] = [
                                'data' => [
                                    'surplus_number' => ['inc', 1],
                                    'already_number' => ['dec', 1],
                                ],
                                'where' => [
                                    'id' => $OrderBenefit['id']
                                ],
                            ];
                        }
                    }
                }
            }
        }
        if (!empty($travelers_ids)) {
            // 旅游商品出行记录设置为无效
            $this->whereIn('id',$travelers_ids)->update(['status' => 2]);
        }
        if (!empty($order_benefit_card_data)) {
            (new OrderBenefit())->updateAll($order_benefit_card_data);
        }
    }
}