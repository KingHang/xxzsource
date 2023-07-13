<?php

namespace app\api\model\plus\assemble;

use app\common\model\plugin\groupsell\BillUser as BillUserModel;

class BillUser extends BillUserModel
{
    public function add($data)
    {
        return self::create($data);
    }

    /**
     * 查询拼团数量，10拼单中 20拼单成功
     */
    public static function getOrderNum($user_id, $assemble_product_id, $assemble_activity_id){
        $model = new self();
        return $model->alias('user')->where('user_id', '=', $user_id)
            ->join('assemble_bill bill', 'bill.assemble_bill_id = user.assemble_bill_id','left')
            ->where('user.user_id', '=', $user_id)
            ->where('bill.groupsell_goods_id', '=', $assemble_product_id)
            ->where('bill.groupsell_activity_id', '=', $assemble_activity_id)
            ->where('bill.status', 'in', [10,20])
            ->count();
    }
}
