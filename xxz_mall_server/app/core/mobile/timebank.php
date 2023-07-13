<?php

function timebankdistancesort($a, $b) {
    return $b['distance'] > $a['distance'] ? -1 : 1;
}

function ranksort($a, $b) {
    return $b['timeaccount'] < $a['timeaccount'] ? -1 : 1;
}

//lcj 20190826
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
require EWEI_SHOPV2_PLUGIN . 'app/core/wxapp/wxBizDataCrypt.php';

class Timebank_EweiShopV2Page extends AppMobilePage {

    protected $member;

    public function __construct() {
        global $_W;
        parent::__construct();
        global $_GPC;
        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid order by openid_wa desc limit 1', array(':mobile' => $_GPC['account'], ':uniacid' => $_W[uniacid]));
        $log = pdo_fetch("select * from " . tablename("ewei_shop_timebank_server") . ' where  server=:server', ['server' => $member['id']]);
        if (empty($log)) {
            $data['openid'] = $member['openid'];
            $data['server'] = $member['id'];
            $data['uniacid'] = $_W['uniacid'];
            $data['status'] = 0;
            if ($member['id'] > 0) {
                pdo_insert('ewei_shop_timebank_server', $data);
                $id = pdo_insertid();
            }
            $member['servestatus'] = -1;
            $member['serveid'] = $id;
        } else {
            if (empty($log['registertime'])) {
                $member['servestatus'] = -1;
            } else {
                $member['servestatus'] = $log['status'];
            }
            $member['timeaccount'] = $log['timeaccount'];
            $member['serveraccount'] = $log['serveraccount'];
            $member['frequency'] = $log['frequency'];
            $member['serveid'] = $log['id'];
        }
        $member = set_medias($member, 'avatar');

        $this->member = $member;

        $data = m('common')->getSysset('app');
        $this->appid = $data['appid'];
        $this->appsecret = $data['secret'];
    }

    /**
     * 时间银行用户注册：2021-6-7
     */
    public function register() {
        global $_W;
        global $_GPC;
        $lang = trim($_GPC['lang']);
        if ($_W['ispost']) {
            $account = trim($_GPC['newaccount']);
            $verifycode = trim($_GPC['verifycode']);

            if (empty($account)) {
                return app_error(-1, '请输入正确的手机号');
            }
            if (empty($verifycode)) {
                if ($lang == 'zh-CN') {
                    return app_error(-1, '请输入验证码');
                } else {
                    return app_error(-1, 'Please enter verification code');
                }
            }

            $member = pdo_fetch('select id,openid,mobile,pwd,salt from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(':mobile' => $account, ':uniacid' => $_W['uniacid']));
            $key = '__ewei_shopv2_member_verifycodesession_' . $_W['uniacid'] . '_' . $account;
            $key_time = '__ewei_shopv2_member_verifycodesendtime_' . $_W['uniacid'];
            $sendcode = m('cache')->get($key);
            $sendtime = m('cache')->get($key_time);
            if (!isset($sendcode) || $sendcode !== $verifycode || !isset($sendtime) || $sendtime + 600 < time()) {
                return app_error(-1, '验证码错误或已过期');
            }

//            $myorigin = userorigin($member['openid']);  && $myorigin == 1
            if (!(empty($member))) {
                return app_error(-1, '此账号已注册, 请直接登录');
            }

            $openid = 'timebank_user_' . $_W['uniacid'] . '_' . $account;
            $nickname = $account;

            $params = [
                'uniacid' => $_W['uniacid'],
                'nickname' => $nickname,
                'openid' => $openid,
                'createtime' => time(),
                'mobileverify' => 1,
                'comefrom' => 'mobile'
            ];

            $encryptedData = trim($_GPC['data']);
            $iv = trim($_GPC['iv']);
            $sessionKey = trim($_GPC['sessionKey']);
            if (empty($encryptedData) || empty($iv)) {
                return app_error(AppError::$ParamsError);
            }

            $pc = new WXBizDataCrypt($this->appid, $sessionKey);
            $errCode = $pc->decryptData($encryptedData, $iv, $data);

            if ($errCode == 0) {
                $data = json_decode($data, true);
                $data['openId'] = trim($_GPC['openId']);
                $this->refine($data['openId']);
                $member= pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid   limit 1', array(':mobile' => $_GPC['account'], ':uniacid' => $_W[uniacid]));

                $params['openid'] = 'sns_wa_' . $data['openId'];
                if ($data['nickName']) {
                    $params['nickname'] = $data['nickName'];
                }
                if ($data['avatarUrl']) {
                    $params['avatar'] = $data['avatarUrl'];
                }
                $params['gender'] = !empty($data['gender']) ? $data['gender'] : '-1';

                if (empty($member)) {
                    $params['uid'] = 0;
                    $params['status'] = 0;
                    $params['mobile'] = $account;
                    $params['createtime'] = time();
                    $params['openid_wa'] = $data['openId'];
                    pdo_insert('ewei_shop_member', $params);

                    $this->tomergewxchat($openid);
                    $data['error'] = 0;
                    $data['message'] = '注册成功，请前往登录';
                    $data['mobile'] = $account;
                    return app_json($data);
                } else {
                    pdo_update('ewei_shop_member', $params, array('id' => $member['id'], 'uniacid' => $member['uniacid']));
                    $data['id'] = $member['id'];
                    $data['uniacid'] = $member['uniacid'];
                    $data['isblack'] = $member['isblack'];

                    $myorigin = userorigin($member['openid']);
                    if (!(empty($member)) && $myorigin == 1) {
                        return app_error(-1, '此账号已注册, 请直接登录');
                    }
                }
            }

            return app_error(-1, '注册失败');
        }
    }

    /**
     * 用户注册
     */
    public function register1() {
        global $_W;
        global $_GPC;
        $lang = trim($_GPC['lang']);
        if ($_W['ispost']) {
            $account = trim($_GPC['newaccount']);
            $pwd = trim($_GPC['password']);
            $verifycode = trim($_GPC['verifycode']);
            if (empty($account)) {
                return app_error(-1, '请输入正确的手机号');
            }
            if (empty($verifycode)) {
                if ($lang == 'zh-CN') {
                    return app_error(-1, '请输入验证码');
                } else {
                    return app_error(-1, 'Please enter verification code');
                }
            }
            if (empty($pwd)) {
                return app_error(-1, '密码不能为空');
            }

            $member = pdo_fetch('select id,openid,mobile,pwd,salt from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(':mobile' => $account, ':uniacid' => $_W['uniacid']));
            $key = '__ewei_shopv2_member_verifycodesession_' . $_W['uniacid'] . '_' . $account;
            $key_time = '__ewei_shopv2_member_verifycodesendtime_' . $_W['uniacid'];
            $sendcode = m('cache')->get($key);
            $sendtime = m('cache')->get($key_time);
            if (!isset($sendcode) || $sendcode !== $verifycode || !isset($sendtime) || $sendtime + 600 < time()) {
                return app_error(-1, '验证码错误或已过期');
            }
            $myorigin = userorigin($member['openid']);
            if (!(empty($member)) && $myorigin == 1) {
                return app_error(-1, '此账号已注册, 请直接登录');
            }
            $salt = ((empty($member) ? '' : $member['salt']));
            if (empty($salt)) {
                $salt = m('account')->getSalt();
            }
            
//            $openid = ((empty($member) ? '' : $member['openid']));
//            $nickname = ((empty($member) ? '' : $member['nickname']));
//            if (empty($openid)) {
//                $openid = 'timebank_user_' . $_W['uniacid'] . '_' . $account;
//                $nickname = $account;
//            }
            
            $openid = 'timebank_user_' . $_W['uniacid'] . '_' . $account;
            $nickname = $account;
            
            $data = array('uniacid' => $_W['uniacid'], 'mobile' => $account, 'nickname' => $nickname, 'openid' => $openid, 'pwd' => md5($pwd . $salt), 'salt' => $salt, 'createtime' => time(), 'mobileverify' => 1, 'comefrom' => 'mobile');

//                  $data['status'] =1;
//                  $data['isagent'] =1;


            pdo_insert('ewei_shop_member', $data);
            $this->tomergewxchat($openid);
            $data['error'] = 0;
            $data['message'] = '注册成功';
            $data['mobile'] = $account;

            return app_json($data);
        }
    }

//检测用户是否已绑定手机号如果已经绑定合并小程序绑定手机号的账户[小程序绑定手机号或公众号绑定手机号时调用]
    public function tomergewxchat($openid) {
        global $_W;
        $_W['openid'] = $openid;
        $member = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_member") . " where `openid`='{$_W['openid']}' ");
        if (empty($member)) {
            return false;
        }
        if (!ismobilenumber($member['mobile']) || $member['mobileverify'] != 1) {
            return false;
        }
        $ckmember = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_member") . " where `openidlist` like '%,{$_W['openid']},%' ");
        //openid已被存储
        if (!empty($ckmember)) {
            return false;
        }
        $mobilesum = pdo_fetch("SELECT count(*) as count FROM " . tablename("ewei_shop_member") . " where `mobile` = '{$member['mobile']}' and id!='{$member['id']}' ");
        if ($mobilesum['count'] > 1) {
            return false;
        }
        $ckmobile = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_member") . " where `mobile` = '{$member['mobile']}' and id!='{$member['id']}' ");
        if (empty($ckmobile)) {
            return false;
        }
        $myorigin = userorigin($_W['openid']);
        $uorigin = userorigin($ckmobile['openid']);
        if ($myorigin == $uorigin) {
            return false;
        }
        if ($myorigin != 5 && $uorigin != 5) {//两个账户里面必须得有一个是公众号
            return false;
        }
        $this->tomergewxchatdo($ckmobile['openid'], $member);
    }

    //合并微信公众号和小程序及其他openid
    //$openid-寻找到的就用户，$member-当前登录的用户
    private function tomergewxchatdo($openid, $member) {
        $uorigin = userorigin($openid);
        $myorigin = userorigin($member['openid']);
        $umember = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_member") . " where `openid`='{$openid}' ");

        $user_credit1 = m("member")->getCredit($member["openid"], "credit1");
        $user_credit2 = m("member")->getCredit($member["openid"], "credit2");
        $user_credit4 = m("member")->getCredit($member["openid"], "credit4");
        $user_credit5 = m("member")->getCredit($member["openid"], "credit5");

        $olduser_credit1 = m("member")->getCredit($umember["openid"], "credit1");
        $olduser_credit2 = m("member")->getCredit($umember["openid"], "credit2");
        $olduser_credit4 = m("member")->getCredit($umember["openid"], "credit4");
        $olduser_credit5 = m("member")->getCredit($umember["openid"], "credit5");

        //得到账户余额合并后额数据
        $udata['credit1'] = floatval($user_credit1) + floatval($olduser_credit1);
        $udata['credit2'] = floatval($user_credit2) + floatval($olduser_credit2);
        $udata['credit4'] = floatval($user_credit4) + floatval($olduser_credit4);
        $udata['credit5'] = floatval($user_credit5) + floatval($olduser_credit5);

        //把新绑定的用户迁移到旧账户并把新账户的openid记录到旧用户的openidlist中
        $list = "";
        if ($uorigin == 5 && $myorigin != $uorigin) {//$umember对应的是公众号并且是旧账户
            if (empty($umember['openidlist'])) {
                $list = ",{$openid},";
            } else {
                $list = $umember['openidlist'] . "{$openid},";
            }
            $udata['openidlist'] = $list;
            pdo_update("ewei_shop_member", $udata, array("id" => $umember['id']));
            $insert = array("openid" => $umember['openid'], "orgopenid" => $member['openid']);
            pdo_insert("shop_member_orgin", $insert);
            pdo_delete("ewei_shop_member", array("id" => $member['id']));
        }

        //$uorigin[$umember]:小程序用户，$myorigin[$member]：当前公众号用户
        if ($myorigin == 5 && $myorigin != $uorigin) {//$member对应的是公众号并且是新绑定手机的用户
            if (empty($member['openidlist'])) {
                $list = ",{$openid},";
            } else {
                $list = $member['openidlist'] . "{$openid},";
            }
            $udata['openidlist'] = $list;
            $udata['openid'] = $member['openid'];
            pdo_update("ewei_shop_member", $udata, array("id" => $umember['id']));
            $insert = array("openid" => $member['openid'], "orgopenid" => $umember['openid']);
            pdo_insert("shop_member_orgin", $insert);

            //迁移时间银行账户信息
            pdo_update("ewei_shop_timebank_order", array("member_openid" => $member['openid']), array("member_openid" => $openid));
            pdo_update("ewei_shop_timebank_server", array("server" => $member['id']), array("server" => $umember['id']));
            pdo_delete("ewei_shop_member", array("id" => $member['id']));
        }
    }

    /**
     * 忘记密码
     */
    public function forget() {
        global $_W;
        global $_GPC;
        $lang = trim($_GPC['lang']);
        if ($_W['ispost']) {
            $account = trim($_GPC['username']);
            $verifycode = trim($_GPC['verifycode']);
            $pwd = trim($_GPC['password']);

            if (empty($account)) {
                return app_error(-1, '请输入正确的手机号');
            }

            if (empty($verifycode)) {
                return app_error(-1, '请输入验证码');
            }

            if (empty($pwd)) {
                return app_error(-1, '密码不能为空');
            }
            $member = pdo_fetch('select id,openid,mobile,pwd,salt from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(':mobile' => $account, ':uniacid' => $_W['uniacid']));
            $key = '__ewei_shopv2_member_verifycodesession_' . $_W['uniacid'] . '_' . $member['mobile'];
            $key_time = '__ewei_shopv2_member_verifycodesendtime_' . $_W['uniacid'];
            $sendcode = m('cache')->get($key);
            $sendtime = m('cache')->get($key_time);
            if (!isset($sendcode) || $sendcode !== $verifycode || !isset($sendtime) || $sendtime + 600 < time()) {
                return app_error(AppError::$VerifyCodeError, '验证码错误或已过期');
            }

            if (empty($member)) {
                return app_error(-1, '此会员未注册');
            }

            $salt = m('account')->getSalt();
            $data = array('salt' => $salt, 'pwd' => md5($pwd . $salt));
            pdo_update('ewei_shop_member', $data, array('id' => $member['id']));



            unset($_SESSION[$key]);
            session_start();
            $_SESSION['registermobile'] = $member['mobile'];
            $data['error'] = 0;
            $data['message'] = '重置密码成功';
            return app_json($data);
        }
    }

    /**
     * 时间银行用户登录，并绑定微信：2021-6-7
     */
    public function login() {
        global $_W;
        global $_GPC;
        $data = array();
        $account = trim($_GPC['username']);
        $openId = trim($_GPC['openId']);
        $verifycode = trim($_GPC['verifycode']);
        if (empty($account)) {
            return app_error(-1, '帐号不能为空');
        }
        if (empty($verifycode)) {
            return app_error(-1, '验证码不能为空');
        }

        $key = '__ewei_shopv2_member_verifycodesession_' . $_W['uniacid'] . '_' . $account;
        $key_time = '__ewei_shopv2_member_verifycodesendtime_' . $_W['uniacid'];
        $sendcode = m('cache')->get($key);
        $sendtime = m('cache')->get($key_time);
        if (!isset($sendcode) || $sendcode !== $verifycode || !isset($sendtime) || $sendtime + 600 < time()) {
            return app_error(-1, '验证码错误或已过期');
        }

        $member = pdo_fetch('select id,openid,mobile,pwd,salt,uniacid from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid order by openid_wa desc limit 1', array(':mobile' => $account, ':uniacid' => $_W['uniacid']));
        if ($_GPC['isDirect'] && $member) {
            $data['id'] = $member['id'];
            $data['mobile'] = $member['mobile'];
            $data['openid'] = $openId;
            $data['uniacid'] = $member['uniacid'];
        } else {
            $encryptedData = trim($_GPC['data']);
            $iv = trim($_GPC['iv']);
            $sessionKey = trim($_GPC['sessionKey']);
            if (empty($encryptedData) || empty($iv)) {
                return app_error(AppError::$ParamsError);
            }

            +
            $pc = new WXBizDataCrypt($this->appid, $sessionKey);
            $errCode = $pc->decryptData($encryptedData, $iv, $data);

            if ($errCode == 0) {
                $data = json_decode($data, true);
                $data['openid'] = $data['openId'] = $openId;
                $this->refine($data['openId']);

                if (empty($member)) {
                    $member = array('mobile' => $account, 'uniacid' => $_W['uniacid'], 'uid' => 0, 'openid' => 'sns_wa_' . $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1', 'openid_wa' => $data['openId'], 'comefrom' => 'sns_wa', 'createtime' => time(), 'status' => 0);
                    pdo_insert('ewei_shop_member', $member);
                    $id = pdo_insertid();
                    $data['id'] = $id;
                    $data['mobile'] = $member['mobile'];
                    $data['uniacid'] = $_W['uniacid'];
                }
                else {
                    $updateData = array('openid_wa' => $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1','openid_wa' => $data['openId']);
                    $result = pdo_update('ewei_shop_member', $updateData, array('id' => $member['id'], 'uniacid' => $member['uniacid']));
                    $data['id'] = $member['id'];
                    $data['mobile'] = $member['mobile'];
                    $data['uniacid'] = $member['uniacid'];
                }
            }
        }

        $data['error'] = 0;
        $data['message'] = '登录成功';
        $GLOBALS['_W']['openid'] = $_W['openid'] = $member['openid'];
        return app_json($data);
    }

    /**
     * 用户登录
     */
    public function login1() {
        global $_W;
        global $_GPC;
        $data = array();
        $account = trim($_GPC['username']);
        $password = trim($_GPC['password']);
        $lang = trim($_GPC['lang']);

        if (empty($password)) {
            return app_error(-1, '密码不能为空');
        }
        if (empty($account)) {
            return app_error(-1, '帐号不能为空');
        }

        $member = pdo_fetch('select id,openid,mobile,pwd,salt from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(':mobile' => $account, ':uniacid' => $_W['uniacid']));
        if (empty($member)) {
            return app_error(-1, '您还未注册，请先去注册！');
        } else {
            if (md5($password . $member['salt']) == $member['pwd']) {
                $member['error'] = 0;
                $member['message'] = '登录成功';
                $GLOBALS['_W']['openid'] = $_W['openid'] = $member['openid'];
                return app_json($member);
            } else {
                return app_error(-1, '密码错误');
            }
        }
    }

    public function getmember() {
        global $_W;
        global $_GPC;
        $member = $this->member;

        return app_json($member);
    }

    public function main() {
        global $_W;
        global $_GPC;
        $member = $this->member;
        $categorys = pdo_fetchall('select  *  from ' . tablename('ewei_shop_timebank_category') . '   where showss = 1 order by orderby desc limit 0,8', array(':uniacid' => $_W['uniacid']));
        $categorys = set_medias($categorys, 'logo');
        $wlkdata['categorys'] = $categorys;
        $knows = pdo_fetchall('select  *  from ' . tablename('ewei_shop_timebank_know') . '   where uniacid = :uniacid ', array(':uniacid' => $_W['uniacid']));
        $knows = set_medias($knows, 'thumb');
        foreach ($knows as &$know) {
            $know['createtime'] = date('Y-m-d H:i:s', $know['createtime']);
        }
        unset($know);
        $wlkdata['knows'] = $knows;
        $rules = pdo_fetchall('select  *  from ' . tablename('ewei_shop_timebank_rule') . '   where uniacid = :uniacid ', array(':uniacid' => $_W['uniacid']));
        $rules = set_medias($rules, 'thumb');
        foreach ($rules as &$rule) {
            $rule['createtime'] = date('Y-m-d H:i:s', $rule['createtime']);
        }
        unset($rule);
        $wlkdata['rules'] = $rules;
        $advs = pdo_fetchall('select id,advname,link,thumb from ' . tablename('ewei_shop_timebank_adv') . ' where enabled=1 order by displayorder desc');
        $advs = set_medias($advs, 'thumb');
        $wlkdata['advs'] = $advs;

        $timegetlist = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_time"));
        $temp_timegetlist = [];
        foreach ($timegetlist as $item) {
            $temp_timegetlist[$item['id']] = $item['name'];
        }

        $manylog = pdo_fetchall("select serverid,count(*) as num from " . tablename("ewei_shop_timebank_many_log") . " group by serverid order by num desc  ");
        $manyall = [];
        foreach ($manylog as $item) {
            $many = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_many') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['serverid'], ':uniacid' => $_W[uniacid]));
            $category = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_category') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $many['category'], ':uniacid' => $_W[uniacid]));
            $many['signnum'] = $item['num'];
            $many['categoryname'] = $category['name'];
            if (!empty($temp_timegetlist[$many['timeget']])) {
                $many['timeget_str'] = $temp_timegetlist[$many['timeget']];
            } else {
                $many['timeget_str'] = "";
            }
            $many['starttime'] = $many['server_time_start'] . '  开始';
            $now = time();
            $many['ing'] = false; //进行中
            if (strtotime($many['server_time_start']) < $now and strtotime($many['server_time_end']) > $now) {
                $many['ing'] = true;
            }
            $many['over'] = false; //已结束
            if (strtotime($many['server_time_end']) < $now) {
                $many['over'] = true;
                $many['starttime'] = $many['server_time_end'] . '  结束';
            }
            if ($many['over']) {
                $many['status'] = 3;
            } elseif ($many['ing']) {
                $many['status'] = 2;
            } else {
                $many['status'] = 1;
            }
            if ($many['status'] != 3) {
                $manyall[] = $many;
            }
        }
        foreach ($manyall as $key => $row) {
            $createtime[$key] = $row['createtime'];
            $signnum[$key] = $row['signnum'];
        }
        array_multisort($createtime, SORT_DESC, $signnum, SORT_DESC, $manyall);
        if (count($manyall) > 6) {
            $manyall = array_slice($manyall, 0, 5);
        }
        $signup = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_timebank_server') . ' where status=1 and  uniacid=:uniacid', array(':uniacid' => $_W[uniacid]));
        $wlkdata['signup'] = $signup;
        $manyall = set_medias($manyall, 'logo');
        $wlkdata['many'] = $manyall;
        return app_json(array('wlkdata' => $wlkdata));
    }

    public function getwechat() {
        global $_W;
        global $_GPC;
        $member = $this->member;
        $pindex = max(1, intval($_GPC['home']));
        $psize = 5;

        $condition = '  and uniacid=:uniacid ';
        $params = array(':uniacid' => $_W['uniacid']);

        if ($_GPC['type'] == 0) {
            if (empty($_GPC['city'])) {
                $_GPC['city'] = '无城市信息';
            }
            $condition .= '  and city like :city ';
            $params[':city'] = '%' . $_GPC['city'] . '%';
        }

        $list = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_wechat') . (' where 1 ' . $condition . ' and status>=0 order by createtime desc LIMIT ') . ($pindex - 1) * $psize . ',' . $psize, $params);
        $total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_timebank_wechat') . (' where 1 ' . $condition), $params);
        foreach ($list as &$item) {
//                  $item['member'] = m('member')->getMember($item['openid']);
            $item['member'] = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $item['openid'], ':uniacid' => $_W[uniacid]));
            $item['member']['avatar'] = tomedia($item['member']['avatar']);
            /*        $item['member2'] = set_medias($item['member'], 'avatar');
              //                  $item['member2']['avatar'] = $item['member']['avatar'];

             */
            $item['images'] = unserialize($item['images']);
            if ($item['images'][0] == null) {
                $item['images'] = array();
            } else {
                $item['images'] = set_medias($item['images']);
            }
            $item['createtime'] = date('Y-m-d', $item['createtime']);
            $like = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_wechat_like') . ' where pid=:pid and openid=:openid and uniacid=:uniacid ', array(':pid' => $item['id'], 'openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            if (empty($like)) {
                $item['islike'] = 0;
            } else {
                $item['islike'] = 1;
            }
            $item['like'] = pdo_fetchall('select l.*,m.nickname as username from ' . tablename('ewei_shop_timebank_wechat_like') . ' l ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid = l.openid  where l.pid=:pid and l.uniacid=:uniacid ', array(':pid' => $item['id'], ':uniacid' => $_W[uniacid]));
            $comment = pdo_fetchall('select co.*,m.nickname as username from ' . tablename('ewei_shop_timebank_wechat_comment') . ' co ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid = co.openid  where co.pid=:pid and co.uniacid=:uniacid and co.status <>-1 order by co.createtime asc ', array(':pid' => $item['id'], ':uniacid' => $_W[uniacid]));
            if (empty($comment)) {
                $comment = array();
            }
            $item['comments']['comment'] = $comment;
            $item['comments']['total'] = count($comment);
            $item['showmore'] = 0;
        }
        unset($item);
        return app_json(array('posts' => $list, 'total' => $total, 'pagesize' => $psize, 'home' => $pindex));
    }

    public function comment() {
        global $_W;
        global $_GPC;
        $member = $this->member;




        $data['openid'] = $member['openid'];
        $data['createtime'] = time();
        $data['content'] = $_GPC['content'];
        $data['uniacid'] = $_W['uniacid'];
        $data['pid'] = $_GPC['pid'];

        pdo_insert('ewei_shop_timebank_wechat_comment', $data);

        return app_json(array());
    }

    public function like() {
        global $_W;
        global $_GPC;
        $member = $this->member;
        if ($_GPC['islike'] == 1) {
            $data['openid'] = $member['openid'];
            $data['createtime'] = time();
            $data['uniacid'] = $_W['uniacid'];
            $data['pid'] = $_GPC['pid'];

            pdo_insert('ewei_shop_timebank_wechat_like', $data);
        } else {
            pdo_delete('ewei_shop_timebank_wechat_like', array('openid' => $member['openid'], 'pid' => $_GPC['pid'], 'uniacid' => $_W['uniacid']));
        }


        return app_json(array());
    }

    public function publish() {
        global $_W;
        global $_GPC;
        $member = $this->member;

        /*  $resimg =json_decode(html_entity_decode($_GPC['resimg']));
          foreach ($resimg as &$item) {
          $item = get_object_vars($item);
          }
          unset($item); */

        $resimg = json_decode(html_entity_decode($_GPC['resimg']));

//
//            foreach ($resimg as $item) {
//                  $images[] = $item['filename'];
//            }
        $data['images'] = serialize($resimg);
        $data['openid'] = $member['openid'];
        $data['createtime'] = time();
        $data['content'] = $_GPC['content'];
        $data['city'] = $_GPC['city'];
        $data['address'] = $_GPC['address'];
        $data['uniacid'] = $_W['uniacid'];
        if (empty($data['content'])) {
            return app_error(-1, '请输入内容');
        }
        pdo_insert('ewei_shop_timebank_wechat', $data);

        return app_json(array());
    }

    //申请个人服务者
    public function apply() {
        global $_W;
        global $_GPC;
        $member = $this->member;

//            $resimg =json_decode(html_entity_decode($_GPC['resimg']));
//            foreach ($resimg as &$item) {
//                  $item = get_object_vars($item);
//            }
//            unset($item);

        $data['uniacid'] = $_W['uniacid'];
//            $data['cardfront'] = $resimg[0]['filename'];
//            $data['cardbehind'] = $resimg[1]['filename'];
        $data['cardfront'] = $_GPC['front'];
        $data['cardbehind'] = $_GPC['back'];
        $data['openid'] = $member['openid'];
        $data['registertime'] = time();
        $data['idcard'] = $_GPC['idcard'];
        $data['province'] = $_GPC['province'];
        $data['city'] = $_GPC['city'];
        $data['area'] = $_GPC['area'];
        $data['street'] = $_GPC['address'];
        $data['status'] = 0;

        if ($_GPC['type'] == 0) {
            $data['server'] = $member['id'];
            $data['name'] = $_GPC['name'];
            $data['tel'] = $_GPC['mobile'];
            $data['mechanism'] = $_GPC['groupid'];

            $old = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where tel=:tel and uniacid=:uniacid   limit 1', array(':tel' => $_GPC['mobile'], ':uniacid' => $_W[uniacid]));

            if (!empty($old) && $old['openid'] != $member['openid']) {
                return app_error(-1, '该号码已经注册');
            }

            if ($_GPC['serveid'] > 0) {
                pdo_update('ewei_shop_timebank_server', $data, array('id' => $_GPC['serveid']));
            } else {
                pdo_insert('ewei_shop_timebank_server', $data);
            }
        } else {
            $data['type'] = $_GPC['grouptype'];
            $data['name'] = $_GPC['groupname'];
            $data['servecode'] = $_GPC['groupsn'];
            $data['chargename'] = $_GPC['name'];
            $data['chargemobile'] = $_GPC['mobile'];
            $data['serveimg'] = $_GPC['serveimg'];
            if ($_GPC['serveid'] > 0) {
                pdo_update('ewei_shop_timebank_group', $data, array('id' => $_GPC['serveid']));
            } else {
                pdo_insert('ewei_shop_timebank_group', $data);
            }
        }
        return app_json(array());
    }

    public function applyinfo() {
        global $_W;
        global $_GPC;
        $member = $this->member;
        $groups = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_group') . ' where status = 1 and uniacid=:uniacid ', array(':uniacid' => $_W[uniacid]));
        foreach ($groups as &$group) {
            $group['value'] = $group['id'];
            $group['label'] = $group['name'];
        }
        unset($group);
        if ($_GPC['type'] == 1) {
            $apply = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_group') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            if (empty($apply['registertime'])) {
                $apply['apstatus'] = 0;
            } else {
                $apply['apstatus'] = 1;
            }
            if ($apply['status'] == 1) {
                $apply['apstatus'] = 2;
            }
        } else {
//                  $apply = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            $apply = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where server=:id and uniacid=:uniacid   limit 1', array(':id' => $member['id'], ':uniacid' => $_W[uniacid]));
            if ($apply['mechanism'] > 0) {
                $apply['group'] = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_group') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $apply['mechanism'], ':uniacid' => $_W[uniacid]));
            }
            if (empty($apply['registertime'])) {
                $apply['apstatus'] = 0;
            } else {
                $apply['apstatus'] = 1;
            }
            if ($apply['status'] == 1) {
                $apply['apstatus'] = 2;
            }
        }
//            $apply['serveimg'] = tomedia($apply['serveimg']);
//            $apply['cardfront'] = tomedia($apply['cardfront']);
//            $apply['cardbehind'] = tomedia($apply['cardbehind']);

        $agreementdata = m("common")->getSysset("agreement");

        $agreement = pdo_fetch('select * from ' . tablename('ewei_shop_agreement') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $agreementdata['timebankid'], ':uniacid' => $_W[uniacid]));
        $file = $_W['attachurl_local'] . 'book.docx';

        return app_json(array('groups' => $groups, 'apply' => $apply, 'agreement' => $agreement, 'file' => $file));
    }

    //获取服务组织
    public function getserve() {
        global $_W;
        global $_GPC;
        $member = $this->member;
        $pindex = max(1, intval($_GPC['home']));
        $psize = 5;
        if ($_GPC['organ'] == 1) {
            $condition = ' and uniacid = :uniacid';
            $paras = array(":uniacid" => $_W['uniacid']);
//                $select_category = empty($_GPC['category']) ? '' : ' and category=' . intval($_GPC['category']) . ' ';
            if ($_GPC['sort'] == 1) {
                $select_order = 'timeaccount';
            } elseif ($_GPC['sort'] == 2) {
                $select_order = 'registertime';
            } elseif ($_GPC['sort'] == 3) {
                $select_order = 'id';
            } else {
                $select_order = 'frequency';
            }
//                $condition = 1;
//                if ($_GPC['date'] == 1) {  //当天
//                    $starttime = strtotime('-1 day');
//                    $endtime = time();
//                    $params[':starttime'] = $starttime;
//                    $params[':endtime'] = $endtime;
//                    $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
//                } elseif ($_GPC['date'] == 2) {     //一周内
//                    $starttime = strtotime('-7 day');
//                    $endtime = time();
//                    $params[':starttime'] = $starttime;
//                    $params[':endtime'] = $endtime;
//                    $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
//                } elseif ($_GPC['date'] == 3) {
//                    $starttime = strtotime('-31 day');
//                    $endtime = time();
//                    $params[':starttime'] = $starttime;
//                    $params[':endtime'] = $endtime;
//                    $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
//                }
            if (!empty($_GPC['keyword'])) {
                $_GPC["keyword"] = trim($_GPC["keyword"]);
                $condition .= ' and name like :keyword';
                $paras[':keyword'] = '%' . $_GPC['keyword'] . '%';
            }
            $groups = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_group') . ' where status = 1 ' . $condition . ' order by ' . $select_order . ' desc limit ' . ($pindex - 1) * $psize . ',' . $psize, $paras);
            $total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_timebank_group') . ' where status = 1' . $condition . ' order by ' . $select_order . ' desc', $paras);
        } else {
            $groups = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_group') . ' where status = 1 and uniacid=:uniacid', array(':uniacid' => $_W[uniacid]));
            $total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_timebank_group') . ' where status = 1 and uniacid=:uniacid', array(':uniacid' => $_W[uniacid]));
        }

        foreach ($groups as &$group) {
            $group['value'] = $group['id'];
            $group['label'] = $group['name'];
            //团队人数
            $signup = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_timebank_server') . ' where mechanism=:mechanism and uniacid=:uniacid ', array(':mechanism' => $group['id'], ':uniacid' => $_W[uniacid]));
            $timeaccount = pdo_fetch('select sum(timeaccount) as timeaccount from ' . tablename('ewei_shop_timebank_server') . ' where mechanism=:mechanism and uniacid=:uniacid ', array(':mechanism' => $group['id'], ':uniacid' => $_W[uniacid]));
            if (!$timeaccount['timeaccount'] > 0) {
                $timeaccount['timeaccount'] = 0;
            }
            $group['signup'] = $signup;
            $group['timeaccount'] = $timeaccount['timeaccount'];
        }
        unset($group);
        if ($_GPC['type'] == 1) {
            $apply = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_group') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
        } else {
//                  $apply = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            $apply = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where server=:id and uniacid=:uniacid   limit 1', array(':id' => $member['id'], ':uniacid' => $_W[uniacid]));
            if ($apply['mechanism'] > 0) {
                $apply['group'] = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_group') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $apply['mechanism'], ':uniacid' => $_W[uniacid]));
            }
        }
//            $apply['serveimg'] = tomedia($apply['serveimg']);
//            $apply['cardfront'] = tomedia($apply['cardfront']);
//            $apply['cardbehind'] = tomedia($apply['cardbehind']);
        $groups = set_medias($groups, 'logo');
        return app_json(array('groups' => $groups, 'apply' => $apply, 'pagesize' => $psize, 'total' => $total));
    }

    //获取组织详情
    public function getservedetail() {
        global $_W;
        global $_GPC;
        $signup = 0;
        $timeaccount = 0;
        $list = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_group') . ' where id=:id and uniacid=:uniacid', array(':id' => $_GPC['id'], ':uniacid' => $_W[uniacid]));
//              $signup= pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_timebank_server') . ' where mechanism=:mechanism and uniacid=:uniacid ', array(':mechanism'=>$list['id'],':uniacid' => $_W[uniacid]));
//              $timeaccount=   pdo_fetch('select sum(timeaccount) as timeaccount from ' . tablename('ewei_shop_timebank_server') . ' where mechanism=:mechanism and uniacid=:uniacid ', array(':mechanism'=>$list['id'],':uniacid' => $_W[uniacid]));
        $user = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where mechanism=:mechanism  and openid=:openid and uniacid=:uniacid ', array(':openid' => $_GPC['openid'], ':mechanism' => $list['id'], ':uniacid' => $_W[uniacid]));
        if (!empty($user)) {//是否已加入
            $list['usertype'] = 1;
            $list['usergrouppass'] = $user['grouppass'];
        } else {
            $list['usertype'] = 0;
        }
        $signupall = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_server') . ' where mechanism=:mechanism  and uniacid=:uniacid ', array(':mechanism' => $list['id'], ':uniacid' => $_W[uniacid]));
        foreach ($signupall as &$item) {
            $signup++;
            $timeaccount += $item['timeaccount'];
            $member = m('member')->getMember($item['openid']);
            $item['avatar'] = $member['avatar'];
            $item['createtime'] = $member['createtime'];
            $item['year'] = date("Y-m-d", $member['createtime']);
            $item['time'] = date("H:i:s", $member['createtime']);
        }
        unset($item);
        $signupall = set_medias($signupall, 'avatar');
        $list['user'] = $signupall;
        $list['signup'] = $signup;
        $list['timeaccount'] = $timeaccount;
        $list = set_medias($list, 'logo');
        return app_json(array('list' => $list));
    }

    /**
     * 上传图片
     */
    public function upload() {
        global $_W;
        global $_GPC;
        load()->func('file');
        $field = isset($_GPC['file']) ? $_GPC['file'] : 'file';

        if (!empty($_FILES)) {
            //
            if (is_array($_FILES)) {
                $files = array();

                foreach ($_FILES as $key => $item) {
//                    $file = array('name' => $name, 'type' => $_FILES[$field]['type'][$key], 'tmp_name' => $_FILES[$field]['tmp_name'][$key], 'error' => $_FILES[$field]['error'][$key], 'size' => $_FILES[$field]['size'][$key]);
                    $file = $item;
                    $ret = $this->uploadFile($file);

                    if ($ret['status'] == 'error') {
                        $ret = array('status' => 0);
                    } else {
                        $ret = array('status' => 1, 'filename' => $ret['filename'], 'url' => trim($_W['attachurl'] . $ret['filename']));
                    }

                    $files[] = $ret;
                }
                //更新用户头像
                if ($_GPC['type'] == 1) {
                    pdo_update('ewei_shop_member', array('avatar' => $ret['filename']), array('openid' => $_GPC['openid']));
                }
                return app_json(array('list' => $files));
            }

            $result = $this->uploadFile($_FILES[$field]);

            if ($result['status'] == 'error') {
                //return show_json(AppError::$UploadFail, $result['message']);
                return show_json(-1, '上传失败！');
            }

            $files = array(
                array('status' => 1, 'url' => trim($_W['attachurl'] . $result['filename']), 'filename' => $result['filename'])
            );
            //更新用户头像
            if ($_GPC['type'] == 1) {
                pdo_update('ewei_shop_member', array('avatar' => $result['filename']), array('account' => $_GPC['account']));
            }

            return app_json(array('files' => $files));
        }
        return show_json(0, '未选择图片！');
    }

    protected function uploadFile($uploadfile) {
        global $_W;
        global $_GPC;
        $result['status'] = 'error';

        if ($uploadfile['error'] != 0) {
            $result['message'] = '上传失败';
            return $result;
        }

        load()->func('file');
        $path = '/images/ewei_shop/' . $_W['uniacid'];

        if (!is_dir(ATTACHMENT_ROOT . $path)) {
            mkdirs(ATTACHMENT_ROOT . $path);
        }

        $_W['uploadsetting'] = array();
        $_W['uploadsetting']['image']['folder'] = $path;
        $_W['uploadsetting']['image']['extentions'] = $_W['config']['upload']['image']['extentions'];
        $_W['uploadsetting']['image']['limit'] = $_W['config']['upload']['image']['limit'];
        $file = file_upload($uploadfile, 'image');

        if (is_error($file)) {
            $ext = pathinfo($uploadfile['name'], PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            $result['message'] = $file['message'] . ' 扩展名: ' . $ext . ' 文件名: ' . $uploadfile['name'];
            return $result;
        }

        if (function_exists('file_remote_upload')) {
            $remote = file_remote_upload($file['path']);

            if (is_error($remote)) {
                $result['message'] = $remote['message'];
                return $result;
            }
        }

        $result['status'] = 'success';
        $result['url'] = $file['url'];
        $result['error'] = 0;
        $result['filename'] = $file['path'];
        $result['url'] = trim($_W['attachurl'] . $result['filename']);
        pdo_insert('core_attachment', array('uniacid' => $_W['uniacid'], 'uid' => $_W['member']['uid'], 'filename' => $uploadfile['name'], 'attachment' => $result['filename'], 'type' => 1, 'createtime' => TIMESTAMP));
        return $result;
    }

    public function remove() {
        global $_W;
        global $_GPC;
        load()->func('file');
        $file = $_GPC['file'];
        file_delete($file);
        return app_json();
    }

    public function cerimg() {
        global $_W;
        global $_GPC;
        $member = $this->member;
        $log = pdo_fetch('select l.* from ' . tablename('ewei_shop_certificate_log') . ' l ' . ' left join ' . tablename('ewei_shop_certificate') . ' cer on cer.id = l.cerid  where l.openid=:openid and cer.type=3  limit 1', array(':openid' => $member['openid']));
        return app_json(array('log' => $log));
    }

    public function getlist() {
        global $_W;
        global $_GPC;
        //$params = array(':uniacid' => $_W['uniacid']);
        $pindex = max(1, intval($_GPC['home']));
        $psize = 5;
        if ($_GPC['current'] == 0) {
            $select_category = $_GPC['category'] > 0 ? 'and category=' . intval($_GPC['category']) . ' ' : ' ';

            $select_order = 'a.id';
            if ($_GPC['type'] == 1) {
                $select_order = 'a.createtime';
            } elseif ($_GPC['type'] == 2) {
                $select_order = 'a.createtime';
            } elseif ($_GPC['type'] == 3) {
                $select_order = 'a.id';
            } elseif ($_GPC['type'] == 4) {
                $select_order = 'limitnum';
            }
            $condition = 1;
            if ($_GPC['date'] == 1) {  //当天
                $starttime = strtotime('-1 day');
                $endtime = time();
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
            } elseif ($_GPC['date'] == 2) {     //一周内
                $starttime = strtotime('-7 day');
                $endtime = time();
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
            } elseif ($_GPC['date'] == 3) {
                $starttime = strtotime('-31 day');
                $endtime = time();
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
            }

            if (!empty($_GPC['keyword'])) {
                $_GPC['keyword'] = trim($_GPC['keyword']);
                $condition .= ' and a.name  like :keyword';
                $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
            }

            if (empty($_GPC['ispost'])) {
                $_GPC['ispost'] = 1;
            }
            $select_ispost = ' and a.ispost=' . intval($_GPC['ispost']) . " ";
            $timegetlist = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_time"));
            $temp_timegetlist = [];
            foreach ($timegetlist as $item) {
                $temp_timegetlist[$item['id']] = $item['name'];
            }
            if ($_GPC['type'] == 3) {
                $sql = 'SELECT a.*,c.name as categoryname  FROM ' . tablename('ewei_shop_timebank_many') . ' a left join ' . tablename('ewei_shop_timebank_category') . ' c on c.id=a.category  WHERE ' . $condition . $select_ispost . $select_category . ' order by ' . $select_order . ' desc  ';
            } else {
                $sql = 'SELECT a.*,c.name as categoryname  FROM ' . tablename('ewei_shop_timebank_many') . ' a left join ' . tablename('ewei_shop_timebank_category') . ' c on c.id=a.category  WHERE ' . $condition . $select_ispost . $select_category . ' order by ' . $select_order . ' desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            }
            $totalsql = 'SELECT count(*) FROM ' . tablename('ewei_shop_timebank_many') . ' a left join ' . tablename('ewei_shop_timebank_category') . ' c on c.id=a.category  WHERE ' . $condition . $select_ispost . $select_category . ' order by ' . $select_order;



            $list = pdo_fetchall($sql, $params);
            $total = pdo_fetchcolumn($totalsql, $params);
            $lists = [];
            if (!empty($list)) {

                foreach ($list as $key => &$value) {
                    $value['starttime'] = $value['server_time_start'] . '  开始';
                    $now = time();
                    $value['ing'] = false; //进行中
                    if (strtotime($value['server_time_start']) < $now and strtotime($value['server_time_end']) > $now) {
                        $value['ing'] = true;
                    }
                    $value['over'] = false; //已结束
                    if (strtotime($value['server_time_end']) < $now) {
                        $value['over'] = true;
                        $value['starttime'] = $value['server_time_end'] . '  结束';
                    }
                    $value['info']['signnum'] = pdo_fetchcolumn("select count(1) from " . tablename("ewei_shop_timebank_many_log") . " where   serverid=" . $value['id'] . " and status !=-1");
                    if (!empty($temp_timegetlist[$value['timeget']])) {
                        $value['timeget_str'] = $temp_timegetlist[$value['timeget']];
                    } else {
                        $value['timeget_str'] = "";
                    }
                    if ($value['over']) {
                        $value[status] = 3;
                    } elseif ($value['ing']) {
                        $value[status] = 2;
                    } else {
                        $value[status] = 1;
                    }
                    if ($value['type'] == 1) {
                        $value['address'] = $value['province'] . $value['city'] . $value['area'];
                        $value['addressstatus'] = 1;
                    } else {
                        $value['address'] = "线上服务";
                        $value['addressstatus'] = 0;
                    }
                    $value = set_medias($value, 'logo');

                    $value['distance'] = m('util')->GetDistance($_GPC['lat'], $_GPC['lng'], $value['lat'], $value['lng'], 2);

                    $lists[$key]['distance'] = $value['distance'];
                    $lists[$key]['id'] = $value['id'];
                    $lists[$key]['name'] = $value['name'];
                    $lists[$key]['logo'] = $value['logo'];
                    $lists[$key]['categoryname'] = $value['categoryname'];
                    $lists[$key]['address'] = $value['address'];
                    $lists[$key]['server_time_start'] = $value['server_time_start'];
                    $lists[$key]['status'] = $value['status'];
                    $lists[$key]['timeget_str'] = $value['timeget_str'];
                    $lists[$key]['addressstatus'] = $value['addressstatus'];
                    $lists[$key]['limitnum'] = $value['limitnum'];
                }
                unset($value);
            }

            if ($_GPC['type'] == 3) {
                usort($lists, 'timebankdistancesort');

                $lists = array_slice($lists, ($pindex - 1) * $psize, $psize);
            }

            $cate = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_category') . ' where uniacid=:uniacid and showss=1 ', array(':uniacid' => $_W[uniacid]));
            return app_json(array('list' => $lists, 'total' => $total, 'pagesize' => $psize, 'cate' => $cate));
        } else {
            $condition = '1';
            $orderby = ' order by a.id desc';
            if ($_GPC['type'] == 2) {
                $orderby = ' order by a.createtime desc';
            }
            $params = array();
            $condition .= " and a.serveopenid is null ";
            if ($_GPC['category'] > 0) {
                $condition .= ' and i.category=:category';
                $params[':category'] = $_GPC['category'];
            }
            if ($_GPC['date'] == 1) {  //当天
                $starttime = strtotime('-1 day');
                $endtime = time();
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
            } elseif ($_GPC['date'] == 2) {     //一周内
                $starttime = strtotime('-7 day');
                $endtime = time();
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
            } elseif ($_GPC['date'] == 3) {
                $starttime = strtotime('-31 day');
                $endtime = time();
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
            }

            if (!empty($_GPC['keyword'])) {
                $_GPC['keyword'] = trim($_GPC['keyword']);
                $condition .= ' and i.name  like :keyword';
                $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
            }

//                  $list = pdo_fetchall('select a.* from ' . tablename('ewei_shop_timebank_servepost') . ' a left join ' . tablename('ewei_shop_timebank_item') . ' i on i.id=a.itemid  where 1 '.$condition.' order by a.id desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
            if ($_GPC['type'] == 3) {
                $sql = 'SELECT a.*  FROM ' . tablename('ewei_shop_timebank_servepost') . ' a left join ' . tablename('ewei_shop_timebank_item') . ' i on i.id=a.itemid  WHERE ' . $condition . $orderby;
                $totalsql = 'SELECT count(*) FROM ' . tablename('ewei_shop_timebank_servepost') . ' a left join ' . tablename('ewei_shop_timebank_item') . ' i on i.id=a.itemid  WHERE ' . $condition;
            } else {
                $sql = 'SELECT a.*  FROM ' . tablename('ewei_shop_timebank_servepost') . ' a left join ' . tablename('ewei_shop_timebank_item') . ' i on i.id=a.itemid  WHERE ' . $condition . $orderby . '  LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
                $totalsql = 'SELECT count(*) FROM ' . tablename('ewei_shop_timebank_servepost') . ' a left join ' . tablename('ewei_shop_timebank_item') . ' i on i.id=a.itemid  WHERE ' . $condition;
            }

            $list = pdo_fetchall($sql, $params);
            $total = pdo_fetchcolumn($totalsql, $params);
            //添加距离排序判断；


            foreach ($list as &$item) {
                $item['addressinfo'] = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['addressid'], ':uniacid' => $_W[uniacid]));
                $item['location'] = $item['addressinfo']['province'] . $item['addressinfo']['city'] . $item['addressinfo']['area'] . $item['addressinfo']['address'];
                $item['item'] = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_item') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['itemid'], ':uniacid' => $_W[uniacid]));
                if ($item['item']['category'] > 0) {
                    $item['item']['categoryinfo'] = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_item_category') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['item']['category'], ':uniacid' => $_W[uniacid]));
                }
                $item['item']['thumb'] = tomedia($item['item']['thumb']);
                $item['finaltimemsg'] = date('m月d日 H:i开始', $item['finaltime']);
                $item['countdown'] = $item['finaltime'] - time();
                $item['createmsg'] = $this->ReckonTime($item['createtime']);
                if ($item['addressinfo']['lat'] > 0 && $_GPC['lat']) {
                    $item['distance'] = m('util')->GetDistance($_GPC['lat'], $_GPC['lng'], $item['addressinfo']['lat'], $item['addressinfo']['lng'], 2);
                } else {
                    $item['distance'] = '99999';
                }
            }
            unset($item);
            if ($_GPC['type'] == 3) {
                usort($list, 'timebankdistancesort');

                $list = array_slice($list, ($pindex - 1) * $psize, $psize);
            }



            $itemcate = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_item_category') . ' where uniacid=:uniacid  ', array(':uniacid' => $_W[uniacid]));


            return app_json(array('list' => $list, 'total' => $total, 'pagesize' => $psize, 'itemcate' => $itemcate));
        }
    }

    //判断几分钟前使用
    public function ReckonTime($time) {
        $NowTime = time();
        if ($time > $NowTime) {
            return false;
        }
        $TimePoor = $NowTime - $time;
        if ($TimePoor == 0) {
            $str = '一眨眼之间';
        } else if ($TimePoor < 60 && $TimePoor > 0) {
            $str = $TimePoor . '秒之前';
        } else if ($TimePoor >= 60 && $TimePoor <= 60 * 60) {
            $str = floor($TimePoor / 60) . '分钟前';
        } else if ($TimePoor > 60 * 60 && $TimePoor <= 3600 * 24) {
            $str = floor($TimePoor / 3600) . '小时前';
        } else if ($TimePoor > 3600 * 24 && $TimePoor <= 3600 * 24 * 7) {
            if (floor($TimePoor / (3600 * 24)) == 1) {
                $str = "昨天";
            } else if (floor($TimePoor / (3600 * 24)) == 2) {
                $str = "前天";
            } else {
                $str = floor($TimePoor / (3600 * 24)) . '天前';
            }
        } else if ($TimePoor > 3600 * 24 * 7) {
            $str = date("m-d", $time);
        }
        return $str;
    }

    //地址部分 所有函数 统一
    public function getaddress() {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $member = $this->member;
        $address = array();
        $alladd = pdo_fetchall('select *  from ' . tablename('ewei_shop_member_address') . ' where  openid=:openid and uniacid=:uniacid  ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));

        $wlkdata['alladd'] = $alladd;
        if (!empty($id)) {
            $address = pdo_fetch('select *  from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
            $wlkdata['address'] = $address;
        }
        if ($_GPC['submit'] == 1) {
            $data['address'] = trim($_GPC['address']);
            $data['realname'] = trim($_GPC['name']);
            $data['mobile'] = trim($_GPC['mobile']);
            $data['province'] = trim($_GPC['province']);
            $data['city'] = trim($_GPC['city']);
            $data['area'] = trim($_GPC['area']);
            $data['street'] = trim($_GPC['street']);
            $data['openid'] = $member['openid'];
            $data['uniacid'] = $_W['uniacid'];
            $data['lat'] = $_GPC['lat'];
            $data['lng'] = $_GPC['lng'];
            if ($_GPC['isdefault']) {
                $data['isdefault'] = 1;
                pdo_update('ewei_shop_member_address', array('isdefault' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $member['openid']));
            }
            if (empty($id)) {
                pdo_insert('ewei_shop_member_address', $data);
                $id = pdo_insertid();
            } else {
                pdo_update('ewei_shop_member_address', $data, array('id' => $id, 'uniacid' => $_W['uniacid'], 'openid' => $member['openid']));
            }
        }
        $wlkdata['id'] = $id;
        return app_json(array('wlkdata' => $wlkdata));
    }

    //预约时间选择数据
    public function timeinfo() {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $member = $this->member;
        $starttime = strtotime(date('Y-m-d', time()));

        for ($di = 1; $di <= 5; $di++) {
            $info['daytime'] = $starttime + 86400 * $di;
            $info['day'] = date('n月j日', $info['daytime']);
            //获取中文星期几
            $weekarray = array("日", "一", "二", "三", "四", "五", "六");
            $info['week'] = $weekarray[date("w", $info['daytime'])];
            $timeinfo[] = $info;
        }
        $wlkdata['timeinfo'] = $timeinfo;
        for ($hi = 16; $hi <= 19 * 2; $hi++) {
            $hourinfo[] = array('hour' => date('H:i', $starttime + 1800 * $hi), 'hi' => $hi);
        }

        $wlkdata['hourinfo'] = $hourinfo;

        //附带选择服务；
        $items = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_item') . ' where uniacid=:uniacid and status=1 ', array(':uniacid' => $_W[uniacid]));
        $items = set_medias($items, 'thumb');
        foreach ($items as &$item) {
            if ($item['multi'] == 1) {
                $item['options'] = unserialize($item['options']);
                foreach ($item['options'] as &$option) {
                    $option['text'] = $option['title'];
                }
                unset($option);
            }
        }
        unset($item);

        $wlkdata['items'] = $items;


        return app_json(array('wlkdata' => $wlkdata));
    }

    //发布个人的需求
    public function servepost() {
        global $_W;
        global $_GPC;
        $member = $this->member;

        $item = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_item') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $_GPC['itemid'], ':uniacid' => $_W[uniacid]));
        if ($item['multi'] == 1) {
            $item['options'] = unserialize($item['options']);
            $checkoption = $item['options'][$_GPC['optionid']];
            $money = $checkoption['give'];
            $data['servetime'] = $checkoption['time'];
            $data['itemtitle'] = $checkoption['title'];
        } else {
            $money = $item['timeaccount'];
            $data['servetime'] = $item['servetime'];
            $data['itemtitle'] = $item['name'];
        }


        $data['uniacid'] = $_W['uniacid'];
        $data['itemid'] = $_GPC['itemid'];
        $data['addressid'] = $_GPC['addressid'];
        $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $_GPC['addressid'], ':uniacid' => $_W[uniacid]));
        if (empty($address['lat']) || empty($address['lng'])) {
            return app_error(1, '所选地址信息错误');
        }
        $data['finaltime'] = $_GPC['finaltime'];
        $data['remark'] = $_GPC['remark'];
        $data['openid'] = $member['openid'];
        $data['createtime'] = time();
        $data['ordersn'] = m("common")->createNO("order", "orderno", "ZM");
        $data['timeaccount'] = $money;
        if ($member['timeaccount'] < $money) {
            return app_error(1, '时间余额不足');
        }
        $this->timeaccount(0, $member['openid'], -$money, '用户发布个人服务');

        pdo_insert('ewei_shop_timebank_servepost', $data);

        return app_json(array());
    }

    public function getorder() {
        global $_W;
        global $_GPC;
        $member = $this->member;
        $pindex = max(1, intval($_GPC['home']));
        $psize = 5;
        if ($_GPC['current'] == 1) {
            $condition = '1';
            $orderc = $_GPC['orderc'] - 1;
            $params = array(':uniacid' => $_W['uniacid']);
            if ($orderc == -1) {//全部
                $condition .= " and (serveopenid is null or serveopenid = '" . $member['openid_wa'] . "' )";
//                        $params[':serveopenid'] = $member['openid'];
            }
            if ($orderc == 0) {//待接单
                $condition .= " and serveopenid is null ";
            }
            if ($orderc == 1) {//待服务
                $condition .= " and serveopenid = '" . $member['openid_wa'] . "' and status=0";
            }
            if ($orderc == 2) {//服务中
                $condition .= " and serveopenid = '" . $member['openid_wa'] . "' and status=1";
            }
            if ($orderc == 3) {//待评价
                $condition .= " and serveopenid = '" . $member['openid_wa'] . "' and status=2";
            }

            $sql = 'SELECT *  FROM ' . tablename('ewei_shop_timebank_servepost') . '  WHERE ' . $condition . ' order by id desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $totalsql = 'SELECT count(*) FROM ' . tablename('ewei_shop_timebank_servepost') . '  WHERE ' . $condition;
            $list = pdo_fetchall($sql, $params);
            $total = pdo_fetchcolumn($totalsql, $params);
            foreach ($list as &$item) {
                $item['addressinfo'] = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['addressid'], ':uniacid' => $_W[uniacid]));
                $item['location'] = $item['addressinfo']['province'] . $item['addressinfo']['city'] . $item['addressinfo']['area'] . $item['addressinfo']['address'];
                $item['item'] = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_item') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['itemid'], ':uniacid' => $_W[uniacid]));
                $item['finaltimemsg'] = date('m-d H:i', $item['finaltime']);
                $item['countdown'] = $item['finaltime'] - time();
                if ($item['status'] == 0) {
                    $item['checkmsg'] = '服务签到';
                } else {
                    $item['checkmsg'] = '服务签出';
                }
            }
            unset($item);
        } elseif ($_GPC['current'] == 0) {
            $condition = '1';
            $orderc = $_GPC['orderc'] - 1;
            $params = array(':uniacid' => $_W['uniacid']);
            if ($orderc == -1) {//全部
                $condition .= " and (m.openid= '" . $member['openid_wa'] . "' )";
//                        $params[':serveopenid'] = $member['openid'];
            }
            if ($orderc == 0) {//待审核
                $condition .= " and (m.openid= '" . $member['openid_wa'] . "' and m.status=0 )";
            }
            if ($orderc == 1) {//待服务
                $condition .= " and (m.openid= '" . $member['openid_wa'] . "' and m.status=1 )";
            }
            if ($orderc == 2) {//服务中
                $condition .= " and (m.openid= '" . $member['openid_wa'] . "' and m.status=2 )";
            }
            if ($orderc == 3) {//已完成
                $condition .= " and (m.openid= '" . $member['openid_wa'] . "' and m.status=3 )";
            }
            $sql = 'SELECT a.*,m.status as status,m.id as logid,m.statrtime as statrtime,m.endtime as endtime  FROM ' . tablename('ewei_shop_timebank_many') . ' a left join ' . tablename('ewei_shop_timebank_many_log') . ' m on m.serverid=a.id  WHERE ' . $condition . ' order by a.id desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $totalsql = 'SELECT count(*) FROM ' . tablename('ewei_shop_timebank_many') . ' a left join ' . tablename('ewei_shop_timebank_many_log') . ' m on m.serverid=a.id  WHERE ' . $condition . ' order by a.id desc';
            $list = pdo_fetchall($sql, $params);

            $total = pdo_fetchcolumn($totalsql, $params);
            foreach ($list as &$item) {
                $host = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_host') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['hostid'], ':uniacid' => $_W[uniacid]));
                $cate = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_category') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['category'], ':uniacid' => $_W[uniacid]));
                $item['hostname'] = $host['name'];
                $item['categoryname'] = $cate['name'];
//                      $item['totaltime']=$item['endtime']-$item['statrtime'];
                $diff = abs($item['statrtime'] - $item['endtime']);
                $day = '';
                $vals = array('天' => '86400', '时' => '3600');
                foreach ($vals as $key => $value) {
                    if ($diff >= $value) {
                        $d = round($diff / $value);
                        $diff %= $value;
                        $day .= $d . $key;
                    }
                }
                $item['totaltime'] = $day;
                $timegetlist = pdo_fetch("select name from " . tablename("ewei_shop_timebank_time") . ' where  id=:id', ['id' => $item['timeget']]);
                $item['timegetname'] = $timegetlist['name'];
            }
            unset($item);
            $list = set_medias($list, 'logo');
        }

        return app_json(array('list' => $list, 'total' => $total, 'pagesize' => $psize));
    }

    //个人需求签到+获取服务状态+接单+评价；
    public function serve() {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $member = $this->member;
        $serve = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_servepost') . ' where id=:id ', array(':id' => $id));
        $serve['servemember'] = pdo_fetch('select s.*,m.avatar from ' . tablename('ewei_shop_timebank_server') . ' s ' . ' left join ' . tablename('ewei_shop_member') . ' m on s.openid = m.openid  where s.openid=:openid and s.uniacid=:uniacid   limit 1', array(':openid' => $serve['serveopenid'], ':uniacid' => $_W[uniacid]));
        $serve['servemember']['avatar'] = tomedia($serve['servemember']['avatar']);


        $addressinfo = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $serve['addressid'], ':uniacid' => $_W[uniacid]));

        $serve['item'] = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_item') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $serve['itemid'], ':uniacid' => $_W[uniacid]));
        $serve['item']['thumb'] = tomedia($serve['item']['thumb']);
//            $comment = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_serve_comment') . ' where pid=:id and uniacid=:uniacid ', array(':pid' => $serve['id'], ':uniacid' => $_W[uniacid]));
        $comment = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_serve_comment') . ' where pid=:id and uniacid=:uniacid ', array(':id' => $serve['id'], ':uniacid' => $_W[uniacid]));
        $serve['comment'] = $comment;
        $serve['finishtime'] = date('Y-m-d H:i:s', $serve['endtime']);
        ;
        //用户进行取消
        if ($_GPC['cancel'] == 1) {
            if ($serve['status'] == 0) {
                pdo_delete('ewei_shop_timebank_servepost', array('id' => $serve['id'], 'uniacid' => $_W['uniacid']));
                $this->timeaccount(0, $member['openid'], $serve['timeaccount'], '用户取消个人服务发布');

                return app_json(['msg' => '取消成功']);
            } else {
                return app_error(1, '服务者已服务中');
            }
        }
        //用户接单处理
        if ($_GPC['pick'] == 1) {
            $apply = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            if ($apply['status'] != 1) {
                return app_error(1, '请审核通过成为服务者再接单');
            }
            if ($serve['openid'] == $apply['openid']) {
                return app_error(1, '不能接自己发布的订单');
            }
            if (empty($serve['serveopenid'])) {
                pdo_update('ewei_shop_timebank_servepost', array('serveopenid' => $member['openid']), array('id' => $serve['id'], 'uniacid' => $_W['uniacid']));
                return app_json(['msg' => '接单成功']);
            } else {
                return app_error(1, '该单子已经被接了');
            }
        }
        //用户进行大卡签到
        if ($_GPC['submit'] == 1) {
            if ($_GPC['host'] == 1) {
                $data['uniacid'] = $_W['uniacid'];
                $data['pid'] = $id;
                $data['openid'] = $member['openid'];
                $data['intime'] = $_GPC['intime'];
                $data['cop'] = $_GPC['cop'];
                $data['env'] = $_GPC['env'];
                $data['serveimg'] = $_GPC['serveimg'];
                $data['content'] = $_GPC['content'];
                pdo_insert('ewei_shop_timebank_serve_comment', $data);

                //print_r($list);exit;
                return app_json(['msg' => '评价成功']);
            } else {
                if ($serve['serveopenid'] != $member['openid']) {

                    return app_error(1, '服务者信息错误');
                }

                $distance = m('util')->GetDistance($_GPC['lat'], $_GPC['lng'], $addressinfo['lat'], $addressinfo['lng'], 2);
                if ($distance > 2 && $serve['status'] < 2) {
                    return app_error(1, '距离目的地' . $distance . 'km');
                }

                $resimg = json_decode(html_entity_decode($_GPC['resimg']));

                if ($serve['status'] == 0) {

                    pdo_update('ewei_shop_timebank_servepost', array('status' => 1, 'starttime' => time(), 'startimg' => $_GPC['serveimg']), array('id' => $id));
                    //print_r($list);exit;
                    return app_json(['msg' => '签到成功']);
                }
                if ($serve['status'] == 1) {
                    //判断签出条件
                    $limittime = $serve['starttime'] + $serve['servetime'] * 60;
                    if (time() < $limittime) {
                        return app_error(1, '服务时间未到');
                    }
                    pdo_update('ewei_shop_timebank_servepost', array('status' => 2, 'endtime' => time(), 'endimg' => $_GPC['serveimg']), array('id' => $id));

                    $money = $serve['timeaccount'];


                    $this->timeaccount(0, $member['openid'], $money, '服务者完成服务');

                    $sever = pdo_fetch("select * from " . tablename("ewei_shop_timebank_server") . " where server=" . $member['id']);
                    $updata['frequency'] = $sever['frequency'] + 1;
                    pdo_update('ewei_shop_timebank_server', $updata, array('server' => $member['id']));
                    if ($sever['mechanism'] > 0 && $sever['grouppass'] == 1) {
                        $group = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_group') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $sever['mechanism'], ':uniacid' => $_W[uniacid]));
                        pdo_update('ewei_shop_timebank_group', array('frequency' => $group['frequency'] + 1), array('id' => $group['id']));
                    }

                    return app_json(['msg' => '签出成功']);
                }
                if ($serve['status'] == 2) {
                    $data['status'] = 3;
                    $data['intime'] = $_GPC['intime'];
                    $data['cop'] = $_GPC['cop'];
                    $data['env'] = $_GPC['env'];
                    $data['serveimg'] = $_GPC['serveimg'];
                    $data['content'] = $_GPC['content'];

                    pdo_update('ewei_shop_timebank_servepost', $data, array('id' => $id));
                    //print_r($list);exit;
                    return app_json(['msg' => '评价成功']);
                }
            }
        }

        return app_json(['serve' => $serve]);
    }

    //我的订单
    public function myorder() {
        global $_W;
        global $_GPC;
        $member = $this->member;

        $pindex = max(1, intval($_GPC['home']));
        $psize = 5;
        $condition = ' and openid = :openid and uniacid=:uniacid';
        $orderc = $_GPC['orderc'] - 1;
        if ($orderc >= 0) {
            $condition .= ' and status=' . $orderc;
        }
        $params = array(':openid' => $member['openid'], ':uniacid' => $_W['uniacid']);


        $list = pdo_fetchall('select * from ' . tablename('ewei_shop_timebank_servepost') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
        $total = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_timebank_servepost') . ' where 1 ' . $condition . ' order by createtime desc  ', $params);


        foreach ($list as &$item) {
            $item['servemember'] = pdo_fetch('select s.*,m.avatar from ' . tablename('ewei_shop_timebank_server') . ' s ' . ' left join ' . tablename('ewei_shop_member') . ' m on s.openid = m.openid  where s.openid=:openid and s.uniacid=:uniacid   limit 1', array(':openid' => $item['serveopenid'], ':uniacid' => $_W[uniacid]));
            $item['servemember']['avatar'] = tomedia($item['servemember']['avatar']);
            if ($item['servemember']['mechanism'] > 0) {
                $item['group'] = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_group') . ' where id=:id    limit 1', array(':id' => $item['servemember']['mechanism']));
            }
            $item['addressinfo'] = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['addressid'], ':uniacid' => $_W[uniacid]));
            $item['location'] = $item['addressinfo']['province'] . $item['addressinfo']['city'] . $item['addressinfo']['area'] . $item['addressinfo']['address'];
            $item['item'] = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_item') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['itemid'], ':uniacid' => $_W[uniacid]));
            $item['item']['thumb'] = tomedia($item['item']['thumb']);
            $item['finaltimemsg'] = date('H:i', $item['finaltime']);

            if (empty($item['serveopenid'])) {
                $item['status'] = -1;
            }
            switch ($item['status']) {
//                              case -1:
//                                    $item['statusmsg'] = '待接单';
//                                    break;
                case 0:
                    $item['statusmsg'] = '待服务';
                    break;
                case 1:
                    $item['statusmsg'] = '服务中';
                    break;
                case 2:
                    $item['statusmsg'] = '待评价';
                    break;
                case 3:
                    $item['statusmsg'] = '已完成';
                    break;
            }
        }
        unset($item);


        return app_json(array('list' => $list, 'total' => $total, 'pagesize' => $psize));
    }

    /**
     * 首页-分类8个
     */
    public function get_category() {
        global $_W;
        global $_GPC;
        $pindex = max(1, intval($_GPC['home']));
        $psize = 8;
        $params = array(':uniacid' => $_W['uniacid']);
        $condition = ' and show=1';
        //$condition = array('show' => 1);
        $sql = 'select  *  from ' . tablename('ewei_shop_timebank_category') . '   where showss = 1 order by orderby desc limit 0,8';
        $list = pdo_fetchall($sql, $params);
        $list = set_medias($list, 'logo');
        $i = 0;
        foreach ($list as $key => &$value) {
            $value['createtime'] = date("Y-m-d H:i", $value['createtime']);
            $i++;
        }
        unset($value);
        if ($i == 0) {
            $lists[] = array();
            return app_json(array('list' => $lists));
        }
        return app_json(array('list' => $list));
    }

    /**
     * 首页-banner
     */
    public function get_banner() {
        global $_W;
        global $_GPC;
        $advs = pdo_fetchall('select id,advname,link,thumb from ' . tablename('ewei_shop_adv') . ' where enabled=1 order by displayorder desc');
        $advs = set_medias($advs, 'thumb');
        return app_json(array('list' => $advs));
    }

    /**
     * 首页-热门任务
     */
    public function get_many() {
        global $_W;
        global $_GPC;
        $params = array(':uniacid' => $_W['uniacid']);
        $page = empty($_GPC['home']) ? '' : $_GPC['home'];
        if (empty($_GPC['ispost'])) {
            $_GPC['ispost'] = 1;
        }
        $select_ispost = ' and a.ispost=' . intval($_GPC['ispost']) . " ";
        if (empty($_GPC['psize'])) {
            $psize = 20;
        }
        $pindex = max(1, intval($page));
        $condition = 1;
        $timegetlist = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_time"));
        $temp_timegetlist = [];
        foreach ($timegetlist as $item) {
            $temp_timegetlist[$item['id']] = $item['name'];
        }
        $sql = 'SELECT a.*,c.name as categoryname  FROM ' . tablename('ewei_shop_timebank_many') . ' a left join ' . tablename('ewei_shop_timebank_category') . ' c on c.id=a.category  WHERE ' . $condition . $select_ispost . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql);
        $lists = [];
        $i = 0;
        if (!empty($list)) {

            foreach ($list as $key => &$value) {
                $value['starttime'] = $value['server_time_start'] . '  开始';
                $now = time();
                $value['ing'] = false; //进行中
                if (strtotime($value['server_time_start']) < $now and strtotime($value['server_time_end']) > $now) {
                    $value['ing'] = true;
                }
                $value['over'] = false; //已结束
                if (strtotime($value['server_time_end']) < $now) {
                    $value['over'] = true;
                    $value['starttime'] = $value['server_time_end'] . '  结束';
                }
                $value['info']['signnum'] = pdo_fetchcolumn("select count(1) from " . tablename("ewei_shop_timebank_many_log") . " where   serverid=" . $value['id'] . " and status !=-1");
                if (!empty($temp_timegetlist[$value['timeget']])) {
                    $value['timeget_str'] = $temp_timegetlist[$value['timeget']];
                } else {
                    $value['timeget_str'] = "";
                }
                if ($value['over']) {
                    $value[status] = 3;
                } elseif ($value['ing']) {
                    $value[status] = 2;
                } else {
                    $value[status] = 1;
                }
                if ($value['type'] == 1) {
                    $value['address'] = $value['province'] . $value['city'] . $value['area'];
                    $value['addressstatus'] = 1;
                } else {
                    $value['address'] = "线上服务";
                    $value['addressstatus'] = 0;
                }
                $value = set_medias($value, 'logo');
                $lists[$key]['id'] = $value['id'];
                $lists[$key]['name'] = $value['name'];
                $lists[$key]['logo'] = $value['logo'];
                $lists[$key]['categoryname'] = $value['categoryname'];
                $lists[$key]['address'] = $value['address'];
                $lists[$key]['server_time_start'] = $value['server_time_start'];
                $lists[$key]['status'] = $value['status'];
                $lists[$key]['timeget_str'] = $value['timeget_str'];
                $lists[$key]['addressstatus'] = $value['addressstatus'];
                $i++;
            }
            unset($value);
            if ($i == 0) {
                $lists[] = array();
                return app_json(array('list' => $lists));
            }
        }
        return app_json(array('list' => $lists));
    }

    /**
     * 服务详情
     */
    public function get_details() {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);

        $catesql = 'select  id,name  from ' . tablename('ewei_shop_timebank_category') . '   where uniacid=:uniacid ';
        $catelist = pdo_fetchall($catesql, array(':uniacid' => $_W['uniacid']));

        $hostsql = 'select  id,name  from ' . tablename('ewei_shop_timebank_host') . '   where uniacid=:uniacid ';
        $hostlist = pdo_fetchall($hostsql, array(':uniacid' => $_W['uniacid']));

        $timegetlist = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_time"));
        $temp_timegetlist = [];
        foreach ($timegetlist as $item) {
            $temp_timegetlist[$item['id']] = $item['name'];
        }


        $timebank = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_timebank_many') . ' WHERE id=:aid  limit 1 ', array(':aid' => $id));
        $cate = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_timebank_category') . ' WHERE id=:id  limit 1 ', array(':id' => $timebank['category']));
        $now = time();


        if (strtotime($timebank['signup_time_end']) < $now) {
            $timebank[status] = 3;
        } elseif (strtotime($timebank['signup_time_start']) < $now and strtotime($timebank['signup_time_end']) > $now) {
            $timebank[status] = 2;
        } else {
            $timebank[status] = 1;
        }
        if (!empty($temp_timegetlist[$timebank['timeget']])) {
            $timebank['timeget_str'] = $temp_timegetlist[$timebank['timeget']];
        } else {
            $timebank['timeget_str'] = "";
        }
        if ($timebank['type'] == 1) {
            $timebank['address'] = $timebank['province'] . $timebank['city'] . $timebank['area'];
            $timebank['addressstatus'] = 1;
        } else {
            $timebank['address'] = "线上服务";
            $timebank['addressstatus'] = 0;
        }
        $timebank = set_medias($timebank, 'logo');
        $manylog = pdo_fetchall("select *  from " . tablename("ewei_shop_timebank_many_log") . " where   serverid=" . $id . " and status !=-1");

        foreach ($manylog as $key => &$value) {
            /* $param[':openid'] = $value['openid'];
              $sql = 'select  avatar  from ' . tablename('ewei_shop_member') . 'where openid=:openid';
              $avatar = pdo_fetchcolumn($sql, $param); */
            $params = array(':openid' => $value['openid'], ':openid_wa' => $value['openid']);
            $condition = '';
            $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
            $user = pdo_fetch($sql, $params);   //return app_json(array('list' => $user));
            if (empty($user['avatar'])) {
                $loguser[$key]['avatar'] = '/static/login/logo.png';
            } else {
                $user = set_medias($user, 'avatar');
                $loguser[$key]['avatar'] = $user['avatar'];
            }
            //$loguser[$key]['avatar'] = pdo_fetchcolumn($sql, $param);
            $loguser[$key]['name'] = $value['name'];
            $loguser[$key]['year'] = date("Y-m-d", $value['createtime']);
            $loguser[$key]['time'] = date("H:i:s", $value['createtime']);
            $loguser[$key]['createtime'] = date("Y-m-d H:i", $value['createtime']);
        }

        $timebank['info']['signnum'] = pdo_fetchcolumn("select count(1) from " . tablename("ewei_shop_timebank_many_log") . " where   serverid=" . $id . " and status !=-1");
        $list['id'] = $timebank['id'];
//        $endtime = date("H:i:s",strtotime($timebank['server_time_end']));
//        $yearend =   date("Y",strtotime($timebank['server_time_end'])).'年';
        $monthend = date("m", strtotime($timebank['server_time_end'])) . '月';
        $dayend = date("d", strtotime($timebank['server_time_end'])) . '日';
        $list['server_time_end'] = $monthend . $dayend;
        $yearstart = date("Y", strtotime($timebank['server_time_start'])) . '年';
        $monthstart = date("m", strtotime($timebank['server_time_start'])) . '月';
        $daystart = date("d", strtotime($timebank['server_time_start'])) . '日';
        $list['server_time_start'] = $yearstart . $monthstart . $daystart;
        $list['signup_time_end'] = $timebank['signup_time_end'];    //报名结束时间
        $list['signup_time_start'] = $timebank['signup_time_start'];  //报名开始时间
        if (strtotime($timebank['signup_time_end']) > time()) {
            $list['timeout'] = 1;
        } else {
            $list['timeout'] = 0;
        }
        $list['name'] = $timebank['name'];
        $list['logo'] = $timebank['logo'];
        $list['addressstatus'] = $timebank['addressstatus'];
        $list['address'] = $timebank['address'];
        $list['timeget_str'] = $timebank['timeget_str'];
        $list['status'] = $timebank['status'];
        $list['limitnum'] = $timebank['limitnum'];  //需求人数 如果为0 则无限制
        $list['signnum'] = $timebank['info'][signnum];  //已报名人数
        $list['lat'] = $timebank['lat'];
        $list['lng'] = $timebank['lng'];
        $list['credit'] = $timebank['credit'];
        $list['categoryname'] = $cate['name'];
        foreach ($hostlist as $key => &$value) {
            if ($value['id'] == $timebank['hostid']) {
                $list['hostname'] = $value['name'];
            }
        }
        $list['signuser'] = $loguser;
        /* $logg = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many_log") . ' where  openid=:openid and serverid=:serverid', ['openid' => $_GPC['openid'], 'serverid' => $_GPC['id']]);
          if ($logg['id']) {
          $list['shenqing'] = 1;
          } else {
          $list['shenqing'] = 0;
          } */
        $list['shenqing'] = 1;
        $params1 = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition1 = '';
        $sqlq = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition1;
        $user1 = pdo_fetch($sqlq, $params1);
        $log = pdo_fetch("select * from " . tablename("ewei_shop_timebank_server") . ' where  server=:server', ['server' => $user1['id']]);
        $sermember = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_many_log') . ' where serverid=:serverid and openid=:openid limit 1', array(':serverid' => $_GPC['id'], ':openid' => $_GPC['openid']));
        $server = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many") . ' where  id=:id', ['id' => $_GPC['id']]);
        $host = pdo_fetch("select * from " . tablename("ewei_shop_timebank_host") . ' where  id=:id', ['id' => $server['hostid']]);
        $list['host'] = $host;
        if ($server['periodicity'] == -1) {
            if ($sermember) {
                //print_r($user1);exit;
                if ((!empty($sermember)) && ($sermember['status'] == 0)) {
                    $list['shenqing'] = -1;
                } else {
                    $list['shenqing'] = 0;
                }

                //show_json(-3, '已经申请过的服务项目不能重复申请哦');
                app_json(-1, '已经申请过的服务项目不能重复申请哦');
            }
        } else {

            if ($server['weekss'] == 1) {

                $starttime = strtotime('-1 day');
                $endtime = time();
                $params = array(':mobile' => $log['tel'], ':serverid' => $_GPC['id']);
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';

                $sql = 'select  count(1)  from ' . tablename('ewei_shop_timebank_many_log') . '   where mobile=:mobile and serverid=:serverid ' . $condition;
                $listss = pdo_fetchcolumn($sql, $params);

                if ($server['nextss'] <= $listss) {
                    $list['shenqing'] = 0;
                    app_json(-1, '已超过报名次数');
                    //show_json(-7, '该服务者已超过报名次数');
                    //show_json(0, '该服务者已超过报名次数');
                }
            } elseif ($server['weekss'] == 7) {
                $starttime = strtotime('-7 day');
                $endtime = time();
                $params = array(':mobile' => $log['tel'], ':serverid' => $_GPC['id']);
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';

                $sql = 'select  count(1)  from ' . tablename('ewei_shop_timebank_many_log') . '   where mobile=:mobile and serverid=:serverid ' . $condition;
                $listss = pdo_fetchcolumn($sql, $params);
                if ($server['nextss'] <= $listss) {
                    $list['shenqing'] = 0;
                    app_json(-1, '已超过报名次数');
                    //show_json(-7, '该服务者已超过报名次数');
                }
            } else {
                $starttime = strtotime('-31 day');
                $endtime = time();
                $params = array(':mobile' => $log['tel'], ':serverid' => $_GPC['id']);
                $params[':starttime'] = $starttime;
                $params[':endtime'] = $endtime;
                $condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';

                $sql = 'select  count(1)  from ' . tablename('ewei_shop_timebank_many_log') . '   where mobile=:mobile and serverid=:serverid ' . $condition;
                $listss = pdo_fetchcolumn($sql, $params);
                if ($server['nextss'] <= $listss) {
                    $list['shenqing'] = 0;
                    app_json(-1, '已超过报名次数');
                    //show_json(-7, '该服务者已超过报名次数');
                }
            }
        }

        $list['contacts'] = m("common")->html_to_images($timebank["content"]);



        //show_json(1, $list);
        return app_json(array('list' => $list));
    }

    /**
     * 所有分类
     */
    public function get_categoryall() {
        global $_W;
        global $_GPC;
        $params = array(':uniacid' => $_W['uniacid']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_timebank_category') . '   where uniacid=:uniacid  and showss = 1 order by orderby desc' . $condition;
        $list = pdo_fetchall($sql, $params);
        $list = set_medias($list, 'logo');
        $i = 0;
        foreach ($list as $key => &$value) {
            $value['createtime'] = date("Y-m-d H:i", $value['createtime']);
            $i++;
        }
        unset($value);
        if ($i == 0) {
            $lists[] = array();
            return app_json(array('list' => $lists));
        }
        return app_json(array('list' => $list));
    }

    /**
     * 服务列表
     */
    public function get_manylist() {
        global $_W;
        global $_GPC;
        //$params = array(':uniacid' => $_W['uniacid']);
        $pindex = max(1, intval($_GPC['home']));
        $psize = 5;
        $select_category = empty($_GPC['category']) ? '' : ' and category=' . intval($_GPC['category']) . ' ';
        if ($_GPC['orderby'] == 4) {
            $select_order = 'createtime';
        } elseif ($_GPC['orderby'] == 3) {
            $select_order = 'createtime';
        } elseif ($_GPC['orderby'] == 2) {
            $select_order = 'id';
        } else {
            $select_order = 'limitnum';
        }
        $condition = 1;
        if ($_GPC['ordertime'] == 1) {  //当天
            $starttime = strtotime('-1 day');
            $endtime = time();
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
            $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
        } elseif ($_GPC['ordertime'] == 2) {     //一周内
            $starttime = strtotime('-7 day');
            $endtime = time();
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
            $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
        } elseif ($_GPC['ordertime'] == 3) {
            $starttime = strtotime('-31 day');
            $endtime = time();
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
            $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
        }

        if (empty($_GPC['ispost'])) {
            $_GPC['ispost'] = 1;
        }
        $select_ispost = ' and a.ispost=' . intval($_GPC['ispost']) . " ";
        if (empty($_GPC['psize'])) {
            $psize = 20;
        }
        $pindex = max(1, intval($page));

        $timegetlist = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_time"));
        $temp_timegetlist = [];
        foreach ($timegetlist as $item) {
            $temp_timegetlist[$item['id']] = $item['name'];
        }
        $sql = 'SELECT a.*,c.name as categoryname  FROM ' . tablename('ewei_shop_timebank_many') . ' a left join ' . tablename('ewei_shop_timebank_category') . ' c on c.id=a.category  WHERE ' . $condition . $select_ispost . $select_category . ' order by ' . $select_order . ' desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql, $params);
        $lists = [];
        $i = 0;
        if (!empty($list)) {

            foreach ($list as $key => &$value) {
                $value['starttime'] = $value['server_time_start'] . '  开始';
                $now = time();
                $value['ing'] = false; //进行中
                if (strtotime($value['server_time_start']) < $now and strtotime($value['server_time_end']) > $now) {
                    $value['ing'] = true;
                }
                $value['over'] = false; //已结束
                if (strtotime($value['server_time_end']) < $now) {
                    $value['over'] = true;
                    $value['starttime'] = $value['server_time_end'] . '  结束';
                }
                $value['info']['signnum'] = pdo_fetchcolumn("select count(1) from " . tablename("ewei_shop_timebank_many_log") . " where   serverid=" . $value['id'] . " and status !=-1");
                if (!empty($temp_timegetlist[$value['timeget']])) {
                    $value['timeget_str'] = $temp_timegetlist[$value['timeget']];
                } else {
                    $value['timeget_str'] = "";
                }
                if ($value['over']) {
                    $value[status] = 3;
                } elseif ($value['ing']) {
                    $value[status] = 2;
                } else {
                    $value[status] = 1;
                }
                if ($value['type'] == 1) {
                    $value['address'] = $value['province'] . $value['city'] . $value['area'];
                    $value['addressstatus'] = 1;
                } else {
                    $value['address'] = "线上服务";
                    $value['addressstatus'] = 0;
                }
                $value = set_medias($value, 'logo');
                $lists[$key]['id'] = $value['id'];
                $lists[$key]['name'] = $value['name'];
                $lists[$key]['logo'] = $value['logo'];
                $lists[$key]['categoryname'] = $value['categoryname'];
                $lists[$key]['address'] = $value['address'];
                $lists[$key]['server_time_start'] = $value['server_time_start'];
                $lists[$key]['status'] = $value['status'];
                $lists[$key]['timeget_str'] = $value['timeget_str'];
                $lists[$key]['addressstatus'] = $value['addressstatus'];
                $i++;
            }
            unset($value);
        }
        if ($i == 0) {
            $lists[] = array();
            return app_json(array('list' => $lists));
        }
        return app_json(array('list' => $lists));
    }

    /**
     * 服务搜索
     */
    public function get_manysearch() {
        global $_W;
        global $_GPC;
        //$params = array(':uniacid' => $_W['uniacid']);
        $page = empty($_GPC['home']) ? '' : $_GPC['home'];
        $select_category = empty($_GPC['category']) ? '' : ' and category=' . intval($_GPC['category']) . ' ';
        $select_title = empty($_GPC['keyword']) ? '' : ' and a.name LIKE "%' . $_GPC['keyword'] . '%" ';
        if ($_GPC['orderby'] == 4) {
            $select_order = 'createtime';
        } elseif ($_GPC['orderby'] == 3) {
            $select_order = 'createtime';
        } elseif ($_GPC['orderby'] == 2) {
            $select_order = 'id';
        } else {
            $select_order = 'limitnum';
        }
        $condition = 1;
        if ($_GPC['ordertime'] == 1) {  //当天
            $starttime = strtotime('-1 day');
            $endtime = time();
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
            $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
        } elseif ($_GPC['ordertime'] == 2) {     //一周内
            $starttime = strtotime('-7 day');
            $endtime = time();
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
            $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
        } elseif ($_GPC['ordertime'] == 3) {
            $starttime = strtotime('-31 day');
            $endtime = time();
            $params[':starttime'] = $starttime;
            $params[':endtime'] = $endtime;
            $condition .= ' AND a.createtime >= :starttime AND a.createtime <= :endtime ';
        }

        if (empty($_GPC['ispost'])) {
            $_GPC['ispost'] = 1;
        }
        $select_ispost = ' and a.ispost=' . intval($_GPC['ispost']) . " ";
        if (empty($_GPC['psize'])) {
            $psize = 20;
        }
        $pindex = max(1, intval($page));

        $timegetlist = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_time"));
        $temp_timegetlist = [];
        foreach ($timegetlist as $item) {
            $temp_timegetlist[$item['id']] = $item['name'];
        }
        $sql = 'SELECT a.*,c.name as categoryname  FROM ' . tablename('ewei_shop_timebank_many') . ' a left join ' . tablename('ewei_shop_timebank_category') . ' c on c.id=a.category  WHERE ' . $condition . $select_ispost . $select_title . $select_category . ' order by ' . $select_order . ' desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql, $params);
        $lists = [];
        $i = 0;
        if (!empty($list)) {

            foreach ($list as $key => &$value) {
                $value['starttime'] = $value['server_time_start'] . '  开始';
                $now = time();
                $value['ing'] = false; //进行中
                if (strtotime($value['server_time_start']) < $now and strtotime($value['server_time_end']) > $now) {
                    $value['ing'] = true;
                }
                $value['over'] = false; //已结束
                if (strtotime($value['server_time_end']) < $now) {
                    $value['over'] = true;
                    $value['starttime'] = $value['server_time_end'] . '  结束';
                }
                $value['info']['signnum'] = pdo_fetchcolumn("select count(1) from " . tablename("ewei_shop_timebank_many_log") . " where   serverid=" . $value['id'] . " and status !=-1");
                if (!empty($temp_timegetlist[$value['timeget']])) {
                    $value['timeget_str'] = $temp_timegetlist[$value['timeget']];
                } else {
                    $value['timeget_str'] = "";
                }
                if ($value['over']) {
                    $value[status] = 3;
                } elseif ($value['ing']) {
                    $value[status] = 2;
                } else {
                    $value[status] = 1;
                }
                if ($value['type'] == 1) {
                    $value['address'] = $value['province'] . $value['city'] . $value['area'];
                    $value['addressstatus'] = 1;
                } else {
                    $value['address'] = "线上服务";
                    $value['addressstatus'] = 0;
                }
                $value = set_medias($value, 'logo');
                $lists[$key]['id'] = $value['id'];
                $lists[$key]['name'] = $value['name'];
                $lists[$key]['logo'] = $value['logo'];
                $lists[$key]['categoryname'] = $value['categoryname'];
                $lists[$key]['address'] = $value['address'];
                $lists[$key]['server_time_start'] = $value['server_time_start'];
                $lists[$key]['status'] = $value['status'];
                $lists[$key]['timeget_str'] = $value['timeget_str'];
                $lists[$key]['addressstatus'] = $value['addressstatus'];
                $i++;
            }
            unset($value);
        }
        if ($i == 0) {
            $lists[] = array();
            return app_json(array('list' => $lists));
        }
        return app_json(array('list' => $lists));
    }

    /**
     * 报名咨询
     */
    public function get_consultation() {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $params = array(':id' => $id);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_timebank_many') . '   where id=:id ' . $condition;
        $listss = pdo_fetch($sql, $params);
        $params = array(':id' => $listss['hostid']);
        $sql = 'select  *  from ' . tablename('ewei_shop_timebank_host') . '   where id=:id ' . $condition;
        $list = pdo_fetch($sql, $params);
        $list = set_medias($list, 'qrcode');
        $lists = [];
        $lists['contacts'] = $list['membername'];
        $lists['tel'] = $list['membermobile'];
        $lists['pic'] = $list['qrcode'];

        return app_json(array('list' => $lists));
    }

    /**
     * 机构列表
     */
    public function get_hostlist() {
        global $_W;
        global $_GPC;
        $hostsql = 'select  id,name  from ' . tablename('ewei_shop_timebank_host') . ' where status = 1 ';
        $hostlist = pdo_fetchall($hostsql);
        return app_json(array('list' => $hostlist));
    }

    /**
     * 判断用户是否已经绑定手机号
     */
    public function get_tel() {
        global $_W;
        global $_GPC;
        $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
        $member2 = pdo_fetch($sql, $params);
        if ($member2['id']) {
            $list['mobile'] = $member2['mobile'];
            return app_json(array('list' => $list));
        } else {
            return app_error(AppError::$MemberphoneError, '此用户没有绑定手机号');
        }
    }

    /**
     * 申请成为机构服务者
     */
    public function get_server() {
        global $_W;
        global $_GPC;

        $mobile = trim($_GPC['tel']);
        $pwd = trim(123456);
        $confirm = 1;
        $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
        $member = pdo_fetch($sql, $params);
        //$member = $this->member;
        $member2 = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid and mobileverify=1 limit 1', array(':mobile' => $mobile, ':uniacid' => $_W['uniacid']));


        if (empty($member2)) {
            $salt = m('account')->getSalt();
            m('bind')->update($member['id'], array('mobile' => $mobile, 'pwd' => md5($pwd . $salt), 'salt' => $salt, 'mobileverify' => 1));
            m('account')->setLogin($member['id']);

            if (empty($member['mobileverify'])) {
                m('bind')->sendCredit($member);
            }
        }

        if (m('bind')->iswxm($member) && m('bind')->iswxm($member2)) {
            if ($confirm) {
                $salt = m('account')->getSalt();
                m('bind')->update($member['id'], array('mobile' => $mobile, 'pwd' => md5($pwd . $salt), 'salt' => $salt, 'mobileverify' => 1));
                m('bind')->update($member2['id'], array('mobileverify' => 0));
                m('account')->setLogin($member['id']);
            }
        }

        if (!m('bind')->iswxm($member2)) {
            if ($confirm) {
                $result = m('bind')->merge($member2, $member);


                $salt = m('account')->getSalt();
                m('bind')->update($member['id'], array('mobile' => $mobile, 'pwd' => md5($pwd . $salt), 'salt' => $salt, 'mobileverify' => 1));
                m('account')->setLogin($member['id']);
            }
        }

        if (!m('bind')->iswxm($member)) {
            if ($confirm) {
                $result = m('bind')->merge($member, $member2);


                $salt = m('account')->getSalt();
                m('bind')->update($member2['id'], array('mobile' => $mobile, 'pwd' => md5($pwd . $salt), 'salt' => $salt, 'mobileverify' => 1));
                m('account')->setLogin($member2['id']);
            }
        }






        $ispost = -1;
        $id = intval($_GPC['id']);
        $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
        $openidid = pdo_fetch($sql, $params);

        if ($openidid['realname'] == "") {
            $hostdata11['realname'] = $_GPC['name'];
            pdo_update('ewei_shop_member', $hostdata11, array('id' => $openidid['id']));
        }
        $params = array(':server' => $openidid['id']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_timebank_server') . '   where server=:server ' . $condition;
        $list = pdo_fetch($sql, $params);
        if ($list['id']) {

            if ($list['status'] == 0) {
                show_json(-2, '正在审核中');
            }
            if ($list['status'] == -1) {
                show_json(-1, '您的审核被拒绝了');
            }
            if ($list['status'] == 1) {
                show_json(0, '您已经成功申请过了');
            }
        }

        // if ($ispost) {
        if (empty($_GPC['name'])) {
            show_json(0, '名称不能为空');
        }
        if (empty($_GPC['tel'])) {
            show_json(0, '电话为不能为空');
        }
        if (empty($_GPC['openid'])) {
            show_json(0, '请选择会员');
        }
        $hostdata['uniacid'] = $_W['uniacid'];
        $hostdata['server'] = $openidid['id'];
        $hostdata['name'] = $_GPC['name'];
        $hostdata['tel'] = $_GPC['tel'];
        $hostdata['mechanism'] = $_GPC['mechanism'];
        $hostdata['registertime'] = time();
        $hostdata['province'] = $_GPC['province'];
        $hostdata['city'] = $_GPC['city'];
        $hostdata['area'] = $_GPC['area'];
        $hostdata['street'] = $_GPC['street'];
        $hostdata['cardfront'] = $_GPC['cardfront'];
        $hostdata['cardbehind'] = $_GPC['cardbehind'];
        $hostdata['status'] = $_GPC['status'];
        $hostdata['ispost'] = $ispost;
        $hostdata['idcard'] = $_GPC['idcard'];
        pdo_insert('ewei_shop_timebank_server', $hostdata);
        $mlogid = pdo_insertid(); //print_r($hostdata);exit;
        if ($mlogid) {
            show_json(1, '申请成功');
        } else {
            show_json(0, '申请失败');
        }

        // if (!empty($id)) {
        //     unset($hostdata["registertime"]);
        //    pdo_update('ewei_shop_timebank_server', $hostdata, array('id' => $id));
        // } else {
        //pdo_insert('ewei_shop_timebank_server', $hostdata);
        //}
        // }
    }

    function getDistance($lat1, $lng1, $lat2, $lng2) {
        global $_W;
        global $_GPC;
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); // deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;

        return $s;
    }

    /**
     * 签到
     */
    public function get_serverstart() {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $openid = $_GPC['openid'];
        $list = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_many_log') . ' where id=:id ', array(':id' => $id));
        if (!$list) {
            show_json(0, '该订单不存在');
        }
        if ($list['status'] == 2) {
            show_json(-3, '您已经签到过了');
        }
        if ($list['status'] == 3) {
            show_json(-1, '该订单已完成');
        }
        $listmany = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_many') . ' where id=:id ', array(':id' => $list['serverid']));

        if ($listmany['type'] == 1) {
            $radLat1 = deg2rad($_GPC['lat']); // deg2rad()函数将角度转换为弧度
            $radLat2 = deg2rad($listmany['lat']);
            $radLng1 = deg2rad($_GPC['lng']);
            $radLng2 = deg2rad($listmany['lng']);

            $a = $radLat1 - $radLat2;
            $b = $radLng1 - $radLng2;

            $juli = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
            $listsys = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_sys') . ' where 1 ');
            // print_r($listsys['distance']);exit;
            if ($juli > $listsys['distance']) {
                return app_error(-1, '距离活动地点太远，请到活动地点进行签到');
//                show_json(-4, '距离活动地点太远，请到活动地点进行签到');
            }
        }
        pdo_update('ewei_shop_timebank_many_log', array('status' => 2, 'statrtime' => time()), array('id' => $id));
//        show_json(1, '签到成功');
        return app_json(1, '签到成功');
    }

    /**
     * 签出
     */
    public function get_serverend() {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $openid = $_GPC['openid'];
        $list = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_many_log') . ' where id=:id ', array(':id' => $id));
        if (!$list) {
            show_json(0, '该订单不存在');
        }
        if ($list['status'] == 1) {
            show_json(-2, '该订单还未签到');
        }
        if ($list['status'] == 3) {
            show_json(-1, '该订单已完成');
        }
        $listmany = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_many') . ' where id=:id ', array(':id' => $list['serverid']));
        if ($listmany['type'] == 1) {
            $radLat1 = deg2rad($_GPC['lat']); // deg2rad()函数将角度转换为弧度
            $radLat2 = deg2rad($listmany['lat']);
            $radLng1 = deg2rad($_GPC['lng']);
            $radLng2 = deg2rad($listmany['lng']);

            $a = $radLat1 - $radLat2;
            $b = $radLng1 - $radLng2;

            $juli = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
            $listsys = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_sys') . ' where 1 ');
            // print_r($listsys['distance']);exit;
            if ($juli > $listsys['distance']) {
                return app_error(-1, '距离活动地点太远，请到活动地点进行签出');
//                show_json(-4, '距离活动地点太远，请到活动地点进行签出');
            }
        }
        pdo_update('ewei_shop_timebank_many_log', array('status' => 3, 'endtime' => time()), array('id' => $id));

        $listmany = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_many') . ' where id=:id ', array(':id' => $list['serverid']));
        $timegetlist = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_time') . ' where id=:id ', array(':id' => $listmany['timeget']));
        $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
        $openidid = pdo_fetch($sql, $params);

        $paramss = array(':server' => $openidid['id']);
        $conditions = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_timebank_server') . '   where server=:server ' . $conditions;
        $listt = pdo_fetch($sql, $paramss);
        $listt['timeaccount'] += $timegetlist['name'];
//		pdo_update('ewei_shop_timebank_server', array('timeaccount' => $listt['timeaccount']), array('server' => $openidid['id']));
        $severss = array(
            'frequency' => $listt['frequency'] + 1,
        );
        pdo_update('ewei_shop_timebank_server', $severss, array('server' => $openidid['id']));

        if ($listt['mechanism'] > 0 && $listt['grouppass'] == 1) {
            $group = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_group') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $listt['mechanism'], ':uniacid' => $_W[uniacid]));
            pdo_update('ewei_shop_timebank_group', array('frequency' => $group['frequency'] + 1), array('id' => $group['id']));
        }
        $this->timeaccount($listmany['hostid'], $openid, $timegetlist['name'], '多人项目服务完成');
        return app_json(1, '签出成功');
    }

    /**
     * 服务者中心
     */
    public function get_members() {
        global $_W;
        global $_GPC;
        $lists = [];
        $lists['openid'] = $_GPC['openid'];
        $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  id,realname,avatar  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
        $user = pdo_fetch($sql, $params);
        $users = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where server=:server ', array(':server' => $user['id']));
        $lists['timeaccount'] = $users['timeaccount'] ? $users['timeaccount'] : "0.00";
        //$user = pdo_fetch('select id,realname,avatar from ' . tablename('ewei_shop_member') . ' where openid=:openid ', array(':openid' => $_GPC['openid']));
        $lists['realname'] = $user['realname'];
        $lists['avatar'] = $user['avatar'];
        $params = array(':openid' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  count(1)  from ' . tablename('ewei_shop_timebank_many_log') . '   where openid=:openid ' . $condition;
        $lists['countss'] = pdo_fetchcolumn($sql, $params);
        if ($users['status'] == 1) {
            $lists['status'] = "1";
        } else {
            $lists['status'] = "0";
        }

        return app_json(array('list' => $lists));
    }

    /**
     * 推荐排序 排序方式（1人气最高 2距离最近 3最近上线 4推荐排序）
     */
    public function get_orderby() {
        global $_W;
        global $_GPC;
        $lista['id'] = 1;
        $lista['name'] = '人气最高';
        $listb['id'] = 2;
        $listb['name'] = '距离最近';
        $listc['id'] = 3;
        $listc['name'] = '最近上线';
        $listd['id'] = 4;
        $listd['name'] = '推荐排序';
        $list[0] = $lista;
        $list[1] = $listb;
        $list[2] = $listc;
        $list[3] = $listd;
        return app_json(array('list' => $list));
    }

    /**
     * 日期筛选（1当天 2一周内 3一年内）
     */
    public function get_ordertime() {
        $lista['id'] = 1;
        $lista['name'] = '当天';
        $listb['id'] = 2;
        $listb['name'] = '一周内';
        $listc['id'] = 3;
        $listc['name'] = '一个月内';
        $list[0] = $lista;
        $list[1] = $listb;
        $list[2] = $listc;
        return app_json(array('list' => $list));
    }

    /**
     * 我的评价
     */
    public function get_mycomment() {
        global $_W;
        global $_GPC;
        //$lists['openid'] =$_GPC['openid'];
        $list = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_comment") . ' where  openid=:openid', ['openid' => $_GPC['openid']]);
        $i = 0;
        foreach ($list as $key => &$value) {
            $value['createtime'] = date("Y-m-d H:i", $value['createtime']);
            $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
            $condition = '';
            $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
            $openidid = pdo_fetch($sql, $params);
            $value['name'] = $openidid['realname'];
            $value['logo'] = $openidid['avatar'];
            $serverinfo = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many") . ' where  id=:id', ['id' => $value['manyid']]);
            $value['manylogo'] = $serverinfo['logo'];
            $value['manyname'] = $serverinfo['name'];
            $value['manyid'] = $value['manyid'];
            $i = 1;
        }
        $list = set_medias($list, 'manylogo');
        $list = set_medias($list, 'logo');
        if ($i == 0) {
            $lists[] = array();
            return app_json(array('list' => $lists));
        }
        return app_json(array('list' => $list));
    }

    /**
     * 我的服务订单
     */
    public function get_myorder() {
        global $_W;
        global $_GPC;


        /* $list = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_order") . ' where  member_openid=:member_openid', ['member_openid' => $_GPC['openid']]);
          $list = set_medias($list, 'serverlogo');
          return app_json(array('list' => $list)); */
        $infos = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_many_log") . ' where  openid=:openid', ['openid' => $_GPC['openid']]);
        foreach ($infos as &$info) {
            if ($info['status'] == 3) {
                $timediff = $info['endtime'] - $info['statrtime'];
                $days = intval($timediff / 86400);

                $remain = $timediff % 86400;
                $hours = intval($remain / 3600);

                $remain = $remain % 3600;
                $mins = intval($remain / 60);

                $secs = $remain % 60;
                $info['hours_min'] = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
                //$min != 0 && $info['hours_min'] .= ':'.$min;
            }
            $info['newdata'] = unserialize($info['info']);
            $info['order'] = pdo_fetch("select * from " . tablename("ewei_shop_order") . " where  id=:orderid and uniacid=:uniacid limit 1", array(":uniacid" => $_W["uniacid"], ":orderid" => $info['orderid']));
            if ($info['order']['agentid'] > 0) {
                $ag = m('member')->getMember($info['order']['agentid']);
                $info['inviter'] = $ag['nickname'];
            } else {
                $info['inviter'] = '平台';
            }
            if ($info['status'] == 0) {
                $info['ststr'] = '待审核';
            }
            if ($info['status'] == 1) {
                $info['ststr'] = '待服务';
            }
            if ($info['status'] == 2) {
                $info['ststr'] = '服务中';
            }
            if ($info['status'] == 3) {
                $info['ststr'] = '已完成';
            }
            if ($info['status'] == -1) {
                $info['ststr'] = '审核拒绝';
            }
            unset($info['newdata']['name']);
            unset($info['newdata']['mobile']);
            $member = m('member')->getMember($info['openid']);
            if ($member['mobile'] == $info['mobile']) {
                $info['signtype'] = '自主报名';
            } else {
                $info['signtype'] = '代报名';
            }
            if ($info['orderid'] < 1) {
                $info['signtype'] = '现场报名';
            }

            $info['comment'] = pdo_fetchcolumn("select count(1)  from " . tablename("ewei_shop_timebank_comment") . ' where uniacid=:uniacid and rid = ' . $info['id'], ['uniacid' => $_W['uniacid']]);
        }
        unset($info);
        foreach ($infos as &$row) {
            $export = array();
            $info['newdata'] = unserialize($row['info']);
            $export['createtime'] = date('Y-m-d H:i', $row['createtime']);
            $export['signtype'] = $row['signtype'];
            $export['inviter'] = $row['inviter'];
            $strarr = ['-1' => "拒绝", '0' => "待审核", '1' => "待服务", '2' => "服务中", '3' => "已完成"];
            $export['ststr'] = $strarr[$row['status']];
            $export['id'] = $row['id'];
            $export['name'] = $row['name'];
            $export['mobile'] = $row['mobile'];
            $export['newdata'] = $info['newdata'];
            foreach ($export['newdata'] as $nk => $nd) {
                $export[$nk] = $nd;
            }
            unset($export['newdata']['name']);
            unset($export['newdata']['mobile']);
            $exps[] = $export;
        }
        foreach ($infos as &$row) {
            $serverinfo = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many") . ' where  id=:id', ['id' => $row['serverid']]);
            if ($serverinfo['type'] == 1) {
                $serverinfo['address'] = $serverinfo['province'] . $serverinfo['city'] . $row['area'];
            } else {
                $serverinfo['address'] = "线上服务";
            }
            $row['severname'] = $serverinfo['name'];
            $row['severlogo'] = $serverinfo['logo'];
            $row['signup_time_start'] = $serverinfo['signup_time_start'];
            $row['address'] = $serverinfo['address'];
            $row['manyid'] = $serverinfo['id'];
            $timegetlist = pdo_fetch("select name from " . tablename("ewei_shop_timebank_time") . ' where  id=:id', ['id' => $serverinfo['timeget']]);
            $row['timeget'] = $timegetlist['name'];
            $hostsql = 'select  id,name  from ' . tablename('ewei_shop_timebank_host') . '   where 1 ';
            $hostlist = pdo_fetchall($hostsql);
            foreach ($hostlist as $key => &$value) {
                if ($value['id'] == $serverinfo['hostid']) {
                    $row['hostname'] = $value['name'];
                }
            }
            $row['hostid'] = $serverinfo['hostid'];
        }
        $i = 0;
        foreach ($infos as &$row) {
            if (!empty($row['severlogo'])) {
                $mseverlogo = "http://img.dfhlyl.com/" . $row['severlogo'];
            } else {
                $mseverlogo = "";
            }
            if ($_GPC['status'] == $row['status']) {
                $lists[$i]['openid'] = $_GPC['openid'];
                $lists[$i]['id'] = $row['id'];
                $lists[$i]['hostname'] = $row['hostname'];
                $lists[$i]['severname'] = $row['severname'];
                $lists[$i]['severlogo'] = $mseverlogo;
                $lists[$i]['signup_time_start'] = $row['signup_time_start'];
                $lists[$i]['address'] = $row['address'];
                $lists[$i]['timeget'] = $row['timeget'];
                $lists[$i]['status'] = $row['status'];
                $lists[$i]['ststr'] = $row['ststr'];
                $lists[$i]['manyid'] = $row['manyid'];
                if ($row['status'] == 3) {
                    $lists[$i]['hours_min'] = $row['hours_min'];
                }
                $i++;
            }
            if ($_GPC['status'] == 5) {

                $lists[$i]['openid'] = $_GPC['openid'];
                $lists[$i]['id'] = $row['id'];
                $lists[$i]['hostname'] = $row['hostname'];
                $lists[$i]['severname'] = $row['severname'];
                $lists[$i]['severlogo'] = $mseverlogo;
                $lists[$i]['signup_time_start'] = $row['signup_time_start'];
                $lists[$i]['address'] = $row['address'];
                $lists[$i]['timeget'] = $row['timeget'];
                $lists[$i]['status'] = $row['status'];
                $lists[$i]['ststr'] = $row['ststr'];
                $lists[$i]['manyid'] = $row['manyid'];
                if ($row['status'] == 3) {
                    $lists[$i]['hours_min'] = $row['hours_min'];
                }
                $i++;
            }
        }

        if ($i == 0) {
            $lists[] = array();
            return app_json(array('list' => $lists));
        }
        return app_json(array('list' => $lists));
    }

    /**
     * 判断用户是否是服务者
     */
    public function get_severstatus() {
        global $_W;
        global $_GPC;
        $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
        $member = pdo_fetch($sql, $params);
        $log = pdo_fetch("select * from " . tablename("ewei_shop_timebank_server") . ' where  server=:server', ['server' => $member['id']]);
        if ($log['status'] == 1) {
            $status = 1;

            //show_json(-2, '1已经成为服务者   2审核中  0未申请');
        } elseif (!$log) {

            $status = 0;

            //show_json(-5, '您的服务者申请正在审核中');
        } else {
            $status = 2;
            //show_json(0, '您还没授权，授权并认证成为后才能参与服务哦！！');
        }
        return app_json(array('list' => $status));
    }

    /**
     * 服务接单
     */
    public function get_ordertaking() {
        global $_W;
        global $_GPC;
        $lists['serverid'] = intval($_GPC['id']);
        $lists['openid'] = $_GPC['openid'];
        $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
        $member = pdo_fetch($sql, $params);

        $server = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many") . ' where  id=:id', ['id' => $_GPC['id']]);




        $log = pdo_fetch("select * from " . tablename("ewei_shop_timebank_server") . ' where  server=:server', ['server' => $member['id']]);
        if ($log['status'] == 1) {
            $logg = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many_log") . ' where  openid=:openid and serverid=:serverid', ['openid' => $_GPC['openid'], 'serverid' => $_GPC['id']]);
            //return app_json(array('list' => $logg));
            if ($logg['status'] == -1) {
                return app_error(-1, '已经申请的服务项目没有被通过，请联系服务需求方咨询');
            }

            /* if ($logg['status'] == 1) {
              show_json(-3, '已经申请过的服务项目不能重复申请哦');
              }
              if ($logg['status'] == 2 or $logg['status'] == 3) {
              show_json(-3, '已经申请过的服务项目不能重复申请哦');
              } */

            /* if ($logg['serverid'] == $_GPC['id']) {
              show_json(-2, '您的申请已经发布给服务需求方  请等候对方审批');
              } */
            $sermember = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_many_log') . ' where serverid=:serverid and mobile=:mobile limit 1', array(':serverid' => $_GPC['id'], ':mobile' => $log['tel']));
            $server = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many") . ' where  id=:id', ['id' => $_GPC['id']]);
            $now = time();
            if (strtotime($server['signup_time_end']) < $now) {
                app_error(-8, '报名已结束');
            }
            if ($server['periodicity'] == -1) {
                if ($sermember) {
                    //show_json(0, '该服务者已报过名');
                    return app_error(-3, '已经申请过的服务项目不能重复申请哦');
                }
            } else {
                if ($server['weekss'] == 1) {
                    $starttime = strtotime('-1 day');
                    $endtime = time();
                    $params = array(':mobile' => $log['tel'], ':serverid' => $_GPC['id']);
                    $params[':starttime'] = $starttime;
                    $params[':endtime'] = $endtime;
                    $condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';

                    $sql = 'select  count(1)  from ' . tablename('ewei_shop_timebank_many_log') . '   where mobile=:mobile and serverid=:serverid ' . $condition;
                    $listss = pdo_fetchcolumn($sql, $params);
                    if ($server['nextss'] <= $listss) {
//						show_json(-7, '该服务者已超过报名次数');
                        return app_error(-1, '已经申请过的服务项目不能重复申请哦');
                    }
                } elseif ($server['weekss'] == 7) {
                    $starttime = strtotime('-7 day');
                    $endtime = time();
                    $params = array(':mobile' => $log['tel'], ':serverid' => $_GPC['id']);
                    $params[':starttime'] = $starttime;
                    $params[':endtime'] = $endtime;
                    $condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';

                    $sql = 'select  count(1)  from ' . tablename('ewei_shop_timebank_many_log') . '   where mobile=:mobile and serverid=:serverid ' . $condition;
                    $listss = pdo_fetchcolumn($sql, $params);
                    if ($server['nextss'] <= $listss) {
                        return app_error(-7, '已经申请过的服务项目不能重复申请哦');
                    }
                } else {
                    $starttime = strtotime('-31 day');
                    $endtime = time();
                    $params = array(':mobile' => $log['tel'], ':serverid' => $_GPC['id']);
                    $params[':starttime'] = $starttime;
                    $params[':endtime'] = $endtime;
                    $condition .= ' AND createtime >= :starttime AND createtime <= :endtime ';

                    $sql = 'select  count(1)  from ' . tablename('ewei_shop_timebank_many_log') . '   where mobile=:mobile and serverid=:serverid ' . $condition;
                    $listss = pdo_fetchcolumn($sql, $params);
                    if ($server['nextss'] <= $listss) {
                        return app_error(-7, '已经申请过的服务项目不能重复申请哦');
                    }
                }
            }



            $server = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many") . ' where  id=:id', ['id' => $_GPC['id']]);
            $yb = pdo_fetchcolumn("select count(1) from " . tablename("ewei_shop_timebank_many_log") . " where   serverid=" . $_GPC['id'] . " and status !=-1");
            if ($server['limitnum'] != 0) {
                $limitnum = $server['limitnum'];  //需求人数 如果为0 则无限制
                $signnum = $yb;  //已报名人数
                if ($limitnum <= $signnum) {
                    return app_error(-6, '您申请的服务项目名额已满');
                }
            }

            $host = pdo_fetch("select * from " . tablename("ewei_shop_timebank_host") . " where id=" . $server['hostid']);
            if ($server['audit'] == 1) {
                $lists['name'] = $log['name'];
                $lists['mobile'] = $log['tel'];
                $lists['createtime'] = time();
                $lists['status'] = 0;
                $sever = pdo_fetch("select * from " . tablename("ewei_shop_timebank_server") . " where server=" . $member['id']);
                //pdo_update('ewei_shop_timebank_server', $sever, array('server' => $member['id']));
                pdo_insert('ewei_shop_timebank_many_log', $lists);
                return app_error(-4, '您的申请已经发布给服务需求方 请等候对方审批');
            } else {
                $lists['name'] = $log['name'];
                $lists['mobile'] = $log['tel'];
                $lists['createtime'] = time();
                $lists['status'] = 1;
                $sever = pdo_fetch("select * from " . tablename("ewei_shop_timebank_server") . " where server=" . $member['id']);
                //pdo_update('ewei_shop_timebank_server', $sever, array('server' => $member['id']));
                pdo_insert('ewei_shop_timebank_many_log', $lists);
                //$sever = pdo_fetch("select * from " . tablename("ims_ewei_shop_timebank_server") . " where server=" . $member['id']);
                //pdo_update('ims_ewei_shop_timebank_server', $sever, array('server' => $member['id']));
                $timegetlist = pdo_fetchall("select * from " . tablename("ewei_shop_timebank_time"));
                $temp_timegetlist = [];
                foreach ($timegetlist as $itemss) {
                    $temp_timegetlist[$itemss['id']] = $itemss['name'];
                }
                $logg = pdo_fetch("select * from " . tablename("ewei_shop_timebank_many_log") . ' where  openid=:openid and serverid=:serverid', ['openid' => $_GPC['openid'], 'serverid' => $_GPC['id']]);
                $orderdata = [
                    'serverid' => $server['id'],
                    'serverlogo' => $server['logo'],
                    'servertitle' => $server['name'],
                    "timeget" => $temp_timegetlist[$server['timeget']],
                    "timegetid" => $server['timeget'],
                    'host_name' => $host['membername'],
                    'host_mobile' => $host['membermobile'],
                    'rid' => $logg['id'],
                    'member_name' => $log['name'],
                    'member_mobile' => $log['tel'],
                    'member_openid' => $_GPC['openid'],
                    'servertype' => 1,
                    'num' => 1,
                    'paytype' => "时间余额",
                    'status' => 1,
                    'ordersn' => $ordersn = m('common')->createNO('order', 'ordersn', 'TB'),
                    'createtime' => time(),
                ];
                pdo_insert("ewei_shop_timebank_order", $orderdata);
                $sql = 'select max(id) from ' . tablename('ewei_shop_timebank_many_log') . ' where 1 ';
                $myangid = pdo_fetch($sql);
                $idd = $myangid['max(id)'];
                $sql = 'select max(id) from ' . tablename('ewei_shop_timebank_order') . ' where 1 ';
                $orderid = pdo_fetch($sql);
                $data = array(
                    'orderid' => $orderid['max(id)'],
                );
                pdo_update('ewei_shop_timebank_many_log', $data, array('id' => $idd));

                return app_error(1, '您申请的服务项目已经报名成功');
            }
        } elseif (!empty($log['cardfront']) && !empty($log['cardbehind'])) {
            return app_error(-5, '您的服务者申请正在审核中！请审核通过后再接单！');
        } else {
            return app_error(-1, '您还没授权，授权并认证成为后才能参与服务哦！！');
        }
    }

    /**
     * 服务者注册协议
     */
    public function server_agreement() {
        global $_W;
        global $_GPC;
        $data = m("common")->getSysset("agreement");
        $infos = pdo_fetch("select * from " . tablename("ewei_shop_agreement") . ' where  id=:id', ['id' => $data['timebankid']]);
        $list['status'] = $data['timebank'];
        $list['contacts'] = m("common")->html_to_images($infos["content"]);
        return app_json(array('list' => $list));
    }

    /**
     * 个人资料修改
     */
    public function setmember() {
        global $_W;
        global $_GPC;
        $data['weixin'] = $_GPC['wx'];
        $data['realname'] = $_GPC['realname'];
        $data['nickname'] = $_GPC['nickname'];
        $data['province'] = $_GPC['province'];
        $data['city'] = $_GPC['city'];
        $data['area'] = $_GPC['area'];
        $data['birthyear'] = $_GPC['birthyear'];
        $data['birthmonth'] = $_GPC['birthmonth'];
        $data['birthday'] = $_GPC['birthday'];
        pdo_update('ewei_shop_member', $data, array('openid' => $_GPC['openid']));
        return app_json(1, '修改成功');
    }

    /**
     * 成为服务者后加入机构
     */
    public function upserve() {
        global $_W;
        global $_GPC;
        $params = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition = '';
        $sql = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition;
        $member = pdo_fetch($sql, $params);
        $log = pdo_fetch("select * from " . tablename("ewei_shop_timebank_server") . ' where  server=:server', ['server' => $member['id']]);
        if (empty($log)) {
            return app_error(-1, '请先授权成为服务者再加入组织！');
        } elseif ($log['status'] == 0) {
            return app_error(-2, '您的服务者申请正在审核中！请审核通过后再加入组织！');
        } elseif ($log['status'] == -1) {
            return app_error(-3, '您的服务者申请被拒绝了！请审核通过后再加入组织！');
        } elseif ($log['status'] == 1) {
            pdo_update('ewei_shop_timebank_server', array('mechanism' => $_GPC['id']), array('openid' => $_GPC['openid']));
            return app_json(1, '加入成功');
        }
    }

    /**
     * 计算汇乐时
     * id 需求方id
     */
    public function timeaccount($id, $openid, $money, $msg) {
        global $_W;
        global $_GPC;
        $server = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_server') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $openid, ':uniacid' => $_W[uniacid]));
        $cset = m("common")->getPluginset("cset");
        $serverbili = $cset['bili'];
        $goodbili = 100 - $cset['bili'];
        $timeaccount = floatval($server['timeaccount']) + floatval($money) * floatval($goodbili / 100);
        $servermoney = floatval($server['servermoney']) + floatval($money) * floatval($serverbili / 100);
        $allmoney = floatval($server['timeaccount']) + floatval($server['servermoney']) + floatval($money);
        if ($allmoney < 0) {
            $allmoney = 0;
        }
        pdo_update('ewei_shop_timebank_server', array('timeaccount' => $timeaccount, 'serveraccount' => $servermoney), array('id' => $server['id']));
        $data['uniacid'] = $_W['uniacid'];
        $data['openid'] = $openid;
        $data['createtime'] = time();
        $data['money'] = $allmoney;
        $data['gives'] = $money;
        $data['remark'] = $msg;
        pdo_insert('ewei_shop_timebank_server_log', $data);
        if ($id > 0) {
            $host = pdo_fetch('select * from ' . tablename('ewei_shop_timebank_host') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $id, ':uniacid' => $_W[uniacid]));
            $hostmoney = $host['timeaccount'] - $money;
            if ($hostmoney < 0) {
                $hostmoney = 0;
            }
            pdo_update('ewei_shop_timebank_host', array('timeaccount' => $hostmoney), array('id' => $host['id']));
            $hostdata['uniacid'] = $_W['uniacid'];
            $hostdata['hostid'] = $id;
            $hostdata['createtime'] = time();
            $hostdata['type'] = 1; //机构
            $hostdata['money'] = $hostmoney;
            $hostdata['gives'] = 0 - $money;
            $hostdata['remark'] = $openid . '服务完成扣减';
            pdo_insert('ewei_shop_timebank_server_log', $hostdata);
        }
    }

    /**
     * 获取公告/制度
     */
    public function knowdetail() {
        global $_W;
        global $_GPC;
        if ($_GPC['type'] == 2) {
            $wlkdata = pdo_fetch('select  *  from ' . tablename('ewei_shop_timebank_rule') . '   where enabled=1 and id=:id and uniacid = :uniacid ', array('id' => $_GPC['id'], ':uniacid' => $_W['uniacid']));
            $wlkdata['file'] = $_W['attachurl_local'] . $wlkdata['file'];
        } elseif ($_GPC['type'] == 1) {
            $wlkdata = pdo_fetch('select  *  from ' . tablename('ewei_shop_timebank_know') . '   where enabled=1 and  id=:id and uniacid = :uniacid ', array('id' => $_GPC['id'], ':uniacid' => $_W['uniacid']));
        }
        $wlkdata['createtime'] = date('Y-m-d H:i:s', $wlkdata['createtime']);
        return app_json(array('wlkdata' => $wlkdata));
    }

    /**
     *  排行榜
     */
    public function rank() {
        global $_W;
        global $_GPC;
        if ($_GPC['type'] == 0) {
            $rank_cache = $this->getserverRank();
        } else {
            $rank_cache = $this->gethostRank();
        }

        $result = $rank_cache['result'];
        $first = $result[0];
//        $paiming = 0;
//        foreach ($result as $key => $val) {
//            if ($val['openid'] == $_W['openid']) {
//                $paiming += $key + 1;
//            }
//        }
//        $num = intval($_W['shopset']['rank']['num']);
//        $user['credit1'] = intval($user['credit1']);
//        $user['paiming'] = empty($paiming) ? '未上榜' : $paiming;
//        $seven = $this->creditChange($user['uid'], 'credit1', 7, $_W['openid']);
//        $user['seven'] = empty($seven) ? 0 : $seven;
//        include $this->template();
        return app_json(array('wlkdata' => $result, 'first' => $first));
    }

    protected function getserverRank($update = false) {
        global $_W;
        $rank_cache = m('cache')->getArray('server_rank');
        if (1 || empty($rank_cache) || $rank_cache['time'] < TIMESTAMP || $update) {
            $num = 50;
            $result = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_timebank_server') . (' WHERE uniacid = :uniacid AND status=1 ORDER BY timeaccount DESC LIMIT ' . $num), array(':uniacid' => $_W['uniacid']));
            foreach ($result as $key => $value) {
                $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $value['openid'], ':uniacid' => $_W[uniacid]));
                $result[$key]['avatar'] = $member['avatar'];
            }
            $result = set_medias($result, 'avatar');
            foreach ($result as &$item) {
                $item['timeaccount'] = floatval($item['timeaccount']);
            }
            unset($item);
            $aa = $result;
            usort($result, 'ranksort');
            $result = array_slice($result, 0, $num);
            m('cache')->set('server_rank', array('time' => TIMESTAMP + 3600, 'result' => $result));
        }
        return $rank_cache;
    }

    protected function gethostRank($update = false) {
        global $_W;
        $rank_cache = m('cache')->getArray('host_rank');
        if (empty($rank_cache) || $rank_cache['time'] < TIMESTAMP || $update) {
            $num = 50;
            $result = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_timebank_group') . ' WHERE status=1 ORDER BY timeaccount DESC LIMIT ' . $num);
            foreach ($result as &$item) {
                $severnum = pdo_fetch('select sum(frequency) as frequency from ' . tablename('ewei_shop_timebank_server') . ' where mechanism=:mechanism and uniacid=:uniacid   limit 1', array(':mechanism' => $item['id'], ':uniacid' => $_W[uniacid]));
                $item['severnum'] = $severnum['frequency'];
            }
            $result = set_medias($result, 'logo');
            usort($result, 'ranksort');
            $result = array_slice($result, 0, $num);
            m('cache')->set('host_rank', array('time' => TIMESTAMP + 3600, 'result' => $result));
        }
        return $rank_cache;
    }

}

?>
