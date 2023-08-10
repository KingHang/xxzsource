<?php

namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\model\user\PointsLog as PointsLogModel;
use app\common\model\user\GrowthLog as GrowthLogModel;
use app\common\model\purveyor\User as SupplierUserModel;
use app\common\model\user\LoginLog as UserLoginLogModel;
use app\timebank\model\Timelog;
use Exception;
use think\facade\Cache;
use think\Validate;

/**
 * 用户模型
 */
class User extends BaseModel
{
    protected $pk = 'user_id';

    protected $name = 'user';

    protected $append = [
        'referee_name',
    ];
    /**
     * 关联分销商用户表
     */
    public function agent()
    {
        return [];
    }
    /**
     * 关联会员等级表
     */
    public function grade()
    {
        return $this->belongsTo('app\\common\\model\\user\\Grade', 'grade_id', 'grade_id');
    }

    /**
     * 关联收货地址表
     */
    public function address()
    {
        return $this->hasMany('app\\common\\model\\user\\UserAddress', 'address_id', 'address_id');
    }

    /**
     * 关联供应商表
     */
    public function supplierUser()
    {
        return $this->hasOne('app\\common\\model\\purveyor\\User', 'user_id', 'user_id');
    }

    /**
     * 关联收货地址表 (默认地址)
     */
    public function addressDefault()
    {
        return $this->belongsTo('app\\common\\model\\user\\UserAddress', 'address_id', 'address_id');
    }

    /**
     * 获取用户信息
     */
    public static function detail($where,$with = [])
    {
        $model = new static;
        $filter = ['is_delete' => 0];
        if (is_array($where)) {
            $filter = array_merge($filter, $where);
        } else {
            $filter['user_id'] = (int)$where;
        }
        $with = !empty($with) ? $with : ['address', 'addressDefault', 'grade'];
        return $model->where($filter)->with($with)->find();
    }

    /**
     * 获取用户信息
     */
    public static function detailByUnionid($unionid)
    {
        $model = new static;
        $filter = ['is_delete' => 0];
        $filter = array_merge($filter, ['union_id' => $unionid]);
        return $model->where($filter)->with(['address', 'addressDefault', 'grade'])->find();
    }

    /**
     * 指定会员等级下是否存在用户
     */
    public static function checkExistByGradeId($gradeId)
    {
        $model = new static;
        return !!$model->where('grade_id', '=', (int)$gradeId)
            ->where('is_delete', '=', 0)
            ->value('user_id');
    }

    /**
     * 累积用户总消费金额
     */
    public function setIncPayMoney($money)
    {
        return $this->where('user_id', '=', $this['user_id'])->inc('pay_money', $money)->update();
    }

    /**
     * 累积用户实际消费的金额 (批量)
     */
    public function onBatchIncExpendMoney($data)
    {
        foreach ($data as $userId => $expendMoney) {
            $this->where(['user_id' => $userId])->inc('expend_money', $expendMoney)->update();
            // 累计当月消费
//            AgentMonthModel::saveByMonth($userId, 'buy', $expendMoney);
            event('UserGrade', $userId);
        }
        return true;
    }

    /**
     * 累积用户实际消费的金额，下单就算，不管退款
     */
    public function incExpendMoney($order)
    {
        $this->where(['user_id' => $order['user_id']])->inc('expend_money', $order['pay_price'])->update();
        event('UserGrade', $order['user_id']);
    }

    /**
     * 累积用户的可用积分数量 (批量)
     * @param $data
     * @return bool
     */
    public function onBatchIncPoints($data)
    {
        foreach ($data as $userId => $expendPoints) {
            $this->where(['user_id' => $userId])->inc('points', $expendPoints)->update();
            event('UserGrade', $userId);
        }
        return true;
    }

    /**
     * 累积用户的可用成长值数量 (批量)
     * @param $data
     * @return bool
     */
    public function onBatchIncGrowthValue($data)
    {
        foreach ($data as $userId => $expendGrowthValue) {
            $this->where(['user_id' => $userId])->inc('growth_value', $expendGrowthValue)->update();
            event('UserGrade', $userId);
            $info[$userId] = self::detail($userId);
            if ($info[$userId] && $info[$userId]['mobile']) {
                $this->growthValueToken($info[$userId]['mobile'], $expendGrowthValue, $userId);
            }
        }
        return true;
    }

    /**
     * 累积用户的CFP等等数量 (批量)
     * @param $data
     * @return bool
     */
    public function onBatchIncGiftcertAmount($data)
    {
        foreach ($data as $userId => $amount) {
            $info[$userId] = self::detail($userId);

            if ($info[$userId] && $info[$userId]['mobile']) {
                $this->giftcertAmountToken($info[$userId]['mobile'], $amount, $userId);
            }
        }

        return true;
    }

    /**
     * 累积用户的可用积分
     * @param $points
     * @param $describe
     * @param int $type
     * @param int $isRefresh 是否刷新用户：0-否，1-是
     * @return bool
     */
    public function setIncPoints($points, $describe, $type = 0, $isRefresh = 0)
    {
        // 新增积分变动明细
        PointsLogModel::add([
            'user_id' => $this['user_id'],
            'value' => $points,
            'describe' => $describe,
            'app_id' => $this['app_id'],
            'type' => $type
        ]);

        // 更新用户可用积分
        $data['points'] = ($this['points'] + $points <= 0) ? 0 : $this['points'] + $points;

        // 刷新用户，避免同事务幻读
        if ($isRefresh) {
            $this['points'] = $data['points'];
        }

        // 用户总积分
        if ($points > 0) {
            $data['total_points'] = $this['total_points'] + $points;
        }

        $this->where('user_id', '=', $this['user_id'])->update($data);
        event('UserGrade', $this['user_id']);
        return true;
    }

    /**
     * 增加用户成长值
     * @param $growthValue
     * @param $describe
     * @param int $type
     * @param int $isRefresh 是否刷新用户：0-否，1-是
     * @return bool
     */
    public function setIncGrowthValue($growthValue, $describe, $type = 0, $isRefresh = 0)
    {
        try {
            // 新增成长值变动明细
            GrowthLogModel::add([
                'user_id' => $this['user_id'],
                'value' => $growthValue,
                'describe' => $describe,
                'app_id' => $this['app_id'],
                'type' => $type
            ]);

            // 更新用户成长值
            $data['growth_value'] = ($this['growth_value'] + $growthValue <= 0) ? 0 : $this['growth_value'] + $growthValue;

            // 刷新用户，避免同事务幻读
            if ($isRefresh) {
                $this['growth_value'] = $data['growth_value'];
            }

            $this->where('user_id', '=', $this['user_id'])->update($data);
            event('UserGrade', $this['user_id']);

            // 同步CFP
            $this->growthValueToken($this['mobile'], $growthValue, $this['user_id']);

            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 根据用户id累积用户的可用积分
     * @param $user_id
     * @param $points
     * @param $describe
     * @param int $type
     * @param int $app_id
     * @return bool
     */
    public function setIncPointsByUser($user_id, $points, $describe, $type = 0, $app_id = 0)
    {
        // 新增积分变动明细
        PointsLogModel::add([
            'user_id' => $user_id,
            'value' => $points,
            'describe' => $describe,
            'app_id' => $app_id ? $app_id : $this['app_id'],
            'type' => $type
        ]);

        $userInfo = self::detail($user_id);

        if ($userInfo) {
            // 更新用户可用积分
            $data['points'] = ($userInfo['points'] + $points <= 0) ? 0 : $userInfo['points'] + $points;

            // 用户总积分
            if ($points > 0) {
                $data['total_points'] = $userInfo['total_points'] + $points;
            }

            $this->where('user_id', '=', $user_id)->update($data);
            event('UserGrade', $user_id);
        }

        return true;
    }

    /**
     * 根据用户id增加用户成长值
     * @param $user_id
     * @param $growthValue
     * @param $describe
     * @param int $type
     * @param int $app_id
     * @return bool
     */
    public function setIncGrowthValueByUser($user_id, $growthValue, $describe, $type = 0, $app_id = 0)
    {
        try {
            // 新增成长值变动明细
            GrowthLogModel::add([
                'user_id' => $user_id,
                'value' => $growthValue,
                'describe' => $describe,
                'app_id' => $app_id ? $app_id : $this['app_id'],
                'type' => $type
            ]);

            $userInfo = self::detail($user_id);

            // 用户成长值
            if ($userInfo && $growthValue > 0) {
                $data['growth_value'] = $userInfo['growth_value'] + $growthValue;
                $this->where('user_id', '=', $user_id)->update($data);
                event('UserGrade', $user_id);

                $this->growthValueToken($userInfo['mobile'], $growthValue, $user_id, $app_id);
            }

            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * 成长值兑换通证
     * @param $mobile
     * @param $growthValue
     * @param $user_id
     * @param int $app_id
     * @return bool
     */
    private function growthValueToken($mobile, $growthValue, $user_id, $app_id = 0)
    {
        return true;
    }

    /**
     * CFP抵扣，赠送CFP等等
     * @param $mobile
     * @param $amount
     * @param $user_id
     * @param string $remark
     * @return bool
     */
    public function giftcertAmountToken($mobile, $amount, $user_id, $remark = '')
    {
       return true;
    }

    //更新用户类型
    public static function updateType($user_id, $user_type)
    {
        $model = new static;
        return $model->where('user_id', '=', $user_id)->update([
            'user_type' => $user_type
        ]);
    }

    /**
     * 用户是否成功成为供应商，如果不是则为审核中
     * 申请中的不算
     */
    public static function isSupplier($user_id)
    {
        return SupplierUserModel::detail([
                'user_id' => $user_id
            ]) != null;
    }

    /**
     * 累计邀请书
     */
    public function setIncInvite($user_id)
    {
        $this->where('user_id', '=', $user_id)->inc('total_invite')->update();
        event('UserGrade', $user_id);
//        event('AgentUserGrade', $user_id);
    }

    public function afterReg($user){
        // 注册之后是否成为分销商
//        $condition = AgentSettingModel::getItem('condition');
//        if($condition['become'] == 0){
//            // 新增分销商用户
//            AgentUserModel::add($user['user_id'], [
//                'real_name' => $user['nickName'],
//                'referee_id' => $user['referee_id'],
//                'mobile' => isset($user['mobile'])?$user['mobile']:'',
//            ]);
//            $user = AgentUserModel::detail($user['user_id']);
//            $grade = (new Grade())::detail((new Grade())::getDefaultGradeId());
//        }
    }

    /**
     * 用户登录
     *
     * @param string    $account    昵称、邮箱、手机号
     * @param string    $password   密码
     * @return boolean
     */
    public static function userCheck($username = '',$password = '',$app_id) {
        $Validate = new Validate();

        //重置登录字段
        $field = $Validate->regex($username, '/^1\d{10}$/') ? 'mobile' : ($Validate->isIdcard($username) ? 'id_card' : 'nickName');
        $where = array($field => $username,'app_id' => $app_id , 'is_delete' => 0);

        //根据登录字段获取会员信息
        $userInfo = static::where($where)->field($field . ',is_delete,password,user_id,mobile,nickName,avatarUrl')->find();

        //判断会员是否存在
        if (!$userInfo) {
            return false;
        }

        //判断会员是否删除
        if ($userInfo['is_delete'] === 1) {
            return false;
        }

        //验证码密码是否正确
        if ($password) {
            if ($userInfo['password'] !== salt_hash($password)) {
                return false;
            }
            //生产会员token
            $token = static::createToken($userInfo['user_id']);
            Cache::tag('cache')->set($token, $userInfo['user_id'], 86400 * 30);

            //生产登录回调信息
            $result['token'] = $token;
            $result['mobile'] = $userInfo['mobile'];
            $result['nickName'] = $userInfo['nickName'];
            $result['user_id'] = $userInfo['user_id'];
            $result['avatarUrl'] = $userInfo['avatarUrl'];

            //插入登陆日志
            UserLoginLogModel::saveLoginLog($result);
            return $result;
        } else {
            return true;
        }
    }

    /**
     * 生成用户认证的token
     */
    public static function createToken($user_id = 0)
    {
        // 生成一个不会重复的随机字符串
        $guid = \getGuidV4();
        // 当前时间戳 (精确到毫秒)
        $timeStamp = microtime(true);
        // 自定义一个盐
        $salt = 'token_salt';
        $str = "{$user_id}_{$timeStamp}_{$guid}_{$salt}_" . static::$app_id;

        $str = md5(uniqid(md5($str), true));  //生成一个不会重复的字符串
        $str = sha1($str);  //加密
        return $str;
    }
    /**
     * 设置错误信息
     */
    private function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }
    public function getRefereeNameAttr($value, $data)
    {
        if (!empty($data['referee_id'])){
            $user = self::field('nickName')->where('user_id',$data['referee_id'])->find();
            return $user;
        }
    }
    public static function getOpenId($data)
    {
        $APPID = $data['app_id'];
        $CODE = $data['code'];
        $SECRET = $data['secret'];
        /**
         * code 换取 session_key
         * ​这是一个 HTTPS 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。
         */
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $result = json_decode(curl($url, [
            'appid' => $APPID,
            'secret' => $SECRET,
            'grant_type' => 'authorization_code',
            'js_code' => $CODE
        ]), true);
        return $result;
    }
    public static function checkMsg($data)
    {
        $APPID = $data['app_id'];
        $SECRET = $data['secret'];
        $token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$SECRET." ";
        if (!$access_token = Cache::get('access_token')) {
            $access_token = json_decode(file_get_contents($token_url),true);
            $access_token = $access_token['access_token'];
            Cache::tag('cache')->set('access_token',$access_token,7200);
        }
        $url = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token=".$access_token."";
        $result = json_decode(curlPost($url,
            json_encode(['content'=>$data['comment_text']])
        ), true);
        return $result;
    }

}