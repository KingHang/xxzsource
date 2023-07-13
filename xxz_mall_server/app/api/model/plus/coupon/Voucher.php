<?php

namespace app\api\model\plus\coupon;

use app\common\model\plugin\voucher\Voucher as CouponModel;

/**
 * 优惠券模型
 */
class Voucher extends CouponModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'is_delete',
        'create_time',
        'update_time',
    ];

    /**
     * 获取用户优惠券总数量(可用)
     */
    public function getCount($user_id)
    {
        return $this->where('user_id', '=', $user_id)
            ->where('is_use', '=', 0)
            ->where('is_expire', '=', 0)
            ->count();
    }

    /**
     * 获取用户优惠券ID集
     */
    public function getUserCouponIds($user_id)
    {
        return $this->where('user_id', '=', $user_id)->column('coupon_id');
    }

    /**
     * 领取优惠券
     */
    public function receive($user, $coupon_id)
    {
        // 获取优惠券信息
        $coupon = Voucher::detail($coupon_id);
        // 验证优惠券是否可领取
        if (!$this->checkReceive($user, $coupon)) {
            return false;
        }
        // 添加领取记录
        return $this->add($user, $coupon);
    }

    /**
     * 添加领取记录
     */
    private function add($user, $coupon)
    {
        // 计算有效期
        if ($coupon['expire_type'] == 10) {
            $start_time = time();
            $end_time = $start_time + ($coupon['expire_day'] * 86400);
        } else {
            $start_time = $coupon['start_time']['value'];
            $end_time = $coupon['end_time']['value'];
        }
        // 整理领取记录
        $data = [
            'coupon_id' => $coupon['coupon_id'],
            'name' => $coupon['name'],
            'color' => $coupon['color']['value'],
            'coupon_type' => $coupon['coupon_type']['value'],
            'reduce_price' => $coupon['reduce_price'],
            'discount' => $coupon->getData('discount'),
            'min_price' => $coupon['min_price'],
            'expire_type' => $coupon['expire_type'],
            'expire_day' => $coupon['expire_day'],
            'start_time' => $start_time,
            'end_time' => $end_time,
            'apply_range' => $coupon['apply_range'],
            'user_id' => $user['user_id'],
            'app_id' => self::$app_id
        ];
        return $this->transaction(function () use ($data, $coupon) {
            // 添加领取记录
            $status = $this->save($data);
            if ($status) {
                // 更新优惠券领取数量
                $coupon->setIncReceiveNum();
            }
            return $status;
        });
    }

    /**
     * 获取优惠券列表
     */
    public function getList($user = false, $limit = null, $only_receive = false, $type = 0 , $shop_supplier_id = 0)
    {
        $model = $this;
        // 构造查询条件
        $model = $model->with(['supplier'])->where('is_delete', '=', 0);
        // 只显示可领取(未过期,未发完)的优惠券
        if ($only_receive) {
            $model = $model->where('	IF ( `total_num` > - 1, `receive_num` < `total_num`, 1 = 1 )')
                ->where('IF ( `expire_type` = 20, (`end_time` + 86400) >= ' . time() . ', 1 = 1 )');
        }
        if ($type) {//显示平台优惠券
            $model = $model->where('shop_supplier_id', '=', 0);
        }
        if ($shop_supplier_id > 0) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        // 优惠券列表
        $couponList = $model->order(['sort' => 'asc', 'create_time' => 'desc'])->limit($limit)->select();
        // 获取用户已领取的优惠券
        $userCouponIds = [];
        if ($user !== false) {
            $CouponModel = new UserVoucher();
            $userCouponIds = $CouponModel->getUserCouponIds($user['user_id']);
        }
        // 整合数据
        if (!empty($couponList)) {
            foreach ($couponList as $key => $item) {
                $couponList[$key]['reduce_price'] = floatval($item['reduce_price']);
                $couponList[$key]['min_price'] = floatval($item['min_price']);
                if ($item['min_price']) {
                    $couponList[$key]['reduce_text'] = $item['coupon_type']['value'] == 20 ? '满' . $couponList[$key]['min_price'] . '可用' : '满' . $couponList[$key]['min_price'] . '减';
                } else {
                    $couponList[$key]['reduce_text'] = '无门槛';
                }
                $couponList[$key]['is_receive'] = in_array($item['coupon_id'], $userCouponIds);
            }
        }
        return $couponList;
    }

    /**
     * 待领取优惠券
     */
    public function getWaitList($data, $user = false, $type = 1, $status = 0)
    {
        $model = $this->alias('uc')->with(['supplier']);
        $field = "*";
        // 构造查询条件
        if ($type) {
            $model = $model->where('shop_supplier_id', $data['shop_supplier_id']);
        } else {
//            $CouponModel = new UserCoupon();
//            $userCouponIds = $CouponModel->getUserCouponIds($user['user_id']);
//            $model = $model->where('coupon_id', 'not in', implode(',', $userCouponIds));
        }
        if ($status) {//显示平台优惠券
            $model = $model->where('shop_supplier_id', '=', 0);
        }
        $model = $model->where('is_delete', '=', 0);
        $model = $model->where('	IF ( `total_num` > - 1, `receive_num` <= `total_num`, 1 = 1 )')
            ->where('IF ( `expire_type` = 20, `end_time` >= ' . time() . ', 1 = 1 )');
        // 用户可领取优惠券列表
        $couponList = $model->field("$field")->order(['sort' => 'asc', 'create_time' => 'desc'])->select();
        // 是否显示在领券中心
        $model = $model->where('show_center', '=', 1);
        // 获取用户已领取的优惠券
        $userCouponIds = [];
        if ($user !== false) {
            $CouponModel = new UserVoucher();
            $userCouponIds = $CouponModel->getUserCouponIds($user['user_id']);
        }
        // 整合数据
        if (!empty($couponList)) {
            foreach ($couponList as $item) {
                $item['reduce_price'] = floatval($item['reduce_price']);
                $item['min_price'] = floatval($item['min_price']);
                if ($item['min_price']) {
                    $item['reduce_text'] = $item['coupon_type']['value'] == 20 ? '满' . $item['min_price'] . '可用' : '满' . $item['min_price'] . '减';
                } else {
                    $item['reduce_text'] = '无门槛';
                }
                $item['is_get'] = in_array($item['coupon_id'], $userCouponIds);
            }
        }
        return $couponList;
    }

    /**
     * 验证优惠券是否可领取
     */
    public function checkReceive()
    {
        if ($this['total_num'] > -1 && $this['receive_num'] >= $this['total_num']) {
            $this->error = '优惠券已发完';
            return false;
        }
        if ($this['expire_type'] == 20 && ($this->getData('end_time') + 86400) < time()) {
            $this->error = '优惠券已过期';
            return false;
        }
        return true;
    }

    /**
     * 累计已领取数量
     */
    public function setIncReceiveNum()
    {
        return $this->where('coupon_id', '=', $this['coupon_id'])->inc('receive_num')->update();
    }

    public function getWhereData($coupon_arr)
    {
        return $this->where('coupon_id', 'in', $coupon_arr)->select();
    }

    /**
     * 查询指定优惠券
     */
    public function getCoupon($value)
    {
        return $this->where('coupon_id', 'in', $value)->select();
    }
}