<?php

namespace app\api\model\plus\coupon;

use app\common\library\helper;
use app\common\model\plugin\voucher\UserVoucher as UserCouponModel;
use app\api\model\plus\coupon\Voucher as CouponModel;
use app\common\model\plugin\agent\UserCoupon as AgentUserCouponModel;

/**
 * 用户优惠券模型
 */
class UserVoucher extends UserCouponModel
{
    /**
     * 获取用户优惠券列表
     */
    public function getList($user_id, $shop_supplier_id = -1, $is_use = false, $is_expire = false, $auto_run = 0)
    {
        // 若开启自动触发事件
        if ($auto_run) {
            // 用户优惠券设置已过期
            event('UserCoupon');
        }
        $model = $this;
        if($shop_supplier_id != -1){
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        $list = $model->with(['supplier', 'coupon'])->where('user_id', '=', $user_id)
            ->where('is_use', '=', $is_use ? 1 : 0)
            ->where('is_expire', '=', $is_expire ? 1 : 0)
            ->select();
        if ($list) {
            foreach ($list as $key => &$value) {
                if ($value['coupon_type']['value'] == 20) {
                    $value['discount'] = rtrim(rtrim(rtrim($value['discount'], '0'), '.'), '0');
                }
                $value['reduce_price'] = floatval($value['reduce_price']);
                $value['min_price'] = floatval($value['min_price']);
                if ($value['min_price']) {
                    $value['reduce_text'] = $value['coupon_type']['value'] == 20 ? '满' . $value['min_price'] . '可用' : '满' . $value['min_price'] . '减';
                } else {
                    $value['reduce_text'] = '无门槛';
                }
            }
        }
        return $list;
    }
    /**
     * 获取用户优惠券列表
     */
    public function getOrderList($user_id, $shop_supplier_id = -1, $is_use = false, $is_expire = false)
    {
        $model = $this;
        if ($shop_supplier_id != -1) {
            $model = $model->where('shop_supplier_id', '=', $shop_supplier_id);
        }
        return $model->with(['supplier', 'coupon'])->where('user_id', '=', $user_id)
            ->group('coupon_id')
            ->where('is_use', '=', $is_use ? 1 : 0)
            ->where('is_expire', '=', $is_expire ? 1 : 0)
            ->select();
    }

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
     * 获取用户优惠券信息
     */
    public function getUserCouponInfo($user_id, $coupon_id)
    {
        return $this->where(['user_id' => $user_id, 'coupon_id' => $coupon_id])->find();
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
     * 批量领取优惠券
     */
    public function receiveList($user, $coupon_ids)
    {
        $coupon_arr = json_decode($coupon_ids, true);
        foreach($coupon_arr as $coupon_id){
            try{
                  $this->receive($user, $coupon_id);
            }catch (\Exception $e){

            }
        }
        return true;
    }

    /**
     * 添加领取记录
     */
    private function add($user, Voucher $coupon)
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
            'app_id' => self::$app_id,
            'shop_supplier_id' => $coupon['shop_supplier_id']
        ];
        return $this->transaction(function () use ($data, $coupon) {
            // 添加领取记录
            $status = $this->create($data);
            if ($status) {
                // 更新优惠券领取数量
                $coupon->setIncReceiveNum();
            }
            return $status;
        });
    }

    /**
     * 邀请有礼优惠券奖励
     * @param $coupon_ids
     * @param $user_id
     */
    public function addUserCoupon($coupon_ids, $user_id)
    {
        $model = new CouponModel();
        $list = $model->where('coupon_id', 'in', $coupon_ids)->select();
        $data = [];
        foreach ($list as $coupon) {
            // 计算有效期
            if ($coupon['expire_type'] == 10) {
                $start_time = time();
                $end_time = $start_time + ($coupon['expire_day'] * 86400);
            } else {
                $start_time = $coupon['start_time']['value'];
                $end_time = $coupon['end_time']['value'];
            }
            // 整理领取记录
            $data[] = [
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
                'user_id' => $user_id,
                'app_id' => self::$app_id,
                'shop_supplier_id' => $coupon['shop_supplier_id']
            ];
        }
        $this->saveAll($data);
        return true;
    }

    /**
     * 验证优惠券是否可领取
     */
    private function checkReceive($user, $coupon)
    {
        if (!$coupon) {
            $this->error = '优惠券不存在';
            return false;
        }
        if (!$coupon->checkReceive()) {
            $this->error = $coupon->getError();
            return false;
        }
        // 验证是否已领取
        $userCouponIds = $this->getUserCouponIds($user['user_id']);
        if (in_array($coupon['coupon_id'], $userCouponIds)) {
            $this->error = '该优惠券已领取';
            return false;
        }
        return true;
    }

    /**
     * 订单结算优惠券列表
     */
    public static function getUserCouponList($user_id, $orderPayPrice, $shop_supplier_id)
    {
        //     新增筛选条件: 最低消费金额
        // 获取用户可用的优惠券列表
        $list = (new self)->getList($user_id, $shop_supplier_id);
        $data = [];
        foreach ($list as $coupon) {
            // 最低消费金额
            if ($orderPayPrice < $coupon['min_price']) continue;
            // 有效期范围内
            if ($coupon['start_time']['value'] > time()) continue;
            $key = $coupon['user_coupon_id'];
            $data[$key] = [
                'user_coupon_id' => $coupon['user_coupon_id'],
                'name' => $coupon['name'],
                'color' => $coupon['color'],
                'coupon_type' => $coupon['coupon_type'],
                'reduce_price' => $coupon['reduce_price'],
                'discount' => $coupon['discount'],
                'min_price' => $coupon['min_price'],
                'expire_type' => $coupon['expire_type'],
                'start_time' => $coupon['start_time'],
                'end_time' => $coupon['end_time'],
                'expire_day' => $coupon['expire_day'],
                'free_limit' => $coupon['coupon']['free_limit'],
                'apply_range' => $coupon['coupon']['apply_range'],
                'product_ids' => $coupon['coupon']['product_ids'],
            ];
            // 计算打折金额
            if ($coupon['coupon_type']['value'] == 20) {
                $reducePrice = helper::bcmul($orderPayPrice, $coupon['discount'] / 100);
                $data[$key]['reduced_price'] = bcsub($orderPayPrice, $reducePrice, 2);
            } else
                $data[$key]['reduced_price'] = $coupon['reduce_price'];
        }
        // 根据折扣金额排序并返回
        return array_sort($data, 'reduced_price', true);
    }

    /**
     * @param $user_id int 用户id
     * @param $days int 连续签到天数
     * @param $sign_conf array 签到配置
     * @return int
     */
    public function setCoupon($user_id, $days, $sign_conf)
    {
        $coupon_num = 0;
        $arr = array_column($sign_conf['reward_data'], 'day');
        if (in_array($days, $arr)) {
            $key = array_search($days, $arr);
            if ($sign_conf['reward_data'][$key]['is_coupon'] == 'true') {
                $coupon = $sign_conf['reward_data'][$key]['coupon'];
                $coupon_arr = array_column($coupon, 'coupon_id');
                $coupon_str = implode(',', $coupon_arr);
                $model = new CouponModel();
                $res = $model->getWhereData($coupon_str)->toArray();
                $res_arr = array_column($res, 'coupon_id');
                $result = [];
                foreach ($coupon as $key => $val) {
                    $j = array_search($coupon[$key]['coupon_id'], $res_arr);
                    for ($i = 0; $i < $coupon[$key]['num']; $i++) {
                        $coupon_num = $coupon[$key]['num'];
                        $result[] = [
                            'coupon_id' => $res[$j]['coupon_id'],
                            'name' => $res[$j]['name'],
                            'color' => $res[$j]['color']['value'],
                            'coupon_type' => $res[$j]['coupon_type']['value'],
                            'reduce_price' => $res[$j]['reduce_price'],
                            'discount' => $res[$j]['discount'],
                            'min_price' => $res[$j]['min_price'],
                            'expire_type' => $res[$j]['expire_type'],
                            'expire_day' => $res[$j]['expire_day'],
                            'start_time' => $res[$j]['start_time']['value'],
                            'end_time' => $res[$j]['end_time']['value'],
                            'apply_range' => $res[$j]['apply_range'],
                            'user_id' => $user_id,
                            'app_id' => self::$app_id,
                        ];
                    }
                }
                self::saveAll($result);
            }
        }
        return $coupon_num;
    }

    //查询分销商优惠券
    public function getAgentCoupon($user, $orderPayPrice = 0, $shop_supplier_id = 0)
    {
        $model = new CouponModel;
        $list = $model->where('is_agent', '=', 1)
            ->where('shop_supplier_id', '=', $shop_supplier_id)
            ->where('is_delete', '=', 0)
            ->select();
        $data = [];
        foreach ($list as $key => &$coupon) {
            // 最低消费金额
            if ($orderPayPrice < $coupon['min_price']) continue;
            //查询使用数量
            $use_count = AgentUserCouponModel::where('coupon_id', '=', $coupon['coupon_id'])
                ->where('agent_id', '=', $user['user_id'])
                ->sum('use_count');
            $use_count = $use_count ? $use_count : 0;
            $coupon['agent_data'] = json_decode($coupon['agent_data'] , true);
            if ($coupon['agent_data'][$user['grade_id']] != -1 && $use_count > $coupon['agent_data'][$user['grade_id']]) {
                continue;
            }
            $num = $coupon['agent_data'][$user['grade_id']] != -1 ? $coupon['agent_data'][$user['grade_id']] - $use_count : $coupon['total_num'] - $use_count;
            if ($num <= 0) {
                continue;
            }
            $key = $coupon['coupon_id'];
            $data[$key] = [
                'coupon_id' => $coupon['coupon_id'],
                'name' => $coupon['name'],
                'color' => $coupon['color'],
                'coupon_type' => $coupon['coupon_type'],
                'reduce_price' => $coupon['reduce_price'],
                'discount' => $coupon['discount'],
                'min_price' => $coupon['min_price'],
                'expire_type' => $coupon['expire_type'],
                'start_time' => $coupon['start_time'],
                'end_time' => $coupon['end_time'],
                'expire_day' => $coupon['expire_day'],
                'free_limit' => $coupon['free_limit'],
                'apply_range' => $coupon['apply_range'],
                'product_ids' => $coupon['product_ids'],
                'num' => $num,
            ];
            // 计算打折金额
            if ($coupon['coupon_type']['value'] == 20) {
                $reducePrice = helper::bcmul($orderPayPrice, $coupon['discount'] / 10);
                $data[$key]['reduced_price'] = bcsub($orderPayPrice, $reducePrice, 2);
            } else {
                $data[$key]['reduced_price'] = $coupon['reduce_price'];
            }
        }
        // 根据折扣金额排序并返回
        return array_sort($data, 'reduced_price', true);
    }

}