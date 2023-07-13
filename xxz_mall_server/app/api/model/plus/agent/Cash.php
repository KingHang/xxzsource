<?php

namespace app\api\model\plus\agent;

use app\common\exception\BaseException;
use app\common\model\plugin\agent\Cash as CashModel;
use app\common\library\easywechat\AppWx;

/**
 * 分销商提现明细模型
 */
class Cash extends CashModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'update_time',
    ];

    /**
     * 获取分销商提现明细
     */
    public function getList($user_id, $apply_status = -1, $data)
    {
        $model = $this;
        if (isset($data['pay_type']) && $data['pay_type']) {
            $model = $model->where('pay_type', '=', $data['pay_type']);
        }
        if (isset($data['month1']) && $data['month1']) {
            $month1 = date('Y-m-01 00:00:00', strtotime($data['month1']));
            $model = $model->where('create_time', '>=', strtotime($month1));
        }
        if (isset($data['month2']) && $data['month2']) {
            $month2 = date('Y-m-01 00:00:00', strtotime("{$data['month2']} +1 month"));
            $model = $model->where('create_time', '<', strtotime($month2));
        }
        return $model->where('user_id', '=', $user_id)
            ->field("*,FROM_UNIXTIME(create_time,'%Y年%m月%d日 %H:%i') create_times")
            ->order(['create_time' => 'desc'])
            ->paginate($data);
    }

    /**
     * 提交申请
     */
    public function submit($agent, $data,$user = [])
    {
        // 数据验证
        $this->validation($agent, $data);
        // 微信打款 获取用户open_id
        $open_id = '';

        if ($data['pay_type'] == 10) {
            if (isset($data['source']) && $data['source'] == 'agent') {
                $app = AppWx::getApp();
                $session = $app->auth->session($data['code']);
                $open_id = $session['openid'];
            } else {
                $open_id = $user['open_id'];
            }
        }
        $order_no = $this->createOrderNo();
        // 新增申请记录
        $this->save(array_merge($data, [
            'order_no' => $order_no,
            'user_id' => $agent['user_id'],
            'apply_status' => 10,
            'app_id' => self::$app_id,
            'source' => isset($data['source']) && $data['source'] == 'agent' ? 1 : 0,
            'open_id' => $open_id
        ]));
        // 冻结用户资金
        $agent->freezeMoney($data['money']);
        return $order_no;
    }

    /**
     * 数据验证
     */
    private function validation($agent, $data)
    {
        // 结算设置
        $settlement = Setting::getItem('settlement');
        // 判断当前等级是否可以提现
        if (empty($agent['grade']) || $agent['grade']['is_cash'] == 0) {
            throw new BaseException(['msg' => '当前等级不可以提现，请先升级']);
        }
        // 最低提现佣金
        if ($data['money'] <= 0) {
            throw new BaseException(['msg' => '提现金额不正确']);
        }
        if ($agent['money'] <= 0) {
            throw new BaseException(['msg' => '当前用户没有可提现佣金']);
        }
        if ($data['money'] > $agent['money']) {
            throw new BaseException(['msg' => '提现金额不能大于可提现佣金']);
        }
        if ($data['money'] < $settlement['min_money']) {
            throw new BaseException(['msg' => '最低提现金额为' . $settlement['min_money']]);
        }
        if (!in_array($data['pay_type'], $settlement['pay_type'])) {
            throw new BaseException(['msg' => '提现方式不正确']);
        }
        if ($data['pay_type'] == '20') {
            if (empty($data['alipay_name']) || empty($data['alipay_account'])) {
                throw new BaseException(['msg' => '请补全提现信息']);
            }
        } elseif ($data['pay_type'] == '30') {
            if (empty($data['bank_name']) || empty($data['bank_account']) || empty($data['bank_card'])) {
                throw new BaseException(['msg' => '请补全提现信息']);
            }
        }
    }

}