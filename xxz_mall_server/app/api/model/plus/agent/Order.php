<?php

namespace app\api\model\plus\agent;

use app\common\library\helper;
use app\common\model\plugin\agent\Order as OrderModel;
use app\common\model\plugin\agent\OrderDetail as OrderDetailModel;
use app\common\model\plugin\agent\Product as AgentProductModel;
use app\common\service\order\OrderService;
use app\common\enum\order\OrderTypeEnum;
use app\common\model\plugin\agent\Setting as AgentSettingModel;
use app\common\model\plugin\agent\User as AgentUserModel;
use app\common\model\plugin\agent\Grade as AgentGradeModel;
use app\common\model\user\User as UserModel;
use app\common\model\plugin\agent\Month as AgentMonthModel;
use app\common\model\plugin\agent\Area as AgentAreaModel;
use app\common\enum\order\OrderSourceEnum;
use app\api\model\order\Order as Order1;

/**
 * 分销商订单模型
 */
class Order extends OrderModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'update_time',
    ];

    /**
     * 获取分销商订单列表
     */
    public function getList($user_id,$first_day=0,$last_day=0, $is_settled = -1)
    {

        $agent_setting = AgentSettingModel::getItem('reward_sale');
        $model = $this;
        $is_settled > -1 && $model = $model->where('is_settled', '=', !!$is_settled);
        if (!empty($first_day)){
            $model = $model->whereTime('create_time', '>', strtotime($first_day))
                ->whereTime('create_time', '<', strtotime($last_day));
        }
        $refee_level = 'first_user_id';
        if ($agent_setting['level_num'] == 2) {
            $refee_level = 'first_user_id|second_user_id';
        } elseif ($agent_setting['level_num'] == 3) {
            $refee_level = 'first_user_id|second_user_id|third_user_id';
        }
        $data = $model->with(['user'])
            ->where($refee_level, '=', $user_id)
            ->order(['create_time' => 'desc'])
            ->paginate(15);
        if ($data->isEmpty())
        {
            return $data;
        }
        // 整理订单信息
        $with = ['product' => ['image', 'refund'], 'address', 'user'];
        return OrderService::getOrderList($data, 'order_master', $with);
    }
    public function getOrderTotalMoney($user_id,$first_day=0,$last_day=0, $is_settled = -1,$type = 0)
    {
        $agent_setting = AgentSettingModel::getItem('reward_sale');
        $model = $this;
        $is_settled > -1 && $model = $model->where('is_settled', '=', !!$is_settled);
        if (!empty($first_day)){
            $model = $model->whereTime('create_time', '>', strtotime($first_day))
                ->whereTime('create_time', '<', strtotime($last_day));
        }
        $refee_level = 'first_user_id';
        if ($agent_setting['level_num'] == 2) {
            $refee_level = 'first_user_id|second_user_id';
        } elseif ($agent_setting['level_num'] == 3) {
            $refee_level = 'first_user_id|second_user_id|third_user_id';
        }
        return $model->alias('ao')->with(['user'])
            ->where($refee_level, '=', $user_id)
            ->SUM('order_price');
    }
    /**
     *格式化卡项结算数据
     */
    public function setCartOrder($order)
    {
        $list = [];
        $card_ids = array_unique(helper::getArrayColumn($order['product'], 'card_id'));
        $card_list = helper::arrayColumn2Key($order['orderCarditem'], 'card_id');
        foreach ($order['product'] as $key=>$product) {
            if (in_array($product['card_id'],$card_ids)) {
                if (empty($list[$product['card_id']])) {
                    $card = $product->toArray();
                    $card['product_id'] = $product['card_id'];
                    $card['total_pay_price'] = $card_list[$product['card_id']]['total_pay_price'];
                    $card['total_pv'] = $card_list[$product['card_id']]['total_price'];
                    $card['pv'] = $card_list[$product['card_id']]['retail_price'];
                    $card['supplier_money'] = $card_list[$product['card_id']]['supplier_money'];
                    $card['total_price'] = $card_list[$product['card_id']]['total_price'];
                    $card['total_num'] = $card_list[$product['card_id']]['total_num'];
                    $card['product_name'] = $card_list[$product['card_id']]['title'];
                    $card['product_price'] = $card_list[$product['card_id']]['retail_price'];
                    $list[$product['card_id']] = $card;
                }
            }
        }
        return $list;
    }
    /**
     * 创建分销商订单记录
     */
    public static function createOrder(&$order, $order_type = OrderTypeEnum::MASTER)
    {
        if ($order['order_source'] == OrderSourceEnum::CARD) {
            $order['product'] = (new static())->setCartOrder($order);
        }
        // 剔除运费
        $order['Valid_pay_price'] = $order['pay_price'] - $order['express_price'];
        // 分销订单模型
        $model = new self;
        // 获取当前买家的所有上级分销商用户id
        $agentUser = $model->getAgentUserId($order['user_id']);

        // 转发店铺下单的时候，无上级的用户下单，转发的人获得佣金
        if ($order['share_id'] && !$agentUser['first_user_id']) {
            $agentUser = $model->getShareUserId($order['share_id']);
        }

        $agent_setting = AgentSettingModel::getAll();

        $order_detail = [];
        $common_detail = [
            'order_id' => $order['order_id'],
            'month' => date('Y-m'),
            'shop_supplier_id' => $order['shop_supplier_id'],
            'app_id' => $order['app_id'],
        ];
        $open_type = AgentSettingModel::getMoneyOpenType($order['app_id']);
        // 如果是按pv结算
        $basic_setting = $agent_setting['basic']['values'];
        if($basic_setting['money_type'] == 2){
            $order['Valid_pay_price'] = $order['total_pv'] ? $order['total_pv'] : 0;
            foreach ($order['product'] as &$product){
                $product['total_pay_price'] = $product['total_pv'] ? $product['total_pv'] : 0;
            }
        }
        // 计算销售奖
        $agent_money = 0;
        $capital = [];
        if(in_array('sale', $open_type)){
            $capital = $model->getCapitalByOrder($order, $agent_setting, $agentUser);
            $model->caclSaleMoney($order, $order_detail, $agent_setting, $agentUser, $capital, $common_detail);
            $agent_money = $capital['first_money'] + $capital['second_money'] + $capital['third_money'];
        }
        // 获取商品奖励基础信息
        $order = $model->getProductReward($order);
        // 计算差额奖
        in_array('diff', $open_type) && $model->caclDiffMoney($order, $order_detail, $agent_setting, $agentUser, $common_detail);
        // 计算团队奖
        $team_user_id = $model->caclTeamMoney($order, $order_detail, $agent_setting, $common_detail,$open_type);
        // 计算级差奖
        in_array('level', $open_type) && $model->caclLevelMoney($order, $order_detail, $agent_setting, $common_detail, $team_user_id);
        if ($team_user_id > 0) {
            // 计算平级奖
            in_array('same', $open_type) && $model->caclSameMoney($order, $order_detail, $agent_setting, $common_detail, $team_user_id);
            // 计算超越奖
            in_array('than', $open_type) && $model->caclThanMoney($order, $order_detail, $agent_setting, $common_detail, $team_user_id);
            // 计算拓展奖
            in_array('expand', $open_type) && $model->caclExpandMoney($order, $order_detail, $agent_setting, $common_detail, $team_user_id);
        }

        // 计算区域代理奖
        in_array('area', $open_type) && $model->caclAreaMoney($order, $order_detail, $agent_setting, $common_detail);
        //批量保存分销金额详情
        (new OrderDetailModel())->saveAll($order_detail);
        // 保存分销订单记录
        $setting = $agent_setting['reward_sale']['values'];
        return $model->save([
            'user_id' => $order['user_id'],
            'order_id' => $order['order_id'],
            'order_type' => $order_type,
            'order_price' => $order['order_price'],
            'first_money' => isset($capital['first_money']) ? $capital['first_money'] : 0,
            'second_money' => isset($capital['second_money']) ? $capital['second_money'] : 0,
            'third_money' => isset($capital['third_money']) ? $capital['third_money'] : 0,
            'agent_money' => $agent_money,
            'first_user_id' => $setting['level_num'] >= 1 ? $agentUser['first_user_id'] : 0,
            'second_user_id' => $setting['level_num'] >= 2 ? $agentUser['second_user_id'] : 0,
            'third_user_id' => $setting['level_num'] >= 3 ? $agentUser['third_user_id'] : 0,
            'is_settled' => 0,
            'shop_supplier_id' => $order['shop_supplier_id'],
            'app_id' => $order['app_id']
        ]);
    }

    /**
     * 计算销售奖
     */
    private function caclSaleMoney($order, &$order_detail, $agent_setting, $agentUser, $capital, $common_detail){
        $setting = $agent_setting['reward_sale']['values'];
        $basic = $agent_setting['bouns_reward']['values'];
        $bouns_basic = $agent_setting['venturebonus_basic']['values'];//考核分红设置
        $product_ids = isset($bouns_basic['product_ids']) ? $bouns_basic['product_ids'] : [];
        $common_detail = [
            'order_id' => $order['order_id'],
            'shop_supplier_id' => $order['shop_supplier_id'],
            'app_id' => $order['app_id'],
        ];
        // 获取当前月第几周
        $weekData = helper::get_week_num(date('Y-m-d', time()));
        foreach ($capital['product'] as $item) {
            if ($item['is_agent'] == 0) {
                continue;
            }
            $type = 'sale';
            $month = date('Y-m');
            $week = 0;
            // 判断商品是否是创业分红商品
            if (in_array($item['product_id'], $product_ids)) {
                $type = 'sale_bonus';
                $week = $weekData['week'];
                if ($basic['settlement_way'] == 0) {
                    $month = $weekData['month'];
                }
            }
            // 1级奖励
            if($setting['level_num'] >= 1 && $agentUser['first_user_id']){
                $first_money = $type == 'sale_bonus' ? $item['total_pay_price'] : $item['first_money'];
                array_push($order_detail, array_merge($common_detail, [
                    'money' => $first_money,
                    'type' => $type,
                    'level_num' => 1,
                    'user_id' => $agentUser['first_user_id'],
                    'team_percent' => $item['first_percent'],
                    'order_product_id' => $item['order_product_id'],
                    'month' => $month,
                    'week' => $week
                ]));
            }
            // 2级奖励
            if($setting['level_num'] >= 2 && $agentUser['second_user_id']){
                $second_money = $type == 'sale_bonus' ? $item['total_pay_price'] : $item['second_money'];
                array_push($order_detail, array_merge($common_detail, [
                    'money' => $second_money,
                    'type' => $type,
                    'level_num' => 2,
                    'user_id' => $agentUser['second_user_id'],
                    'team_percent' => $item['second_percent'],
                    'order_product_id' => $item['order_product_id'],
                    'month' => $month,
                    'week' => $week
                ]));
            }
            // 3级奖励
            if($setting['level_num'] >= 3 && $agentUser['third_user_id']){
                $third_money = $type == 'sale_bonus' ? $item['total_pay_price'] : $item['third_money'];
                array_push($order_detail, array_merge($common_detail, [
                    'money' => $third_money,
                    'type' => $type,
                    'level_num' => 3,
                    'user_id' => $agentUser['third_user_id'],
                    'team_percent' => $item['third_percent'],
                    'order_product_id' => $item['order_product_id'],
                    'month' => $month,
                    'week' => $week
                ]));
            }
        }
    }

    /**
     * 计算差额奖
     */
    private function caclDiffMoney($order, &$order_detail, $agent_setting, $agentUser, $common_detail){
        $setting = $agent_setting['reward_diff']['values'];

        // 购买人等级
        $buy_user = AgentUserModel::detail($order['user_id']);
        foreach ($order['product'] as $product){
            $agent_product = AgentProductModel::detail($product['product_id'], $product['spec_sku_id']);
            $cost_price = json_decode($agent_product['cost_price'], true);
            if($buy_user){
                $buy_price = helper::priceOnNull($cost_price, $buy_user['grade_id']);
            }else{
                $buy_price = helper::number2($product['total_pay_price'] / $product['total_num']);
            }
            $buy_price = $buy_price ? $buy_price : 0;
            // 1级奖励
            if($setting['level_num'] >= 1){
                $first_user = AgentUserModel::detail($agentUser['first_user_id']);
                if(in_array($first_user['grade_id'], $setting['grade'])){
                    $first_price = helper::priceOnNull($cost_price, $first_user['grade_id']);
                    $first_price = $first_price ? $first_price : 0;
                    $send_money = $buy_price - $first_price;
                    if($send_money > 0){
                        $left_money = $first_price;
                        array_push($order_detail, array_merge($common_detail, [
                            'money' => $send_money * $product['total_num'],
                            'type' => 'diff',
                            'level_num' => 1,
                            'user_id' => $agentUser['first_user_id'],
                            'order_product_id' => $product['order_product_id']
                        ]));
                    }
                }
            }
            // 2级奖励
            if($setting['level_num'] >= 2 && $agentUser['second_user_id']){
                $second_user = AgentUserModel::detail($agentUser['second_user_id']);
                if(in_array($second_user['grade_id'], $setting['grade'])){
                    $second_price = helper::priceOnNull($cost_price, $second_user['grade_id']);
                    $second_price = $second_price ? $second_price : 0;
                    $send_money = $buy_price - $second_price;
                    if($send_money > 0){
                        $left_money = $second_price;
                        array_push($order_detail, array_merge($common_detail, [
                            'money' => $send_money * $product['total_num'],
                            'type' => 'diff',
                            'level_num' => 2,
                            'user_id' => $agentUser['second_user_id'],
                            'order_product_id' => $product['order_product_id']
                        ]));
                    }
                }
            }
            // 3级奖励
            if($setting['level_num'] >= 3 && $agentUser['third_user_id']){
                $third_user = AgentUserModel::detail($agentUser['third_user_id']);
                if(in_array($third_user['grade_id'], $setting['grade'])){
                    $third_price = helper::priceOnNull($cost_price, $third_user['grade_id']);
                    $third_price = $third_price ? $third_price : 0;
                    $send_money = $buy_price - $third_price;
                    if($send_money > 0){
                        array_push($order_detail, array_merge($common_detail, [
                            'money' => $send_money * $product['total_num'],
                            'type' => 'diff',
                            'level_num' => 3,
                            'user_id' => $agentUser['third_user_id'],
                            'order_product_id' => $product['order_product_id'],
                        ]));
                    }
                }
            }
        }
    }

    /**
     * 如果不是分销订单也要计算团队佣金
     */
    private function saveTeamMoney($order, $agent_setting){
        $setting = $agent_setting['condition']['values'];
        $gradeIds = AgentGradeModel::getTeamGradeList($setting['team_level']);
        $agent_user = AgentUserModel::detail($order['user_id']);
        if(!$agent_user){
            return;
        }
        if(in_array($agent_user['grade_id'], $gradeIds)){
            $team_user_id = $agent_user['user_id'];
            AgentMonthModel::saveByMonth($team_user_id, 'team', $order['pay_price'],$order['pay_price'],0,$order['app_id'],0,$order);
        }
    }
    /**
     * 计算团队奖AND计算团队金额
     */
    private function caclTeamMoney($order, &$order_detail, $agent_setting, $common_detail,$open_type = []){
        $user = UserModel::detail($order['user_id']);
        $agent_user = AgentUserModel::detail($user['user_id']);
        $setting = $agent_setting['condition']['values'];
        $gradeIds = AgentGradeModel::getTeamGradeList($setting['team_level']);
        $team_user_id = 0;
        $basic_setting = $agent_setting['basic']['values'];

        // 兼容普通会员找上级团长
        if (empty($agent_user) && $user['referee_id'] > 0) {
            $agent_user = [
                'user_id' => $user['user_id'],
                'grade_id' => 0,
                'referee_id' =>  $user['referee_id'],
            ];
        }
        $refereeIdArr = [];
        // 往上找团长
        while(true){
            if(!$agent_user){
                break;
            }
            if (in_array($agent_user['user_id'], $refereeIdArr)) {
                break;
            }
            array_push($refereeIdArr, $agent_user['user_id']);
            if(in_array($agent_user['grade_id'], $gradeIds)){
                $team_user_id = $agent_user['user_id'];
                break;
            } else {
                AgentMonthModel::saveByMonth($agent_user['user_id'], 'team', 0,0,0,$order['app_id'],0,$order);
            }
            $agent_user = AgentUserModel::detail($agent_user['referee_id']);
        }
        if($team_user_id > 0){
            // 判断团队奖是否发放
            if (in_array('team', $open_type)) {
                foreach ($order['product'] as $item) {
                    $reward_team = isset($item['agent_product']['reward_team']) ? json_decode($item['agent_product']['reward_team'],true) : [];
                    $reward_team['is_open'] = isset($reward_team['is_open']) ? $reward_team['is_open'] : 1;
                    if ($reward_team['is_open'] != 0) {
                        array_push($order_detail, array_merge($common_detail, [
                            'money' => $basic_setting['money_type'] == 2 ? $item['total_pv'] : $item['total_pay_price'],
                            'type' => 'team',
                            'user_id' => $team_user_id,
                            'order_product_id' => $item['order_product_id']
                        ]));
                    }
                }
            }

            // 累计当月业绩
            AgentMonthModel::saveByMonth($team_user_id, 'team', $order['pay_price'],$order['pay_price'],0,$order['app_id'],0,$order);
            // 是否只统计直属团队
            $basic = $agent_setting['basic']['values'];
            if($basic['team_type'] == 1){
                //包含下属团队
                $max_grade = AgentGradeModel::getMaxGrade();
                $team_user = AgentUserModel::detail($team_user_id);
                // 不是最高级别，并且有上级
                if($team_user['grade']['grade_id'] != $max_grade['grade']['grade_id'] && $team_user['referee_id'] > 0){
                    $agent_user = AgentUserModel::detail($team_user['referee_id']);
                    while(true){
                        if(!$agent_user){
                            break;
                        }
                        // 如果上级比当前团队长等级高
                        if($agent_user['grade']['weight'] > $team_user['grade']['weight']){
                            // 累计当月业绩
                            AgentMonthModel::saveByMonth($agent_user['user_id'], 'team', $order['pay_price'],0,0,$order['app_id'],0,$order);
                        } else {
                            AgentMonthModel::saveByMonth($agent_user['user_id'], 'team', 0,0,0,$order['app_id'],0,$order);
                        }
                        // 没有上级退出
                        if($agent_user['referee_id'] == 0){
                            break;
                        }
                        // 最大等级退出
                        if($team_user['grade']['grade_id'] == $max_grade['grade']['grade_id']){
                            break;
                        }
                        $agent_user = AgentUserModel::detail($agent_user['referee_id']);
                    }

                } else {
                    AgentMonthModel::saveByMonth($agent_user['user_id'], 'team', 0,0,0,$order['app_id'],0,$order);
                }
            }
        }
        return $team_user_id;
    }
    /**
     * 计算创业分红
     */
    public function caclBounsMoney($order, $agent_setting){
        $user = UserModel::detail($order['user_id']);
        $agent_user = AgentUserModel::detail($user['user_id']);
        $setting = $agent_setting['venturebonus_basic']['values'];
        $gradeIds = AgentGradeModel::getTeamGradeList($setting['grade_id']);
        $team_user_id = 0;
        // 累计当月自购业绩
        AgentMonthModel::saveByMonth($agent_user['user_id'], 'bouns', 0 , 0,$order['money'],$order['app_id'],$order['order_id'],$order);
        // 兼容普通会员找上级团长
        if (empty($agent_user) && $user['referee_id'] > 0) {
            $agent_user = [
                'user_id' => $user['user_id'],
                'grade_id' => 0,
                'referee_id' =>  $user['referee_id'],
            ];
        }
        // 往上找团长
        while(true){
            if(!$agent_user){
                break;
            }
            if(in_array($agent_user['grade_id'], $gradeIds)){
                $team_user_id = $agent_user['user_id'];
                break;
            }
            $agent_user = AgentUserModel::detail($agent_user['referee_id']);
        }
        // 找到团长再记录创业分红团队信息
        if ($team_user_id > 0) {
            // 累计当月业绩
            AgentMonthModel::saveByMonth($team_user_id, 'bouns', $order['money'],$order['money'],0,$order['app_id'],$order['order_id'],$order);
            //包含下属团队
            $max_grade = AgentGradeModel::getMaxGrade();
            $team_user = AgentUserModel::detail($team_user_id);
            // 不是最高级别，并且有上级
            if($team_user['grade']['grade_id'] != $max_grade['grade']['grade_id'] && $team_user['referee_id'] > 0){
                $agent_user = AgentUserModel::detail($team_user['referee_id']);
                while(true){
                    // 如果上级比当前团队长等级高
                    if($agent_user['grade']['weight'] > $team_user['grade']['weight']){
                        // 累计当月业绩
                        AgentMonthModel::saveByMonth($agent_user['user_id'], 'bouns', $order['money'],0,0,$order['app_id'],$order['order_id'],$order);
                    }
                    // 没有上级退出
                    if($agent_user['referee_id'] == 0){
                        break;
                    }
                    // 最大等级退出
                    if($team_user['grade']['grade_id'] == $max_grade['grade']['grade_id']){
                        break;
                    }
                    $agent_user = AgentUserModel::detail($agent_user['referee_id']);
                }
            }
        }
        return $team_user_id;
    }
    /**
     * 计算级差奖
     */
    private function caclLevelMoney($order, &$order_detail, $agent_setting, $common_detail, $team_user_id,$send_time = 0){
        $settings = $agent_setting['reward_level']['values'];
        $basic_setting = $agent_setting['basic']['values'];
        $bouns_basic = $agent_setting['venturebonus_basic']['values'];//考核分红设置
        $product_ids = isset($bouns_basic['product_ids']) ? $bouns_basic['product_ids'] : [];
        $team_agent_user = AgentUserModel::detail($team_user_id);
        $agent_user = AgentUserModel::detail($team_agent_user['referee_id']);
        $order_user = AgentUserModel::detail($order['user_id']);
        $order_referee_user = AgentUserModel::detail($order_user['referee_id']);
        $max_grade_id = AgentGradeModel::getMaxGradeId();
        $first_referee_user = $order_referee_user;

        // 兼容创业级差奖 找购买创业分红的上级
        while(true){
            if(!$first_referee_user){
                break;
            }
            // 获取月结信息
            $month_info = (new AgentMonthModel())->getMonthDetail($first_referee_user['user_id'], $common_detail['month']);
            if ($month_info['venturebonus_buy_money'] > 0) {
                break;
            }
            $first_referee_user = AgentUserModel::detail($first_referee_user['referee_id']);
        }

        $bonus_agent_user = AgentUserModel::detail($first_referee_user['referee_id']);
        // 已经发放
        foreach ($order['product'] as $item) {
            $user = $agent_user;
            $order_agent = $team_agent_user;
            $type = 'level';
            // 判断商品是否是创业分红商品
            if (in_array($item['product_id'], $product_ids)) {
                $type = 'level_bonus';
                $user = $bonus_agent_user;
                $order_agent = $first_referee_user;
            }
            if (!$order_agent) {
                break;
            }
            $percent_data = (new AgentSettingModel())->getMinAeward('reward_level',$order['app_id'],'level1');
            $pay_price = $basic_setting['money_type'] == 2 ? $item['total_pv'] : $item['total_pay_price'];
            if (isset($item['agent_product'])) {
                $reward_level = isset($item['agent_product']['reward_level']) ? json_decode($item['agent_product']['reward_level'],true) : [];
                $reward_level['is_open'] = isset($reward_level['is_open']) ? $reward_level['is_open'] : 1;
                if ($reward_level['is_open'] == 1) {
                    // 默认佣金比例
                    $has_send = isset($percent_data['grade_list'][$order_agent['grade_id']]) ? $percent_data['grade_list'][$order_agent['grade_id']] : 0;
                    $max_send = $percent_data['max_percent'];
                    // 结算时 获取当前考核档位
                    if ($send_time == 1) {
                        $month_info = (new AgentMonthModel())->getMonthDetail($order_agent['user_id'], $common_detail['month']);
                        $reward_arr  = (new AgentMonthModel())->rewardAssessment('level',$order['app_id'],$order_agent,$percent_data['agent_setting'],$month_info);
                        if (!empty($reward_arr['reward'])) {
                            $has_send = $reward_arr['reward'][$order_agent['grade_id']]['level1'];
                        }
                    }
                } elseif ($reward_level['is_open'] == 2) {
                    // 自定义佣金
                    $has_send = isset($reward_level['list'][$order_agent['grade_id']]) ? $reward_level['list'][$order_agent['grade_id']] : 0;
                    $max_send = isset($reward_level['list'][$max_grade_id]) ? $reward_level['list'][$max_grade_id] : 0;
                } else {
                    continue;
                }
                $send_level = 0;
                // 找上级，是否是同级
                while(true){
                    if(!$user){
                        break;
                    }

                    // 是否大于
                    if($user['grade']['weight'] > $order_agent['grade']['weight']){

                        // 发放级差奖
                        $send_percent = 0;
                        $month_info = (new AgentMonthModel())->getMonthDetail($user['user_id'], $common_detail['month']);
                        if ($reward_level['is_open'] == 1) {
                            // 默认佣金比例
                            $send_percent = $percent_data['grade_list'][$user['grade_id']] - $has_send;
                            if ($send_time == 1) {
                                $reward_arr = (new AgentMonthModel())->rewardAssessment('level', $order['app_id'], $user, $percent_data['agent_setting'], $month_info);
                                if (!empty($reward_arr['reward'])) {
                                    $send_percent = $reward_arr['reward'][$user['grade_id']]['level1'] - $has_send;
                                }
                            }

                        } elseif ($reward_level['is_open'] == 2) {
                            // 自定义佣金
                            $reward_level_grade = isset($reward_level['list'][$user['grade_id']]) ? $reward_level['list'][$user['grade_id']] : 0;
                            $send_percent = $reward_level_grade - $has_send;
                        }
                        $send_percent = $send_percent ? $send_percent : 0;
                        $money = $pay_price * $send_percent/100;

                        // 级差奖发放比例大于0就通过   创业级差奖发放比例大于0且考核通过
                        if(($send_percent > 0 && $type == 'level')
                            || ($type == 'level_bonus' && $send_percent > 0 && $month_info['venturebonus_buy_money'] > 0)){
                            $send_level++;
                            array_push($order_detail, array_merge($common_detail, [
                                'money' => $money,
                                'type' => $type,
                                'user_id' => $user['user_id'],
                                'team_user_id' => $team_user_id,
                                'team_percent' => $send_percent,
                                'level_num' => $send_level,
                                'order_product_id' => $item['order_product_id'],
                                'order_money' => $pay_price
                            ]));
                            $has_send += $send_percent;
                        }
                    }
                    // 发放完成
                    if($has_send >= $max_send){
                        break;
                    }
                    $user = AgentUserModel::detail($user['referee_id']);
                }
            }
        }
    }
    /**
     * 计算平级奖
     */
    private function caclSameMoney($order, &$order_detail, $agent_setting, $common_detail, $team_user_id){
        $settings = $agent_setting['reward_same']['values'];
        $basic_setting = $agent_setting['basic']['values'];
        $send_level = 0;
        $total_level = $settings['level_num'];
        if($total_level == 0){
            return;
        }
        $team_agent_user = AgentUserModel::detail($team_user_id);
        $agent_user = AgentUserModel::detail($team_agent_user['referee_id']);

        // 找上级，是否是同级
        while(true){
            if(!$agent_user){
                break;
            }
            // 是否同级
            if($agent_user['grade_id'] == $team_agent_user['grade_id']){
                $send_level++;
                // 发放同级奖
                foreach ($order['product'] as $item) {
                    $pay_price = $basic_setting['money_type'] == 2 ? $item['total_pv'] : $item['total_pay_price'];
                    $reward_same = isset($item['agent_product']['reward_same']) ? json_decode($item['agent_product']['reward_same'],true) : [];
                    $reward_same['is_open'] = isset($reward_same['is_open']) ? $reward_same['is_open'] : 1;
                    $send_percent = 0;
                    if ($reward_same['is_open'] == 1) {
                        // 默认佣金比例
                        $percent_data = (new AgentSettingModel())->getMinAeward('reward_same',$order['app_id'],'level'.$send_level);
//                        $send_percent = $settings['level'.$send_level];
                        $send_percent = isset($percent_data['grade_list'][$agent_user['grade_id']]) ? $percent_data['grade_list'][$agent_user['grade_id']] : 0;
                    } elseif ($reward_same['is_open'] == 2) {
                        // 自定义佣金
                        $send_percent = isset($reward_same['list']['level'.$send_level]) ? $reward_same['list']['level'.$send_level] : 0;
                    } else {
                        continue;
                    }
                    $send_percent = $send_percent ? $send_percent : 0;
                    $money = $pay_price * $send_percent/100;
                    if ($money > 0) {
                        array_push($order_detail, array_merge($common_detail, [
                            'money' => helper::number2($money),
                            'type' => 'same',
                            'user_id' => $agent_user['user_id'],
                            'team_user_id' => $team_user_id,
                            'level_num' => $send_level,
                            'team_percent' => $send_percent,
                            'order_product_id' => $item['order_product_id'],
                            'order_money' => $pay_price
                        ]));
                    }
                }
            }
            if($send_level >= $total_level){
                break;
            }
            $agent_user = AgentUserModel::detail($agent_user['referee_id']);
        }
    }
    /**
     * 计算超越奖
     */
    private function caclThanMoney($order, &$order_detail, $agent_setting, $common_detail, $team_user_id){
        $settings = $agent_setting['reward_than']['values'];
        $basic_setting = $agent_setting['basic']['values'];
        $send_level = 0;
        $total_level = $settings['level_num'];
        if($total_level == 0){
            return;
        }
        $team_agent_user = AgentUserModel::detail($team_user_id);
        $condition = $agent_setting['condition']['values'];
        $gradeIds = AgentGradeModel::getTeamGradeList($condition['team_level']);
        $agent_user = AgentUserModel::detail($team_agent_user['referee_id']);
        // 找上级，是否是比当前等级低
        while(true){
            if(!$agent_user){
                break;
            }
            // 是否被超越
            if($agent_user['grade']['weight'] < $team_agent_user['grade']['weight']){
                // 是否是团队长
                if(!in_array($agent_user['grade_id'], $gradeIds)){
                    $agent_user = AgentUserModel::detail($agent_user['referee_id']);
                    continue;
                }
                $send_level++;
                // 发放同级奖
                foreach ($order['product'] as $item) {
                    $pay_price = $basic_setting['money_type'] == 2 ? $item['total_pv'] : $item['total_pay_price'];
                    $reward_than = isset($item['agent_product']['reward_than']) ? json_decode($item['agent_product']['reward_than'],true) : [];
                    $reward_than['is_open'] = isset($reward_than['is_open']) ? $reward_than['is_open'] : 1;
                    $send_percent = 0;
                    if ($reward_than['is_open'] == 1) {
                        // 默认佣金比例
//                        $send_percent = $settings['level'.$send_level];
                        $percent_data = (new AgentSettingModel())->getMinAeward('reward_than',$order['app_id'],'level'.$send_level);
                        $send_percent = isset($percent_data['grade_list'][$agent_user['grade_id']]) ? $percent_data['grade_list'][$agent_user['grade_id']] : 0;
                    } elseif ($reward_than['is_open'] == 2) {
                        // 自定义佣金
                        $send_percent = isset($reward_than['list']['level'.$send_level]) ? $reward_than['list']['level'.$send_level] : 0;
                    } else {
                        continue;
                    }
                    $send_percent = $send_percent ? $send_percent : 0;
                    $money = $pay_price * $send_percent/100;
                    if ($money > 0) {
                        array_push($order_detail, array_merge($common_detail, [
                            'money' => helper::number2($money),
                            'type' => 'than',
                            'user_id' => $agent_user['user_id'],
                            'team_user_id' => $team_user_id,
                            'level_num' => $send_level,
                            'team_percent' => $send_percent,
                            'order_product_id' => $item['order_product_id'],
                            'order_money' => $pay_price
                        ]));
                    }
                }

            }
            if($send_level >= $total_level){
                break;
            }
            $agent_user = AgentUserModel::detail($agent_user['referee_id']);
        }
    }
    /**
     * 计算拓展奖
     */
    private function caclExpandMoney($order, &$order_detail, $agent_setting, $common_detail, $team_user_id){
        $settings = $agent_setting['reward_expand']['values'];
        $basic_setting = $agent_setting['basic']['values'];
        $bouns_basic = $agent_setting['venturebonus_basic']['values'];//考核分红设置
        $product_ids = isset($bouns_basic['product_ids']) ? $bouns_basic['product_ids'] : [];
        $send_level = 0;
        $total_level = $settings['level_num'];
        if($total_level == 0){
            return;
        }
        $team_agent_user = AgentUserModel::detail($team_user_id);
        $condition = $agent_setting['condition']['values'];
        $gradeIds = AgentGradeModel::getTeamGradeList($condition['team_level']);
        $agent_user = AgentUserModel::detail($team_agent_user['referee_id']);
        // 找上级，是否是比当前等级低
        while(true){
            if(!$agent_user){
                break;
            }
            // 是否是团队长
            if(!in_array($agent_user['grade_id'], $gradeIds)){
                $agent_user = AgentUserModel::detail($agent_user['referee_id']);
                continue;
            }
            $send_level++;
            // 发放同级奖
            foreach ($order['product'] as $item) {
                $pay_price = $basic_setting['money_type'] == 2 ? $item['total_pv'] : $item['total_pay_price'];
                $reward_expand = isset($item['agent_product']['reward_expand']) ? json_decode($item['agent_product']['reward_expand'],true) : [];
                $reward_expand['is_open'] = isset($reward_expand['is_open']) ? $reward_expand['is_open'] : 1;
                $send_percent = 0;
                if ($reward_expand['is_open'] == 1) {
                    // 默认佣金比例
//                    $send_percent = $settings['level'.$send_level];
                    $percent_data = (new AgentSettingModel())->getMinAeward('reward_expand',$order['app_id'],'level'.$send_level);
                    $send_percent = isset($percent_data['grade_list'][$agent_user['grade_id']]) ? $percent_data['grade_list'][$agent_user['grade_id']] : 0;
                } elseif ($reward_expand['is_open'] == 2) {
                    // 自定义佣金
                    $send_percent = isset($reward_expand['list']['level'.$send_level]) ? $reward_expand['list']['level'.$send_level] : 0;
                } else {
                    continue;
                }
                $type = 'expand';
                // 判断商品是否是创业分红商品
                if (in_array($item['product_id'], $product_ids)) {
                    $type = 'expand_bonus';
                }

                $send_percent = $send_percent ? $send_percent : 0;
                $money = $pay_price * $send_percent / 100;
                if ($money > 0) {
                    array_push($order_detail, array_merge($common_detail, [
                        'money' => helper::number2($money),
                        'type' => $type,
                        'user_id' => $agent_user['user_id'],
                        'team_user_id' => $team_user_id,
                        'level_num' => $send_level,
                        'team_percent' => $send_percent,
                        'order_product_id' => $item['order_product_id'],
                        'order_money' => $pay_price
                    ]));
                }
            }

            if($send_level >= $total_level){
                break;
            }
            $agent_user = AgentUserModel::detail($agent_user['referee_id']);
        }
    }
    /**
     * 计算区域代理奖
     */
    private function caclAreaMoney($order, &$order_detail, $agent_setting, $common_detail){
        $setting = $agent_setting['reward_area']['values'];
        $productIds = $setting['product_ids'];
        $money = $order['Valid_pay_price'];
        if($setting['open_hei']){
            foreach ($order['product'] as $product){
                // 黑名单
                if(in_array($product['product_id'], $productIds)){
                    $money -= $product['total_pay_price'];
                }
            }
        }
        if($money > 0){
            $province = AgentAreaModel::detail($order['address']['province_id'], 1);
            if($province){
                array_push($order_detail, array_merge($common_detail, [
                    'money' => helper::number2($money * $setting['province'] / 100),
                    'type' => 'area',
                    'level_num' => 1, //省代
                    'user_id' => $province['user_id'],
                    'area_id' => $order['address']['province_id'],
                    'team_percent' => $setting['province']
                ]));
                AgentMonthModel::addMonth($province['user_id'],$order['app_id']);
            }
            $city = AgentAreaModel::detail($order['address']['city_id'], 2);
            if($city){
                array_push($order_detail, array_merge($common_detail, [
                    'money' => helper::number2($money * $setting['city'] / 100),
                    'type' => 'area',
                    'level_num' => 2, //市代
                    'user_id' => $city['user_id'],
                    'area_id' => $order['address']['city_id'],
                    'team_percent' => $setting['city']
                ]));
                AgentMonthModel::addMonth($city['user_id'],$order['app_id']);
            }
            $region = AgentAreaModel::detail($order['address']['region_id'], 3);
            if($region){
                array_push($order_detail, array_merge($common_detail, [
                    'money' => helper::number2($money * $setting['region'] / 100),
                    'type' => 'area',
                    'level_num' => 3, //市代
                    'user_id' => $region['user_id'],
                    'area_id' => $order['address']['region_id'],
                    'team_percent' => $setting['region']
                ]));
                AgentMonthModel::addMonth($region['user_id'],$order['app_id']);
            }
        }
    }

    /**
     * 获取当前买家的所有上级分销商用户id
     */
    private function getAgentUserId($user_id)
    {
        $agentUser = [
            'first_user_id' => Referee::getRefereeUserId($user_id, 1, true),
            'second_user_id' => Referee::getRefereeUserId($user_id, 2, true),
            'third_user_id' => Referee::getRefereeUserId($user_id, 3, true)
        ];
        // 分销商自购
        /*if ($self_buy && User::isAgentUser($user_id)) {
            return [
                'first_user_id' => $user_id,
                'second_user_id' => $agentUser['first_user_id'],
                'third_user_id' => $agentUser['second_user_id'],
            ];
        }*/
        return $agentUser;
    }

    /**
     * 获取当前分享的人的所有上级分销商用户id
     * @param $shareId
     * @return array
     */
    private function getShareUserId($shareId)
    {
        return [
            'first_user_id' => $shareId,
            'second_user_id' => Referee::getRefereeUserId($shareId, 1, true),
            'third_user_id' => Referee::getRefereeUserId($shareId, 2, true)
        ];
    }

    /**
     * 结算时计算佣金计算
     * @param $app_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setMonthlyAward($app_id)
    {
        $order_detail = []; // 新增奖励数组
        $order_detail_data = []; // 修改奖励数组
        $agent_setting = AgentSettingModel::getAll($app_id);
        $reward_level = $agent_setting['reward_level']['values'];
        $reward_than = $agent_setting['reward_than']['values'];// 越级奖配置
        $reward_same = $agent_setting['reward_same']['values'];// 平级奖配置
        $reward_expand = $agent_setting['reward_expand']['values'];// 拓展奖配置
        $open_type = AgentSettingModel::getMoneyOpenType($app_id);
        $basic_setting = $agent_setting['basic']['values'];

        // 获取上月起始时间
        $time = strtotime('-1 month');
        $status_time = mktime(0,0,0,date('m',$time),01,date('Y',$time));
        $end_time = mktime(23,59,59,date('m',$time),date('t',$time),date('Y',$time));

        // 获取未重新计算佣金的分销订单
        $list = $this->with(['order'=>['product'],'user'])
            ->where('create_time', 'between', [$status_time, $end_time])
            ->where('is_award' , '=' , 0)
            ->where('is_invalid' , '=' , 0)
            ->select();

        $ids = [];
        if(!empty($list)) {
            foreach ($list as $item) {
                if ($item['is_award'] == 1) {
                    continue;
                }
                $order = $item['order'];
                $common_detail = [
                    'order_id' => $order['order_id'],
                    'month' => date('Y-m' , strtotime($item['create_time'])),
                    'shop_supplier_id' => $order['shop_supplier_id'],
                    'app_id' => $order['app_id'],
                ];
                if ($order['order_source'] == OrderSourceEnum::CARD) {
                    $order['product'] = (new static())->setCartOrder($order);
                }
                // 剔除运费
                $order['Valid_pay_price'] = $order['pay_price'] - $order['express_price'];
                // 分销订单模型
                $model = new self;
                $order = $model->getProductReward($order);
                // 如果是按pv结算
                if($basic_setting['money_type'] == 2){
                    $order['Valid_pay_price'] = $order['total_pv'] ? $order['total_pv'] : 0;
                    foreach ($order['product'] as &$product){
                        $product['total_pay_price'] = $product['total_pv'] ? $product['total_pv'] : 0;
                    }
                }
//                var_dump($item['user']);die;
                // 找团长
                $team_user_id = (new AgentUserModel())->getTeamUserId($item['user']);
                // 判断级差奖计算奖励实际
                if ($reward_level['set_reward_time'] == 1) {
                    // 支付时生产的级差奖奖励失效
                    $order_detail_data[] = [
                        'where' => [
                            'order_id' => $item['order_id'],
                            'type' => 'level'
                        ],
                        'data' => ['is_invalid' => 1],
                    ];
                    // 创业级差奖
                    $order_detail_data[] = [
                        'where' => [
                            'order_id' => $item['order_id'],
                            'type' => 'level_bonus'
                        ],
                        'data' => ['is_invalid' => 1],
                    ];
                    // 开始计算奖项
                    in_array('level', $open_type) && $model->caclLevelMoney($order,$order_detail ,$agent_setting,$common_detail,$team_user_id,1);
                }
                // 判断平级奖计算奖励时机
                if ($reward_same['set_reward_time'] == 1) {
                    // 支付时生产的平级奖奖励失效
                    $order_detail_data[] = [
                        'where' => [
                            'order_id' => $item['order_id'],
                            'type' => 'same'
                        ],
                        'data' => ['is_invalid' => 1],
                    ];
                    // 计算平级级奖
                    in_array('same', $open_type) && $model->caclSameMoney($order, $order_detail, $agent_setting, $common_detail, $team_user_id);
                }
                 // 判断越级奖计算奖励时机
                if ($reward_than['set_reward_time'] == 1) {
                    // 支付时生产的越级奖奖励失效
                    $order_detail_data[] = [
                        'where' => [
                            'order_id' => $item['order_id'],
                            'type' => 'than'
                        ],
                        'data' => ['is_invalid' => 1],
                    ];
                    // 计算越级奖
                    in_array('than', $open_type) && $model->caclThanMoney($order, $order_detail, $agent_setting, $common_detail, $team_user_id);
                }
                 // 判断拓展奖计算奖励时机
                if ($reward_expand['set_reward_time'] == 1) {
                    // 支付时生产的拓展奖奖励失效
                    $order_detail_data[] = [
                        'where' => [
                            'order_id' => $item['order_id'],
                            'type' => 'expand'
                        ],
                        'data' => ['is_invalid' => 1],
                    ];
                    $order_detail_data[] = [
                        'where' => [
                            'order_id' => $item['order_id'],
                            'type' => 'expand_bonus'
                        ],
                        'data' => ['is_invalid' => 1],
                    ];
                    // 计算拓展奖
                    in_array('expand', $open_type) && $model->caclExpandMoney($order, $order_detail, $agent_setting, $common_detail, $team_user_id);
                }
                $ids[] = $item['id'];
            }
        }
        // 批量失效奖励
        !empty($order_detail_data) && (new OrderDetailModel())->updateAll($order_detail_data);
        //批量保存分销金额详情
        !empty($order_detail) && (new OrderDetailModel())->saveAll($order_detail);
        // 标记分销订单已重新计算
        $this->whereIn('id' , $ids)->update([
            'is_award' => 1,
        ]);
        return true;
    }
}
