<?php

namespace app\api\model\user;

use app\api\model\plus\agent\Setting as AgentSettingModel;
use app\api\model\settings\Settings as SettingModel;
use app\common\model\app\AppByte as AppByteModel;
use app\common\model\app\AppMp as AppMpModel;
use app\common\model\store\Clerk as ClerkModel;
use app\common\model\home\CenterMenu as CenterMenuModel;
use app\common\service\ztservice\ZtService;
use app\timebank\ztservice\Service;
use think\Exception;
use think\facade\Cache;
use app\common\exception\BaseException;
use app\common\model\user\User as UserModel;
use app\api\model\plus\agent\Referee as RefereeModel;
use app\common\library\easywechat\AppWx;
use app\common\model\user\Grade as GradeModel;
use app\common\library\wechat\WxBizDataCrypt;
use app\api\model\plus\activity\ActivityHostRember;
use app\api\model\plus\agent\User as AgentUserModel;

/**
 * 用户模型类
 */
class User extends UserModel
{
    private $token;

    /**
     * 隐藏字段
     */
    protected $hidden = [
        'open_id',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];
    /**
     * 追加字段
     * @var string[]
     */
    protected $append = [
        'is_host_user', // 是否是活动主办方程序
        'format_birthday',
    ];

    public function getIsHostUserAttr($value,$data)
    {
        return !!(new ActivityHostRember())->checkUserAuth($data['user_id']);
    }
    public function getFormatBirthdayAttr($value,$data)
    {
        return isset($data['birthday']) && $data['birthday'] > 0 ? date('Y-m-d',$data['birthday']) : '';
    }
    /**
     * 获取用户信息
     */
    public static function getUser($token,$field='',$with = '')
    {
        $field = $field == '' ? '*' : $field;
        $with = $with == '' ? ['address', 'addressDefault', 'grade', 'supplierUser'] : $with;
        $userId = Cache::get($token);
//        $userId = 24639;
        return (new static())->field($field)->where(['user_id' => $userId])->with($with)->find();
    }

    /**
     * 用户登录
     */
    public function login($post)
    {
        //判断来源
        if ($post['source'] == 'kdd') {
            // 微信登录 获取session_key
            $app = AppWx::getKddApp();
        } else {
            // 微信登录 获取session_key
            $app = AppWx::getApp();
        }
        $session = $app->auth->session($post['code']);
        $pc = new WxBizDataCrypt($app['config']['app_id'], $session['session_key']);
        $errCode = $pc->decryptData(urldecode($post['encrypted_data']), urldecode($post['iv']), $data);
        if ($errCode !== 0) {
            return false;
        }
        $data = json_decode($data, true);
        // 自动注册用户
        $refereeId = isset($post['referee_id']) ? $post['referee_id'] : null;
        $userInfo = json_decode(htmlspecialchars_decode($post['user_info']), true);

        $reg_source = $post['source'];
        $userInfo['mobile'] = $data['phoneNumber'];
        $user_id = $this->register($session['openid'], $userInfo, $refereeId, $session, $reg_source);
        // 生成token (session3rd)
        $this->token = $this->token($session['openid']);
        // 记录缓存, 7天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 30);
        return $user_id;
    }

    /**
     * 字节小程序用户登录
     */
    public function byteLogin($post)
    {
        // 字节小程序登录
        // 获取当前小程序信息
        $wxConfig = AppByteModel::getAppByteCache(self::$app_id);

        // 验证appid和appsecret是否填写
        if (empty($wxConfig['byteapp_id']) || empty($wxConfig['byteapp_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-应用-公众号设置] mpapp_id 和 mpapp_secret']);
        }

        $config = [
            'appid' => $wxConfig['byteapp_id'],
            'secret' => $wxConfig['byteapp_secret'],
            'code' => $post['code'],
        ];
        $str = http_build_query($config);

        $url = 'https://developer.toutiao.com/api/apps/jscode2session?' . $str;

        //获取token，如果没有就弹出错误
        try {
            $token = json_decode(file_get_contents($url, false));
        } catch (\Exception $e) {
            $this->error = json_encode($e);
            return false;
        }

        if (isset($token->errcode)) {
            log_write($token->errcode);
            log_write($token->errmsg);
            $this->error = $token->errmsg;
            return false;
        }

        $userInfo = [
            'nickName' => $post['nickName'],
            'avatarUrl' => $post['avatarUrl'],
        ];

        $reg_source = $post['source'];
        $refereeId = isset($post['referee_id']) ? $post['referee_id'] : null;
        $unionId = [$token->unionid];

        $user_id = $this->register($token->openid, $userInfo, $refereeId, $unionId, $reg_source);
        // 生成token (session3rd)
        $this->token = $this->token($token->openid);
        // 记录缓存, 7天
        Cache::tag('cache')->set($this->token, $user_id, 86400 * 7);
        return $user_id;
    }

    /**
     * 用户登录
     */
    public function bindMobile($post)
    {
        // 微信登录 获取session_key
        $app = AppWx::getApp();
        $session = $app->auth->session($post['code']);
        $iv = urldecode($post['iv']);
        $encrypted_data = urldecode($post['encrypted_data']);
        $pc = new WxBizDataCrypt($app['config']['app_id'], $session['session_key']);
        $errCode = $pc->decryptData($encrypted_data, $iv, $data);
        if ($errCode == 0) {
            $data = json_decode($data, true);
            return $this->save([
                'mobile' => $data['phoneNumber']
            ]);
        }
        return false;
    }

    /**
     * 修改用户信息
     * @param $post
     * @return bool
     * @throws BaseException
     */
    public function updateUser($post)
    {
        if (isset($post['type']) && $post['type'] == 2) {
            if (!$post['weixin']) {
                throw new BaseException(['msg' => '微信号不能为空']);
            }

            return $this->save([
                'weixin' => $post['weixin']
            ]);
        } else {
            
			$userData = [];
			if (isset($post['nickName'])) {
				$userData['nickName'] = $post['nickName'];
			}
			if (isset($post['avatarUrl'])) {
				$userData['avatarUrl'] = $post['avatarUrl'];
			}
			if (empty($userData)) {
                throw new BaseException(['msg' => '请选择修改项']);
            }
            return $this->save($userData);
        }
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
        $app_id = self::$app_id;
        // 生成一个不会重复的随机字符串
        $guid = \getGuidV4();
        // 当前时间戳 (精确到毫秒)
        $timeStamp = microtime(true);
        // 自定义一个盐
        $salt = 'token_salt';
        return md5("{$app_id}_{$timeStamp}_{$openid}_{$guid}_{$salt}");
    }

    /**
     * 自动注册用户
     */
    private function register($open_id, $data, $refereeId = null, $decryptedData = [], $reg_source = 'wx')
    {
        //通过unionid查询用户是否存在
        $user = null;
//        if (isset($decryptedData['unionid']) && !empty($decryptedData['unionid'])) {
//            $data['union_id'] = $decryptedData['unionid'];
//            $user = self::detailByUnionid($decryptedData['unionid']);
//        }
        if (isset($data['mobile']) && !empty($data['mobile'])) {
            $user = self::detail(['mobile'=>$data['mobile']]);
        }
//        if (!$user) {
//            // 通过open_id查询用户是否已存在
//            $user = self::detail(['open_id' => $open_id]);
//        }
        if ($user) {
            $userData = ['avatarUrl'=>$data['avatarUrl']];
            if ($reg_source == 'wx') {
                $userData['shopopen_id'] = $open_id;
            }
            if ($userOpenId = $user->open_id){
                if ($userOpenId == $open_id){
                    $user->save($userData);
                    return $user->user_id;
                }else{
                    $userData['open_id'] = $open_id;
                    $user->save($userData);
                    return $user->user_id;
                }
            }else{
                $userData['open_id'] = $open_id;
                $user->save($userData);
                return $user->user_id;
            }
        } else {
            $model = $this;
            $data['referee_id'] = $refereeId;
            $data['reg_source'] = $reg_source;
            $data['avatarUrl'] = $data['avatarUrl'];
            //默认等级
            $data['grade_id'] = GradeModel::getDefaultGradeId();

            // 接入中台create接口
            if (isset($data['mobile']) && !empty($data['mobile'])) {
                $access_token = (new Service())->getZtAccessToken('access_token');
                $ztInfo['mobile'] = $data['mobile'];
                $ztInfo['username'] = isset($data['nickName']) ? $data['nickName'] : '';
                $ztInfo['avatar'] = isset($data['avatarUrl']) ? $data['avatarUrl'] : '';
                $ztInfo['gender'] = isset($data['gender']) ? $data['gender'] : 2;
                $ztInfo['access_token'] = $access_token;
                (new ZtService())->blockchainTimebankCreate($ztInfo, 2);
            }
        }
        if ($reg_source == 'wx') {
            $data['shopopen_id'] = $open_id;
        }
        // 获取成长值配置
        $growSetting = SettingModel::getItem('grow');
        $is_grow_register = isset($growSetting['is_grow']) && $growSetting['is_grow'] == '1' && isset($growSetting['is_register']) && $growSetting['is_register'] == '1' ? 1 : 0;
        $register_grow = isset($growSetting['register_grow']) && $growSetting['register_grow'] >= 0 ? $growSetting['register_grow'] : 0;
        $is_grow_invite = isset($growSetting['is_grow']) && $growSetting['is_grow'] == '1' && isset($growSetting['is_invite']) && $growSetting['is_invite'] == '1' ? 1 : 0;
        $invite_grow = isset($growSetting['invite_grow']) && $growSetting['invite_grow'] >= 0 ? $growSetting['invite_grow'] : 0;

        $this->startTrans();
        try {
            // 保存/更新用户记录
            if (!$model->save(array_merge($data, [
                'open_id' => $open_id,
                'app_id' => self::$app_id
            ]))
            ) {
                throw new BaseException(['msg' => '用户注册失败']);
            }
            if (!$user && $refereeId > 0) {
                // 记录推荐人关系
                RefereeModel::createRelation($model['user_id'], $refereeId);
                // 更新用户邀请数量
                (new UserModel())->setIncInvite($refereeId);
                // 推荐人送成长值
                if ($is_grow_invite == 1 && $invite_grow > 0) {
                    $this->setIncGrowthValueByUser($refereeId, $invite_grow, '邀请用户注册成功发放奖励', 7);
                }
            }
            // 注册后事件
            (new UserModel())->afterReg($model);
            // 注册送成长值
            if (!$user && $is_grow_register == 1 && $register_grow > 0) {
                $this->setIncGrowthValueByUser($model['user_id'], $register_grow, '用户注册成功', 2);
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }
        return $model['user_id'];
    }

    public function getByMobile($mobile)
    {
        return self::where(['mobile' => $mobile])->find();
    }

    /**
     * 统计被邀请人数
     */
    public function getCountInv($user_id)
    {
        return $this->where('referee_id', '=', $user_id)->count('user_id');
    }

    /**
     * 签到更新用户积分
     */
    public function setPoints($user_id, $days, $sign_conf, $sign_date, $is_points = 0, $is_grow = 0)
    {
        $rank = $sign_conf['ever_sign'];
        $grow = isset($sign_conf['ever_grow']) ? $sign_conf['ever_grow'] : 0;
        if ($sign_conf['is_increase'] == 'true') {
            if ($days >= $sign_conf['no_increase']) {
                $days = $sign_conf['no_increase'] - 1;
            }
            $rank = ($days - 1) * $sign_conf['increase_reward'] + $rank;
            $grow = ($days - 1) * $sign_conf['increase_grow'] + $grow;
        }
        if ($is_points) {
            // 新增积分变动明细
            $this->setIncPoints($rank, '用户签到：签到日期' . $sign_date, 1, 1);
        }
        if ($is_grow) {
            // 新增成长值变动明细
            $this->setIncGrowthValue($grow, '用户签到：签到日期' . $sign_date, 1, 1);
        }
        return ['exchangepurch' => $rank, 'grow' => $grow];
    }

    /**
     * 个人中心菜单列表
     */
    public static function getMenus($user, $source)
    {
        // 系统菜单
        $sys_menus = CenterMenuModel::getSysMenu();
        // 查询用户菜单
        $model = new CenterMenuModel();
        $user_menus = $model->getAll();
        $user_menu_tags = [];
        foreach ($user_menus as $menu){
            $menu['sys_tag'] != '' && array_push($user_menu_tags, $menu['sys_tag']);
        }
        $save_data = [];
        foreach($sys_menus as $menu){
            if($menu['sys_tag'] != '' && !in_array($menu['sys_tag'], $user_menu_tags)){
                $save_data[] = array_merge($sys_menus[$menu['sys_tag']], [
                    'sort' => 100,
                    'app_id' => self::$app_id
                ]);
            }
        }
        if(count($save_data) > 0){
            $model->saveAll($save_data);
            Cache::delete('center_menu_' . self::$app_id);
            $user_menus = $model->getAll();
        }
        //判断是否入住店铺
        $noShow = [];
        if ($user['user_type'] == 1) {
            array_push($noShow, 'my_shop', 'app_shop');
        } else if ($user['user_type'] == 2) {
            // 申请中或者已入驻成功
            array_push($noShow, 'shop');
            // 入驻成功
            if (UserModel::isSupplier($user['user_id'])) {
                array_push($noShow, 'app_shop');
            } else {
                array_push($noShow, 'my_shop');
            }
        }
        $menus_arr = [];
        foreach ($user_menus as $menu) {
            if($menu['sys_tag'] != '' && $menu['status'] == 1 && !in_array($menu['sys_tag'], $noShow)){
                array_push($menus_arr, $menu);
            }
        }
        foreach ($menus_arr as $menus){
            if(strpos($menus['image_url'], 'http') !== 0){
                $menus['image_url'] = self::$base_url . $menus['image_url'];
            }
        }
        return $menus_arr;
    }

    //获取分销商客户
    public function getAgentUserList($user, $data)
    {
        $userIds = (new AgentUserModel())->getLowerDirectUserIds($user);
        $model = $this->withJoin('agent','left');
        $model = $model->where('user.user_id', 'in', $userIds);
        $data['search'] && $model = $model->where('user.nickName|user.mobile|agent.real_name|user.realname', 'like', '%' . $data['search'] . '%');
        $list = $model
            ->where('user.is_delete', '=', 0)
            ->paginate($data);
        return $list;
    }
}
