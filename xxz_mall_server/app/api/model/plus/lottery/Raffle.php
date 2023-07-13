<?php

namespace app\api\model\plus\lottery;

use app\common\model\plugin\raffle\Raffle as LotteryModel;
use app\api\model\plus\coupon\UserVoucher;
use think\facade\Cache;

/**
 *转盘模型
 */
class Raffle extends LotteryModel
{
    /**
     * 转盘详情
     * @param $data
     */
    public function getDetail()
    {
        $detail = self::detail();
        $detail['lottery_data'] = json_decode($detail['lottery_data'], 1);
        return $detail;
    }

    /**
     * 已抽奖次数
     * @param $data
     */
    public function getNum($user)
    {
        //抽奖次数
        $time = strtotime(date('Y-m-d', time()));
        $num = Cache::get('draw_' . $time . $user['user_id']);
        return $num ? $num : 0;
    }

    /**
     * 抽奖
     * @param $data
     */
    public function getdraw($user)
    {
        $this->startTrans();
        try {
            //奖品详情
            $detail = self::detail();
            if ($detail['status'] == 0) {
                $this->error = "抽奖未开启";
                return false;
            }
            if ($user['exchangepurch'] < $detail['exchangepurch']) {
                $this->error = "积分不足";
                return false;
            }
            $time = strtotime(date('Y-m-d', time()));
            //判断今日抽奖次数
            $num = Cache::get('draw_' . $time . $user['user_id']);
            if ($num && $num >= $detail['times']) {
                $this->error = "今日抽奖次数已用完";
                return false;
            }
            $LotteryPrize = new rafflePrize;
            $drawArr = $LotteryPrize::detail($detail['lottery_id']);//json_decode($detail['lottery_data'], 1);
            //shuffle($drawArr); //打乱数组顺序
            $arr = [];
            $default = [];
            foreach ($drawArr as $key => $val) {
                if ($val['stock'] - $val['draw_num'] > 0) {
                    $arr[$key] = $val['stock'];//概率数组
                }
                if ($val['is_default'] == 1) {
                    $default = $val;//默认中奖项
                }
            }
            if ($arr) {
                $rid = $this->get_rand($arr); //根据概率获取奖项
                $result = $drawArr[$rid]; //中奖项
            } else {
                $result = $default; //默认中奖项
            }
            if (!$result) {
                $this->error = "礼品已抽完，请稍后再试";
                return false;
            }
            //返回前端文案
            if ($result['type']) {
                $result['rewordtitle'] = '恭喜中奖了';
                $result['rewordmsg'] = $result['type'] == 1 ? '奖品可在 我的优惠券 中查看' : '可在 个人中心 查看我的积分';
                $result['rewordbtn'] = '开心收下';
            } else {
                $result['rewordtitle'] = '很遗憾';
                $result['rewordmsg'] = '别气馁，下次再试试';
                $result['rewordbtn'] = '再抽一次';
            }
            $arr && $LotteryPrize->where('prize_id', '=', $drawArr[$rid]['prize_id'])->inc('draw_num', 1)->update();
            //添加中奖记录
            $record = [
                'record_name' => $result['name'],
                'user_id' => $user['user_id'],
                'prize_id' => $result['prize_id'],
                'prize_type' => $result['type'],
                'award_id' => $result['award_id'],
                'status' => $result['type'] == 3 ? 0 : 1,
                'app_id' => self::$app_id,
                'exchangepurch' => $result['exchangepurch'],
            ];
            //更新用户积分
            $user->setIncPoints(-$detail['exchangepurch'], '抽奖消费积分',7);
            //更新积分优惠券
            if (in_array($result['type'], [1, 2])) {
                $this->addDraw($result, $user);
            }
            (new Record)->save($record);
            //今日时间 记录缓存, 24小时
            if ($num) {
                Cache::inc('draw_' . $time . $user['user_id']);
            } else {
                $num = 0;
                Cache::set('draw_' . $time . $user['user_id'], $num + 1, 3600 * 24);
            }
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    //更新积分优惠券
    private function addDraw($data, $user)
    {
        if ($data['type'] == 1) {//优惠券
            $UserCouponModel = new UserVoucher;
            $UserCouponModel->addUserCoupon([$data['award_id']], $user);
        } elseif ($data['type'] == 2) {//积分
            $user->setIncPoints($data['exchangepurch'], '抽奖获取积分',8);
        }
    }

    //计算中奖
    private function get_rand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);  //返回随机整数
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}