<?php

namespace app\api\model\plus\agent;

use app\api\model\order\Order as OrderModel;
use app\api\model\plus\agent\Referee as RefereeModel;
use app\api\model\user\User as UserModel;
use app\common\model\plugin\agent\Apply as ApplyModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 分销商申请模型
 */
class Apply extends ApplyModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'create_time',
        'update_time',
    ];

    /**
     * 是否为分销商申请中
     */
    public static function isApplying($user_id)
    {
        $detail = self::detail(['user_id' => $user_id]);
        return $detail ? ((int)$detail['apply_status']['value'] === 10) : false;
    }

    /**
     * 提交申请
     */
    public function submit($user, $data)
    {
        // 成为分销商条件
        $config = Setting::getItem('condition');
        // 如果之前有关联分销商，则继续关联之前的分销商
        $has_referee_id = Referee::getRefereeUserId($user['user_id'], 1);
        if($has_referee_id > 0){
            $referee_id = $has_referee_id;
        }else{
            $referee_id = $data['referee_id'] > 0 ?$data['referee_id']:0;
        }
        // 数据整理
        $data = [
            'user_id' => $user['user_id'],
            'real_name' => trim($data['name']),
            'mobile' => trim($data['mobile']),
            'referee_id' => $referee_id,
            'apply_type' => $config['become'],
            'apply_time' => time(),
            'app_id' => self::$app_id,
        ];
        if ($config['become'] == 10) {
            $data['apply_status'] = 20;
        } elseif ($config['become'] == 20) {
            $data['apply_status'] = 10;
        }
        return $this->add($user, $data);
    }

    /**
     * 提交申请
     * @param $user
     * @param $data
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function submitData($user, $data)
    {
        // 成为分销商条件
        $config = Setting::getItem('condition');

        // 如果之前有关联分销商，则继续关联之前的分销商
        $has_referee_id = Referee::getRefereeUserId($user['user_id'], 1);

        if ($has_referee_id > 0) {
            $referee_id = $has_referee_id;
        } else {
            // 若传的手机号
            if (isset($data['mobile']) && $data['mobile']) {
                $userModel = new UserModel();
                $refereeInfo = $userModel->getByMobile($data['mobile']);
                $referee_id = $refereeInfo ? $refereeInfo['user_id'] : 0;
            } else {
                $referee_id = isset($data['referee_id']) && $data['referee_id'] > 0 ? $data['referee_id'] : 0;
            }
        }

        // 数据整理
        $data = [
            'user_id' => $user['user_id'],
            'real_name' => $user['realname'],
            'mobile' => $user['mobile'],
            'referee_id' => $referee_id,
            'apply_type' => $config['become'],
            'apply_time' => time(),
            'app_id' => self::$app_id,
        ];

        if ($config['become'] == 10) {
            $data['apply_status'] = 20;
        } elseif ($config['become'] == 20) {
            $data['apply_status'] = 10;
        }

        return $this->add($user, $data);
    }

    /**
     * 检测申请状态
     * @param $user
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function checkApply($user)
    {
        // 判断是否分销商
        $isAgent = User::isAgentUser($user['user_id']);

        $isApplying = false;
        $isPop = false;
        $type = 0;
        $name = '';
        $setmsg = '';

        $isVerify = 0;//是否审核通过

        // 获取分销商申请状态
        $detail = self::detail(['user_id' => $user['user_id']]);

        if ($detail) {
            $isApplying = (int)$detail['apply_status']['value'] === 10;

            // 成为分销商条件
            $config = Setting::getItem('condition');

            // 判断有没有购买过
            $is_buy = false;

            if ($detail['apply_type'] == '30' && !empty($config['product_ids'])) {
                $buyNum = OrderModel::getUserBuyProduct($user['user_id'], $config['product_ids']);
                $is_buy = $buyNum > 0;
            }

            switch ($detail['apply_type']['value']) {
                case '20':
                    $type = 3;
                    $name = '审核';
                    $setmsg = '审核通过';
                    break;
                case '30':
                    if ($isApplying && $is_buy) {
                        $isAgent = true;
                        $isApplying = false;
                        $isVerify = 1;
                    }
                    $type = 5;
                    $name = '任务：购买指定商品(0/1)';
                    $setmsg = '您已完成购买任务';
                    break;
                case '40':
                    if ($isApplying && $user['expend_money'] >= $config['meet_amount']) {
                        $isAgent = true;
                        $isApplying = false;
                        $isVerify = 1;
                    }
                    $type = 4;
                    $name = '任务：购物满'.$config['meet_amount'].'元(进行中)';
                    $setmsg = '您已完成购买任务';
                    break;
            }

            // 已经成为分销商且没弹窗过，弹窗
            $isPop = $isAgent && !$detail['is_pop'];
        }

        // 满足条件，成为分销商
        if ($detail) {
            if ($isVerify) {
                $this->startTrans();
                // 新增分销商用户
                User::add($user['user_id'], [
                    'real_name' => $user['realname'],
                    'mobile' => $user['mobile'],
                    'referee_id' => $user['referee_id'],
                ]);
                $detail->save([
                    'audit_time' => time(),
                    'apply_status' => 20
                ]);
                $this->commit();
            }

            // 标记已弹窗
            if ($isPop) {
                $detail->save(['is_pop' => 1]);
            }
        }

        return [
            'is_agent' => $isAgent,
            'is_applying' => $isApplying,
            'is_pop' => $isPop,
            'name' => $name,
            'type' => $type,
            'setmsg' => $setmsg
        ];
    }

    /**
     * 更新分销商申请信息
     * @param $user
     * @param $data
     * @return bool
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    private function add($user, $data)
    {
        // 实例化模型
        $model = self::detail(['user_id' => $user['user_id']]) ?: $this;
        // 更新记录
        $this->startTrans();
        try {
            // 保存申请信息
            $model->save($data);
            // 无需审核，自动通过
            if ($data['apply_type'] == 10) {
                // 新增分销商用户记录
                User::add($user['user_id'], [
                    'real_name' => $data['real_name'],
                    'mobile' => $data['mobile'],
                    'referee_id' => $data['referee_id']
                ]);
            }
            // 记录推荐人关系
            if ($data['referee_id'] > 0) {
                RefereeModel::createRelation($user['user_id'], $data['referee_id']);
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}
