<?php



//lcj 20190826
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Index_EweiShopV2Page extends AppMobilePage {

    protected $member;

    public function __construct() {
        global $_W;
        parent::__construct();
        global $_GPC;
        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid=:openid or openid_wa=:openid and uniacid=:uniacid   limit 1', array(':openid' => $_GPC['openid'], ':uniacid' => $_W[uniacid]));

        $member = set_medias($member, 'avatar');
        if(!empty($member['status']) && !empty($member['isagent'])){
            $agentLevel = p('commission')->getLevel($member['openid']);
            $set = p('commission')->getSet();
            if (empty($agentLevel['id'])) {
                $agentLevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
            }
            $member['levelname']=$agentLevel['levelname'];
        }

        $member['credit1'] = m('member')->getCredit($member['openid'],'credit1');
        $member['credit2'] = m('member')->getCredit($member['openid'],'credit2');
        $member['balance'] = m('member')->getBalance($member['openid']);
        $member['gecoupon'] = m('member')->getCredit($member['openid'],'gecoupon');
        $member['merge_sum'] = $member['credit2']+ $member['balance'];
        $this->member = $member;
    }

    /**
     * 用户注册
     */
    public function register() {
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
                return app_error(-1, '请输入验证码');
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
            if (!(empty($member)) ) {
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
            
            $openid = 'groupearn_user_' . $_W['uniacid'] . '_' . $account;
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
     * 用户登录
     */
    public function login() {
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
        //模拟登入
//        $member = m('member')->getMember('sns_wa_oCtCJ5cs_qCny5yFlUMAJlzwZwu0');//皮鞋男
//        $member = m('member')->getMember('sns_wa_oCtCJ5Y6dg_NTFKaAq4OuDKcrSZM');//郭
//        $member = m('member')->getMember('ophPm0tuvXD47AJdzbvVGzAzl5xw');//骇客猪
//        return app_json($member);

//        $member = m('member')->getMember('sns_wa_oCtCJ5cs_qCny5yFlUMAJlzwZwu0');
//          return app_json($member);


        $member = pdo_fetch('select id,openid,mobile,pwd,salt from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(':mobile' => $account, ':uniacid' => $_W['uniacid']));
        if (empty($member)) {
            return app_error(-1, '用户不存在');
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
      public function loginh5() {
            global $_W;
            global $_GPC;
//            $member = m('member')->getMember('ophPm0u9bnifm4CrObqQIwv1GQIg');//王博
//            return app_json($member);
            $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $_GPC['openid2'], ':uniacid' => $_W['uniacid']));
            if (empty($member)) {
                  return app_error(-1, '用户不存在');
            } else {
                  $member['error'] = 0;
                  $member['message'] = '登录成功';
                  $GLOBALS['_W']['openid'] = $_W['openid'] = $member['openid'];
                  return app_json($member);

            }
      }
      public function smslogin() {
            global $_W;
            global $_GPC;
            $data = array();
            $mobile = trim($_GPC['mobile']);
            $code = trim($_GPC['code']);

            if (empty($code)) {
                  return app_error(-1, '验证码不能为空');
            }
            if (empty($mobile)) {
                  return app_error(-1, '帐号不能为空');
            }


            $member = pdo_fetch('select id,openid,mobile,pwd,salt from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(':mobile' => $mobile, ':uniacid' => $_W['uniacid']));
            if (empty($member)) {
                  return app_error(-1, '用户不存在');
            } else {
                  $key = '__ewei_shopv2_member_verifycodesession_' . $_W['uniacid'] . '_' . $mobile;
                  $key_time = '__ewei_shopv2_member_verifycodesendtime_' . $_W['uniacid'];
                  $sendcode = m('cache')->get($key);
                  $sendtime = m('cache')->get($key_time);
                  if (!isset($sendcode) || $sendcode !== $code || !isset($sendtime) || $sendtime + 600 < time()) {
                        return app_error(-1, '验证码错误或已过期');
                  }
                  return app_json($member);
            }
      }

    public function getmember() {
        global $_W;
        global $_GPC;
        $member = $this->member;

        //获取用户的订单信息
          $ordernum[0] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupearn=1 and status=0  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
          $ordernum[1] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupearn=1 and status=1  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
          $ordernum[2] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupearn=1 and status=2  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
          $ordernum[3] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupearn=1 and status=3  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));

        return app_json($member);
    }
      public function getordernum() {
            global $_W;
            global $_GPC;
            $member = $this->member;

            //获取用户的订单信息
            $ordernum[0] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupearn=1 and status=0  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            $ordernum[1] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupearn=1 and status=1  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            $ordernum[2] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupearn=1 and status=2  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            $ordernum[3] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupearn=1 and status=3  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));

            return app_json(array('ordernum'=>$ordernum));
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
        if ($_GPC['delete']==1){
              if (empty($address)){
                    return app_error(1, '地址未找到！');
              }
              pdo_delete('ewei_shop_member_address', array('id'=>$address['id']));
              return app_json('删除成功！');

        }
        if ($_GPC['submit'] == 1) {
            $data['address'] = trim($_GPC['address']);
            if (empty($data['address'])){
                  return app_error(1, '请输入详细地址！');
            }
            $data['realname'] = trim($_GPC['name']);
              if (empty($data['realname'])){
                    return app_error(1, '请输入姓名！');
              }
            $data['mobile'] = trim($_GPC['mobile']);
              if (empty($data['mobile'])){
                    return app_error(1, '请输入电话！');
              }
            $data['province'] = trim($_GPC['province']);
              if (empty($data['province'])){
                    return app_error(1, '请选择省份！');
              }
            $data['city'] = trim($_GPC['city']);
            $data['area'] = trim($_GPC['area']);
            $data['street'] = trim($_GPC['street']);
            $data['openid'] = $member['openid'];
            $data['uniacid'] = $_W['uniacid'];
            $data['lat'] = $_GPC['lat'];
            $data['lng'] = $_GPC['lng'];
            $data['isdefault']=0;
            if ($_GPC['isdefault']!='false') {
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


      /**
       * 创建订单
       */
      public function create() {
            global $_W;
            global $_GPC;
            $num = max(1, $_GPC['num']);

            $id = intval($_GPC['id']);

            $member = $this->member;

            $credit = $member['credit1'];
            $money = $member['credit2'];

            if (empty($id)) {
                  return NULL;
            }

            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $_GPC['id'], ':uniacid' => $_W[uniacid]));

            if ($_GPC['option_id']>0){
               $groups_option = pdo_get('ewei_shop_groupearn_goods_option', array('id' => $_GPC['option_id']));

               if (!empty($groups_option)){
                     $goods['price'] = $groups_option['marketprice'];
                     $goods['groupearnprice'] = $groups_option['price'];
                     $goods['optiontitle'] = $groups_option['title'];
               }
            }
            if (empty($goods)) {
                  return app_error(1, '未找到商品！');
            }

            if ($goods['status']!=1) {
                  return app_error(1, '商品已下架！');

            }

            $goods = set_medias($goods, 'thumb');


            return app_json(array('goods' => $goods));

      }
      public function create_order()
      {
            global $_W;
            global $_GPC;


            $uniacid = $_W['uniacid'];
            $isverify = false;
            $goodid = intval($_GPC['id']);



            $member = $this->member;
            $openid = $member['openid'];
            $credit = array();


            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . '
				where id = :id and uniacid = :uniacid and deleted = 0 ', array(':id' => $goodid, ':uniacid' => $uniacid));

            if ($goods['status']!=1) {
                  return app_error(1, '您选择的商品已经下架，请浏览其他商品或联系商家！');
            }

            if ($goods['group_time_start']>time()){
                  return app_error(1, '您选择的商品未开始！');

            }
            if ($goods['group_time_end']<time()){
            return app_error(1, '您选择的商品已结束！');

      }

            if ($_GPC['option_id']>0){
                  $groups_option = pdo_get('ewei_shop_groupearn_goods_option', array('id' => $_GPC['option_id']));

                  if (!empty($groups_option)){
                        $goods['price'] = $groups_option['marketprice'];
                        $goods['groupearnprice'] = $groups_option['price'];
                        $goods['optiontitle'] = $groups_option['title'];
                  }
            }
            $orderolds= pdo_fetchall('select sum(og.total) as ogtotal from ' . tablename('ewei_shop_order') .' o ' . ' left join ' . tablename('ewei_shop_order_goods') . ' og on og.orderid = o.id  where o.isgroupearn=1 and og.goodsid=:id and o.openid = :openid and o.status >= :status  and o.uniacid = :uniacid', array(':id' => $goodid,':openid' => $member['openid'], ':status' => 0, ':uniacid' => $uniacid));

            $ordernum=0;
            foreach ($orderolds as $item) {
                  $ordernum+=$item['ogtotal'];
            }

            if (!empty($goods['purchaselimit']) && $goods['purchaselimit'] < $ordernum+$_GPC['total']) {
                  return app_error(1, '您已到达此商品购买上限，请浏览其他商品或联系商家！');
            }

            if ($goods['stock']-$_GPC['total'] <= 0) {
                  return app_error(1, '您选择的商品库存不足，请浏览其他商品或联系商家！');
            }

            $price = $goods['groupearnprice']*$_GPC['total'];



            $goods_price = $price;

            $price += $goods['freight'];


            if (!empty($goods['isverify'])) {
                  $isverify = true;
                  $goods['freight'] = 0;
                  $storeids = array();
                  $merchid = 0;

                  if (!empty($goods['storeids'])) {
                        $merchid = $goods['merchid'];
                        $storeids = array_merge(explode(',', $goods['storeids']), $storeids);
                  }

                  if (empty($storeids)) {
                        if (0 < $merchid) {
                              $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                        }
                        else {
                              $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                        }
                  }
                  else if (0 < $merchid) {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                  }
                  else {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                  }

                  $verifycode = 'GE' . random(8, true);

                  while (1) {
                        $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_groups_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(':verifycode' => $verifycode, ':uniacid' => $_W['uniacid']));

                        if ($count <= 0) {
                              break;
                        }

                        $verifycode = 'GE' . random(8, true);
                  }

                  $verifynum = !empty($goods['verifytype']) ? $verifynum = $goods['verifynum'] : 1;
            }
            else {
                  $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . '
				where openid=:openid and deleted=0 and id=:id  and uniacid=:uniacid limit 1', array(':uniacid' => $uniacid, ':openid' => $member['openid'],':id'=>$_GPC['aid']));
            }

            $creditdeduct = pdo_fetch('SELECT creditdeduct,groupsdeduct,credit,groupsmoney FROM' . tablename('ewei_shop_groups_set') . 'WHERE uniacid = :uniacid ', array(':uniacid' => $uniacid));

            if (intval($creditdeduct['creditdeduct'])) {
                  if (intval($creditdeduct['groupsdeduct'])) {
                        if (0 < $goods['deduct']) {
                              $credit['deductprice'] = round(intval($member['credit1']) * $creditdeduct['groupsmoney'], 2);

                              if ($price <= $credit['deductprice']) {
                                    $credit['deductprice'] = $price;
                              }

                              if ($goods['deduct'] <= $credit['deductprice']) {
                                    $credit['deductprice'] = $goods['deduct'];
                              }

                              $credit['credit'] = floor($credit['deductprice'] / $creditdeduct['groupsmoney']);

                              if ($credit['credit'] < 1) {
                                    $credit['credit'] = 0;
                                    $credit['deductprice'] = 0;
                              }

                              $credit['deductprice'] = $credit['credit'] * $creditdeduct['groupsmoney'];
                        }
                        else {
                              $credit['deductprice'] = 0;
                        }
                  }
                  else {
                        $sys_data = m('common')->getPluginset('sale');

                        if (0 < $goods['deduct']) {
                              $credit['deductprice'] = round(intval($member['credit1']) * $sys_data['money'], 2);

                              if ($price <= $credit['deductprice']) {
                                    $credit['deductprice'] = $price;
                              }

                              if ($goods['deduct'] <= $credit['deductprice']) {
                                    $credit['deductprice'] = $goods['deduct'];
                              }

                              $credit['credit'] = floor($credit['deductprice'] / $sys_data['money']);

                              if ($credit['credit'] < 1) {
                                    $credit['credit'] = 0;
                                    $credit['deductprice'] = 0;
                              }

                              $credit['deductprice'] = $credit['credit'] * $sys_data['money'];
                        }
                        else {
                              $credit['deductprice'] = 0;
                        }
                  }
            }






            $ordersn = m('common')->createNO('groups_order', 'orderno', 'CP');

            if ($_W['ispost'] == true) {
                  if (empty($_GPC['aid']) && !$isverify) {
                        return app_error(1, '请选择收货地址！');
                  }

                  if ($isverify) {
                        if (empty($_GPC['realname']) || empty($_GPC['mobile'])) {
                              return app_error(1, '联系人或联系电话不能为空！');
                        }
                  }

                  if (0 < intval($_GPC['aid']) && !$isverify) {
                        $order_address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid   limit 1', array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => intval($_GPC['aid'])));

                        if (empty($order_address)) {
                              return app_error(1, '未找到地址');
                        }

                        if (empty($order_address['province']) || empty($order_address['city'])) {
                              return app_error(1, '地址请选择省市信息');
                        }
                  }

                  $carrier['carrier_realname'] = $_GPC['realname'];
                  $carrier['carrier_mobile'] = $_GPC['mobile'];
                  $data = array('uniacid' => $_W['uniacid'],
                      'openid' => $openid,
                      'ordersn' => $ordersn,
                      'price' => $price,
                      'goodsprice' => $goods_price,
                      'dispatchprice' => $goods['freight'],
                      'status' => 0,
                      'addressid' => intval($_GPC['aid']),
                      'address' => iserializer($order_address),
                      'carrier' => iserializer($carrier),
                      'isverify' => intval($goods['isverify']),
                      'verifytype' => intval($goods['verifytype']),
                      'verifycodes' => !empty($verifycode) ? $verifycode : 0,
                      'createtime' => TIMESTAMP,
                        'remark'=>$_GPC['remark'],
                        'isgroupearn'=>1,
                        'agentid'=>$member['agentid'],
                        'agentarea'=>$member['agentarea'],

                  );

                  if ($goods['gecoupon']>0&&$goods['gecoupon_need']>0){
                        if ($member['gecoupon']<$goods['gecoupon_need']){
                              return app_error(1, '开团券不足！');

                        }else{
                              m('member')->setCredit($member['openid'], 'gecoupon', -$goods['gecoupon_need'], array(0, '购买拼赚商品扣除开团券'));

                        }
                  }


                  $order_insert = pdo_insert('ewei_shop_order', $data);

                  if (!$order_insert) {
                        return app_error(1, '生成订单失败！');
                  }

                  $orderid = pdo_insertid();


                  $pvprice = pdo_fetchcolumn('select productprice from ' . tablename('ewei_shop_goods') . ' where id=:id    limit 1', array(':id' => $goods['gid']));
                  if ($_GPC['option_id']>0){
                        $pvprice = $groups_option['marketprice'];
                  }
                        $groups_order_goods = array(
                            'uniacid' => $_W['uniacid'],
                            'goodsid' => $goodid,
                            'groupearngoodsid' => $goods['gid'],
                            'total' => $_GPC['total'],
                            'orderid' => $orderid,
                            'optionid'=>$groups_option['id'],
                            'optionname'=>$groups_option['title'],
                            'price' => $goods['groupearnprice']*$_GPC['total'],
                            'pvprice' => $pvprice,
                            'realprice' => $goods['groupearnprice']*$_GPC['total'],
                            'createtime' => TIMESTAMP,
                            'ge1' => $_GPC['total'],
                            'ge2' => $_GPC['total'],
                            'ge3' => $_GPC['total'],
                            'ge4' => $_GPC['total'],
                            'ge5' => $_GPC['total'],
                            'geself' => $_GPC['total'],
                        );
                        pdo_insert('ewei_shop_order_goods', $groups_order_goods);

                  p('commission')->groupearncalculate($orderid);
                  $order = pdo_fetch('select * from ' . tablename('ewei_shop_groups_order') . '
					where id = :id and uniacid = :uniacid ', array(':id' => $orderid, ':uniacid' => $uniacid));
                  return app_json(array( 'orderid' => $orderid));
            }


      }
      public function express()
      {
            global $_W;
            global $_GPC;
            $openid = $_W['openid'];
            $uniacid = $_W['uniacid'];
            $orderid = intval($_GPC['id']);
            $sendtype = intval($_GPC['sendtype']);
            $bundle = trim($_GPC['bundle']);
            $cycelid = intval($_GPC['cycelid']);

            if (empty($orderid)) {
                  return app_error(AppError::$OrderNotFound);
            }

            if (!empty($cycelid)) {
                  $order = pdo_fetch('select expresscom,expresssn,addressid,status,express,sendtype from ' . tablename('ewei_shop_cycelbuy_periods') . ' where id=:id and uniacid=:uniacid', array(':id' => $cycelid, ':uniacid' => $uniacid));
            }
            else {
                  $order = pdo_fetch('select expresscom,expresssn,addressid,status,express,sendtype from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

                  if (empty($order)) {
                        return app_error(AppError::$OrderNotFound);
                  }

                  if (empty($order['addressid'])) {
                        return app_error(AppError::$OrderNoExpress);
                  }

                  if ($order['status'] < 2) {
                        return app_error(AppError::$OrderNoExpress);
                  }
            }

            $bundlelist = array();
            if (!empty($order['sendtype']) && $sendtype == 0) {
                  $i = 1;

                  while ($i <= intval($order['sendtype'])) {
                        $bundlelist[$i]['code'] = chr($i + 64);
                        $bundlelist[$i]['sendtype'] = $i;
                        $bundlelist[$i]['orderid'] = $orderid;
                        $goods_arr = pdo_fetchall('select g.title,g.thumb,og.total,og.optionname as optiontitle,og.expresssn,og.express,
                    og.sendtype,og.expresscom,og.sendtime from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.sendtype = ' . $i . ' and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));

                        foreach ($goods_arr as &$goods) {
                              $goods['thumb'] = tomedia($goods['thumb']);
                        }

                        unset($goods);
                        $bundlelist[$i]['goods'] = $goods_arr;

                        if (empty($bundlelist[$i]['goods'])) {
                              unset($bundlelist[$i]);
                        }

                        ++$i;
                  }

                  $bundlelist = array_values($bundlelist);
            }

            $condition = '';

            if (0 < $sendtype) {
                  $condition = ' and og.sendtype = ' . $sendtype;
            }

            $goods = pdo_fetchall('select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,og.expresssn,og.express,
            og.sendtype,og.expresscom,og.sendtime,g.storeids from ' . tablename('ewei_shop_order_goods') . ' og ' .' left join ' . tablename("ewei_shop_groupearn_goods") . ' geg on geg.id=og.goodsid  left join ' . tablename('ewei_shop_goods') . ' g on g.id=geg.gid ' . ' where og.orderid=:orderid and og.single_refundtime=0 ' . $condition . ' and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));

            if (0 < $sendtype) {
                  $order['express'] = $goods[0]['express'];
                  $order['expresssn'] = $goods[0]['expresssn'];
                  $order['expresscom'] = $goods[0]['expresscom'];
            }

            $expresslist = m('util')->getExpressList($order['express'], $order['expresssn']);
            $status = '';

            if (!empty($expresslist)) {
                  if (strexists($expresslist[0]['step'], '已签收')) {
                        $status = '已签收';
                  }
                  else if (count($expresslist) <= 2) {
                        $status = '备货中';
                  }
                  else {
                        $status = '配送中';
                  }
            }

            return app_json(array('com' => $order['expresscom'], 'sn' => $order['expresssn'], 'status' => $status, 'count' => count($goods), 'thumb' => tomedia($goods[0]['thumb']),'title'=>$goods[0]['title'], 'expresslist' => $expresslist, 'bundlelist' => $bundlelist));
      }
      public function get_option()
      {
            global $_W;
            global $_GPC;
            $specArr = $_GPC['spec_id'];
            $specArr =explode(',',$specArr);
            asort($specArr);
            $groups_goods_id = $_GPC['groups_goods_id'];

            if (!empty($specArr)) {
                  $spec_id = implode('_', $specArr);
                  $goods_option = pdo_get('ewei_shop_groupearn_goods_option', array('groups_goods_id' => $groups_goods_id, 'specs' => $spec_id, 'uniacid' => $_W['uniacid']));
                  return app_json(array('data' => $goods_option));
            }
      }




      public function goodsdetail(){
            global $_W;
            global $_GPC;
            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $_GPC['id'], ':uniacid' => $_W[uniacid]));
//            $goods['thumb'] = tomedia($goods['thumb']);
            $goods['thumb'] = iunserializer($goods['thumb_url']);
            foreach ($goods['thumb'] as $good) {
                  $img['src'] = tomedia($good);
                  $goods['images'][] = $img;
            }
            unset($good);
            $data = pdo_fetch('select id,deleted from ' . tablename('ewei_shop_member_groupearn_favorite') . ' where uniacid=:uniacid and goodsid=:id and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':id' => $_GPC['id']));
            if(empty($data)){
                  $goods['isfavorite']=false;
            }else{
                  if(empty($data['deleted'])){
                        $goods['isfavorite']=true;
                  }else{
                        $goods['isfavorite']=false;
                  }
            }

            $shopgoods = pdo_fetch('select * from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $goods['gid'], ':uniacid' => $_W[uniacid]));

            $goods['shopgoods'] = $shopgoods;


            $specs = array();

            if (!empty($goods['more_spec'])) {

                  $specArr = pdo_getall('ewei_shop_goods_spec', array('goodsid' => $goods['gid'], 'uniacid' => $_W['uniacid']), array('id', 'title'), '', array('displayorder' => 'desc'));
                  foreach ($specArr as $k => $v) {
                        $specArr[$k]['item'] = pdo_getall('ewei_shop_goods_spec_item', array('uniacid' => $_W['uniacid'], 'specid' => $v['id']), array('id', 'specid', 'title', 'thumb'), '', array('displayorder' => 'desc'));
                        foreach ($specArr[$k]['item'] as &$item) {
                              $item['status']='';
                        }
                        unset($item);

                  }
                  $goods['specArr'] = $specArr;
            }




            $member = $this->member;

            return app_json(array('goods' => $goods,'member'=>$member));

      }
      public function agent(){
            global $_W;
            global $_GPC;
            $agent = m('member')->getMember($_GPC['agentid']);
            $member = $this->member;
            if ($member['agentid']>0){
                  return app_error('已有上级');
            }
            if ($member['id'] == $agent['id']){
                  return app_error('上级不能是自己');
            }
            if ($agent['status']!=1){
                  return app_error('上级不是分销商');
            }
            pdo_update('ewei_shop_member', array('agentid' => $agent['id']), array('id' => $member['id'],'uniacid' => $_W['uniacid']));
            return app_json(1);

      }

      public function category(){
            global $_W;
            global $_GPC;
            $cate =   pdo_fetchall('select * from ' . tablename('ewei_shop_groupearn_category') . ' where enabled=:enabled and uniacid=:uniacid  ', array(':enabled' => 1, ':uniacid' => $_W[uniacid]));
            $list[]=array('state'=>-1,'text'=>'推荐');
            foreach ($cate as $c){
                  $item['state'] =$c['id'];
                  $item['text'] =$c['name'];
                  $list[]=$item;
            }
            $adv = $this->adv();
            return app_json(array('cate' => $list,'adv'=>$adv));
    }
      public function adv(){
            global $_W;
            global $_GPC;
            $adv =   pdo_fetchall('select * from ' . tablename('ewei_shop_groupearn_adv') . ' where enabled=:enabled and uniacid=:uniacid  ', array(':enabled' => 1, ':uniacid' => $_W[uniacid]));
            foreach ($adv as $c){
                  $item['image'] =tomedia($c['thumb']);
                  $item['title'] =$c['title'];
                  $item['link'] =$c['link'];
                  $list[]=$item;
            }
            return $list;
      }
      public function getgoods(){
            global $_W;
            global $_GPC;
            $pindex = max(1, intval($_GPC['home']));
            $psize = 5;

            $timer = time();

          $condition = '  and status=:status and uniacid=:uniacid and group_time_end >= ' . time() . ' and  group_time_start <=' . time();
          $params = array(':status' => 1, ':uniacid' => $_W['uniacid']);


          if (!empty($_GPC['keyword'])) {
              $_GPC['keyword'] = trim($_GPC['keyword']);
              $condition .= ' and title  like :keyword';
              $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
          }elseif(!empty($_GPC['cateid'])){
                if ($_GPC['cateid']==-1){
                      $orderby=' order by displayorder desc, sales desc,createtime desc';
                }else{
                      $cateid = $_GPC['cateid'];
                      $condition.=' and category=:category';
                      $params['category']=$cateid;
                      $orderby =' order by createtime desc';
                }

          }


            $list =   pdo_fetchall('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where 1 ' .$condition.$orderby. '  LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
            $total = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupearn_goods') . ' where 1 ' . $condition . ' order by createtime desc  ', $params);

            foreach ($list as &$item){
                  $item['thumb']=tomedia($item['thumb']);
                  $item['price'] = pdo_fetchcolumn('select marketprice from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['gid'], ':uniacid' => $_W[uniacid]));
            }
            unset($item);
          return app_json(array('list' => $list, 'total' => $total, 'pagesize' => $psize));
      }
      public function getorder(){
            global $_W;
            global $_GPC;
            $member = $this->member;
            $status = $_GPC['status'];
            $pindex = max(1, intval($_GPC['home']));
            $psize = 5;
//            $condition = ' and openid=:openid  and uniacid=:uniacid ';
            $condition = ' and o.openid=:openid  and o.uniacid=:uniacid ';
            $params = array(':openid' => $member['openid'], ':uniacid' => $_W['uniacid']);
            if($status=='all'){

            }elseif ($status=='group'){
                  $condition = ' and o.openid=:openid  and o.uniacid=:uniacid and o.status=1 and gb.success=0 and o.isgroupearn=2 ';
                  $params = array(':openid' => $member['openid'], ':uniacid' => $_W['uniacid']);
           }else{
                  $condition.=' and o.status=:status';
                  if ($status=='1') {
                      $condition.=' and (o.isgroupearn != 2 or (o.isgroupearn = 2 AND gb.success = 1))';
                  }
                  $params[':status']=$status;
            }


            $list =   pdo_fetchall('select o.*,gb.success from ' . tablename('ewei_shop_order') .' o ' . ' left join ' . tablename('ewei_shop_groupbuy_order') . ' gb on gb.oriorderid = o.id  where 1 ' .$condition. ' order by o.createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
            $total = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order')  .' o ' . ' left join ' . tablename('ewei_shop_groupbuy_order') . ' gb on gb.oriorderid = o.id  where 1 ' . $condition . ' order by o.createtime desc  ', $params);


         /*   if ($status=='group'){

            }else{
                  $list =   pdo_fetchall('select * from ' . tablename('ewei_shop_order') . ' where 1 ' .$condition. ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
                  $total = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where 1 ' . $condition . ' order by createtime desc  ', $params);

            }*/


           foreach ($list as &$item){
                  $ordergoods = pdo_fetchall('select * from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid ', array(':orderid' => $item['id'], ':uniacid' => $_W[uniacid]));
                  foreach ($ordergoods as $g){
                        if ($item['isgroupearn']==0){
                              $goods = pdo_fetch('select * from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $g['goodsid'], ':uniacid' => $_W[uniacid]));
                        }
                        if ($item['isgroupearn']==1){
                              $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $g['goodsid'], ':uniacid' => $_W[uniacid]));
                        }
                        if ($item['isgroupearn']==2){
                              $groupbuyorder = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_order') . ' where oriorderid=:oriorderid and uniacid=:uniacid   limit 1', array(':oriorderid' => $item['id'], ':uniacid' => $_W[uniacid]));
                              $item['groupbuyorderid'] = $groupbuyorder['id'];
                              $item['groupbuyorder'] = $groupbuyorder;
                              $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $g['goodsid'], ':uniacid' => $_W[uniacid]));
                        }
                     $goodslist['title']=  $goods['title'];
                     $goodslist['thumb']= tomedia($goods['thumb']);
                     $goodslist['price']= $g['price'];
                     $goodslist['total']= $g['total'];
                     $goodslist['optionid']= $g['optionid'];
                     $goodslist['optionname']= $g['optionname'];
                     $item['balance_pay']= $goods['balance_pay'];
                     $item['goodslist'][]=$goodslist;
                  }
                  switch ($item['status']){
                        case -1;
                              $item['statustext']='已关闭';
                              $item['statuscolor']='#333333';
                        break;
                        case 0;
                              $item['statustext']='待付款';
                              $item['statuscolor']='#F54423';
                        break;
                        case 1;
                              $item['statustext']='买家已付款';
                              $item['statuscolor']='#333333';
                        break;
                        case 2;
                              $item['statustext']='卖家已发货';
                              $item['statuscolor']='#F54423';
                        break;
                        case 3;
                              $item['statustext']='已完成';
                              $item['statuscolor']='#333333';
                        break;
                        case 4;
                              $item['statustext']='退款中';
                              $item['statuscolor']='#333333';
                        break;
                  }

                 if ($item['status']==1&&$item['success']==0&&$item['isgroupearn']==2){
                       $item['statustext']='拼团中';
                       $item['statuscolor']='#333333';
                 }
                 if ($item['status']==1&&$item['success']==-1){
                       $item['statustext']='拼团失败';
                       $item['statuscolor']='#333333';
                 }
                 if ($item['status']==-1&&$item['success']==-1){
                       $item['statustext']='拼团已退款';
                       $item['statuscolor']='#333333';
                 }


            }
            unset($item);
            return app_json(array('list' => $list, 'total' => $total, 'pagesize' => $psize));
      }
      public function orderdetail()
      {
          global $_W;
          global $_GPC;
          $id = $_GPC['id'];
          $order =  pdo_fetch('select * from '.tablename('ewei_shop_order').' where id=:id and uniacid=:uniacid',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
          $ordergoods = pdo_fetchall('select * from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid ', array(':orderid' => $order['id'], ':uniacid' => $_W['uniacid']));
          foreach ($ordergoods as $g){
              $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $g['goodsid'], ':uniacid' => $_W[uniacid]));
              $goods['thumb']= tomedia($goods['thumb']);
              $goods['total']= $g['total'];
              $goods['price']= $g['price'];
              $data= $this->groupdone($order['id'],$goods['id']);
              foreach ($data['tuan'] as $t){
                    $order['tuan'][]=  $t;
              }
              $groupnum = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_groupearn_group') . ' where goodsid=:goodsid and openid=:openid and uniacid=:uniacid', array(':goodsid' => $goods['id'],':openid' => $order['openid'], ':uniacid' => $_W[uniacid]));
              $order['goodslist'][]=$goods;
              $order['groupnum']+=$groupnum;
              $order['allorders']+=$data['allorders'];

          }
          $address =  pdo_fetch('select * from '.tablename('ewei_shop_member_address').' where id=:id and uniacid=:uniacid',array(':id'=>$order['addressid'],':uniacid'=>$_W['uniacid']));
          $order['address']=$address['province'].' '.$address['city'].' '.$address['area'].' '.$address['address'];
          $order['realname']=$address['realname'];
          $order['mobile']=$address['mobile'];
          $order['createtime'] = date('Y-m-d H:i:s', $order['createtime']);
          $order['paytime'] = date('Y-m-d H:i:s', $order['paytime']);
          $order['tuantotal']  = count($order['tuan']);


          //退款筛选
            $canrefund = false;
            $tradeset = m('common')->getSysset('trade');
            if ($order['status'] == 1 || $order['status'] == 2) {
                  $canrefund = true;
                  if ($order['status'] == 2 && $order['price'] == $order['dispatchprice']) {
                        if (0 < $order['refundstate']) {
                              $canrefund = true;
                        }
                        else {
                              $canrefund = false;
                        }
                  }
            }
            else {
                  if ($order['status'] == 3) {
                        if ($order['isverify'] != 1 && empty($order['virtual'])) {
                              if (0 < $order['refundstate']) {
                                    $canrefund = true;
                              }
                              else {
                                    $refunddays = intval($tradeset['refunddays']);

                                    if (0 < $refunddays) {
                                          $days = intval((time() - $order['finishtime']) / 3600 / 24);

                                          if ($days <= $refunddays) {
                                                $canrefund = true;
                                          }
                                    }
                              }
                        }
                  }
            }
            $order['canrefund'] = $canrefund;
            $order['refundtext'] = ($order['status'] == 1 ? '申请退款' : '申请售后') . (!empty($order['refundstate']) ? '中' : '');
            $order['cancancelrefund'] = 0 < $order['refundstate'];

          return app_json(array('list' => $order));
      }

      public function groupdone($orderid,$goodsid){
            global $_W;
            $member = $this->member;

            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $goodsid, ':uniacid' => $_W[uniacid]));

            $selforder =  pdo_fetchall('select o.*,og.id as ogid, og.geself from ' . tablename('ewei_shop_order') .' o ' . ' left join ' . tablename('ewei_shop_order_goods') . ' og on og.orderid = o.id  where og.goodsid=:goodsid and o.openid=:openid and o.status>=1 and o.isgroupearn =1 and o.uniacid=:uniacid', array('goodsid'=>$goodsid,':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));

            if (empty($selforder)){
                  return false;
            }
            $allorders = array();
            foreach ($selforder as $item) {
                  $allorders[] = $item;
            }


            $l1order =  pdo_fetchall('select o.*,og.id as ogid from ' . tablename('ewei_shop_order') .' o ' . ' left join ' . tablename('ewei_shop_order_goods') . ' og on og.orderid = o.id  where og.goodsid=:goodsid and o.agentid=:agentid and o.status>=1 and o.isgroupearn =1  and o.uniacid=:uniacid ', array('goodsid'=>$goodsid,':agentid' => $member['id'], ':uniacid' => $_W[uniacid]));

            foreach ($l1order as &$item) {
                  $item['level']=1;
            }
            unset($item);



            $downs = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $member['id'], ':uniacid' => $_W[uniacid]));

            $l2order = array();
            $l2order = p('groupearn')->find_n($downs,2,$goodsid);
            $allorders = array_merge($allorders,$l1order);
            if(!empty($l2order)){
                  $allorders = array_merge($allorders,$l2order);
            }

            foreach ($downs as $down) {
                  $downs2 = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $down['id'], ':uniacid' => $_W[uniacid]));
                  $l3order = p('groupearn')->find_n($downs2,3,$goodsid);
                  $allorders = array_merge($allorders,$l3order);
                  foreach ($downs2 as $down2) {
                        $downs3 = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $down2['id'], ':uniacid' => $_W[uniacid]));
                        $l4order = p('groupearn')->find_n($downs3,4,$goodsid);
                        $allorders = array_merge($allorders,$l4order);
                        foreach ($downs3 as $down3) {
                              $downs4 = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $down3['id'], ':uniacid' => $_W[uniacid]));
                              $l5order = p('groupearn')->find_n($downs4,5,$goodsid);
                              $allorders = array_merge($allorders,$l5order);
                        }
                  }
            }





            foreach ($allorders as $a){
                 $tuan[] =  pdo_fetch('select id,avatar,nickname from ' . tablename('ewei_shop_member') . ' where openid=:openid and uniacid=:uniacid   limit 1', array(':openid' => $a['openid'], ':uniacid' => $_W[uniacid]));
            }
            $tuan =  $this->array_unique_new($tuan);
            $data['tuan']=$tuan;
            $data['allorders']=count($allorders);
            return $data;


            /*if (count($allorders)>$goods['groupnum']){
                  $finalorders = array_slice($allorders,0,$goods['groupnum']);

                  $groupinfo['uniacid'] = $_W['uniacid'];
                  $groupinfo['goodsid'] = $goodsid;
                  $groupinfo['openid'] = $member['openid'];
                  $groupinfo['createtime'] = time();
                  foreach ($finalorders as $finalorder) {
                        $groupinfo['ogids'][]=$finalorder['ogid'];

                  }
                  $groupinfo['ogids']=implode(',',$groupinfo['ogids']);

                  pdo_insert('ewei_shop_groupearn_group', $groupinfo);

                  $groupid = pdo_insertid();

                  foreach ($finalorders as $finalorder) {
                        if ($finalorder['level']==0){
                              pdo_update('ewei_shop_order_goods', array('geself' => $groupid), array('id' => $finalorder['ogid'],'uniacid' => $_W['uniacid']));
                        }
                        if ($finalorder['level']==1){
                              pdo_update('ewei_shop_order_goods', array('ge1' => $groupid), array('id' => $finalorder['ogid'],'uniacid' => $_W['uniacid']));
                        }
                        if ($finalorder['level']==2){
                              pdo_update('ewei_shop_order_goods', array('ge2' => $groupid), array('id' => $finalorder['ogid'],'uniacid' => $_W['uniacid']));
                        }
                  }



            }*/



      }
      function array_unique_new($arr){

            $t = array_map('serialize', $arr);//利用serialize()方法将数组转换为以字符串形式的一维数组

            $t = array_unique($t);//去掉重复值

            $new_arr = array_map('unserialize', $t);//然后将刚组建的一维数组转回为php值

            return $new_arr;

      }

      //拼赚分红 本月预计分红
      public function groupearn(){
            global $_W;
            global $_GPC;
            $member = $this->member;
            $year = date('Y');
            $month = intval(date('m'));
            $bonusall = p('groupearn')->getBonusData($year, $month, 0, $_W['openid']);
            $bonudata = p('groupearn')->getBonus( $member['openid'],array('ok', 'lock', 'total'));

            $wlkdata =array();
            $wlkdata['bonus_wait'] = $bonusall['aagents'][$member ['id']]['bonusmoney1'] + $bonusall['aagents'][$member ['id']]['bonusmoney2'] + $bonusall['aagents'][$member ['id']]['bonusmoney3']+ $bonusall['aagents'][$member ['id']]['bonusmoney4']+ $bonusall['aagents'][$member ['id']]['bonusmoney5']+ $bonusall['aagents'][$member ['id']]['bonusmoney6'];

            $wlkdata['groupnum'] = $bonusall['aagents'][$member ['id']]['groupnum'];
            if (empty( $wlkdata['groupnum'])){
                  $wlkdata['groupnum'] = 0;
            }

            $wlkdata['ok'] = $bonudata['ok'];
            $wlkdata['lock'] = $bonudata['lock'];
            $wlkdata['total'] = $bonudata['total'];




            return app_json(array('wlkdata' => $wlkdata,'member'=>$member));

      }
      //每个月分红明细
      public function yearinfo(){
            global $_W;
            global $_GPC;
            $member = $this->member;

            $year = $_GPC['year']?$_GPC['year']:date('Y');
//            $year =date('Y');

            $starttime = strtotime($year.'-1-1');

            $endtime = strtotime("+1 year",$starttime);



            $bonus = pdo_fetchall('select *  from ' . tablename('ewei_shop_groupearn_billp') . ' where openid=:openid  and uniacid=:uniacid and createtime >= ' . $starttime . ' and  createtime <=' . $endtime, array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));


            foreach ($bonus as &$b) {

                  $bill = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_bill') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $b['billid'], ':uniacid' => $_W[uniacid]));


                  $b['total'] = $b['commoney'] +$b['paymoney1']+$b['paymoney2']+$b['paymoney3']+$b['paymoney4']+$b['paymoney5']+$b['paymoney6'];
                  $b['month'] = $bill['month'];
                  $b['createtime'] = date('Y-m-d H:i:s',$b['createtime']);
            }
            unset($b);
            return app_json(array('wlkdata' => $bonus,'member'=>$member,'year'=>$year));

      }
      //每个月成团明细
      public function groupinfo(){
            global $_W;
            global $_GPC;
            $member = $this->member;

            $year = $_GPC['year']?$_GPC['year']:date('Y');

            $month = $_GPC['month']?$_GPC['month']:intval(date('m'));

            $starttime = strtotime($year . '-' . $month . '-1');

            $endtime = strtotime("+1 month",$starttime);


            $wlkdata =array();


            $groups = pdo_fetchall('select * from ' . tablename('ewei_shop_groupearn_group') . ' where  openid=:openid and uniacid=:uniacid  and createtime >= ' . $starttime . ' and  createtime <=' . $endtime, array(':openid'=>$member['openid'], ':uniacid' => $_W[uniacid]));


            foreach ($groups as $b) {
                      if (!isset($wlkdata[$b['goodsid']])){
                            $b['num']=1;
                            $wlkdata[$b['goodsid']] = $b;
                      }else{
                            $wlkdata[$b['goodsid']]['num']+=1;
                      }

            }
            unset($b);

            foreach ($wlkdata as &$wlkdatum) {
                  $wlkdatum['goods'] = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $wlkdatum['goodsid'], ':uniacid' => $_W[uniacid]));
                  $wlkdatum['goods']['thumb']  = tomedia($wlkdatum['goods']['thumb']);
            }
            unset($wlkdatum);


            return app_json(array('wlkdata' => $wlkdata,'member'=>$member,'year'=>$year,'month'=>$month,'groups'=>$groups));

      }
      //券点明细
      public function gecouponinfo(){
            global $_W;
            global $_GPC;
            $member = $this->member;



            $list = pdo_fetchall('select * from ' . tablename('ewei_shop_member_credit_record') . ' where  openid=:openid  and credittype = :credittype  ', array(':credittype'=>'gecoupon',':openid'=>$member['openid']));

            foreach ($list as &$item) {
                  $item['remark'] = str_replace('OPENID:','',$item['remark']);
                  $item['remark'] = str_replace($member['openid'],'',$item['remark']);
                  $item['createtime'] = date('Y-m-d H:i:s',$item['createtime']);
            }
            unset($item);


            return app_json(array('list' => $list));

      }
      public function toggle()
      {
            global $_W;
            global $_GPC;
            $id = intval($_GPC['id']);
            $isfavorite = number_format($_GPC['isfavorite']);
            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

            if (empty($goods)) {
                  show_json(0, '商品未找到');
            }

            $data = pdo_fetch('select id,deleted from ' . tablename('ewei_shop_member_groupearn_favorite') . ' where uniacid=:uniacid and goodsid=:id and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':id' => $id));

            if (empty($data)) {
                  if (!empty($isfavorite)) {
                        $data = array('uniacid' => $_W['uniacid'], 'goodsid' => $id, 'openid' => $_W['openid'], 'createtime' => time());
                        pdo_insert('ewei_shop_member_groupearn_favorite', $data);
                  }
            }
            else {
                  pdo_update('ewei_shop_member_groupearn_favorite', array('deleted' => $isfavorite ? 0 : 1), array('id' => $data['id'], 'uniacid' => $_W['uniacid']));
            }

            show_json(1, array('isfavorite' => $isfavorite));
      }
      public function getfavoritelist(){
            global $_W;
            global $_GPC;
            $pindex = max(1, intval($_GPC['home']));
            $psize = 5;

            $condition = '  and uniacid=:uniacid and deleted=0 and openid=:openid';
            $params = array(':openid' => $_W['openid'],':uniacid' => $_W['uniacid']);

            $list =   pdo_fetchall('select * from ' . tablename('ewei_shop_member_groupearn_favorite') . ' where 1 ' .$condition. ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
            $total = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_member_groupearn_favorite') . ' where 1 ' . $condition . ' order by createtime desc  ', $params);
            $goods=[];
            foreach ($list as &$item){
                $g = pdo_fetch('select * from ' . tablename('ewei_shop_groupearn_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['goodsid'], ':uniacid' => $_W[uniacid]));
                  $g['thumb']=tomedia($g['thumb']);
                  $goods[]=$g;
            }
            unset($item);
            return app_json(array('list' => $goods, 'total' => $total, 'pagesize' => $psize));
      }
      public function removefavorite()
      {
            global $_W;
            global $_GPC;
            $id = $_GPC['id'];
            pdo_update('ewei_shop_member_groupearn_favorite', array('deleted' => 1), array('openid' => $_W['openid'],'uniacid' => $_W['uniacid'],'goodsid'=>$id));
            app_json(1);
      }
      //创业中心数据
       public function commissioninfo()
       {
             global $_W;
             global $_GPC;
             $member = $this->member;

             $commember = p('commission')->getInfo( $member['openid'],array('ordercount0','total', 'ok', 'apply', 'check', 'lock', 'pay', 'wait', 'fail'));

             $bonusinfo =array();
             $bonusinfo['ok'] = $commember['commission_ok'];
             $bonusinfo['lock'] = $commember['commission_lock'];
             $bonusinfo['total'] = $commember['commission_total'];
             $bonusinfo['ordercount'] = $commember['ordercount0'];
             $bonusinfo['agentcount'] = $commember['agentcount'];
             $applynum = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_apply') . ' where 1  and mid=:mid and uniacid=:uniacid ' , array(':uniacid' => $_W['uniacid'], ':mid' => $member['id']));
             $bonusinfo['applynum'] =$applynum;
             return app_json(array('member'=>$member,'bonusinfo'=>$bonusinfo));


       }
       public function commissiondetail(){
             global $_W;
             global $_GPC;
             $member = $this->member;
             $type = $_GPC['type'];

             $list = pdo_fetchall('select * from ' . tablename('ewei_shop_member_credit_record') . ' where num>0 and openid=:openid and credittype=:credittype  and uniacid=:uniacid order by id desc', array(':openid' => $member['openid'],':credittype' => $type, ':uniacid' => $_W[uniacid]));

             foreach ($list as &$item) {
                   $item['createtime'] = date('Y-m-d H:i:s',$item['createtime']);
//                   $nedd = mb_strpos($item['remark'],'OPEN');
//                   $item['remark'] =mb_substr($item['remark'],0,$nedd);

             }
             unset($item);
             return app_json(array('list'=>$list));


       }
}

?>
