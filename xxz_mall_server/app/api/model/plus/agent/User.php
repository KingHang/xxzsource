<?php

namespace app\api\model\plus\agent;

use app\common\model\plugin\agent\Month as MonthModel;
use app\common\model\plugin\agent\User as UserModel;
use think\facade\Cache;
use app\api\model\order\Order as OrderModel;
use app\common\library\helper;
use app\api\model\order\OrderRefund as OrderRefundModel;
use app\api\model\order\OrderGoods as OrderProductModel;
use app\common\model\user\Sms as SmsModel;
use app\common\exception\BaseException;
use app\api\model\plus\agent\OrderSettled as OrderSettledModel;
use app\common\model\plugin\agent\Setting as AgentSettingModel;
use app\common\enum\order\OrderPayStatusEnum;
use app\common\model\plugin\agent\Grade as AgentGradeModel;
use app\common\model\user\User as UserModel1;

/**
 * 分销商用户模型
 * @property mixed user_id
 * @property mixed real_name
 * @property mixed mobile
 * @property int|mixed referee_id
 * @property string app_id
 * @property mixed create_time
 * @property mixed update_time
 */
class User extends UserModel
{
    private $token;

    /**
     * 隐藏字段
     */
    protected $hidden = [
        'create_time',
        'update_time',
    ];

    /**
     * 资金冻结
     */
    public function freezeMoney($money)
    {
        return $this->save([
            'money' => $this['money'] - $money,
            'freeze_money' => $this['freeze_money'] + $money,
        ]);
    }

    /**
     * 获取token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 生成用户认证的token
     */
    private function token($openid)
    {
        return md5('agent_' . $openid . 'token_salt');
    }

    /**
     * 手机号密码用户登录
     */
    public function phoneLogin($data)
    {
        $user = $this->where('mobile', '=', $data['mobile'])
            ->where('password', '=', md5($data['password']))
            ->order('is_delete ASC')
            ->find();
        if (!$user) {
            $this->error = '手机号或密码错误';
            return false;
        } else {
            if ($user['is_delete'] == 1) {
                $this->error = '手机号被禁止或删除，请联系客服';
                return false;
            }
            // 非团长禁止登陆
            $data = AgentSettingModel::getAll();
            $setting = $data['condition']['values'];
            $gradeIds = AgentGradeModel::getTeamGradeList($setting['team_level']);
            if (!in_array($user['grade_id'] , $gradeIds) && $user['is_work'] == 0) {
                $team_name = $setting['team_name'] ? $setting['team_name'] : '团长';
                $this->error = '非' . $team_name  . '级别不能使用此功能';
                return false;
            }
            $user_id = $user['user_id'];
            $mobile = $user['mobile'];
        }
        // 生成token (session3rd)
        $this->token = $this->token($mobile);
        // 记录缓存, 30天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 30);
        return $user_id;
    }

    /**
     * 手机号密码用户登录
     */
    public function smslogin($data)
    {
        if (!$this->check($data)) {
            return false;
        }
        $user = $this->where('mobile', '=', $data['mobile'])->order('user_id desc')->find();
        if (!$user) {
            $this->error = '手机号不存在';
            return false;
        } else {
            if ($user['is_delete'] == 1) {
                $this->error = '手机号被禁止或删除，请联系客服';
                return false;
            }
            // 非团长禁止登陆
            $data = AgentSettingModel::getAll();
            $setting = $data['condition']['values'];
            $gradeIds = AgentGradeModel::getTeamGradeList($setting['team_level']);
            if (!in_array($user['grade_id'] , $gradeIds) && $user['is_work'] == 0) {
                $team_name = $setting['team_name'] ? $setting['team_name'] : '团长';
                $this->error = '非' . $team_name . '级别不能使用此功能';
                return false;
            }
            $user_id = $user['user_id'];
            $mobile = $user['mobile'];
        }
        // 生成token (session3rd)
        $this->token = $this->token($mobile);
        // 记录缓存, 30天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 30);
        return $user_id;
    }

    /*
    *重置密码
    */
    public function resetpassword($data)
    {
        if (!$this->check($data)) {
            return false;
        }
        $user = $this->where('mobile', '=', $data['mobile'])->order('user_id desc')->find();
        if ($user) {
            if ($user['is_delete'] == 1) {
                $this->error = '手机号被禁止或删除，请联系客服';
                return false;
            }
            return $this->where('mobile', '=', $data['mobile'])->update([
                'password' => md5($data['password'])
            ]);
        } else {
            $this->error = '手机号不存在';
            return false;
        }

    }

    /**
     * 验证
     */
    private function check($data)
    {
        //判断验证码是否过期、是否正确
        $sms_model = new SmsModel();
        $sms_record_list = $sms_model
            ->where('mobile', '=', $data['mobile'])
            ->order(['create_time' => 'desc'])
            ->limit(1)->select();

        if (count($sms_record_list) == 0) {
            $this->error = '未查到短信发送记录';
            return false;
        }
        $sms_model = $sms_record_list[0];
        if ((time() - strtotime($sms_model['create_time'])) / 60 > 30) {
            $this->error = '短信验证码超时';
            return false;
        }
        if ($sms_model['code'] != $data['code']) {
            $this->error = '验证码不正确';
            return false;
        }
        return true;
    }

    /**
     * 获取用户信息
     */
    public static function getUser($token)
    {
        $userId = Cache::get($token);
//        $userId = '12209';
        return (new static())->where(['user_id' => $userId])->with(['user', 'grade', 'area'])->find();
    }

    //查询今日数据
    public function getIndexData($user)
    {
        $userIds = $this->getDirectUserIds($user);
        $OrderModel = new OrderModel;
        $start_time = strtotime(date('Y-m-d', time()));
        $end_time = strtotime(date('Y-m-d', time())) + 86399;

        // 获取团长等级
        $setting = AgentSettingModel::getItem('condition');
        $gradeIds = AgentGradeModel::getTeamGradeList($setting['team_level']);
        //今日订单数据
        $orderTodayCount = (new OrderModel())->where('user_id', 'in', $userIds)
            ->where('create_time', 'between', [$start_time, $end_time])
            ->where('pay_status' , '=' , OrderPayStatusEnum::SUCCESS)
            ->count();
        //新增成员
        $userTodayCount = (new Referee())->where('user_id', 'in', $userIds)
            ->where('create_time', 'between', [$start_time, $end_time])
            ->count();
        $orderTodayMoney = $OrderModel->where('user_id', 'in', $userIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('pay_time', 'between', [$start_time, $end_time])
            ->sum('pay_price');
        $orderCount = [
            'all' => $this->getCount('all', $user['user_id']),
            'payment' => $this->getCount('payment', $user['user_id']),
            'delivery' => $this->getCount('delivery', $user['user_id']),
            'received' => $this->getCount('received', $user['user_id']),
            'rights' => $this->getCount('rights', $user['user_id']),
        ];

        $data = [
            'orderTodayCount' => $orderTodayCount ? $orderTodayCount : 0,
            'userTodayCount' => $userTodayCount,
            'orderTodayMoney' => $orderTodayMoney ? $orderTodayMoney : 0,
            'orderCount' => $orderCount,
            'directTeam' => $this->getDirectTeam($user,$userIds),
            'lowTeam' => $this->getLowTeam($user,$gradeIds),
            'team' => $this->getTeam($user,$userIds,$gradeIds),
        ];
        return $data;
    }

    //我的团队成员
    public function getTeam($user,$userIds,$gradeIds)
    {
        // 剔除自己
        $userIds = array_diff($userIds,[$user['user_id']]);
        //直属成员
        $setting = (new Setting())::getItem('condition');
        //直属成员
        $directList = (new Referee())->with(['user', 'agent'])
            ->where('user_id', 'in', $userIds)
            ->limit(10)
            ->group('user_id')
            ->select();
        //直属数量
        $directCount = count($userIds);
        //下级团队长
        $lowList = (new Referee())->alias('r')
            ->with(['user', 'agent'])
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->join('agent_grade g' , 'g.grade_id = au.grade_id')
            ->where('g.weight' , '<' , $user['grade']['weight'])
            ->where('agent_id', '=', $user['user_id'])
            ->where('au.grade_id', 'in', $gradeIds)
            ->field('r.*')
            ->limit(10)
            ->select();
        $lowCount = (new Referee())->alias('r')
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->join('agent_grade g' , 'g.grade_id = au.grade_id')
            ->where('g.weight' , '<' , $user['grade']['weight'])
            ->where('agent_id', '=', $user['user_id'])
            ->where('au.grade_id', 'in', $gradeIds)
            ->count();
        //平级团队长
        $flatList = (new Referee())->alias('r')
            ->with(['user', 'agent'])
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->where('agent_id', '=', $user['user_id'])
            ->where('au.grade_id', '=', $user['grade_id'])
            ->where('au.grade_id', 'in', $gradeIds)
            ->field('r.*')
            ->limit(10)
            ->select();
        $flatCount = (new Referee())->alias('r')
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->where('agent_id', '=', $user['user_id'])
            ->where('au.grade_id', '=', $user['grade_id'])
            ->where('au.grade_id', 'in', $gradeIds)
            ->count();
        //越级成员
        $upList = (new Referee())->alias('r')
            ->with(['user', 'agent'])
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->join('agent_grade g' , 'g.grade_id = au.grade_id')
            ->where('g.weight' , '>' , $user['grade']['weight'])
            ->where('agent_id', '=', $user['user_id'])
            ->where('au.grade_id', '>=', $setting['team_level'])
            ->field('r.*')
            ->limit(10)
            ->select();
        $upCount = (new Referee())->alias('r')
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->join('agent_grade g' , 'g.grade_id = au.grade_id')
            ->where('g.weight' , '>' , $user['grade']['weight'])
            ->where('agent_id', '=', $user['user_id'])
            ->where('au.grade_id', 'in', $gradeIds)
            ->count();
        $data['directList'] = $directList;
        $data['directCount'] = $directCount;
        $data['lowList'] = $lowList;
        $data['lowCount'] = $lowCount;
        $data['flatList'] = $flatList;
        $data['flatCount'] = $flatCount;
        $data['upList'] = $upList;
        $data['upCount'] = $upCount;
        $data['team_name'] = isset($setting['team_name']) && $setting['team_name'] != '' ? $setting['team_name'] : '';
        return $data;
    }

    //直属团队数据
    public function getDirectTeam($user,$userLevelIds)
    {
        $OrderModel = new OrderModel;
        //上月销售额
        $startLastMonth = strtotime(date('Y-m-01', strtotime('-1 month')));
        $endLastMonth = strtotime(date('Y-m-t', strtotime('-1 month'))) + 86399;
        $LastMonthMoney = $OrderModel->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('pay_time', 'between', [$startLastMonth, $endLastMonth])
            ->sum('pay_price');
        //当月销售额
        $startOnMonth = date('Y-m-01', time());
        $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
        $startOnMonth = strtotime($startOnMonth);
        $OnMonthMoney = $OrderModel->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('pay_time', 'between', [$startOnMonth, $endOnMonth])
            ->sum('pay_price');
        //差额
        $differMoney = round($OnMonthMoney - $LastMonthMoney, 2);
        //本月进行中
        $inMonthMoney = $OrderModel->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '=', 10)
            ->where('pay_time', 'between', [$startOnMonth, $endOnMonth])
            ->sum('pay_price');
        //已完成
        $ofMonthMoney = $OrderModel->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '=', 30)
            ->where('pay_time', 'between', [$startOnMonth, $endOnMonth])
            ->sum('pay_price');
        //累积销售额
        $totalMoney = $OrderModel->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->sum('pay_price');
        //订单数据
        $list = $OrderModel->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->limit(2)
            ->field("*,FROM_UNIXTIME(create_time,'%m-%d') as create_times")
            ->order('order_id desc')
            ->select();
        $data['monthMoney'] = $OnMonthMoney ? $OnMonthMoney : 0;
        $data['differMoney'] = $differMoney ? $differMoney : 0;
        $data['inMonthMoney'] = $inMonthMoney ? $inMonthMoney : 0;
        $data['ofMonthMoney'] = $ofMonthMoney ? $ofMonthMoney : 0;
        $data['totalMoney'] = $totalMoney ? $totalMoney : 0;
        $data['inRate'] = $inMonthMoney ? round($inMonthMoney / $OnMonthMoney, 2) * 100 : 0;
        $data['offRate'] = $ofMonthMoney ? round($ofMonthMoney / $OnMonthMoney, 2) * 100 : 0;
        $data['list'] = $list;
        return $data;
    }

    //下属团队数据
    public function getLowTeam($user,$gradeIds)
    {
        $setting = (new Setting())::getItem('condition');
        $startOnMonth = date('Y-m', time());
        // 获取下级团长信息
        $userIds = (new Referee())->alias('r')
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->join('user u', 'u.user_id=r.user_id' , 'left')
            ->where('au.grade_id', 'in', $gradeIds)
            ->where('r.agent_id', '=', $user['user_id'])
            ->field('r.user_id,u.nickName')
            ->select();
        $totalMoney = 0; // 累计销售额
        $monthMoney = 0; // 当月销售额
        $levelList = []; // 下级团队累积销售占比
        $levelMonthList = []; // 下级团队当月销售占比
        if (!empty($userIds)) {
            foreach ($userIds as $itemIds) {
                $level = $itemIds;
                $levelMonth = $itemIds;
                // 获取下级团长直属成员id
                // 累计销售额
                $totalMoney += $level['money'] = (new MonthModel())
                    ->where('user_id' , '=' , $itemIds['user_id'])
                    ->where('month' , '=' , $startOnMonth)->sum('direct_team_money');
                //当月销售额
                $monthMoney += $levelMonth['money'] = (new MonthModel())
                    ->where('user_id' , '=' , $itemIds['user_id'])
                    ->where('month' , '=' , $startOnMonth)->value('direct_team_money');
                if ($level['money'] > 0) {
                    $level['money'] = round($level['money'], 2);
                    $levelList[] = $level;
                }
                if ($levelMonth['money'] > 0) {
                    $levelMonth['money'] = round($levelMonth['money'], 2);
                    $levelMonthList[] = $levelMonth;
                }
            }
        }
        $data['monthMoney'] = $monthMoney ? $monthMoney : 0;
        $data['totalMoney'] = $totalMoney ? $totalMoney : 0;
        $data['levelList'] = $levelList;
        $data['levelMonthList'] = $levelMonthList;
        return $data;
    }

    //直属团队数据
    public function orderSale($param, $user)
    {
        $model = new OrderModel;
        $model1 = new OrderModel;
        $model2 = new OrderModel;
        $userLevelIds = $this->getDirectUserIds($user);
        if ($param['month']) {
            $startOnMonth = date('Y-m-01', strtotime($param['month']));
            $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
            $startOnMonth = strtotime($startOnMonth);
            $model = $model->where('create_time', 'between', [$startOnMonth, $endOnMonth]);
            $model1 = $model1->where('create_time', 'between', [$startOnMonth, $endOnMonth]);
            $model2 = $model2->where('create_time', 'between', [$startOnMonth, $endOnMonth]);
        }
        //订单数据
        $list = $model->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->order('order_id desc')
            ->paginate($param);
        $finish = $model1->with(['order'])->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '=', 30)
            ->sum('pay_price');
        $progress = $model2->with(['order'])->where('user_id', 'in', $userLevelIds)
            ->where('pay_status', '=', 20)
            ->where('order_status', '=', 10)
            ->sum('pay_price');
        $data['finish'] = $finish ? $finish : 0;
        $data['progress'] = $progress ? $progress : 0;
        $data['list'] = $list;
        return $data;
    }

    //获取订单数量
    public function getCount($type, $userid)
    {
        $orderModel = new OrderModel();
        $userIds = (new Referee())->where('agent_id', '=', $userid)->column('user_id');
        $filter = [];
        switch ($type) {
            case 'all':
                break;
            case 'payment':
                $filter['pay_status'] = 10;
                break;
            case 'delivery':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'received':
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                break;
            case 'rights'://售后维权
                return OrderRefundModel::where('user_id', 'in', $userIds)->count();
                break;

        }
        $userIds = array_merge($userIds, [$userid]);
        return $orderModel->where('user_id', 'in', $userIds)
            ->where('order_status', '<>', 20)
            ->where($filter)
            ->where('is_delete', '=', 0)
            ->count();
    }

    /**
     * 获取订单列表
     */
    public function getOrderList($data, $user)
    {
        $userIds = $this->getLowerDirectUserIds($user);
        $model = (new OrderModel())->alias('o');
        $filter = [];
        // 订单数据类型
        switch ($data['type']) {
            case 'all':
                break;
            case 'payment';
                $filter['pay_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'delivery';
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'received';
                $filter['pay_status'] = 20;
                $filter['delivery_status'] = 20;
                $filter['receipt_status'] = 10;
                $filter['order_status'] = 10;
                break;
            case 'comment';
                $filter['is_comment'] = 0;
                $filter['order_status'] = 30;
                break;
            case 'finish';
                //$filter['is_comment'] = 1;
                $filter['order_status'] = 30;
                break;
            case 'rights';
                $order_id = OrderRefundModel::where('user_id', 'in', $userIds)->group('order_id')->column('order_id');
                $model = $model->where('o.order_id', 'in', $order_id);
                break;
            case 'cancel';
                $filter['order_status'] = 20;
                break;
            case 'uncancel';
                $filter['order_status'] = 21;
                break;
        }
        if (isset($data['search']) && $data['search']) {
            $model = $model->where('o.order_no|u.nickName|u.mobile|u.realname', 'like', '%' . $data['search'] . '%');
        }
        if (isset($data['pay_type'])) {
            $data['pay_type'] = json_decode($data['pay_type'],1);
            if (!empty($data['pay_type'])) {
                if (in_array(30,$data['pay_type'])) {
                    $data['pay_type'] = array_merge(array_diff($data['pay_type'], array(30)));
                    $pay_type = implode(',',$data['pay_type']);
                    $model = $model->whereRaw("o.pay_method = 2 OR o.pay_type in ($pay_type)");
                } else {
                    $model = $model->whereIn('o.pay_type',  $data['pay_type']);
                }
                $model = $model->where('o.pay_status', '=', 20);
            }
        }
        if (isset($data['delivery_type']) && $data['delivery_type']) {
            $model = $model->where('o.delivery_type', '=', $data['delivery_type']);
        }
        if (isset($data['order_source']) && $data['order_source']) {
            $data['order_source'] = json_decode($data['order_source'],1);
            if (!empty($data['order_source'])) {
                $model = $model->whereIn('o.order_source', $data['order_source']);
            }
        }
        if (isset($data['remark']) && $data['remark'] != '') {
            if ($data['remark'] == 1) {
                $model = $model->where('o.buyer_remark', '<>', '');
            } else {
                $model = $model->where('o.buyer_remark', '=', '');
            }

        }
        if (isset($data['time_type']) && $data['time_type'] > 0) {
            switch ($data['time_type']) {
                case '1'://近一周
                    $startTime = strtotime("-7 days");
                    $endTime = time();
                    break;
                case '2'://近一月
                    $startTime = strtotime("-30 days");
                    $endTime = time();
                    break;
                case '3'://一个月前
                    $startTime = 0;
                    $endTime = strtotime("-30 days");
                    break;
            }
            $model = $model->where('o.create_time', 'between', [$startTime, $endTime]);
        }
        $list = $model->with(['product.image', 'user', 'orderCarditem'])
            ->join('user u' , 'u.user_id = o.user_id' , 'left')
            ->where('o.user_id', 'in', $userIds)
            ->where($filter)
            ->where('o.is_delete', '=', 0)
            ->field('o.*')
            ->order(['o.create_time' => 'desc'])
            ->paginate($data);
        foreach ($list as &$item) {
            $item['team_user'] = '';
            if ($item['team_id'] > 0 && $item['team_id'] != $item['agent_id']) {
                $team_user = $this->where('user_id' , '=' ,$item['team_id'])->field('real_name,mobile')->find();
                if (!empty($team_user)) {
                    $item['team_user'] = $team_user['real_name'] != '' ? $team_user['real_name'] . "(" . $team_user['mobile'] . ")" : $team_user['mobile'];
                }
            }
            $item['total_num'] = OrderProductModel::where('order_id', '=', $item['order_id'])->sum('total_num');
        }
        return $list;
    }

    //订单详情
    public function orderDetail($order_id)
    {
        $model = new OrderModel();
        $order = $model->where(['order_id' => $order_id])->with(['product' => ['image', 'refund'], 'address', 'express', 'extractStore', 'supplier', 'user', 'extract', 'orderCarditem'])->find();
        if (empty($order)) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        $agent = '';
        $award = '';
        if ($order['team_id'] > 0) {
            $agent = $this->where('user_id', '=', $order['team_id'])
                ->with('user')
                ->find();
            $saleDetail = OrderDetail::where('user_id', '=', $order['team_id'])
                ->where('type', '=', 'sale')
                ->where('is_invalid', '=', '0')
                ->find();
            $level = '';
            if ($saleDetail) {
                switch ($saleDetail['level_num']) {
                    case '0':
                        $level = '';
                        break;
                    case '1':
                        $level = '一级';
                        break;
                    case '2':
                        $level = '二级';
                        break;
                    case '3':
                        $level = '三级';
                        break;
                }
            }
            $settings = AgentSettingModel::getItem('reward_team');
            $reward = $settings['team_check'][0];
            $reward_percent = isset($reward[$agent['grade_id']]) ? $reward[$agent['grade_id']] : 0;
            $send_money = helper::number2($order['pay_price'] * $reward_percent / 100);
            $award = [
                'money' => $saleDetail ? $saleDetail['money'] : 0,
                'level' => $level,
                'team_money' => $send_money,
            ];
        }
        $order['agent'] = $agent;
        $order['award'] = $award;
        return $order;
    }

    //我的团队
    public function getTeamList($data, $user,$userIds)
    {
        $model = (new userModel1());
        $setting = (new Setting())::getItem('condition');
        $gradeIds = AgentGradeModel::getTeamGradeList($setting['team_level']);

        $sort = ['au.create_time' => 'desc'];
        $MonthModel = new MonthModel;

        switch ($data['type']) {
            case 'direct'://直属
                $list = $this->getDirectData($data, $user);
                $setting_basic = (new Setting())::getItem('basic');
                return [
                    'list' => $list,
                    'team_name' => isset($setting['team_name']) && $setting['team_name'] != '' ? $setting['team_name'] : '团队长',
                    'agent_user_name' => isset($setting_basic['agent_user_name']) && $setting_basic['agent_user_name'] != '' ? $setting_basic['agent_user_name'] : '分销商'
                ];
                break;
            case 'low'://下级
                $model = $model->where('ag.weight', '<', $user['grade']['weight']);
                break;
            case 'flat'://平级
                $model = $model->where('au.grade_id', '=', $user['grade_id']);
                break;
            case 'up'://越级
                $model = $model->where('ag.weight', '>', $user['grade']['weight']);
                break;
        }
        if (isset($data['search']) && $data['search']) {
            $model = $model->where('r.nickName|real_name|u.mobile', 'like', '%' . $data['search'] . '%');
        }
        if (isset($data['sortType']) && $data['sortType'] == 'money') {
            $sort = $data['moneyPrice'] ? ['totalMoney' => 'desc'] : ['totalMoney' => 'asc'];
        }
        if (isset($data['sortType']) && $data['sortType'] == 'month') {
            $sort = $data['monthPrice'] ? ['sellMoney' => 'desc'] : ['sellMoney' => 'asc'];
        }
        if (isset($data['sortType']) && $data['sortType'] == 'team') {
            $sort = $data['teamValue'] ? ['team_num' => 'desc'] : ['team_num' => 'asc'];
        }
        $totalMoneySql = $MonthModel->field(['SUM(team_money)'])
            ->where('user_id', '=', 'r.user_id')->buildSql();
        $sellMoneySql = $MonthModel->field(['team_money'])
            ->where('month', '=', date('Y-m'))
            ->where('user_id', '=', 'r.user_id')->buildSql();
        $list = $model->alias('r')
            ->with(['agent.grade'])
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->join('agent_grade ag', 'ag.grade_id=au.grade_id')
            ->join('user u', 'u.user_id=r.user_id')
            ->whereIn('r.user_id', $userIds)
            ->where('au.grade_id', 'in', $gradeIds)
            ->field("u.*,$sellMoneySql as sellMoney,$totalMoneySql as totalMoney")
            ->order($sort)
            ->paginate($data);
        foreach ($list as &$value) {
            // 获取直属团队人数
            $userIds = $this->getDirectUserIds($value);
            $value['team_num'] = count($userIds);
            $time = strtotime($value['agent']['create_time']);
            if (date('Y', $time) == date('Y')) {
                $value['agent']['create_times'] = date('m月d日', $time);
            } else {
                $value['agent']['create_times'] = date('Y-m-d', $time);
            }
            $monthInfo = (new MonthModel)->where('user_id', '=', $value['user_id'])
                ->where('month', '=', date('Y-m'))
                ->field("team_money,buy_money")
                ->find();
            $value['buyMoney'] = $monthInfo['buy_money'] ? $monthInfo['buy_money'] : 0;
            $value['sellMoney'] = $value['sellMoney'] ? $value['sellMoney'] : 0;
            $value['totalMoney'] = $value['totalMoney'] ? $value['totalMoney'] : 0;
        }
        return [
            'list' => $list,
            'team_name' => isset($setting['team_name']) && $setting['team_name'] != '' ? $setting['team_name'] : '团队长',
        ];
    }

    //直属团队数据
    public function getDirectData($data, $user)
    {
        $model = (new UserModel1());
        $sort = ['r.create_time' => 'desc'];
        $OrderModel = new OrderModel();
        $MonthModel = new MonthModel();
        $setting = (new Setting())::getItem('condition');
        $userIds = $this->getDirectUserIds($user);
        if (isset($data['search']) && $data['search']) {
            $model = $model->where('r.nickName|r.mobile|au.real_name', 'like', '%' . $data['search'] . '%');
        }
        if (isset($data['user_type']) && $data['user_type'] == 1) {//会员
            if (isset($data['sortType']) && $data['sortType'] == 'money') {
                $sort = $data['moneyPrice'] ? ['expend_money' => 'desc'] : ['expend_money' => 'asc'];
            }
            if (isset($data['sortType']) && $data['sortType'] == 'time') {
                $sort = $data['timeValue'] ? ['r.create_time' => 'desc'] : ['r.create_time' => 'asc'];
            }
            $usersId = $this->where('user_id', 'in', $userIds)->column('user_id');
            $usersId && $model = $model->where('r.user_id', 'not in', $usersId);
        } elseif (isset($data['user_type']) && $data['user_type'] == 2) {//分销商
            if (isset($data['sortType']) && $data['sortType'] == 'sellMoney') {
                $sort = $data['sellPrice'] ? ['sellMoney' => 'desc'] : ['sellMoney' => 'asc'];
            }
            if (isset($data['sortType']) && $data['sortType'] == 'time') {
                $sort = $data['timeValue'] ? ['au.create_time' => 'desc'] : ['au.create_time' => 'asc'];
            }
            $usersId = $this->where('user_id', 'in', $userIds)->column('user_id');
            $model = $model->where('r.user_id', 'in', $usersId);
        }
        if (isset($data['sortType']) && $data['sortType'] == 'month') {
            $sort = $data['monthPrice'] ? ['buyMoney' => 'desc'] : ['buyMoney' => 'asc'];
        }
        $startOnMonth = date('Y-m-01', time());
        $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
        $startOnMonth = strtotime($startOnMonth);
        $buyMoneySql = $OrderModel->field(['sum(pay_price)'])
            ->where('create_time', 'between', [$startOnMonth, $endOnMonth])
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('user_id', '=', 'r.user_id')->buildSql();
        $totalbuyMoneySql = $OrderModel->field(['sum(pay_price)'])
            ->where('pay_status', '=', 20)
            ->where('order_status', '<>', 20)
            ->where('user_id', '=', 'r.user_id')->buildSql();
        $sellMoneySql = $MonthModel->field(['team_money'])
            ->where('month', '=', date('Y-m-d'))
            ->where('user_id', '=', 'r.user_id')->buildSql();
        $list = $model->alias('r')
            ->with(['agent.grade'])
            ->where('r.user_id' , 'in' , $userIds)
            ->whereRaw('r.user_id != ' . $user['user_id'])
            ->join('agent_user au' , 'au.user_id = r.user_id' , 'left')
            ->field("r.*,$totalbuyMoneySql AS totalbuyMoney,$buyMoneySql AS buyMoney,$sellMoneySql as sellMoney")
            ->order($sort)
            ->paginate($data);
        foreach ($list as &$item) {
            $time = strtotime($item['create_time']);
            if (date('Y', $time) == date('Y')) {
                $item['create_times'] = date('m月d日', $time);
            } else {
                $item['create_times'] = date('Y-m-d', $time);
            }
            if ($item['agent'] && $item['agent']['create_time']) {
                $agentTime = strtotime($item['agent']['create_time']);
                if (date('Y', $agentTime) == date('Y')) {
                    $item['agent']['create_times'] = date('m月d日', $agentTime);
                } else {
                    $item['agent']['create_times'] = date('Y-m-d', $agentTime);
                }
            }
            $item['sellMoney'] = $item['sellMoney'] ? $item['sellMoney'] : 0;
            $item['buyMoney'] = $item['buyMoney'] ? $item['buyMoney'] : 0;
            $item['totalbuyMoney'] = $item['totalbuyMoney'] ? $item['totalbuyMoney'] : 0;
        }
        return $list;
    }

    //我的团队数量
    public function getTeamCount($type, $user,$userIds)
    {
        $model = (new userModel1());
        $setting = (new Setting())::getItem('condition');
        $gradeIds = AgentGradeModel::getTeamGradeList($setting['team_level']);
        switch ($type) {
            case 'direct':
                // 获取直属成员数量（不算自己）
                $userIds = $this->getDirectUserIds($user);
                return count($userIds) - 1;
                break;
            case 'low':
                $model = $model->where('ag.weight', '<', $user['grade']['weight']);
                break;
            case 'flat':
                $model = $model->where('ag.weight', '=', $user['grade']['weight']);
                break;
            case 'up':
                $model = $model->where('ag.weight', '>', $user['grade']['weight']);
                break;
        }
        $model = $model->whereIn('r.user_id',$userIds);
        $count = $model->alias('r')
            ->join('agent_user au', 'au.user_id=r.user_id')
            ->join('agent_grade ag', 'au.grade_id=ag.grade_id')
//            ->where('agent_id', '=', $user['user_id'])
            ->where('au.grade_id', 'in', $gradeIds)
            ->count();
        return $count;
    }

    //获取最新结算月份数据
    public function getMoth($user)
    {
        $OrderDetailModel = new OrderDetail();
        $startOnMonth = date('Y-m-01', time());
        $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
        $startOnMonth = strtotime($startOnMonth);
        $infoMoney = $OrderDetailModel->where('user_id', '=', $user['user_id'])
            ->where('type', '<>', 'team')
            ->where('is_invalid', '=', '0')
            ->where('create_time', 'between', [$startOnMonth, $endOnMonth])
            ->value('sum(money)');
        $money = 0;
        $month = date('m');
        $monthData = date('Y-m');
        if ($infoMoney) {
            $money = $infoMoney ? $infoMoney : 0;
        }
        $OrderSettledModel = new OrderSettledModel();
        $team_money = $OrderSettledModel->where('type', '=', 'team')->where('month', '=', $monthData)->value('money');
        $team_money = $team_money ? $team_money : 0;
        $money = $money + $team_money;
        $data['year'] = date('Y');
        $data['month'] = $month;
        $data['money'] = $money;
        $data['monthData'] = $monthData;
        $data['totalMoney'] = $user['money'] + $user['freeze_money'] + $user['total_money'];
        return $data;
    }

    //获取奖励明细
    public function getAward($data, $user)
    {
        $OrderDetailModel = new OrderDetail();
        $RefereeModel = (new Referee());
        $OrderSettledModel = new OrderSettledModel();
        $startOnMonth = date('Y-m-01', strtotime($data['month']));
        $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
        $startOnMonth = strtotime($startOnMonth);
        $orderDetail = $OrderDetailModel->where('user_id', '=', $user['user_id'])
            ->where('create_time', 'between', [$startOnMonth, $endOnMonth])
            ->where('type', '<>', 'team')
            ->where('is_invalid', '=', '0')
            ->order('is_settled desc')
            ->group('is_settled')
            ->field('is_settled,sum(money) money,type,level_num')
            ->select()->toarray();

        // 获取等级列表
        $gradeList = (new AgentGradeModel)->getLists()->toArray();
        // 等级id赋值为键值
        $gradeList = helper::arrayColumn2Key($gradeList, 'grade_id');
        $settled_money = 0;
        $unsettled_money = 0;
        $totalMoney = 0;
        $mothList = [];
        if ($orderDetail) {
            //预计奖励
            $team_money = $OrderSettledModel->where('type', '=', 'team')->where('month', '=', $data['month'])->where('user_id' , '=' , $user['user_id'])->value('money');
            $team_money = $team_money ? $team_money : 0;
            foreach ($orderDetail as $value) {
                if ($value['is_settled'] == 0) {
                    $unsettled_money = isset($value['money']) ? $value['money'] : 0;
                } elseif ($value['is_settled'] == 1) {
                    $settled_money = isset($value['money']) ? $value['money'] : 0;
                }
            }
            $settled_money = $settled_money + $team_money;
            $totalMoney = $settled_money + $unsettled_money;

            $mothList = $OrderDetailModel::where('create_time', 'between', [$startOnMonth, $endOnMonth])
                ->where('user_id', '=', $user['user_id'])
                ->where('is_invalid', '=', '0')
                ->where('type', 'not in', "exchangepurch")
                ->whereRaw("(is_settled =1 and settled_id > 0) OR (is_settled = 0 and settled_id = 0)")
                ->group('type')
                ->field('type,sum(money) as money,is_settled,type,level_num,team_percent')
                ->select()->toarray();
            foreach ($mothList as $key => &$item) {
                $item['money'] = round($item['money'], 2);
                $noSettledMoney = $OrderDetailModel::where('create_time', 'between', [$startOnMonth, $endOnMonth])
                    ->where('user_id', '=', $user['user_id'])
                    ->where('type', '=', $item['type'])
                    ->where('is_invalid', '=', '0')
                    ->where('is_settled', '=', 0)
                    ->where('settled_id' , '=' , 0)
                    ->sum('money');
                $item['noSettledMoney'] = 0;
                $item['firstMoney'] = 0;
                if (in_array($item['type'], ['level', 'same', 'than', 'expand','level_bonus','expand_bonus'])) {
                    $thisMonth = strtotime(date('Y-m'));
                    $firstMoney = $OrderDetailModel::where('create_time', 'between', [$startOnMonth, $endOnMonth])
                        ->where('user_id', '=', $user['user_id'])
                        ->where('is_invalid', '=', '0')
                        ->where('level_num', '=', 1)
                        ->where('type', '=', $item['type'])
                        ->whereRaw("IF('$thisMonth' > '$startOnMonth' , is_settled = 1 , '1')")
                        ->sum('money');
                    $secondMoney = $OrderDetailModel::where('create_time', 'between', [$startOnMonth, $endOnMonth])
                        ->where('user_id', '=', $user['user_id'])
                        ->where('level_num', '=', 2)
                        ->where('type', '=', $item['type'])
                        ->where('is_invalid', '=', '0')
                        ->whereRaw("IF('$thisMonth' > '$startOnMonth' , is_settled = 1 , '1')")
                        ->sum('money');

                    $item['firstMoney'] = $firstMoney ? $firstMoney : 0;
                    $item['secondMoney'] = $secondMoney ? $secondMoney : 0;
                }
                $item['noSettledMoney'] = $noSettledMoney ? $noSettledMoney : 0;
                $item['settledMoney'] = round($item['money'] - $item['noSettledMoney'], 2);
                $item['orderCount'] = $OrderDetailModel::where('user_id', '=', $user['user_id'])
                    ->where('create_time', 'between', [$startOnMonth, $endOnMonth])
                    ->where('type', '=', $item['type'])
                    ->where('is_invalid', '=', '0')
                    ->count();
                $item['refereeCount'] = 0;
                $item['saleCount'] = 0;
                $item['leaderCount'] = 0;
                $item['directorCount'] = 0;
                $item['joinerCount'] = 0;
                $item['lowNum'] = 0;
                $item['flatNum'] = 0;
                $item['upNum'] = 0;
                $item['expandNum'] = 0;
                $item['team_money'] = 0;
                $item['buy_money'] = 0;

                if ($item['type'] == 'referee') {//推荐奖
                    //推荐人数
                    $refereeData = $RefereeModel->alias('r')
                        ->where('r.create_time', 'between', [$startOnMonth, $endOnMonth])
                        ->where('agent_id', '=', $user['user_id'])
                        ->where('r.level', '=', 1)
                        ->join('agent_user au', 'au.user_id=r.user_id')
                        ->join('agent_grade g', 'g.grade_id=au.grade_id')
                        ->order('g.grade_id asc')
                        ->group('au.grade_id')
                        ->field('g.name,count(au.user_id) as num,g.grade_id')
                        ->select()->toarray();

                    $item['refereeCount'] = 0;
                    if (!empty($refereeData)) {
                        foreach ($refereeData as $gradeItem)
                        {
                            $item['refereeCount'] += $gradeItem['num'];
                            if (isset($gradeList[$gradeItem['grade_id']])) {
                                $gradeList[$gradeItem['grade_id']]['num'] =  $gradeItem['num'];
                            }
                        }
                    }
                    $gradeList = array_values($gradeList);
                    // 基数数组 添加空值 页面显示
                    if (count($gradeList)%2 != 0) {
                        $gradeList[count($gradeList)] = ['name'=>'','grade_id'=>''];
                    }
                    $item['gradeList'] = $gradeList;
                }
                //下级团队
                if ($item['type'] == "level") {
                    $item['lowNum'] = $this->getTeamMothCount('low', $user, $data['month']);
                }
                if ($item['type'] == "level_bonus") {
                    $item['lowNum'] = $this->getTeamMothCount('bonus_low', $user, $data['month']);
                }
                if ($item['type'] == "same") {
                    //平级团队
                    $item['flatNum'] = $this->getTeamMothCount('flat', $user, $data['month']);
                }
                if ($item['type'] == "sale_bonus") {
                    //平级团队
                    $item['flatNum'] = $this->getTeamMothCount('bonus_flat', $user, $data['month']);
                }
                if ($item['type'] == "than") {
                    //越级团队
                    $item['upNum'] = $this->getTeamMothCount('up', $user, $data['month']);
                }
                if ($item['type'] == "expand") {
                    //拓展团队
                    $item['expandNum'] = $this->getTeamMothCount('expand', $user, $data['month']);
                }
                if ($item['type'] == "expand_bonus") {
                    //拓展团队
                    $item['expandNum'] = $this->getTeamMothCount('expand_bonus', $user, $data['month']);
                }

                //区域销售奖
                $item['areaMoney'] = 0;
                if ($item['type'] == 'area') {
                    // 设置区域查询条件
                    $agent_level = isset($user['area']['agent_level']) ? $user['area']['agent_level'] : 0;
                    if ($agent_level == 1) {
                        $where = ['province_id' => isset($user['area']['province_id']) ? $user['area']['province_id'] : 0];
                    } elseif ($agent_level == 2) {
                        $where = ['city_id' => isset($user['area']['city_id']) ? $user['area']['city_id'] : 0];
                    } elseif ($agent_level == 3) {
                        $where = ['region_id' => isset($user['area']['region_id']) ? $user['area']['region_id'] : 0];
                    } else {
                        $where = ['province_id' => 0];
                    }
                    $pay_price = $OrderDetailModel->alias('od')
                        ->join('order o', 'o.order_id=od.order_id')
                        ->join('order_address oa', 'oa.order_id=od.order_id')
                        ->where('od.create_time', 'between', [$startOnMonth, $endOnMonth])
                        ->where('od.user_id', '=', $user['user_id'])
                        ->where('od.is_invalid', '=', '0')
                        ->where('od.type', '=', 'expand')
                        ->where('order_status', '<>', 20)
                        ->where('pay_status', '=', 20)
                        ->where($where)
                        ->sum('pay_price');
                    $item['areaMoney'] = $pay_price;
                }
                if ($item['type'] == "team") {
                    if ($data['month'] == date('Y-m')) {
                        unset($mothList[$key]);
                    }
//
                    $orderCount = $OrderDetailModel
                        ->where('user_id' , '=' , $user['user_id'])
                        ->where('create_time', 'between', [$startOnMonth, $endOnMonth])
                        ->where('type', '=', 'team')
                        ->where('is_invalid', '=', '0')
                        ->whereRaw("IF(" . strtotime(date('Y-m')) . " > create_time , is_settled = 1 , 1 )")
                        ->count();
                    $item['orderCount'] = $orderCount;
                    $monthInfo = (new MonthModel)->where('user_id', '=', $user['user_id'])
                        ->where('month', '=', $data['month'])
                        ->field("team_money,buy_money")
                        ->find();
                    $item['team_money'] = $monthInfo['team_money'];
                    $item['buy_money'] = $monthInfo['buy_money'];
                    $teamMoney = $OrderSettledModel->field('percent,money')
                        ->where('type', '=', 'team')
                        ->where('month', '=', $data['month'])
                        ->where('user_id', '=', $user['user_id'])
                        ->find();
                    $item['money'] = isset($teamMoney['money']) ? $teamMoney['money'] : 0;
                    $item['money'] = round($item['money'], 2);
                    $item['team_percent'] = $teamMoney['percent'];
                }
                if ($item['type'] == "bouns") {
                    if ($data['month'] == date('Y-m')) {
                        unset($mothList[$key]);
                    }
//
                    $orderCount = $OrderDetailModel
                        ->where('user_id' , '=' , $user['user_id'])
                        ->where('create_time', 'between', [$startOnMonth, $endOnMonth])
                        ->where('type', '=', 'bouns')
                        ->where('is_invalid', '=', '0')
                        ->whereRaw("IF(" . strtotime(date('Y-m')) . " > create_time , is_settled = 1 , 1 )")
                        ->count();
                    $item['orderCount'] = $orderCount;
                    $monthInfo = (new MonthModel)->where('user_id', '=', $user['user_id'])
                        ->where('month', '=', $data['month'])
                        ->field("venturebonus_team_money,venturebonus_buy_money")
                        ->find();
                    $item['team_money'] = $monthInfo['venturebonus_team_money'];
                    $item['buy_money'] = $monthInfo['venturebonus_buy_money'];
                    $teamMoney = $OrderSettledModel->field('percent,money')
                        ->where('type', '=', 'bouns')
                        ->where('month', '=', $data['month'])
                        ->where('user_id', '=', $user['user_id'])
                        ->find();
                    $item['money'] = isset($teamMoney['money']) ? $teamMoney['money'] : 0;
                    $item['money'] = round($item['money'], 2);
                    $item['team_percent'] = $teamMoney['percent'];
                }
            }
        }
        array_multisort($mothList);
        $info['settled_money'] = $settled_money;
        $info['unsettled_money'] = $unsettled_money;
        $info['totalMoney'] = round($totalMoney, 2);
        $info['mothList'] = $mothList;
        return $info;
    }

    //我的团队数量
    public function getTeamMothCount($type, $user, $month)
    {
        $model = new OrderDetail();
        $startOnMonth = date('Y-m-01', strtotime($month));
        $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
        $startOnMonth = strtotime($startOnMonth);
        switch ($type) {
            case 'low':
                $model = $model->where('type', '=', 'level');
                break;
            case 'flat':
                $model = $model->where('type', '=', 'same');
                break;
            case 'up':
                $model = $model->where('type', '=', 'than');
                break;
            case 'expand':
                $model = $model->where('type', '=', 'expand');
                break;
            case 'bonus_low':
                $model = $model->where('type', '=', 'level_bonus');
                break;
            case 'bonus_flat':
                $model = $model->where('type', '=', 'sale_bonus');
                break;
        }

        return $model->alias('od')->where('od.create_time', 'between', [$startOnMonth, $endOnMonth])
            ->where('od.user_id', '=', $user['user_id'])
            ->where('od.team_user_id', '>', 0)
            ->where('od.is_invalid', '=', '0')
            ->group('od.team_user_id')
            ->count();
    }

    //获取累积奖励
    public function getReward($data, $user)
    {
        $search_type = isset($data['search_type']) ? $data['search_type'] : 0;
        $model = new OrderDetail();
        $startOnMonth = date('Y-m-01', strtotime($data['month']));
        $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
        $startOnMonth = strtotime($startOnMonth);
        $thisMonth = strtotime(date('Y-m'));
        $model = $model->alias('od');
        if ($search_type == 1) {
            $model = $model->where('od.create_time', 'between', [$startOnMonth, $endOnMonth]);
        } else {
            $model = $model->where('od.create_time', '<=', $endOnMonth);
        }
        $model = $model->where('od.user_id', '=', $user['user_id']);
        if ($data['type']) {
            $model = $model->where('od.type', '=', $data['type']);
        }
        $field_money = '';
        if ($data['type'] != 'referee') {
            $model = $model->join('order o', 'od.order_id=o.order_id','left');
            $field = 'o.order_no,';
            if ($data['type'] == 'sale_bonus') {
                $field_money = "(od.money*od.team_percent*0.01) as sale_money ,";
            }
            if ($data['type'] == '0') {
                $model = $model->join('agent_order_settled aos', 'od.settled_id=aos.settled_id','left');
                $field = "IF(od.type = 'referee' , aos.settled_no , o.order_no) as order_no,";
            }
//            $model = $model->whereRaw('(od.is_settled = 1 AND od.settled_id > 0) OR (od.is_settled = 0 AND od.settled_id = 0)');
            $model = $model->whereRaw("IF(od.type in ('level','same','than','expand') AND $thisMonth > od.create_time , od.is_settled = 1 , 1 )");
        } else {
            $model = $model->join('agent_order_settled o', 'od.settled_id=o.settled_id');
            $field = "o.settled_no as order_no,";
        }
        $model = $model->where('od.is_invalid', '=', '0');
//        $model = $model->whereRaw('od.is_invalid', '=', '0');
        $list = $model
            ->field($field. $field_money . "od.*,FROM_UNIXTIME(od.create_time,'%Y-%m') as months")
            ->order('create_time DESC')
            ->paginate($data);
        $mothIds = array_unique(helper::getArrayColumn($list, 'months'));
        $monthList = [];
        foreach ($mothIds as $key => &$item) {
            $lists = [];
            foreach ($list as $value) {
                if ($item == $value['months']) {
                    $value['sale_money'] = isset($value['sale_money']) ? round($value['sale_money'], 2) : 0;
                    $lists[] = $value;
                }
            }
            $month['months'] = $item;
            $month['lists'] = $lists;
            $startOnMonth1 = date('Y-m-01', strtotime($item));
            $endOnMonth1 = strtotime(date('Y-m-d', strtotime("$startOnMonth1 +1 month -1 day"))) + 86399;
            $startOnMonth1 = strtotime($startOnMonth1);
            $models = OrderDetail::where('create_time', 'between', [$startOnMonth1, $endOnMonth1])
                ->where('user_id', '=', $user['user_id'])
                ->where('is_invalid', '=', '0');
            if ($data['type']) {
                $models = $models->where('type', '=', $data['type']);
            }
            $modelss = OrderDetail::where('create_time', 'between', [$startOnMonth1, $endOnMonth1])
                ->where('user_id', '=', $user['user_id'])
                ->where('is_invalid', '=', '0');
            if ($data['type']) {
                $modelss = $modelss->where('type', '=', $data['type']);
            }
            $settled_money = $models->where('is_settled', '=', 1)
//                ->fetchSql()
                ->sum('money');
//            var_dump($settled_money);die;
            if (in_array($data['type'],  ['level', 'same', 'than', 'expand'])) {
                // 剔除考核不达标的
                $modelss = $modelss->whereRaw("IF($thisMonth > create_time , detail_id = 0 , 1 )");
            }
            $unsettled_money = $modelss->where('is_settled', '=', 0)
                ->where('settled_id' , '=' , 0)
                ->sum('money');
            //预计奖励
            $settled_money = $settled_money ? $settled_money : 0;
            $unsettled_money = $unsettled_money ? $unsettled_money : 0;
            $totalMoney = round($settled_money + $unsettled_money, 2);
            $month['settled_money'] = $settled_money;
            $month['unsettled_money'] = $unsettled_money;
            $month['totalMoney'] = $totalMoney;
            $monthList[] = $month;
        }
        $info['list'] = $list;
        $info['monthList'] = $monthList;
        return $info;
    }

    //获取排行榜
    public function getRank($data, $user)
    {
        $setting = (new Setting())::getItem('condition');
        $model = new MonthModel;
        $startOnMonth = date('Y-m-01', strtotime($data['month']));
        $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
        $startOnMonth = strtotime($startOnMonth);
        if ($data['type'] == 1) {
            //个人排行榜
            $model = $model->where('buy_money', '>', 0)->order('buy_money desc');
        } else {
            //团队排行
            $model = $model->where('direct_team_money', '>', 0)->order('direct_team_money desc');
        }
        //销售排行榜
        $list = $model->with(['user' => ['user', 'grade'] , 'orUser'])
            ->where('month', '=', $data['month'])
            ->paginate($data);
        $per_page = $list->listRows();
        $current_page = $list->currentPage();
        $userIdList = [];
        foreach ($list as $key => &$item) {
            // 普通会员分销
            if (empty($item['user']) && !empty($item['orUser'])) {
                $item['user'] = [
                    'useer_id' => $item['orUser']['user_id'],
                    'grade' => [],
                    'user' => $item['orUser'],
                    'grade_id' => 0,
                    'real_name' => $item['orUser']['nickName']
                ];
            }

            $item['team_money'] = $item['direct_team_money'];
            $item['totalNum'] = 0;
            $item['totalOrder'] = 0;
            if ($data['type'] == 2) {
                $userLevelIds = $this->getDirectUserIds($item);
                $userIdList[$item['user_id']] = $userLevelIds;
                $item['totalNum'] = count($userLevelIds);
                $item['totalOrder'] = (new OrderModel())->where('user_id', 'in', $userLevelIds)
                    ->where('create_time', 'between', [$startOnMonth, $endOnMonth])
                    ->where('order_status', '<>', 20)
                    ->where('pay_status', '=', 20)
                    ->count();
            }
            //排名
            $item['rank'] = ($current_page - 1) * $per_page + $key + 1;
        }
        $total = $list->total();
        $models = (new MonthModel)->where('month', '=', $data['month']);
        $userInfo = (new MonthModel)->where('user_id', '=', $user['user_id'])->where('month', '=', $data['month'])->find();
        $user['team_money'] = 0;
        $user['buy_money'] = 0;
        //个人信息
        if ($data['type'] == 1) {
            //个人
            if (!$userInfo) {
                $user['rank'] = $total + 1;
            } else {
                $rank = $models->where('buy_money', '>', $userInfo['buy_money'])->count();
                $user['rank'] = $rank + 1;
                $user['buy_money'] = $userInfo['buy_money'];
            }
        } else {
            if (!$userInfo) {
                $user['rank'] = $total + 1;
            } else {
                $rank = $models->where('direct_team_money', '>', $userInfo['direct_team_money'])->count();
                $user['rank'] = $rank + 1;
                $user['team_money'] = $userInfo['direct_team_money'];
            }
            $userIds = isset($userIdList[$user['user_id']]) ? $userIdList[$user['user_id']] : [$user['user_id']];
            $user['totalNum'] = count($userIds);
            $user['totalOrder'] = (new OrderModel())->where('user_id', 'in', $userIds)
                ->where('create_time', 'between', [$startOnMonth, $endOnMonth])
                ->where('order_status', '<>', 20)
                ->where('pay_status', '=', 20)
                ->count();
        }
        $info['list'] = $list;
        $info['userInfo'] = $user;
        return $info;
    }

}