<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
/**
 * 商品订单模型
 */
class OrderBenefit  extends BaseModel
{
    protected $name = 'order_benefit';
    protected $pk = 'id';
    /**
     *关联图片
     */
    public function file()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }
    public function product()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderGoods', 'order_product_id', 'order_product_id');
    }
    /**
     * 修改订单权益卡权益剩余次数
    */
    public function setBenefitCardNum($benefit_id,$order_product_id,$number)
    {
        $orderBenefit = $this->with(['product'])->where('benefit_id' , '=' , $benefit_id)->where('order_product_id','=',$order_product_id)->find();
        $data = [
            'surplus_number' => $orderBenefit['surplus_number'] - $number,
            'already_number' =>  $orderBenefit['already_number'] + $number,
        ];
        $orderBenefit['product']['product_type'] = 3;
        (new OrderGoods())->setTimesProduct([$orderBenefit['product']],3);
        return $orderBenefit->save($data);
    }
    /**
     * 获取权益总可用次数和已使用次数
    */
    public function getBenefitNumber($order_product_id)
    {
        return $this->field("sum(number) as verify_num, sum(already_number) as already_verify")->where('order_product_id' , '=' , $order_product_id)
            ->find();
    }
}