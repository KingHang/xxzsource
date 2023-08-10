<?php

namespace app\mall\model\user;

use app\common\library\helper;
use app\common\model\user\UserTag as UserTagModel;
use app\mall\model\user\GradeLog as GradeLogModel;
use app\mall\model\user\BalanceLog as BalanceLogModel;
use app\common\model\user\User as UserModel;
use app\common\enum\user\grade\ChangeTypeEnum;
use app\common\enum\user\balanceLog\BalanceLogSceneEnum as SceneEnum;
use app\mall\model\user\PointsLog as PointsLogModel;

/**
 * 用户模型
 */
class User extends UserModel
{
    /**
     * 获取当前用户总数
     */
    public function getUserTotal($day = null)
    {
        $model = $this;
        if (!is_null($day)) {
            $startTime = strtotime($day);
            $model = $model->where('create_time', '>=', $startTime)
                ->where('create_time', '<', $startTime + 86400);
        }
        return $model->where('is_delete', '=', '0')->count();
    }

    /**
     * 获取用户id
     * @return \think\Collection
     */
    public function getUsers($where = null)
    {
        // 获取用户列表
        return $this->where('is_delete', '=', '0')
            ->where($where)
            ->order(['user_id' => 'asc'])
            ->field(['user_id'])
            ->select();
    }

    /**
     * 获取用户列表
     */
    public static function getList($nickName, $grade_id, $reg_date, $gender, $params)
    {
        $model = new static();
        //检索：用户名/手机号
        if (!empty($nickName)) {
            $model = $model->where('user.nickName|user.mobile|user.realname', 'like', '%' . $nickName . '%');
        }
        // 检索：会员等级
        if ($grade_id > 0) {
            $model = $model->where('user.grade_id', '=', (int)$grade_id);
        }
        //检索：注册时间
        if (!empty($reg_date[0])) {
            $model = $model->whereTime('user.create_time', 'between', $reg_date);
        }
        // 检索：性别
        if (!empty($gender) && $gender > -1) {
            $model = $model->where('user.gender', '=', (int)$gender);
        }
        // 检索：标签
        if (!empty($params['tag_id']) && $params['tag_id'] > 0) {
            $model = $model->where('tag.tag_id', '=', (int)$params['tag_id']);
        }

        // 获取用户列表
        $list = $model->alias('user')->with(['grade'])->distinct(true)->field(['user.*'])
            ->where('user.is_delete', '=', '0')
            ->join('user_tag tag', 'user.user_id = tag.user_id','left')
            ->order(['user.create_time' => 'desc'])
            ->hidden(['open_id', 'union_id'])
            ->paginate($params);
        if ($list) {
            foreach ($list as $key => &$value) {
                $value['time'] = '0.00';
                $value['cfp'] = '0.00';
            }
        }
        return $list;
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        return $this->transaction(function () {
            // 标记为已删除
            $status = $this->save(['is_delete' => 1]);
            return $status;
        });
    }

    /**
     * 新增记录
     */
    public function add($data)
    {
        return $this->save($data);
    }

    /**
     * 修改记录
     */
    public function edit($data)
    {
        $data['create_time'] = strtotime($data['create_time']);
        $data['update_time'] = time();
        return $this->save($data);
    }

    /**
     * 修改用户等级
     */
    public function updateGrade($data)
    {
        if (!isset($data['remark'])) {
            $data['remark'] = '';
        }
        // 变更前的等级id
        $oldGradeId = $this['grade_id'];
        return $this->transaction(function () use ($oldGradeId, $data) {
            // 更新用户的等级
            $status = $this->save(['grade_id' => $data['grade_id']]);
            // 新增用户等级修改记录
            if ($status) {
                (new GradeLogModel)->save([
                    'user_id' => $this['user_id'],
                    'old_grade_id' => $oldGradeId,
                    'new_grade_id' => $data['grade_id'],
                    'change_type' => ChangeTypeEnum::ADMIN_USER,
                    'remark' => $data['remark'],
                    'app_id' => $this['app_id']
                ]);
            }
            return $status !== false;
        });
    }

    /**
     * 消减用户的实际消费金额
     */
    public function setDecUserExpend($userId, $expendMoney)
    {
        return $this->where(['user_id' => $userId])->dec('expend_money', $expendMoney)->update();
    }

    /**
     * 用户充值
     */
    public function recharge($storeUserName, $source, $data)
    {
        if ($source == 0) {
            return $this->rechargeToBalance($storeUserName, $data['balance']);
        } elseif ($source == 1) {
            return $this->rechargeToPoints($storeUserName, $data['exchangepurch']);
        }
        return false;
    }

    /**
     * 用户充值：余额
     */
    private function rechargeToBalance($storeUserName, $data)
    {
        if (!isset($data['money']) || $data['money'] === '' || $data['money'] < 0) {
            $this->error = '请输入正确的金额';
            return false;
        }
        // 判断充值方式，计算最终金额
        $money = 0;
        if ($data['mode'] === 'inc') {
            $diffMoney = $this['balance'] + $data['money'];
            $money = $data['money'];
        } elseif ($data['mode'] === 'dec') {
            $diffMoney = $this['balance'] - $data['money'] <= 0 ? 0 : $this['balance'] - $data['money'];
            $money = -$data['money'];
        } else {
            $diffMoney = $data['money'];
            $money = $diffMoney - $this['balance'];
        }
        // 更新记录
        $this->transaction(function () use ($storeUserName, $data, $diffMoney, $money) {
            // 更新账户余额
            $this->where('user_id', '=', $this['user_id'])->update(['balance' => $diffMoney]);
            // 新增余额变动记录
            BalanceLogModel::add(SceneEnum::ADMIN, [
                'user_id' => $this['user_id'],
                'money' => $money,
                'remark' => $data['remark'],
            ], [$storeUserName]);
        });
        return true;
    }

    /**
     * 用户充值：积分
     */
    private function rechargeToPoints($storeUserName, $data)
    {
        if (!isset($data['value']) || $data['value'] === '' || $data['value'] < 0) {
            $this->error = '请输入正确的积分数量';
            return false;
        }
        $points = 0;
        // 判断充值方式，计算最终积分
        if ($data['mode'] === 'inc') {
            $diffMoney = $this['exchangepurch'] + $data['value'];
            $points = $data['value'];
        } elseif ($data['mode'] === 'dec') {
            $diffMoney = $this['exchangepurch'] - $data['value'] <= 0 ? 0 : $this['exchangepurch'] - $data['value'];
            $points = -$data['value'];
        } else {
            $diffMoney = $data['value'];
            $points = $data['value'] - $this['exchangepurch'];
        }
        // 更新记录
        $this->transaction(function () use ($storeUserName, $data, $diffMoney, $points) {
            $totalPoints = $this['total_points'] + $points <= 0? 0 : $this['total_points'] + $points;
            // 更新账户积分
            $this->where('user_id', '=', $this['user_id'])->update([
                'exchangepurch' => $diffMoney,
                'total_points' => $totalPoints
            ]);
            // 新增积分变动记录
            PointsLogModel::add([
                'user_id' => $this['user_id'],
                'value' => $points,
                'describe' => "后台管理员 [{$storeUserName}] 操作",
                'remark' => $data['remark'],
            ]);
        });
        event('UserGrade', $this['user_id']);
        return true;
    }


    /**
     * 获取用户统计数量
     */
    public function getUserData($startDate = null, $endDate = null, $type)
    {
        $model = $this;
        if(!is_null($startDate)){
            $model = $model->where('create_time', '>=', strtotime($startDate));
        }
        if(is_null($endDate)){
            $model = $model->where('create_time', '<', strtotime($startDate) + 86400);
        }else{
            $model = $model->where('create_time', '<', strtotime($endDate) + 86400);
        }
        if($type == 'user_total' || $type == 'user_add'){
            return $model->count();
        } else if($type == 'user_pay'){
            return $model->where('pay_money', '>', '0')->count();
        } else if($type == 'user_no_pay'){
            return $model->where('pay_money', '=', '0')->count();
        }
        return 0;
    }

    public function editTag($data){
        // 删除所有标签
        (new UserTagModel())->where('user_id', '=', $this['user_id'])
            ->delete();
        $tag_list = [];
        foreach ($data['checkedTag'] as $val) {
            $tag_list[] = [
                'user_id' => $this['user_id'],
                'tag_id' => $val,
                'app_id' => self::$app_id
            ];
        }
        return (new UserTagModel())->saveAll($tag_list);
    }
}
