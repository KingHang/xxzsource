<?php

namespace app\api\model\plus\sign;

use app\common\model\plugin\sign\Sign as SignModel;
use app\common\exception\BaseException;
use app\api\model\settings\Settings as SettingModel;
use app\common\model\plugin\sign\SignReward;
use think\db\exception\DbException;
use think\Paginator;

/**
 * 用户签到模型模型
 */
class Sign extends SignModel
{
    protected $error = '';

    /**
     * 连续签到天数
     * @param $user_id
     * @param $sign_date
     * @param int $is_replenish
     * @return bool|int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDays($user_id, $sign_date,$is_replenish = 0)
    {
        $row = $this->where(function ($query) use ($is_replenish,$sign_date,$user_id) {
            $query->where('user_id', '=', $user_id);
            if ($is_replenish === 1) {
                $query->where('create_time', '<', strtotime($sign_date)+24*60*60);
            }
        })->order(['create_time' => 'desc'])->find();

        if (empty($row)) {
            return 1;
        }
        $dif = (strtotime($sign_date) - strtotime($row['create_time'])) / (24 * 60 * 60);
        if (strtotime($row['sign_date']) == strtotime($sign_date)) {
//            throw new BaseException(['msg' => '今天已签到']);
            $this->error = $is_replenish ===1 ? '已签到，不能进行补签' : '今天已签到';
            return false;
        }
        if ($dif > 1) {
            return 1;
        }
        if ($dif < 1) {
            return $row['days'] + 1;
        }
    }

    /**
     * 签到
     * @param $user
     * @param $post
     * @return bool|string|array
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\PDOException
     */
    public function add($user,$post)
    {
        $user_id = $user['user_id'];

        //积分配置和成长值配置
        $is_points = SettingModel::getIsPoints();
        $points_name = SettingModel::getPointsName();
        $is_grow = SettingModel::getIsSignGrow();
        $growth_name = SettingModel::getGrowthName();

        //获取签到配置
        $sign_conf = SettingModel::getItem('sign');
        //签到日期
        $is_replenish = 0;
        if (isset($post['day']) && $post['day'] > 0) {
            if (intval(date('d', time())) == $post['day']) {
                $this->error = '当天不能进行补签，请正常签到';
                return false;
            }
            // 验证用户剩余积分是否满足补签扣除
            if ($is_points && isset($sign_conf['replenish_sign']) && $sign_conf['replenish_sign'] > $user['exchangepurch']) {
                $this->error = '剩余'.$points_name.'不足，无法进行补签';
                return false;
            }
            // 验证用户剩余成长值是否满足补签扣除
            if ($is_grow && isset($sign_conf['replenish_grow']) && $sign_conf['replenish_grow'] > $user['growth_value']) {
                $this->error = '剩余'.$growth_name.'不足，无法进行补签';
                return false;
            }
            $is_replenish = 1;
            // 补签
            $sign_date = date('Y-m-d', mktime(0, 0, 0, date('m'), $post['day'], date('Y')));
        } else {
            // 日常签到
            $sign_date = date('Y-m-d', time());
            $days = $this->getDays($user_id, $sign_date,$is_replenish);
            if (!empty($sign_conf['reward_data'])){
                if ($this->check_is_reach($sign_conf,$days,$user_id)==false){
                    $this->error = '请先领取奖励后再签到';
                    return false;
                }
            }
        }
        //获取连续签到天数
        $days = $this->getDays($user_id, $sign_date,$is_replenish);
        if (!$days) {
            return false;
        }

        // 更新记录
        $this->startTrans();
        try {
            //修改用户积分
            $pointsInfo = $user->setPoints($user_id, $days, $sign_conf, $sign_date, $is_points, $is_grow);

            $prize = '';
            $msg = "签到成功";
            if ($is_points) {
                $prize .= ($pointsInfo['exchangepurch'].$points_name);
                $msg = "，奖励{$points_name}{$pointsInfo['exchangepurch']}个";
            }
            if ($is_grow) {
                $prize .= ('，'.$pointsInfo['grow'].$growth_name);
                $msg = "，奖励{$growth_name}{$pointsInfo['grow']}个";
            }
            $prize = trim($prize, '，');

            $data = [
                'user_id' => $user_id,
                'sign_date' => $sign_date,
                'sign_day' => isset($post['day']) && $post['day'] > 0 ? $post['day'] : intval(date('d', time())),
                'days' => $days,
                'exchangepurch' => $is_points ? $pointsInfo['exchangepurch'] : 0,
                'growth_value' => $is_grow ? $pointsInfo['grow'] : 0,
                'prize' => $prize,
                'app_id' => self::$app_id
            ];

            // 补签扣除 补签积分
            if (isset($post['day']) && $post['day'] > 0) {
                $data['replenish_time'] = time();
                $data['type'] = 1;
                $data['update_time'] = $data['create_time'] = mktime(date('H'), date('i'), date('s'), date('m'), $post['day'], date('Y'));
                if ($is_points && isset($sign_conf['replenish_sign']) && $sign_conf['replenish_sign'] > 0) {
                    $user->setIncPoints('-' . $sign_conf['replenish_sign'], '补签:补签日期'.$sign_date, 2);
                }
                if ($is_grow && isset($sign_conf['replenish_grow']) && $sign_conf['replenish_grow'] > 0) {
                    $user->setIncGrowthValue('-' . $sign_conf['replenish_grow'], '补签:补签日期'.$sign_date, 6);
                }
            }

            $this->save($data);

            $this->commit();

            return array(
                'msg' => $msg,
                'exchangepurch' => $is_points ? $pointsInfo['exchangepurch'] : 0,
                'grow' => $is_grow ? $pointsInfo['grow'] : 0
            );
        } catch (\Exception $e) {
            $this->rollback();
            return '';
        }
    }

    /**
     * 获取签到信息
     * @param $user
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getListByUserId($user)
    {
        //积分配置和成长值配置
        $is_points = SettingModel::getIsPoints();
        $points_name = SettingModel::getPointsName();
        $is_grow = SettingModel::getIsSignGrow();
        $growth_name = SettingModel::getGrowthName();

        //获取签到配置
        $sign_conf = SettingModel::getItem('sign');
        $count_day = date("t"); // 获取本月总天数
        $this_day = intval(date('d', time()));
        $SignReward = new SignReward();
        $user_id = $user['user_id'];
        $str = date('Y-m-d', time());
        $arr = explode('-', $str);
        $start_time = strtotime($arr[0] . '-' . $arr[1] . '-01');
        $list = $this->where('user_id', '=', $user_id)
            ->where('create_time', 'between', [$start_time, time()])
            ->order(['create_time' => 'desc'])->select()->toArray();
        $res = array_column($list, 'sign_day');
        $day_list = array();
        // 获取本月第一天是当周第几天
        $week = date("w",mktime(0,0,0,date('m'),1,date('Y'))); // 星期几
        // 补齐第一周日期
        for($i=1;$i<=$week;$i++){
            $day_list[] = array(
                'day' => '',
                'is_sign' => 0,
                'is_today' => 0, // 是否当天(1:是，0：否)
                'before_today' => 1, // 是否今天之前（1：是，0否）
                'can_replenish' => 0, // 是否可以补签 1：可以，0不可以
                'after_today' => 0// 是否今天之后 1：是，0否
            );
        }
        // 获取当月天数 并标记是否签到
        for($i=1;$i<=$count_day;$i++){
            $day_list[] = array(
                'day' => $i,
                'is_sign' => in_array($i,$res) ? 1 : 0,
                'is_today' => $this_day == $i ? 1 : 0, // 是否当天(1:是，0：否)
                'before_today' => $this_day > $i ? 1: 0, // 是否今天之前（1：是，0否）
                'can_replenish' => !in_array($i,$res) && $this_day > $i ? 1 : 0, // 是否可以补签 1：可以，0不可以
                'after_today' => $this_day < $i ? 1: 0, // 是否今天之后 1：是，0否
            );
        }
        $len = count($list);
        $is_reach =0.1;
        $while_day = 1;
        $create_time = 0;
        $dayss = 0;
        if ($len == 0) {
            $days =  $this->getDays($user_id, 0,0) - 1;
            $is_sign = 0;
        }else{
            $sign_date = date('Y-m-d', time());
            $dif = (strtotime($sign_date) - strtotime($list[0]['create_time'])) / (24 * 60 * 60);
            if ($dif > 1) {
                $days = 0;
            }
            if ($dif < 1) {
                $days = $list[0]['days'];

                $rewardData = array();
                if (!empty($sign_conf['reward_data'])){
                    foreach ($sign_conf['reward_data'] as $reward){
                        $rewardData[] = $reward['day'];
                    }
                    if ($days>max($rewardData)){//如果连续签到天数>设置最大天数 取余
//                    $is_reach = $days / max($rewardData);
                        $while_day = ceil($days / max($rewardData));//取整
                        $dayss = $days % max($rewardData);//取余
                    }
                }

                $create_time = strtotime($list[0]['create_time']);
            }

            $is_sign = ($list[0]['sign_date'] == date('Y-m-d', time())) ? 1 : 0;
        }

        // 合计签到次数
        $count = $this->where('user_id' , '=' , $user_id)->count();

        $list = array(
            'while_day' => $while_day,//第几轮签到
            'list' => $day_list,// 签到记录
            'cont_sign' => $days,// 连续签到天数
            'month_sign' => $len,// 当月签到次数
            'is_sign' => $is_sign,//是否签到 0：否，1：是
            'this_day' => $this_day,// 当天时间
            'count' => $count,
            'exchangepurch' => $user['exchangepurch'],
            'growth_value' => $user['growth_value'],
            'replenish_sign' => isset($sign_conf['replenish_sign']) ? $sign_conf['replenish_sign'] : 0,// 补签所需积分
            'replenish_grow' => isset($sign_conf['replenish_grow']) ? $sign_conf['replenish_grow'] : 0,// 补签所需成长值
            'is_points' => $is_points,
            'points_name' => $points_name,
            'is_grow' => $is_grow,
            'growth_name' => $growth_name
        );

        //获取连续天道奖励
        $reward_data = array();
        if (isset($sign_conf['reward_data'])) {
            foreach ($sign_conf['reward_data'] as $key=>$val) {
                $data = array(
                    'grow' => isset($val['grow']) ? $val['grow'] : 0,
                    'exchangepurch' => $val['exchangepurch'],
                    'day'   => $val['day'],
                    'day_text'   => '连续' . $val['day'] . '天',
                    'is_reach' => 0, // 是否达成
                    'is_receive' => 0// 是否领取
                );
                // 判断是否满足领取条件
                if($dayss ==0){
                    $dayss = $days;
                }
                if ($val['day'] <=$dayss) {
                    $data['is_reach'] = 1;
                }
                // 判断是否领取
                if ($data['is_reach'] == 1) {
                    $reach_time =  $create_time - (($days - $val['day']) * 24 * 60 *60);
                    if ($SignReward->checkReach($user_id,$reach_time,$val['day'],0,$while_day)) {
                        $data['is_receive'] = 1;
                    }
                }
                $reward_data[] = $data;
            }
        }

        // 获取累计签到奖励
        $total_data = array();
        if (isset($sign_conf['total_data'])) {
            foreach ($sign_conf['total_data'] as $key=>$val) {
                $data = array(
                    'grow' => $val['total_grow'],
                    'exchangepurch' => $val['total_point'],
                    'day'   => $val['total_day'],
                    'day_text'   => $val['total_day'] . '天',
                    'is_reach' => 0, // 是否达成
                    'is_receive' => 0// 是否领取
                );
                // 判断是否满足领取条件
                if ($val['total_day'] <= $list['month_sign']) {
                    $data['is_reach'] = 1;
                }
                // 判断是否领取
                if ($data['is_reach'] == 1) {
                    $reach_time = mktime(0, 0, 0, date('m'), 1, date('Y'));
                    if ($SignReward->checkReach($user_id,$reach_time,$val['total_day'],1)) {
                        $data['is_receive'] = 1;
                    }
                }
                $total_data[] = $data;
            }
        }
        return compact('list', 'reward_data', 'total_data');
    }

    /**
     * 领取签到奖励
     * @param $user
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function receive($user,$data)
    {
        // 验证传参
        if (!in_array($data['type'] , array(1,2))) {
            $this->error = '无效奖励类型';
            return false;
        }
        //积分配置和成长值配置
        $is_points = SettingModel::getIsPoints();
        $is_grow = SettingModel::getIsSignGrow();
        $list = $this->getListByUserId($user);
        $is_receive = 0;
        //领取连续签到奖励
        if ($data['type'] == 1) {
            if (empty($list['reward_data'])) {
                $this->error = '当前奖励已失效';
                return false;
            }
            foreach ($list['reward_data'] as $val) {
                if ($val['is_reach'] === 1 && $val['is_receive'] === 0 && $val['day'] == $data['day']) {
                    if ($is_points) {
                        // 新增积分变动明细
                        $user->setIncPoints($val['exchangepurch'], '连续签到' . $val['day'] . '天', 1);
                    }
                    if ($is_grow) {
                        // 新增成长值变动明细
                        $user->setIncGrowthValue($val['grow'], '连续签到' . $val['day'] . '天', 1);
                    }
                    // 增加奖励明细
                    (new SignReward())->save(array(
                        'user_id' => $user['user_id'],
                        'while_day'=>$list['list']['while_day'],
                        'days' => $data['day'],
                        'exchangepurch' => $val['exchangepurch'],
                        'growth_value' => $val['grow'],
                        'type' => 0,
                        'app_id' => self::$app_id,
                    ));
                    $is_receive = 1;
                }
            }
        } elseif ($data['type'] == 2) {
            if (empty($list['total_data'])) {
                $this->error = '当前奖励已失效';
                return false;
            }
            // 累计签到奖励
            foreach ($list['total_data'] as $val) {
                if ($val['is_reach'] === 1 && $val['is_receive'] === 0 && $val['day'] == $data['day']) {
                    if ($is_points) {
                        // 新增积分变动明细
                        $user->setIncPoints($val['exchangepurch'], '累计签到' . $val['day'] . '天', 1);
                    }
                    if ($is_grow) {
                        // 新增成长值变动明细
                        $user->setIncGrowthValue($val['grow'], '累计签到' . $val['day'] . '天', 1);
                    }
                    // 增加奖励明细
                    (new SignReward())->save(array(
                        'user_id' => $user['user_id'],
                        'days' => $data['day'],
                        'exchangepurch' => $val['exchangepurch'],
                        'growth_value' => $val['grow'],
                        'type' => 1,
                        'app_id' => self::$app_id,
                    ));
                    $is_receive = 1;
                }
            }
        }
        if ($is_receive === 1) {
            return true;
        } else {
            $this->error = '无效奖励';
            return false;
        }
    }
    public function check_is_reach($sign_conf,$days,$user_id)
    {
        $SignReward = new SignReward();
        $str = date('Y-m-d', time());
        $arr = explode('-', $str);
        $start_time = strtotime($arr[0] . '-' . $arr[1] . '-01');
        $create_time =0;
        $list = $this->where('user_id', '=', $user_id)
            ->where('create_time', 'between', [$start_time, time()])
            ->order(['create_time' => 'desc'])->select()->toArray();
        if ($days>1 && !empty($list)){
            $create_time = strtotime($list[0]['create_time']);
            $days = $days-1;
        }
        $rewardData = array();
        foreach ($sign_conf['reward_data'] as $reward){
            $rewardData[] = $reward['day'];
        }
        $while_day =1;
        $dayss =0;
        $is_reach = 0;
        if ($days>max($rewardData)){//如果连续签到天数>设置最大天数 取余
            $while_day = ceil($days / max($rewardData));//取整
            $dayss = $days % max($rewardData);
        }
        foreach ($sign_conf['reward_data'] as $key=>$val) {
            // 判断是否满足领取条件
            if($dayss ==0){
                $dayss = $days;
            }
            if ($val['day'] <=$dayss) {
                $reach_time =  $create_time - (($days - $val['day']) * 24 * 60 *60);
                if (!$SignReward->checkReach($user_id,$reach_time,$val['day'],0,$while_day)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 获取用户签到记录列表
     * @param $userId
     * @param $data
     * @return Paginator
     * @throws DbException
     */
    public function getLogList($userId, $data)
    {
        $model = $this;
        if (isset($data['month'])) {
            $month = $data['month'] ? $data['month'] : date('Y-m');
            $beginTime = date('Y-m-01', strtotime($month));
            $endTime = date('Y-m-01', strtotime("{$month} +1 month"));
            $model = $model->where('sign_date', '>=', $beginTime);
            $model = $model->where('sign_date', '<', $endTime);
        }
        $limit = isset($data['limit']) && $data['limit'] ? $data['limit'] : 30;
        // 获取列表数据
        return $model->field('user_sign_id, user_id, DATE_FORMAT(sign_date, "%m-%d") as sign_date, exchangepurch, growth_value, create_time, type, if(type = 1, "补签", "日常签到") as type_name')
            ->where('user_id', '=', $userId)
            ->order(['sign_date' => 'desc'])
            ->paginate($limit);
    }
}
