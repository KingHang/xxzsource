<?php

//lcj 20200507
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Recognition_EweiShopV2Page extends AppMobilePage {

    protected $member;

    public function __construct() {
        global $_W;
        parent::__construct();
        $this->member = m('member')->getInfo($_W['openid']);

        if ($this->iswxapp) {
            $needbind = false;
            if (empty($_W['shopset']['app']['isclose']) && !empty($_W['shopset']['app']['openbind']) || !empty($_W['shopset']['wap']['open'])) {
                $needbind = true;
            }

            if (!$needbind) {
                return app_error(AppError::$BindNotOpen);
            }
        }
    }

    public function main() {
        global $_W;
        $member = $this->member;
        $key_time = '__ewei_shopv2_member_verifycodesendtime_' . $_W['uniacid'];
        $sendtime = m('cache')->get($key_time);
        if (empty($sendtime) || $sendtime + 60 < time()) {
            $endtime = 0;
        } else {
            $endtime = 60 - (time() - $sendtime);
        }

        $memberArr = array('mobile' => $member['mobile']);
        $wapset = m('common')->getSysset('wap');
        $domain = 'https://' . $_SERVER['HTTP_HOST'];
        $verifycode_img = $domain . '/app/ewei_shopv2_api.php?i=' . $_W['uniacid'] . '&r=sms.captcha&time=' . time() . '&openid=' . $_W['openid'];
        return app_json(array('member' => $memberArr, 'binded' => !empty($member['mobile']) && !empty($member['mobileverify']) ? 1 : 0, 'endtime' => $endtime, 'smsimgcode' => $wapset['smsimgcode'], 'verifycode_img' => $verifycode_img));
    }

    /**
     * 用户登录
     */
    public function login() {
        global $_W;
        global $_GPC;
        $data = array();
        $username = trim($_GPC['username']);
        $password = trim($_GPC['password']);
        if (empty($password)) {
            return json_app(1, $data, "密码不能为空");
        }
        if (empty($username)) {
            return json_app(1, $data, "帐号不能为空");
        }
        if (!user_check(array('username' => $username))) {
            return json_app(1, $data, "用户不存在");
        }

        if (!user_check(array('username' => $username, 'password' => $password))) {
            return json_app(1, $data, "用户名或密码错误");
        }
        $account = user_single(array('username' => $username));
        $founders = explode(',', $_W['config']['setting']['founder']);

        if (!in_array($account['uid'], $founders)) {
            if ($account['status'] != 2) {
                return json_app(1, $data, "操作员已被禁用");
            }
        }

        $role = $this->we_role($account['uid']);
        if (empty($role)) {
            return json_app(1, $data, "此账号没有管理权限");
        }

        if ($role == 'operator') {
            $roleuser = pdo_fetch('SELECT id, uid, uniacid FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $account['uid']));

            if (empty($roleuser)) {
                return json_app(1, $data, "此账号没有管理权限");
            }

            $admin = 0;
        } else {
            $admin = 1;
        }
        $data['uid'] = $account['uid'];
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $account['uid']));
        if (!$tokeninfo['token']) {
            $str = md5(uniqid(md5(microtime(true)), true));  //生成一个不会重复的字符串
            $str = sha1($str);  //加密
            pdo_update('ewei_shop_perm_user', array('token' => $str), array('uid' => $account['uid']));
            $data['token'] = $str;
        } else {
            $data['token'] = $tokeninfo['token'];
        }


        $data['username'] = $account['username'];

        return json_app(0, $data, '登录成功');
    }

    /**
     * 获取角色
     * @param int $uid
     * @return bool|string
     */
    private function we_role($uid = 0) {
        global $_W;
        $founders = explode(',', $_W['config']['setting']['founder']);

        if (in_array($uid, $founders)) {
            return 'founder';
        }

        $vice_founder = pdo_fetchcolumn('SELECT COUNT(id)FROM ' . tablename('uni_account_users') . 'WHERE uid=:uid AND role=:role', array(':uid' => $uid, ':role' => 'vice_founder'));

        if (!empty($vice_founder)) {
            return 'vice_founder';
        }

        $owner = pdo_fetchcolumn('SELECT COUNT(id)FROM ' . tablename('uni_account_users') . 'WHERE uid=:uid AND role=:role', array(':uid' => $uid, ':role' => 'owner'));

        if (!empty($owner)) {
            return 'owner';
        }

        $operator = pdo_fetchcolumn('SELECT COUNT(id)FROM ' . tablename('uni_account_users') . 'WHERE uid=:uid AND role=:role', array(':uid' => $uid, ':role' => 'operator'));

        if (!empty($operator)) {
            return 'operator';
        }

        $manager = pdo_fetchcolumn('SELECT COUNT(id)FROM ' . tablename('uni_account_users') . 'WHERE uid=:uid AND role=:role', array(':uid' => $uid, ':role' => 'manager'));

        if (!empty($manager)) {
            return 'manager';
        }

        return false;
    }

    /**
     * 人脸身份管理
     */
    public function facemanage_list() {
        global $_W;
        global $_GPC;

        $data = array();
        $pluginname = 'recognition.many.main';
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));

        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {

            $isport = $_GPC['ispost'];
            $params = array();
            $page = empty($_GPC['home']) ? '' : $_GPC['home'];


            if (empty($_GPC['ispost'])) {
                $_GPC['ispost'] = 1;
            }

            $pindex = max(1, intval($page));
            $psize = 20;
            $condition = "";

            if ($_GPC['ispost'] == 1) {
                $list = pdo_fetchall('SELECT id,realname,mobile,facepic,facestatus  FROM ' . tablename('ewei_shop_member') . ' where facetime!=0 and facestatus=1' . $condition . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_member') . ' WHERE facestatus=1  ' . $condition);
            } elseif ($_GPC['ispost'] == -1) {
                $list = pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_member') . ' where  facetime!=0 and facestatus=-1' . $condition . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_member') . ' WHERE facestatus=-1  ' . $condition);
            } elseif ($_GPC['ispost'] == -2) {
                $list = pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_member') . ' where  facetime!=0 and facetoken!="" ' . $condition . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken!="" ' . $condition);
            }



            foreach ($list as $key => &$value) {
                $sequipment = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid =:duid  limit 1', array(':duid' => $_GPC['duid']));
                if ($sequipment['id']) {
                    $sstore = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_store') . ' WHERE id =:id  limit 1', array(':id' => $sequipment['sstore']));

                    $data[$key]['id'] = $value['id'];
                    $data[$key]['realname'] = $value['realname'];
                    $data[$key]['mobile'] = $value['mobile'];
                    $data[$key]['facepic'] = $value['facepic'];
                    $data[$key]['facestatus'] = $value['facestatus'];
                    $data[$key]['sstorename'] = $sstore['storename'];
                    if ($value['facestatus'] == 1) {
                        $data[$key]['statusname'] = '启用';
                    } else {
                        $data[$key]['statusname'] = '禁用';
                    }
                }
            }
            if ($data) {
                $datalist['list'] = $data;
                $datalist['total'] = $total;    //总条数
                $datalist['pindex'] = $pindex;  //当前页
                return json_app(0, $datalist, '成功');
            } else {
                return json_app(1, $datalist, "无记录");
            }
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    /**
     * 人脸编辑
     */
    public function face_edit() {
        global $_W;
        global $_GPC;
        $duid = trim($_GPC['duid']);
        $uid = trim($_GPC['uid']);
        $id = trim($_GPC['id']);
        $data = array();
        if ($id) {
            $memberinfo = m('member')->getMember($id);
            if ($memberinfo['id']) {
                $data1['realname'] = $_GPC['realname'];
                $data1['mobile'] = $_GPC['mobile'];
                $data1['facestatus'] = $_GPC['facestatus'];
                pdo_update('ewei_shop_member', $data1, array('id' => $id));
                return json_app(0, $data, '修改成功');
            } else {
                return json_app(1, $data, "没有找到该用户");
            }
        } else {

            return json_app(1, $data, "id不能为空");
        }
    }

    /**
     * 人脸删除
     */
    public function face_delete() {
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.many';
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        //print_r($tokeninfo);exit;
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {
            $id = intval($_GPC['id']);
            if ($id) {
                $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE id=:id  limit 1 ', array(':id' => $id));
                if ($info['id']) {
                    pdo_delete('ewei_shop_member', array('id' => $id));
                    return json_app(0, $data, '成功');
                } else {
                    return json_app(1, $data, "没有找到该用户");
                }
            } else {
                return json_app(1, $data, "id不能为空");
            }
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    /**
     * 人脸详情
     */
    public function face_details() {
        global $_W;
        global $_GPC;
        $duid = trim($_GPC['duid']);
        $uid = trim($_GPC['uid']);
        $id = trim($_GPC['id']);
        $data = array();
        if ($id) {
            $memberinfo = m('member')->getMember($id);
            if ($memberinfo['id']) {
                $data['id'] = $memberinfo['id'];
                $data['realname'] = $memberinfo['realname'];
                $data['mobile'] = $memberinfo['mobile'];
                $data['facestatus'] = $memberinfo['facestatus'];
                $data['facepic'] = $memberinfo['facepic'];
                //pdo_update('ewei_shop_member', $data1, array('id' => $id));
                return json_app(0, $data, '成功');
            } else {
                return json_app(1, $data, "没有找到该用户");
            }
        } else {

            return json_app(1, $data, "id不能为空");
        }
    }

    /**
     * 通道统计
     */
    public function passCount() {
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.pass.main';
        $uid = trim($_GPC['uid']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (!in_array($pluginname, $role_perms) && !in_array($pluginname, $user_perms)) {
            return json_app(1, $data, "此账号没有管理权限");
        }

        $params = array(':uniacid' => $_W['uniacid']);

        $equipmentinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid=:duid  limit 1 ', array(':duid' => $_GPC['duid']));

        if (empty($equipmentinfo['id'])) {
            return json_app(1, $data, "设备码错误");
        }

        // 初始化返回数据格式
        $datalist = [
            [
                'type' => 2,
                'logo' => 'http://img.dfhlyl.com/images/1/2021/08/kGblyL6BG9v4AfLbGlw0ywgY1Gl9gl.png',
                'typename' => '消费'
            ],
            [
                'type' => 1,
                'logo' => 'http://img.dfhlyl.com/images/1/2021/08/xzRJ8LzFRmc0cMLW5YNMG0MF78WLfM.png',
                'typename' => '签到'
            ],
            [
                'type' => 3,
                'logo' => 'http://img.dfhlyl.com/images/1/2021/08/PZvhT1jTr7neKrqHMRQk8rST7VNvHt.png',
                'typename' => '人脸登记'
            ],
            [
                'type' => 4,
                'logo' => 'http://img.dfhlyl.com/images/1/2021/08/vqdQqUzKoOeEouUUtDqQoUEtQo5qqK.png',
                'typename' => '活动'
            ]
        ];

        $select_equipment = empty($equipmentinfo['id']) ? '' : ' and equipment_id=' . intval($equipmentinfo['id']) . ' ';

        if (!empty($datalist)) {
            foreach ($datalist as $key => &$value) {
                // 今日人次
                $totay[$key] = pdo_fetchcolumn('SELECT SUM(ssum) FROM ' . tablename('ewei_shop_recognition_pass') . ' where FROM_UNIXTIME(createtime, "%Y-%m-%d") = curdate() and type = ' . $value['type'] . $select_equipment);
                $value['today'] = $totay[$key] ? $totay[$key] : '0';

                // 7日人次
                $seven[$key] = pdo_fetchcolumn('SELECT SUM(ssum) FROM ' . tablename('ewei_shop_recognition_pass') . ' where FROM_UNIXTIME(createtime, "%Y-%m-%d") > DATE_SUB(curdate(), INTERVAL -7 DAY) and type = ' . $value['type'] . $select_equipment);
                $value['seven'] = $seven[$key] ? $seven[$key] : '0';

                // 累计人次
                $total[$key] = pdo_fetchcolumn('SELECT SUM(ssum) FROM ' . tablename('ewei_shop_recognition_pass') . ' where type = ' . $value['type'] . $select_equipment);
                $value['total'] = $total[$key] ? $total[$key] : '0';

                // 服务列表
                $serverList[$key] = pdo_fetchall('SELECT id, name FROM ' . tablename('ewei_shop_recognition_pass') . ' where type = ' . $value['type'] . $select_equipment  . ' order by createtime desc LIMIT 0,3', $params);

                if (!empty($serverList[$key])) {
                    foreach ($serverList[$key] as $k => &$v) {
                        $v['today'] = '0';
                        $v['seven'] = '0';
                        $v['total'] = '0';
                    }
                }

                $value['list'] = $serverList[$key];
            }
        }

        $data['list'] = $datalist;

        return json_app(0, $data, '成功');
    }

    /**
     * 通道统计详情
     */
    public function passCountInfo() {
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.pass.main';
        $uid = trim($_GPC['uid']);
        $type = trim($_GPC['type']);
        $keyword = trim($_GPC['keyword']);
        $pcate = trim($_GPC['pcate']);

        if (!$type) {
            return json_app(1, $data, "类型不能为空");
        }

        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (!in_array($pluginname, $role_perms) && !in_array($pluginname, $user_perms)) {
            return json_app(1, $data, "此账号没有管理权限");
        }

        $params = array(':uniacid' => $_W['uniacid']);

        $page = empty($_GPC['home']) ? '' : $_GPC['home'];

        $equipmentinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid=:duid  limit 1 ', array(':duid' => $_GPC['duid']));

        if (empty($equipmentinfo['id'])) {
            return json_app(1, $data, "设备码错误");
        }

        $select_equipment = empty($equipmentinfo['id']) ? '' : ' and equipment_id=' . intval($equipmentinfo['id']) . ' ';

        $pindex = max(1, intval($page));
        $psize = 50;

        // 服务列表
        $serverList = pdo_fetchall('SELECT id, name FROM ' . tablename('ewei_shop_recognition_pass') . ' where type = ' . $type . $select_equipment  . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
        $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_recognition_pass') . ' where type = ' . $type . $select_equipment );

        if (!empty($serverList)) {
            foreach ($serverList as $key => &$value) {
                $value['today'] = '0';
                $value['seven'] = '0';
                $value['total'] = '0';
            }
        }

        $data['list'] = $serverList;
        $data['total'] = $total;    //总条数
        $data['pindex'] = $pindex;  //当前页

        return json_app(0, $data, '成功');
    }

    /**
     * 通道列表
     */
    public function pass_list() {
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.pass.main';
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {
            $isport = $_GPC['ispost'];
            $params = array(':uniacid' => $_W['uniacid']);
            $page = empty($_GPC['home']) ? '' : $_GPC['home'];
            $equipmentinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid=:duid  limit 1 ', array(':duid' => $_GPC['duid']));
            if (empty($equipmentinfo['id'])) {
                return json_app(1, $data, "设备码错误");
            }
            $select_equipment = empty($equipmentinfo['id']) ? '' : ' and equipment_id=' . intval($equipmentinfo['id']) . ' ';
            if (empty($_GPC['ispost'])) {
                $_GPC['ispost'] = 1;
            }
            $pindex = max(1, intval($page));
            $psize = 50;
            $condition = "";
            //ispost 1启用 -1禁用 -2 全部

            if ($_GPC['ispost'] == 1) {

                $list = pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_recognition_pass') . ' where status=1' . $select_equipment . $condition . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_recognition_pass') . ' WHERE status=1  ' . $select_equipment . $condition);
            } elseif ($_GPC['ispost'] == -1) {
                $list = pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_recognition_pass') . ' where status=-1' . $select_equipment . $condition . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_recognition_pass') . ' WHERE status=-1  ' . $select_equipment . $condition);
                //print_r($list);exit;
            } elseif ($_GPC['ispost'] == -2) {
                $list = pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_recognition_pass') . ' where 1' . $select_equipment . $condition . ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
                $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_recognition_pass') . ' WHERE 1' . $select_equipment . $condition);
            }


            $pager = pagination2($total, $pindex, $psize);

            $shop = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_store') . ' WHERE status =1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
            foreach ($list as $key => &$value) {
                $sstore = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_store') . ' WHERE id =:id  limit 1', array(':id' => $value['store_id']));
                $value['sstorename'] = $sstore['storename'];
                $equipment = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE id=:id  limit 1 ', array(':id' => $value['equipment_id']));
                $value['equipment_name'] = $equipment['sname'];
                $value['duid'] = $equipment['duid'];
            }
            $datalist = array();
            foreach ($list as $key => &$value) {

                $datalist[$key]['id'] = $value['id'];
                $datalist[$key]['name'] = $value['name'];
                $datalist[$key]['logo'] = $value['logo'];
                $datalist[$key]['type'] = $value['type'];
                if ($value['type'] == 1) {
                    $datalist[$key]['typename'] = '签到';
                } elseif ($value['type'] == 2) {
                    $datalist[$key]['typename'] = '消费';
                } elseif ($value['type'] == 3) {
                    $datalist[$key]['typename'] = '人脸登记';
                } else {
                    $datalist[$key]['typename'] = '活动核销';
                }
                $datalist[$key]['status'] = $value['status'];
                if ($value['status'] == 1) {
                    $datalist[$key]['statusname'] = '启用';
                } else {
                    $datalist[$key]['statusname'] = '禁用';
                }
                $datalist[$key]['ssum'] = $value['ssum'];
                $datalist[$key]['duid'] = $value['duid'];
                $datalist[$key]['equipment_name'] = $value['equipment_name'];
            }

            $datalist = set_medias($datalist, 'logo');
            if ($datalist) {
                $data['list'] = $datalist;
                $data['total'] = $total;    //总条数
                $data['pindex'] = $pindex;  //当前页
            } else {
                return json_app(1, $data, "无记录");
            }


            return json_app(0, $data, '成功');
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    /**
     * 通道详情/ 编辑
     */
    public function pass_details() {
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.many.main';
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        //print_r($tokeninfo);exit;
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {
            $id = intval($_GPC['id']);
            $shop = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_store') . ' WHERE status =1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
            $equipment = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE 1 ', array());
            $list = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_recognition_pass') . ' WHERE id=:id  limit 1 ', array(':id' => $id));


            $datalist['id'] = $list['id'];
            $datalist['name'] = $list['name'];
            $datalist['logo'] = $list['logo'];
            $datalist['type'] = $list['type'];
            if ($list['type'] == 1) {
                $datalist['typename'] = '签到';
            } elseif ($list['type'] == 2) {
                $datalist['typename'] = '消费';
                $datalist['server_id'] = $list['server_id'];
                $server_idarr = explode("-", $list['server_id']);
                $goodinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods') . ' WHERE id=:id  limit 1 ', array(':id' => $server_idarr[0]));
                $datalist['server_name'] = $goodinfo['title'];
                if (!empty($server_idarr[1])) {
                    $shopgg = pdo_fetch('SELECT title FROM ' . tablename('ewei_shop_goods_option') . ' WHERE id=:id limit 1', array(':id' => $server_idarr[1]));
                    $datalist['server_name'] = $datalist['server_name'] . '-' . $shopgg['title'];
                }
            } elseif ($list['type'] == 4) {
                $datalist['typename'] = '活动';
                $datalist['activity_id'] = $list['activity_id'];
                $shopgg = pdo_fetch('SELECT name FROM ' . tablename('ewei_shop_activity') . ' WHERE id=:id limit 1', array(':id' => $list['activity_id']));
                $datalist['activity_name'] = $shopgg['name'];
            } else {
                $datalist['typename'] = '人脸登记';
            }
            $datalist['status'] = $list['status'];
            if ($list['status'] == 1) {
                $datalist['statusname'] = '启用';
            } else {
                $datalist['statusname'] = '禁用';
            }
            $datalist['rorder'] = $list['rorder'];


            $datalist = set_medias($datalist, 'logo');
            return json_app(0, $datalist, '成功');
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    /**
     * 根据服务id查服务详情
     */
    public function server_info() {
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.many';
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        //print_r($tokeninfo);exit;
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }
        $server_id = $_GPC['server_id'];
        $server_idarr = explode("-", $server_id);
        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {
            $shop = pdo_fetch('SELECT id,thumb,title,marketprice,serverlength,cates FROM ' . tablename('ewei_shop_goods') . ' WHERE id=:id limit 1', array(':id' => $server_idarr[0]));
            if (!$shop['id']) {
                return json_app(1, $data, "未找到服务");
            }
            if (!empty($server_idarr[1])) {
                $shopgg = pdo_fetch('SELECT title FROM ' . tablename('ewei_shop_goods_option') . ' WHERE id=:id limit 1', array(':id' => $server_idarr[1]));
                $shop['title'] = $shop['title'] . '-' . $shopgg['title'];
            }
            $category = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_category') . 'WHERE id=:id LIMIT 1', array(':id' => $shop['cates']));
            $shop['categoryname'] = $category['name'];
            $shop = set_medias($shop, 'thumb');
            return json_app(0, $shop, '成功');
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    /**
     * 通道增加、修改
     */
    public function pass_post() {
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.many.main';
        $uid = trim($_GPC['uid']);
        $duid = trim($_GPC['duid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        //print_r($tokeninfo);exit;
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {
            $id = intval($_GPC['id']);
            if (empty($_GPC['name'])) {
                return json_app(1, $data, "通道名称不能为空");
            }
            $data1['rorder'] = $_GPC['rorder'];
            $data1['name'] = $_GPC['name'];
            $data1['logo'] = $_GPC['logo'];
            $data1['type'] = $_GPC['type'];
            if ($_GPC['type'] == 2) {
                $data1['server_id'] = $_GPC['server_id'];
            }
            if ($_GPC['type'] == 4) {
                $data1['activity_id'] = $_GPC['activity_id'];
            }

            $equipment = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE status=1 and duid=:duid limit 1 ', array(':duid' => $_GPC['duid']));
            if (empty($equipment)) {
                return json_app(1, $data, "请先在设置中开启本机设置");
            }
            $data1['store_id'] = $equipment['sstore'];
            $data1['equipment_id'] = $equipment['id'];
            $data1['status'] = $_GPC['status'];
            if (empty($id)) {
                $data1['createtime'] = time();
                pdo_insert('ewei_shop_recognition_pass', $data1);
                $sql = 'select max(id) from ' . tablename('ewei_shop_recognition_pass') . ' where 1 ';
                $myangid = pdo_fetch($sql);
                $idd['id'] = $myangid['max(id)'];

                return json_app(0, $idd, '增加成功');
            } else {

                unset($data1["createtime"]);

                pdo_update('ewei_shop_recognition_pass', $data1, array('id' => $id));
                $idd['id'] = $id;
                return json_app(0, $idd, '修改成功');
            }
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    /**
     * 门店列表（只有id和名称）
     */
    public function store_list() {
        global $_W;
        global $_GPC;
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.many';
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        //print_r($tokeninfo);exit;
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {
            $shop = pdo_fetchall('SELECT id,storename FROM ' . tablename('ewei_shop_store') . ' WHERE status =1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
            return json_app(0, $shop, '成功');
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    /**
     * 删除通道
     */
    public function pass_delete() {

        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.many';
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        //print_r($tokeninfo);exit;
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }

        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {
            $id = intval($_GPC['id']);
            if ($id) {
                $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_recognition_pass') . ' WHERE id=:id  limit 1 ', array(':id' => $id));
                if ($info) {
                    pdo_delete('ewei_shop_recognition_pass', array('id' => $id));
                    return json_app(0, $data, '成功');
                } else {
                    return json_app(1, $data, "没有找到该通道");
                }
            } else {
                return json_app(1, $data, "通道id不能为空");
            }
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    /**
     * 人脸录入
     */
    public function face_info() {
        global $_W;
        global $_GPC;
        $duid = trim($_GPC['duid']);
        $pid = trim($_GPC['pid']);
        $data1 = array();
        $memberinfo = m('member')->getMobileMember($_GPC['mobile']);
        $equipment = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid=:duid limit 1 ', array(':duid' => $_GPC['duid']));
        $data1['uniacid'] = $_W['uniacid'];
        $data1['equipment_id'] = $equipment['id'];   //关联设备
        $data1['realname'] = $_GPC['realname'];
        $data1['mobile'] = $_GPC['mobile'];
        $data1['agentarea'] = $_GPC['agentarea'];
        $data1['gender'] = $_GPC['gender'];   //1男  2女
        $data1['birthyear'] = $_GPC['birthyear'];   //生日年
        $data1['birthmonth'] = $_GPC['birthmonth'];    //生日月
        $data1['birthday'] = $_GPC['birthday'];    //生日天
        $data1['province'] = $_GPC['province'];    //省
        $data1['city'] = $_GPC['city'];    //市
        $data1['area'] = $_GPC['area'];    //区    
        $data1['facetoken'] = $_GPC['facetoken'];
        $data1['facepic'] = $_GPC['facepic'];
        $data1['mobileverify'] = 1; //1-手机号已认证
        
        $data1['facetime'] = time();
        if (!$_GPC['realname']) {
            return json_app(1, $data, '姓名不能为空');
        }
        if (!$_GPC['mobile']) {
            return json_app(1, $data, '手机号不能为空');
        }

        //print_r($memberinfo);exit;
        if (intval($memberinfo['id']) > 0) {
            $data1['facestatus'] = 1;
            pdo_update('ewei_shop_member', $data1, array('id' => $memberinfo['id']));
            $data1['mid'] = $memberinfo['id'];
            pdo_query('update ' . tablename('ewei_shop_recognition_pass') . ' set ssum=ssum+1 where id=' . $pid);
            $this->addMemberPush($data1);
            return json_app(0, $data1, '修改成功');
        } else {
            $ch = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $head = 'ox3E-0' . substr($ch, mt_rand(0, 31), 1);
            $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
            $length = 21;
            $string = '';
            for ($i = $length; $i > 0; $i--) {
                $string .= $char[mt_rand(0, strlen($char) - 1)];
            }
            $data1['openid'] = $head . $string;
            $data1['createtime'] = time();
            //print_r($data1);exit;
            pdo_insert('ewei_shop_member', $data1);
            $newmid = pdo_insertid();
            $data1['mid'] = $newmid;
            pdo_query('update ' . tablename('ewei_shop_recognition_pass') . ' set ssum=ssum+1 where id=' . $pid);
            /* $sql = 'select max(id) from ' . tablename('ewei_shop_recognition_pass') . ' where 1 ';
              $myangid = pdo_fetch($sql);
              $idd['id'] = $myangid['max(id)']; */
            $this->addMemberPush($data1);
            return json_app(0, $data1, '增加成功');
        }
    }

    /**
     * 上传图片
     */
    public function upload() {
        global $_W;
        global $_GPC;
        load()->func('file');
        $field = isset($_GPC['file']) ? $_GPC['file'] : 'file';

        if (!empty($_FILES[$field]['name'])) {
            //
            if (is_array($_FILES[$field]['name'])) {
                $files = array();

                foreach ($_FILES[$field]['name'] as $key => $name) {
                    $file = array('name' => $name, 'type' => $_FILES[$field]['type'][$key], 'tmp_name' => $_FILES[$field]['tmp_name'][$key], 'error' => $_FILES[$field]['error'][$key], 'size' => $_FILES[$field]['size'][$key]);

                    $ret = $this->uploadFile($file);

                    if ($ret['status'] == 'error') {
                        $ret = array('status' => 0);
                    } else {
                        $ret = array('status' => 1, 'filename' => $ret['path'], 'url' => trim($_W['attachurl'] . $ret['filename']));
                    }

                    $files[] = $ret;
                }
                return json_app(0, $files, '成功');
                //return app_json(array('list' => $files));
            }

            $result = $this->uploadFile($_FILES[$field]);

            if ($result['status'] == 'error') {
                return json_app(1, $data, "上传失败");
                return show_json(-1, '上传失败！');
            }

            $files = array(
                array('status' => 1, 'url' => trim($_W['attachurl'] . $result['filename']), 'filename' => $result['filename'])
            );
            return json_app(0, $files, '成功');
            // return app_json(array('files' => $files));
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

    /**
     * 签到
     */
    public function dosign() {
        global $_W;
        global $_GPC;
        $pid = $_GPC['pid'];
        $data = array();
        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken=:facetoken  limit 1 ', array(':facetoken' => $_GPC['facetoken']));
        if (!$info['id']) {
            return json_app(-1, $data, "用户token错误");
        }

        $equipmentinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid=:duid  limit 1 ', array(':duid' => $_GPC['duid']));
        if (empty($equipmentinfo['id'])) {
            return json_app(1, $data, "设备码错误");
        }

        $psign = p('sign');
        $set = $psign->getSet();

        if (empty($set['isopen'])) {
            return json_app(-1, $data, $set['textcredit'] . $set['textsign'] . '未开启!');
            //app_error($set['textcredit'] . $set['textsign'] . '未开启!');
        }

        $date = "";
        $date = ($date == 'null' ? '' : $date);
        $signinfo = $psign->getSign($date);


        $condition = '';

        if (!empty($set['cycle'])) {
            $month_start = mktime(0, 0, 0, date('m'), 1, date('Y'));
            $month_end = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
            $condition .= ' and `time` between ' . $month_start . ' and ' . $month_end . ' ';
        }

        $records = pdo_fetchall('select * from ' . tablename('ewei_shop_sign_records') . ' where openid=:openid and `type`=0 and uniacid=:uniacid ' . $condition . ' order by `time` desc ', array(':uniacid' => $_W['uniacid'], ':openid' => $info['openid']));

        //清除重复签到数据
        $ddata = array();
        foreach ($records as $rk => $rv) {
            $cachetime = date("Y-m-d", $rv['time']);
            if ($rv['type'] == 0) {
                if (in_array($cachetime, $ddata)) {
                    pdo_query("DELETE FROM " . tablename("ewei_shop_sign_records") . " WHERE `time`='{$rv['time']}' and `openid`='{$rv['openid']}' and `uniacid`='{$_W['uniacid']}' LIMIT 1");
                } else {
                    $ddata[] = $cachetime;
                }
            }
        }


        $signed = 0;
        $orderindex = 0;
        $order = array();
        $orderday = 0;

        if (!empty($records)) {
            foreach ($records as $key => $item) {
                $day = date('Y-m-d', $item['time']);
                $today = date('Y-m-d', time());
                if (empty($date) && $day == $today) {
                    $signed = 1;
                }

                if (!empty($date) && $day == $date) {
                    $signed = 1;
                }

                if (1 < count($records) && $key == 0) {
                    if (date('Y-m-d', $records[$key + 1]['time']) == date('Y-m-d', strtotime('-1 day'))) {
                        ++$order[$orderindex];
                    }
                }

                $dday = date('d', $item['time']);
                $pday = date('d', isset($records[$key + 1]['time']) ? $records[$key + 1]['time'] : 0);

                if ($dday - $pday == 1) {
                    ++$order[$orderindex];
                } else {
                    if ($dday == 1 && date('d', isset($records[$key + 1]['time']) ? $records[$key + 1]['time'] : 0) == date('t', strtotime('-1 month', $item['time']))) {
                        ++$order[$orderindex];
                    } else {
                        ++$orderindex;
                        ++$order[$orderindex];
                    }
                }

                if ($this->dateplus($day, $orderday) == $this->dateminus($today, 1)) {
                    ++$orderday;
                }
            }
        }

        $signinfo = array('order' => empty($order) ? 0 : max($order), 'orderday' => empty($signed) ? $orderday : $orderday + 1, 'sum' => count($records), 'signed' => $signed);




        if (!empty($date)) {
            $datemonth = date('Y-m', strtotime($date));
            $thismonth = date('Y-m', time());

            if ($datemonth < $thismonth) {
                return json_app(-1, $data, $set['textsign'] . '月份小于当前月份，不可补签!');
                //app_error($set['textsign'] . '月份小于当前月份，不可补签!');
            }
        }

        if (!empty($signinfo['signed'])) {
            return json_app(-1, $data, '已经' . $set['textsign'] . '，不要重复' . $set['textsign'] . '哦~');
            //app_error('已经' . $set['textsign'] . '，不要重复' . $set['textsign'] . '哦~');
        }

        if (!empty($date) && (time() < strtotime($date))) {
            return json_app(-1, $data, "日期大于当前日期");
            //app_error($set['textsign'] . '日期大于当前日期!');
        }

        $member = m('member')->getMember($info['openid']);
        $reword_special = iunserializer($set['reword_special']);
        $credit = 0;
        if (!empty($set['reward_default_day']) && (0 < $set['reward_default_day'])) {
            $credit = $set['reward_default_day'];
            $message = (empty($date) ? '日常' . $set['textsign'] . '+' : $set['textsignold'] . '+');
            $message .= $set['reward_default_day'] . $set['textcredit'];
        }

        if (!empty($set['reward_default_first']) && (0 < $set['reward_default_first']) && empty($signinfo['sum']) && empty($date)) {
            $credit = $set['reward_default_first'];
            $message = '首次' . $set['textsign'] . '+' . $set['reward_default_first'] . $set['textcredit'];
        }

        if (!empty($reword_special) && empty($date)) {
            foreach ($reword_special as $item) {
                $day = date('Y-m-d', $item['date']);
                $today = date('Y-m-d', time());
                if (($day === $today) && !empty($item['credit'])) {
                    $credit = $credit + $item['credit'];

                    if (!empty($message)) {
                        $message .= "\r\n";
                    }

                    $message .= (empty($item['title']) ? $today : $item['title']);
                    $message .= $set['textsign'] . '+' . $item['credit'] . $set['textcredit'];
                    break;
                }
            }
        }

        if (!empty($date) && !empty($set['signold']) && (0 < $set['signold_price'])) {
            if (empty($set['signold_type'])) {
                if ($member['credit2'] < $set['signold_price']) {
                    return json_app(-1, $data, $set['textsignold'] . '失败! 您的' . $set['textmoney'] . '不足, 无法' . $set['textsignold']);
                    //app_error($set['textsignold'] . '失败! 您的' . $set['textmoney'] . '不足, 无法' . $set['textsignold']);
                }

                m('member')->setCredit($info['openid'], 'credit2', 0 - $set['signold_price'], $set['textcredit'] . $set['textsign'] . ': ' . $set['textsignold'] . '扣除' . $set['signold_price'] . $set['textmoney']);
            } else {
                if ($member['credit1'] < $set['signold_price']) {
                    return json_app(-1, $data, $set['textsignold'] . '失败! 您的' . $set['textcredit'] . '不足, 无法' . $set['textsignold']);
                    //app_error($set['textsignold'] . '失败! 您的' . $set['textcredit'] . '不足, 无法' . $set['textsignold']);
                }

                m('member')->setCredit($info['openid'], 'credit1', 0 - $set['signold_price'], $set['textcredit'] . $set['textsign'] . ': ' . $set['textsignold'] . '扣除' . $set['signold_price'] . $set['textcredit']);
            }
        }

        if (!empty($credit) && (0 < $credit)) {
            m('member')->setCredit($info['openid'], 'credit1', 0 + $credit, $set['textcredit'] . $set['textsign'] . ': ' . $message);
        }

        $arr = array('uniacid' => $_W['uniacid'], 'time' => empty($date) ? time() : strtotime($date), 'openid' => $info['openid'], 'credit' => $credit, 'log' => $message);
        pdo_insert('ewei_shop_sign_records', $arr);
        $signinfo = $psign->getSign();
        $member = m('member')->getMember($info['openid']);
        $result = array('success' => 1, 'message' => $set['textsign'] . '成功!' . $message, 'signorder' => $signinfo['orderday'], 'signsum' => $signinfo['sum'], 'addcredit' => $credit, 'credit' => intval($member['credit1']));
        $psign->updateSign($signinfo);

        pdo_query('update ' . tablename('ewei_shop_recognition_pass') . ' set ssum=ssum+1 where id=' . $pid);

        if (p('lottery')) {
            $res = p('lottery')->getLottery($member['openid'], 2, array('day' => $signinfo['orderday']));

            if ($res) {
                p('lottery')->getLotteryList($member['openid'], array('lottery_id' => $res));
            }

            $result['lottery'] = p('lottery')->check_isreward();
        } else {
            $result['lottery']['is_changes'] = 0;
        }
        $data['id'] = $member['id'];
        $data['realname'] = $member['realname'];
        $data['addcredit'] = $result['addcredit'];

        return json_app(0, $data, $result['message']);
        //app_json($result);
    }

    /**
     * 服务列表
     */
    public function sever_list() {
        global $_W;
        global $_GPC;
        $data = array();
        $pindex = max(1, intval($_GPC['home']));
        $psize = 20;
        $condition = ' WHERE g.`uniacid` = :uniacid and g.type<>9 and g.type=6';
        $params = array(':uniacid' => $_W['uniacid']);
        $condition .= ' AND g.`status` > 0 and g.`checked`=0 and g.`total`>0 and g.`deleted`=0';
        $condition .= " AND g.isknowledge=0 and g.isactivity=0 ";
        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' AND ( g.`title` LIKE :keyword ';
            $condition .= ' )';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }
        if (!empty($_GPC['cate'])) {
            $_GPC['cate'] = intval($_GPC['cate']);
            $condition .= ' AND ( FIND_IN_SET(' . $_GPC['cate'] . ',cates)<>0 OR pcate=' . $_GPC['cate'] . ' OR ccate=' . $_GPC['cate'] . ' )';
        }

        $sql = 'SELECT count(*) FROM ' . tablename('ewei_shop_goods') . 'g ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if (!empty($total)) {
            $sql = 'select g.id,g.title,g.cates,g.total,g.hasoption,g.marketprice,g.thumb,g.serverlength,IFNULL(gop.marketprice,g.marketprice) as marketprice,IFNULL(gop.title,"") as ggtitle,IFNULL(gop.id,"") as ggid from ' . tablename('ewei_shop_goods') . ' g left join ' . tablename('ewei_shop_goods_option') . ' gop on g.id=gop.goodsid ' . $condition . ' ORDER BY g.`status` DESC, g.`displayorder` DESC,
                g.`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $params);

            //print_r($list);exit;
            foreach ($list as $key => &$value) {
                $data[$key]['id'] = !empty($value['ggid']) ? $value['id'] . '-' . $value['ggid'] : $value['id'];
                $data[$key]['title'] = !empty($value['ggtitle']) ? $value['title'] . '-' . $value['ggtitle'] : $value['title'];
                $data[$key]['thumb'] = $value['thumb'];
                $data[$key]['marketprice'] = $value['marketprice'];
                $data[$key]['serverlength'] = $value['serverlength'];
                $value['allcates'] = explode(',', $value['cates']);
                $data[$key]['cates'] = $value['cates'];
                $category = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_category') . 'WHERE id=:id LIMIT 1', array(':id' => $value['cates']));
                $data[$key]['categoryname'] = $category['name'];
            }
            $data = set_medias($data, 'thumb');
            $pager = pagination2($total, $pindex, $psize);
            if ($data) {
                $data1['list'] = $data;
                $data1['total'] = $total;    //总条数
                $data1['pindex'] = $pindex;  //当前页
                return json_app(0, $data1, '成功');
            } else {
                return json_app(1, $data, "无记录");
            }
        } else {
            return json_app(1, $data, '无数据');
        }
    }

    /**
     * 根据设备编码获取默认消费通道
     */
    public function default_con() {
        global $_W;
        global $_GPC;
        $data = array();
        $uid = trim($_GPC['uid']);
        $pid = trim($_GPC['pid']);
        $equipmentinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid=:duid  limit 1 ', array(':duid' => $_GPC['duid']));
        if (empty($equipmentinfo['id'])) {
            return json_app(1, $data, "设备码错误");
        }
        $select_equipment = empty($equipmentinfo['id']) ? '' : ' and id=' . intval($pid) . ' ';


        $condition = "";

        $list = array();
        $list = pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_recognition_pass') . ' where type=2' . $select_equipment . $condition . ' order by createtime desc  ');
        $list = set_medias($list, 'logo');
        return json_app(0, $list[0], "成功");
    }

    /**
     * 消费
     */
    public function consumption() {
        global $_W;
        global $_GPC;
        $data = array();
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $facetoken = trim($_GPC['facetoken']);
        $con_id = trim($_GPC['con_id']);
        $pid = trim($_GPC['pid']);

        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken=:facetoken  limit 1 ', array(':facetoken' => $facetoken));
        if (!$info['id']) {
            return json_app(-1, $data, "用户token错误，找不到该用户");
        }
        /* $roleuser = pdo_fetch('SELECT id, uid, uniacid FROM' . tablename('ewei_shop_perm_user') . 'WHERE token=:token LIMIT 1', array(':token' => $token));
          //$memberinfo = m('member')->getMember($uid);
          if (!$roleuser['id']) {

          return json_app(-1, $data, "操作员token不正确");
          }
          if ($roleuser['uid'] != $uid) {
          return json_app(-1, $data, "操作员token与uid不匹配");
          } */
        $con_list = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_recognition_pass') . ' WHERE id=:id  limit 1 ', array(':id' => $con_id));
        $server_idarr = explode("-", $con_list['server_id']);
        if (!empty($server_idarr[1])) {
            $order_goods = pdo_fetch("select og.* from " . tablename('ewei_shop_order') . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on o.id = og.orderid " . " where og.goodsid = :goodsid  and og.optionid=:optionid and o.openid=:openid and status=2 ORDER BY o.id DESC ", array(':goodsid' => $server_idarr[0], ':optionid' => $server_idarr[1], ':openid' => $info['openid']));
        } else {
            $order_goods = pdo_fetch("select og.* from " . tablename('ewei_shop_order') . " o " . " left join  " . tablename("ewei_shop_order_goods") . " og on o.id = og.orderid " . " where og.goodsid = :goodsid  and o.openid=:openid and status=2 ORDER BY o.id DESC ", array(':goodsid' => $server_idarr[0], ':openid' => $info['openid']));
        }
        if (empty($order_goods)) {
            return json_app(-1, $data, "未查询到服务");
        }
        $orderid = $order_goods['orderid'];
        $ordergoodsid = $order_goods['id'];
        $verycodearr = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_verifygoods') . ' WHERE openid=:openid and orderid=:orderid and ordergoodsid=:ordergoodsid limit 1 ', array(':openid' => $info['openid'], ':orderid' => $orderid, ':ordergoodsid' => $ordergoodsid));
        $verifycode = $verycodearr['verifycode'];
        $result = $this->complete($verifycode, 1, '');
        $data = array();
        if (!is_error($result)) {
            pdo_query('update ' . tablename('ewei_shop_recognition_pass') . ' set ssum=ssum+1 where id=' . $pid);
            $verifygoodlogs = pdo_fetchall('select *  from ' . tablename('ewei_shop_verifygoods_log') . '    where verifygoodsid =:id  ', array(':id' => $verycodearr['id']));
            $verifynum = 0;
            foreach ($verifygoodlogs as $verifygoodlog) {
                $verifynum += intval($verifygoodlog['verifynum']);
            }
            $lastverifys = intval($verycodearr['limitnum']) - $verifynum;
            $data['orderid'] = $orderid;
            $data['number'] = $lastverifys;
            $data['realname'] = $info['realname'];
            $data['userid'] = $info['id'];
            $goodinfo = pdo_fetch('SELECT id,thumb,title,marketprice,serverlength,cates FROM ' . tablename('ewei_shop_goods') . ' WHERE id=:id limit 1', array(':id' => $server_idarr[0]));
            if (!empty($server_idarr[1])) {
                $shopgg = pdo_fetch('SELECT title FROM ' . tablename('ewei_shop_goods_option') . ' WHERE id=:id limit 1', array(':id' => $server_idarr[1]));
                $goodinfo['title'] = $goodinfo['title'] . '-' . $shopgg['title'];
            }
            $data['server_name'] = $goodinfo['title'];
            $data['server_id'] = $goodinfo['id'];
            return json_app(0, $data, "成功!剩余次数" . $lastverifys);
        } else {
            return json_app(-1, $data, $result['message']);
        }
    }

    /*  门店 */

    public function mendianList() {
        global $_W;
        global $_GPC;
        $data = array();
        $sql = 'SELECT * FROM ' . tablename('ewei_shop_store') . ' where status=1 ';
        $data = pdo_fetchall($sql);
        if (!empty($data)) {
            return json_app(0, $data, '成功');
        } else {
            return json_app(1, $data, '无数据');
        }
    }

    /*  本机设置  */

    public function doEquipmentSave() {
        global $_W;
        global $_GPC;
        $duid = $_GPC['duid'];
        $sname = $_GPC['sname'];
        $sstore = $_GPC['sstore'];

        if (empty($_GPC['sstore'])) {
            show_json(0, '未选择所属门店');
        }
        //检测所属门店信息
        $store = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_store") . " where `id`='{$_GPC['sstore']}' ");
        if (empty($store)) {
            show_json(0, '选择的门店不存在');
        }

        $status = $_GPC['status'];
        $storeinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid="' . $duid . '"');
        if ($storeinfo) {
            pdo_query("update " . tablename('ewei_shop_equipment') . '  set 
								sname = "' . $sname . '",
								snum = "' . $duid . '",
								sstore = "' . $sstore . '",
								scategory = "1",
								status = "' . $status . '",
                                                                abonus_id = "' . $store['abonus_id'] . '",
                                                                merchid = "' . $store['merchid'] . '",
								createtime = now(),
								online = 1 where duid="' . $duid . '"');
					$minfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE openid="' . $duid . '"');
					if(empty($minfo)){
						$data = array('uniacid' => $_W['uniacid'], 'nickname' => '人脸face'.$duid, 'openid' => 'face'.$duid,  'createtime' => time(), 'comefrom' => '人脸app');
						pdo_insert('ewei_shop_member', $data);
					}
        } else {
            pdo_query("insert into " . tablename('ewei_shop_equipment') . '  set 
								sname = "' . $sname . '",
								snum = "' . $duid . '",
								sstore = "' . $sstore . '",
								scategory = "1",
								duid = "' . $duid . '",
								status = "' . $status . '",
								createtime = now(),
                                                                abonus_id = "' . $store['abonus_id'] . '",
                                                                merchid = "' . $store['merchid'] . '",
								online = 1');
				$data = array('uniacid' => $_W['uniacid'], 'nickname' => '人脸face'.$duid, 'openid' => 'face'.$duid,  'createtime' => time(), 'comefrom' => '人脸app');
				pdo_insert('ewei_shop_member', $data);
        }
        return json_app(0, array(), '成功');
    }

    public function menDianDetail() {
        global $_W;
        global $_GPC;
        $storeinfo = array();
        $duid = $_GPC['duid'];
        $storeinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid="' . $duid . '"');
        $minfo = pdo_fetch('SELECT id  FROM ' . tablename('ewei_shop_member') . ' WHERE openid="face' . $duid . '"');
        $storeinfo['mid'] = $minfo['id'];
        return json_app(0, $storeinfo, '成功');
    }

    public function yunyingList() {
        global $_W;
        global $_GPC;
        $data = array();
        $sql = 'SELECT areaname FROM ' . tablename('ewei_shop_abonus_area') . ' where status=1 ';
        $data = pdo_fetchall($sql);
        if (!empty($data)) {
            return json_app(0, $data, '成功');
        } else {
            return json_app(1, $data, '无数据');
        }
    }

    public function queryServer() {
        global $_W;
        global $_GPC;
        $item = array();
        $pid = $_GPC['pid'];
        $sql = 'SELECT * FROM ' . tablename('ewei_shop_recognition_pass') . '  where id="' . $pid . '"';
        $itemPass = pdo_fetch($sql);
        if ($itemPass['type'] == 2) {
            $server_id = $itemPass['server_id'];
            $server_idarr = explode("-", $server_id);
            $item = pdo_fetch('SELECT id,thumb,title,marketprice,serverlength,cates FROM ' . tablename('ewei_shop_goods') . ' WHERE id=:id limit 1', array(':id' => $server_idarr[0]));
            if (!$item['id']) {
                return json_app(1, $item, "未找到服务");
            }
            if (!empty($server_idarr[1])) {
                $shopgg = pdo_fetch('SELECT title FROM ' . tablename('ewei_shop_goods_option') . ' WHERE id=:id limit 1', array(':id' => $server_idarr[1]));
                $item['title'] = $item['title'] . '-' . $shopgg['title'];
            }
        }
        $thumb = tomedia($item['thumb']);
        return json_app(0, array('title' => $item['title'], 'thumb' => $thumb), '成功');
    }

    public function queryYunYing() {
        global $_W;
        global $_GPC;
        $duid = $_GPC['duid'];
        $telephone = $_GPC['telephone'];
        $data = array();

        $sql = 'SELECT agentarea FROM ' . tablename('ewei_shop_member') . ' where uniacid='.$_W['uniacid'].' and mobile="'.$telephone.'"';

        $item = pdo_fetch($sql);
        if (!empty($item['agentarea'])) {
            return json_app(0, array('title' => $item['agentarea']), '成功');
        } else {
            $sql = 'SELECT s.abonus_id FROM ' . tablename('ewei_shop_equipment') . ' e left join  ' . tablename('ewei_shop_store') . '  s on e.sstore=s.id where e.duid="' . $duid . '"';
            $item = pdo_fetch($sql);
            $abonus_id = $item['abonus_id'];
            $sql = 'SELECT areaname FROM ' . tablename('ewei_shop_abonus_area') . ' where id="' . $abonus_id . '"';
            $item = pdo_fetch($sql);
            return json_app(0, array('title' => $item['areaname']), '成功');
        }
    }

    public function queryDuid() {
        global $_W;
        global $_GPC;
        $data = array();
        $sql = 'SELECT * FROM ' . tablename('ewei_shop_equipment') . ' where status=1 and duid="' . $_GPC['duid'] . '"';
        $data = pdo_fetch($sql);
        if (!empty($data)) {
            $data = array('has' => 1);
        } else {
            $data = array('has' => 0);
        }
        return json_app(0, $data, '成功');
    }

    public function allow($verifycode, $times) {
        global $_W;
        global $_GPC;
        $verifygood = pdo_fetch('select *  from ' . tablename('ewei_shop_verifygoods') . ' where uniacid=:uniacid and  verifycode=:verifycode and invalid =0  limit 1 ', array(':uniacid' => $_W['uniacid'], ':verifycode' => $verifycode));

        if (empty($verifygood)) {
            return error(1, '未查询到服务或核销码已失效,请核对核销码!');
        }

        $order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid  limit 1', array(':id' => $verifygood['orderid'], ':uniacid' => $verifygood['uniacid']));

        if (empty($order)) {
            return error(-1, '订单不存在!');
        }

        $used = 0;

        if (!empty($verifygood['limitnum'])) {
            $verifygoodlogs = pdo_fetchall('select *  from ' . tablename('ewei_shop_verifygoods_log') . '    where verifygoodsid =:id  ', array(':id' => $verifygood['id']));
            $verifynum = 0;

            foreach ($verifygoodlogs as $verifygoodlog) {
                $verifynum += intval($verifygoodlog['verifynum']);
            }

            $lastverifys = intval($verifygood['limitnum']) - $verifynum;

            if ($lastverifys < $times) {
                return error(1, '商品可核销次数不足!');
            }

            if ($lastverifys == $times) {
                $used = 1;
            }
        }

        if (empty($verifygood['limittype'])) {
            $limitdate = intval($verifygood['starttime']) + intval($verifygood['limitdays']) * 86400;
        } else {
            $limitdate = intval($verifygood['limitdate']);
        }

        if ($limitdate < time()) {
            return error(1, '该商品已过期!');
        }

        $verifygoods_detail = pdo_fetch('select vg.*,g.id as goodsid ,g.title,g.subtitle,g.thumb,vg.storeid  from ' . tablename('ewei_shop_verifygoods') . '   vg
		 inner join ' . tablename('ewei_shop_order_goods') . ' og on vg.ordergoodsid = og.id
		 inner join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id
		 where  vg.id =:id and  vg.verifycode=:verifycode and vg.uniacid=:uniacid and vg.invalid =0 limit 1', array(':id' => $verifygood['id'], ':uniacid' => $_W['uniacid'], ':verifycode' => $verifycode));
        $order['verifytype'] = 1;

        if (intval($lastverifys) <= 0) {
            $lastverifys = '不限';
        }

        return array('verifygood' => $verifygood, 'saler' => $saler, 'used' => $used, 'store' => $store, 'lastverifys' => $lastverifys, 'order' => $order, 'allgoods' => $verifygoods_detail);
    }

    public function complete($verifycode, $times, $remarks) {
        global $_W;
        global $_GPC;
        $allow = $this->allow($verifycode, $times);

        if (is_error($allow)) {
            return $allow;
        }

        extract($allow);
        $data = array('uniacid' => $_W['uniacid'], 'verifygoodsid' => $verifygood['id'], 'salerid' => $saler['id'], 'storeid' => $store['id'], 'verifynum' => $times, 'verifydate' => time(), 'remarks' => $remarks);
        pdo_insert('ewei_shop_verifygoods_log', $data);
        $logid = pdo_insertid();
        m('notice')->sendVerifygoodMessage($logid);
        pdo_query('update ' . tablename('ewei_shop_verifygoods') . ' set used=:used,codeinvalidtime=0 where id=:id', array(':id' => $verifygood['id'], ':used' => $used));

        if (!empty($verifygood['activecard'])) {
            com_run('wxcard::updateusercardbyvarifygoodid', $verifygood['id']);
        }

        $finishorderid = 0;
        $isonlyverifygood = m('order')->checkisonlyverifygoods($verifygood['orderid']);

        if ($isonlyverifygood) {
            $status = pdo_fetchcolumn('select status  from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and id=:id  limit 1 ', array(':uniacid' => $_W['uniacid'], ':id' => $verifygood['orderid']));

            $limitnum = $verifygood['limitnum'];
            $hexiaonum = pdo_fetchcolumn('select count(*)  from ' . tablename('ewei_shop_verifygoods_log') . ' where uniacid=:uniacid and verifygoodsid=:verifygoodsid  limit 1 ', array(':uniacid' => $_W['uniacid'], ':verifygoodsid' => $verifygood['id']));
            if ($status == 2 && ($limitnum == $hexiaonum)) {
                $finishorderid = $verifygood['orderid'];
                $this->finishorder($finishorderid);
            }
        }

        return array('verifygoodid' => $verifygood['id'], 'orderid' => $finishorderid);
    }

    public function finishorder($id) {
        global $_W;
        $item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_order') . ' WHERE id = :id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
        if (empty($item) || $item['status'] != 2) {
            return false;
        }

        pdo_update('ewei_shop_order', array('status' => 3, 'sendtime' => time(), 'finishtime' => time()), array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
        m('order')->fullback($item['id']);
        if (p('ccard') && !empty($item['ccardid'])) {
            p('ccard')->setBegin($item['id'], $item['ccardid']);
        }

        m('member')->upgradeLevel($item['openid'], $item['id']);
        m('order')->setStocksAndCredits($item['id'], 3);
        m('order')->setGiveBalance($item['id'], 1);
        m('notice')->sendOrderMessage($item['id']);
        com_run('printer::sendOrderMessage', $item['id']);

        if (com('coupon')) {
            com('coupon')->sendcouponsbytask($item['id']);

            if (!empty($item['couponid'])) {
                com('coupon')->backConsumeCoupon($item['id']);
            }
        }

        if (p('lineup')) {
            p('lineup')->checkOrder($item);
        }

        if (p('commission')) {
            p('commission')->checkOrderFinish($item['id']);
        }

        if (p('lottery')) {
            $res = p('lottery')->getLottery($item['openid'], 1, array('money' => $item['price'], 'paytype' => 2));

            if ($res) {
                p('lottery')->getLotteryList($item['openid'], array('lottery_id' => $res));
            }
        }

        plog('order.op.finish', '订单完成 ID: ' . $item['id'] . ' 订单号: ' . $item['ordersn']);
        return true;
    }

    public function blockList() {
        global $_W;
        global $_GPC;
        $duid = $_GPC['duid'];

        if ($_GPC['face']==1){
              $sql = 'select p.* from ' . tablename('ewei_shop_recognition_pass') . ' p left join ' . tablename('ewei_shop_equipment') . ' e on p.equipment_id=e.id where p.status=1 and (p.type=1 or p.type=3) and e.duid="' . $duid . '" order by p.type asc';

        }else{
              $sql = 'select p.* from ' . tablename('ewei_shop_recognition_pass') . ' p left join ' . tablename('ewei_shop_equipment') . ' e on p.equipment_id=e.id where p.status=1 and (p.type=2 or p.type=4) and e.duid="' . $duid . '" order by p.type asc';

        }


          $data = pdo_fetchall($sql);
        if (!empty($data)) {
            return json_app(0, $data, '成功');
        } else {
            return json_app(0, $data, '无数据');
        }
    }

    function addMemberPush($data) {
        pdo_insert('ewei_shop_member_push', $data);
    }

    function http_request($url, $data = null, $headers, $item) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_NOSIGNAL, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        // 在尝试连接时等待的秒数
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        // 最大执行时间
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $output = curl_exec($curl);
        curl_close($curl);
        if (!$output) {
            $dt = array();
            $dt['pushid'] = $item['mid'];
            $dt['uniacid'] = $item['uniacid'];
            $dt['pushtime'] = date("Y-m-d H:i:s", time());
            $dt['pushrealname'] = $item['realname'];
            $dt['pushmobile'] = $item['mobile'];
            $dt['pushcode'] = '504';
            $dt['pushmsg'] = '请求超时';
            pdo_insert('ewei_shop_member_push_log', $dt);
        } else {
            $out = json_decode($output, true);
            $dt = array();
            $dt['pushid'] = $item['mid'];
            $dt['uniacid'] = $item['uniacid'];
            $dt['pushtime'] = date("Y-m-d H:i:s", time());
            $dt['pushrealname'] = $item['realname'];
            $dt['pushmobile'] = $item['mobile'];
            $dt['pushcode'] = $out['code'];
            $dt['pushmsg'] = $out['message'];
            pdo_insert('ewei_shop_member_push_log', $dt);
            return $output;
        }
    }

    //会员绑定推送
    function memPush() {
        global $_W;
        global $_GPC;
        $hlcloudset = m("common")->getPluginset("hlcloud");

        if (!empty($_POST['home'])) {
            $pindex = $_POST['home'];
            $psize = $_POST['pageSize'];
            $sql = 'select * FROM ' . tablename('ewei_shop_member_push') . ' where (isdone=0 or isdone=2) and pnum<' . $hlcloudset['pmaxnum'] . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        } else {
            $sql = 'select * FROM ' . tablename('ewei_shop_member_push') . ' where (isdone=0 or isdone=2) and pnum<' . $hlcloudset['pmaxnum'];
        }
        $data = pdo_fetchall($sql);
        $totalPage = pdo_fetchcolumn('select count(*) FROM ' . tablename('ewei_shop_member_push') . ' where (isdone=0 or isdone=2) and pnum<' . $hlcloudset['pmaxnum']);

        if (!empty($data)) {
            foreach ($data as $item) {
                $todata = array();
                $url = "http://m.dfhlyl.com:8070/Persion/dfhl/openapi/hlb";
                $todata['mid'] = $item['mid'];
                $todata['realName'] = $item['realname'];
                $todata['mobile'] = $item['mobile'];
                $todata['sex'] = ($item['gender'] == '1' ? '00' : '01');
                $todata['birthDay'] = $item['birthyear'] . '-' . $item['birthmonth'] . '-' . $item['birthday'];
                $todata['region'] = $item['agentarea'];
                $todata['province'] = $item['province'];
                $todata['city'] = $item['city'];
                $todata['area'] = $item['area'];
                $todata['address'] = '';
                $todata['facePicUrl'] = $item['facepic'];
                $tojson = json_encode($todata, JSON_UNESCAPED_UNICODE);
                $tobase64 = base64_encode($tojson);
                $sign = '';
                $clientTime = time();
                $methodName = "saveMember";
                $security = "aewfaacaeff3223kopj34io324jondfnereusgnieg";
                $sign = "methodName=" . $methodName . "|dataJson=" . $tobase64 . "|clientTime=" . $clientTime . "|security=" . $security;
                $sign = md5($sign);
                $topostdata = array();
                $topostdata['methodName'] = $methodName;
                $topostdata['dataJson'] = $tobase64;
                $topostdata['clientTime'] = $clientTime;
                $topostdata['sign'] = $sign;
                $topostdata = json_encode($topostdata);
                $header = array('Content-Type:application/json');
                $code = $this->http_request($url, $topostdata, $header, $item);
                $codes = json_decode($code, true);
                if ($codes['code'] == '200') {
                    pdo_update('ewei_shop_member_push', array('isdone' => 1), array('id' => $item['id']));
                    pdo_query("update " . tablename('ewei_shop_member_push') . "set pnum=pnum+1 where id=" . $item['id']);
                } else {
                    pdo_update('ewei_shop_member_push', array('isdone' => 2), array('id' => $item['id']));
                    pdo_query("update " . tablename('ewei_shop_member_push') . "set pnum=pnum+1 where id=" . $item['id']);
                }
            }
        }
        die(json_encode(array('code' => 200, 'returnData' => array('totalPage' => $totalPage))));
    }

    //会员绑定推送
    function memPushOne() {
        global $_W;
        global $_GPC;
        $hlcloudset = m("common")->getPluginset("hlcloud");
        $id = $_GPC['id'];
        $sql = 'select * FROM ' . tablename('ewei_shop_member_push') . ' where (isdone=0 or isdone=2) and pnum<' . $hlcloudset['pmaxnum'] . ' and id=' . $id;
        $data = pdo_fetchall($sql);
        if (!empty($data)) {
            foreach ($data as $item) {
                $todata = array();
                $url = "http://m.dfhlyl.com:8070/Persion/dfhl/openapi/hlb";
                $todata['mid'] = $item['mid'];
                $todata['realName'] = $item['realname'];
                $todata['mobile'] = $item['mobile'];
                $todata['sex'] = ($item['gender'] == '1' ? '00' : '01');
                $todata['birthDay'] = $item['birthyear'] . '-' . $item['birthmonth'] . '-' . $item['birthday'];
                $todata['region'] = $item['agentarea'];
                $todata['province'] = $item['province'];
                $todata['city'] = $item['city'];
                $todata['area'] = $item['area'];
                $todata['address'] = '';
                $todata['facePicUrl'] = $item['facepic'];
                $tojson = json_encode($todata, JSON_UNESCAPED_UNICODE);
                $tobase64 = base64_encode($tojson);
                $sign = '';
                $clientTime = time();
                $methodName = "saveMember";
                $security = "aewfaacaeff3223kopj34io324jondfnereusgnieg";
                $sign = "methodName=" . $methodName . "|dataJson=" . $tobase64 . "|clientTime=" . $clientTime . "|security=" . $security;
                $sign = md5($sign);
                $topostdata = array();
                $topostdata['methodName'] = $methodName;
                $topostdata['dataJson'] = $tobase64;
                $topostdata['clientTime'] = $clientTime;
                $topostdata['sign'] = $sign;
                $topostdata = json_encode($topostdata);
                $header = array('Content-Type:application/json');
                $code = $this->http_request($url, $topostdata, $header, $item);
                $codes = json_decode($code, true);
                if ($codes['code'] == '200') {
                    pdo_update('ewei_shop_member_push', array('isdone' => 1), array('id' => $item['id']));
                    pdo_query("update " . tablename('ewei_shop_member_push') . "set pnum=pnum+1 where id=" . $item['id']);
                } else {
                    pdo_update('ewei_shop_member_push', array('isdone' => 2), array('id' => $item['id']));
                    pdo_query("update " . tablename('ewei_shop_member_push') . "set pnum=pnum+1 where id=" . $item['id']);
                }
            }
        }
        header("location:" . webUrl('hlcloud/synchro'));
    }

    /**
     * 活动列表
     */
    public function activity_list() {
        global $_W;
        global $_GPC;
        $data = array();
        $pindex = max(1, intval($_GPC['home']));
        $psize = 20;
        $condition = ' WHERE g.`uniacid` = :uniacid ';
        $params = array(':uniacid' => $_W['uniacid']);
        $condition .= ' AND isshow=1 and activity_time_start<=now() and activity_time_end>=now()';
        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' AND ( g.`name` LIKE :keyword ';
            $condition .= ' )';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }
        if (!empty($_GPC['cate'])) {
            $_GPC['cate'] = intval($_GPC['cate']);
            $condition .= ' AND category=' . $_GPC['cate'];
        }

        $sql = 'SELECT count(*) FROM ' . tablename('ewei_shop_activity') . 'g ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if (!empty($total)) {
            $sql = 'select g.* from ' . tablename('ewei_shop_activity') . ' g  ' . $condition . ' ORDER BY 
                g.`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $params);

            //print_r($list);exit;
            foreach ($list as $key => &$value) {
                $data[$key]['id'] = $value['id'];
                $data[$key]['title'] = $value['name'];
                $data[$key]['thumb'] = $value['logo'];
                $data[$key]['category'] = $value['category'];
                $category = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_activity_category') . 'WHERE id=:id LIMIT 1', array(':id' => $value['category']));
                $data[$key]['categoryname'] = $category['name'];
            }
            $data = set_medias($data, 'thumb');
            $pager = pagination2($total, $pindex, $psize);
            if ($data) {
                $data1['list'] = $data;
                $data1['total'] = $total;    //总条数
                $data1['pindex'] = $pindex;  //当前页
                return json_app(0, $data1, '成功');
            } else {
                return json_app(1, $data, "无记录");
            }
        } else {
            return json_app(1, $data, '无数据');
        }
    }

    /**
     * 根据活动id查活动详情
     */
    public function activity_info() {
        global $_W;
        global $_GPC;
        $data = array();
        $pluginname = 'recognition.many';
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $tokeninfo = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_perm_user') . 'WHERE uid=:uid LIMIT 1', array(':uid' => $uid));
        //print_r($tokeninfo);exit;
        $user_perms = array();  //操作员权限
        $role_perms = array();  //角色权限
        if (!empty($tokeninfo)) {
            //所属角色
            $role = pdo_fetch("SELECT * FROM " . tablename('ewei_shop_perm_role') . " WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1", array(':uniacid' => $_W['uniacid'], ':id' => $tokeninfo['roleid']));
            if (!empty($role)) {
                $role_perms = explode(',', $role['perms2']);
            }
            $user_perms = explode(',', $tokeninfo['perms2']);
        }
        $activity_id = $_GPC['activity_id'];
        if (in_array($pluginname, $role_perms) || in_array($pluginname, $user_perms)) {
            $shop = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_activity') . ' WHERE id=:id limit 1', array(':id' => $activity_id));
            if (!$shop['id']) {
                return json_app(1, $data, "未找到服务");
            }
            $category = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_category') . 'WHERE id=:id LIMIT 1', array(':id' => $shop['category']));
            $shop['categoryname'] = $category['name'];
            $shop = set_medias($shop, 'logo');
            return json_app(0, $shop, '成功');
        } else {
            return json_app(1, $data, "此账号没有管理权限");
        }
    }

    public function queryActivity() {
        global $_W;
        global $_GPC;
        $item = array();
        $pid = $_GPC['pid'];
        $sql = 'SELECT * FROM ' . tablename('ewei_shop_recognition_pass') . '  where id="' . $pid . '"';
        $itemPass = pdo_fetch($sql);
        if ($itemPass['type'] == 4) {
            $server_id = $itemPass['activity_id'];
            $item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_activity') . ' WHERE id=:id limit 1', array(':id' => $server_id));
            if (!$item['id']) {
                return json_app(1, $item, "未找到活动");
            }
            $item['title'] = $item['name'];
        }
        $thumb = tomedia($item['logo']);
        return json_app(0, array('title' => $item['title'], 'thumb' => $thumb), '成功');
    }

    public function default_activity_con() {
        global $_W;
        global $_GPC;
        $data = array();
        $uid = trim($_GPC['uid']);
        $pid = trim($_GPC['pid']);
        $equipmentinfo = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_equipment') . ' WHERE duid=:duid  limit 1 ', array(':duid' => $_GPC['duid']));
        if (empty($equipmentinfo['id'])) {
            return json_app(1, $data, "设备码错误");
        }
        $select_equipment = empty($equipmentinfo['id']) ? '' : ' and id=' . intval($pid) . ' ';


        $condition = "";

        $list = array();
        $list = pdo_fetchall('SELECT *  FROM ' . tablename('ewei_shop_recognition_pass') . ' where type=4' . $select_equipment . $condition . ' order by createtime desc  ');
        $list = set_medias($list, 'logo');
        return json_app(0, $list[0], "成功");
    }

    public function consumption_activity() {
        global $_W;
        global $_GPC;
        $data = array();
        $uid = trim($_GPC['uid']);
        $token = trim($_GPC['token']);
        $facetoken = trim($_GPC['facetoken']);
        $con_id = trim($_GPC['con_id']);
        $pid = trim($_GPC['pid']);

        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken=:facetoken  limit 1 ', array(':facetoken' => $facetoken));

        if (!$info['id']) {
            return json_app(-1, $data, "用户token错误，找不到该用户");
        }

        $con_list = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_recognition_pass') . ' WHERE id=:id  limit 1 ', array(':id' => $con_id));
        $activity_id = $con_list['activity_id'];
        $activity_item = pdo_fetch("select *  from  " . tablename("ewei_shop_activity") . " where id=:id ORDER BY id DESC ", array(':id' => $activity_id));
        if (empty($activity_item)) {
            return json_app(-1, $data, "未查询到活动");
        }
        $openid = $info['openid'];
        $data['realname'] = $info['realname'];
        $data['userid'] = $info['id'];
        $data['activity_name'] = $activity_item['name'];
        $data['activity_id'] = $activity_item['id'];

        $log = pdo_fetch("select * from " . tablename('ewei_shop_activity_log') . " where openid='{$openid}' and activityid=:activityid limit 1", array(':activityid' => $activity_id));
        if ($log['status'] < 1) {
            return json_app(-1, $data, "报名还未审核");
        }
        if ($log['status'] == 2) {
            return json_app(-1, $data, "该门票已经被核销，不能重复使用");
        }
        pdo_update('ewei_shop_activity_log', array('status' => 2), array('id' => $log['id']));
        return json_app(0, $data, "成功");
    }

    /**
     * 获取服务列表
     */
    public function getServerList() {
        global $_W;
        global $_GPC;
        $data = array();
        $pindex = max(1, intval($_GPC['home']));
        $psize = 10000;
        $condition = ' WHERE g.`uniacid` = :uniacid and g.type<>9 and g.type=6';
        $params = array(':uniacid' => $_W['uniacid']);
        $condition .= ' AND g.`status` > 0 and g.`checked`=0 and g.`total`>0 and g.`deleted`=0';
        $condition .= " AND g.isknowledge=0 and g.isactivity=0 ";
        if (!empty($_GPC['keyword'])) {
            $_GPC['keyword'] = trim($_GPC['keyword']);
            $condition .= ' AND ( g.`title` LIKE :keyword ';
            $condition .= ' )';
            $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
        }
        if (!empty($_GPC['cate'])) {
            $_GPC['cate'] = intval($_GPC['cate']);
            $condition .= ' AND ( FIND_IN_SET(' . $_GPC['cate'] . ',cates)<>0 OR pcate=' . $_GPC['cate'] . ' OR ccate=' . $_GPC['cate'] . ' )';
        }

        $sql = 'SELECT count(*) FROM ' . tablename('ewei_shop_goods') . 'g ' . $condition;
        $total = pdo_fetchcolumn($sql, $params);
        if (!empty($total)) {
            $sql = 'select g.id,g.title,g.cates,g.total,g.hasoption,g.marketprice,g.thumb,g.serverlength,IFNULL(gop.marketprice,g.marketprice) as marketprice,IFNULL(gop.title,"") as ggtitle,IFNULL(gop.id,"") as ggid from ' . tablename('ewei_shop_goods') . ' g left join ' . tablename('ewei_shop_goods_option') . ' gop on g.id=gop.goodsid ' . $condition . ' ORDER BY g.`status` DESC, g.`displayorder` DESC,
                g.`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
            $list = pdo_fetchall($sql, $params);

            foreach ($list as $key => &$value) {
                $data[$key]['id'] = $data[$key]['markid'] = !empty($value['ggid']) ? $value['id'] . '-' . $value['ggid'] : $value['id'];
                $data[$key]['name'] = !empty($value['ggtitle']) ? $value['title'] . '-' . $value['ggtitle'] : $value['title'];
                $data[$key]['cover'] = $value['thumb'];
                $data[$key]['unitprice'] = $data[$key]['marketprice'] = $value['marketprice'];
                $data[$key]['serverlength'] = $value['serverlength'];
                $value['allcates'] = explode(',', $value['cates']);
                $data[$key]['cates'] = $value['cates'];
                $category = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_category') . 'WHERE id=:id LIMIT 1', array(':id' => $value['cates']));
                $data[$key]['categoryname'] = $category['name'];
            }
            $data = set_medias($data, 'cover');
            $pager = pagination2($total, $pindex, $psize);
            if ($data) {
                $data1['list'] = $data;
                $data1['total'] = $total;    //总条数
                $data1['pindex'] = $pindex;  //当前页
                return json_app(0, $data1, '成功');
            } else {
                return json_app(1, $data, "无记录");
            }
        } else {
            return json_app(1, $data, '无数据');
        }
    }

    /**
     * 获取服务详情
     */
    public function getServerDetail() {
        global $_W;
        global $_GPC;
        $id = trim($_GPC['id']);
        $data = [];

        if (!empty($id)) {
            $condition = ' where g.id = ' . $id;
            $sql = 'select g.id,g.title,g.content,g.cates,g.total,g.hasoption,g.marketprice,g.thumb,g.serverlength,IFNULL(gop.marketprice,g.marketprice) as marketprice,IFNULL(gop.title,"") as ggtitle,IFNULL(gop.id,"") as ggid from ' . tablename('ewei_shop_goods') . ' g left join ' . tablename('ewei_shop_goods_option') . ' gop on g.id=gop.goodsid ' . $condition;
            $server = pdo_fetch($sql);

            if ($server) {
                $data = [
                    'id' => !empty($server['ggid']) ? $server['id'] . '-' . $server['ggid'] : $server['id'],
                    'markid' => !empty($server['ggid']) ? $server['id'] . '-' . $server['ggid'] : $server['id'],
                    'name' => !empty($server['ggtitle']) ? $server['title'] . '-' . $server['ggtitle'] : $server['title'],
                    'cover' => $server['thumb'],
                    'unitprice' => $server['marketprice'],
                    'marketprice' => $server['marketprice'],
                    'serverlength' => $server['serverlength'],
                    'content' => $server['content'],
                ];
                $data = set_medias($data, 'cover');
            }

            if ($data) {
                return json_app(0, $data, '成功');
            } else {
                return json_app(1, $data, "无记录");
            }
        } else {
            return json_app(1, $data, '无数据');
        }
    }

    /**
     * 获取卡项详情
     */
    public function getCardItemDetail()
    {
        global $_W;
        global $_GPC;
        $id = trim($_GPC['id']);
        $lng = trim($_GPC['lng']);
        $lat = trim($_GPC['lat']);

        $data = array();
        if (!$lng) {
            return json_app(-1, $data, "请上传经纬度");
        }
        if (!$lat) {
            return json_app(-1, $data, "请上传经纬度");
        }

        if ($id) {
            $sql = 'select * from ' . tablename('ewei_shop_virtual_carditem') . ' where id=:id limit 1';
            $card = pdo_fetch($sql, array(':id' => $id));

            if ($card['id']) {
                $data['id'] = $card['id'];
                $data['markid'] = $card['id'];
                $data['name'] = $card['title'];
                $data['cover'] = $card['background_image'];
                $data['unitprice'] = $data['marketprice'] = $card['retail_price'];
                $data['pv'] = $card['pv'];
                $data['particulars'] = $card['particulars']; // 详情
                $data['sales'] = $card['sales']; // 销量
                // 有效期
                if ($card['valid_period']) {
                    $validPeriod = unserialize($card['valid_period']);
                    switch ($validPeriod['valid_type']) {
                        case 1:
                            $data['valid_time'] = "永久有效";
                            break;
                        case 2:
                            $data['valid_time'] = $validPeriod['beforeday'];
                            break;
                        case 3:
                            $data['valid_time'] = "购买后". $validPeriod['withins']. "天内有效";
                            break;
                        case 4:
                            $data['valid_time'] = "首次使用后". $validPeriod['firstly']. "天内有效";
                            break;
                    }
                }

                // 服务
                $sql = 'select g.title,c.price,c.disposable,g.thumb from ' . tablename('ewei_shop_virtual_carditem_server').
                    ' c left join ' . tablename('ewei_shop_goods') . ' g on c.server_id = g.id  where c.card_id = ' . $card['id'];
                $servers = pdo_fetchall($sql);
                $servers = set_medias($servers, 'thumb');
                $data['servers'] = $servers;

                // 门店 shop_list
                $shops = [];
                if ($card['shop_list']) {
                    $sql = 'select storename,tel,mobile,lat,lng,address from ' . tablename('ewei_shop_store') . ' where id in (' .$card['shop_list'] . ')';
                    $shops = pdo_fetchall($sql);

                    foreach ($shops as &$shop) {
                        $shop['distance'] = $this->getDistance($shop['lng'], $shop['lat'], $lng, $lat);
                    }
                }
                $data['shops'] = $shops;

                return json_app(0, $data, '成功');
            } else {
                return json_app(-1, $data, "没有找到该卡项");
            }
        } else {
            return json_app(-1, $data, "id不能为空");
        }
    }

    /**
     * 获取卡项服务协议
     */
    public function getCardItemAgreement()
    {
        global $_W;
        global $_GPC;
        $id = trim($_GPC['id']);

        $card = [
            'protocol_title' => '卡项协议',
            'protocol_content' => '卡项协议',
        ];

        return json_app(0, $card, '成功');

        $data = array();
        if ($id) {
            $sql = 'select protocol_title,protocol_content from ' . tablename('ewei_shop_virtual_carditem') . ' where id=:id limit 1';
            $card = pdo_fetch($sql, array(':id' => $id));

            if ($card) {
                return json_app(0, $card, '成功');
            } else {
                return json_app(-1, $data, "没有找到该协议");
            }
        } else {
            return json_app(-1, $data, "id不能为空");
        }
    }

    /**
     * 获取卡项列表
     */
    public function getCardItemList()
    {
        global $_W;
        global $_GPC;
        $data = array();

        $data = array();
        $sql = 'SELECT count(*) FROM ' . tablename('ewei_shop_virtual_carditem') . ' where 1';
        $total = pdo_fetchcolumn($sql);
        if (!empty($total)) {
            $sql = 'select id,id as markid, title as name,retail_price as marketprice,retail_price as unitprice,pv,background_image as cover from ' . tablename('ewei_shop_virtual_carditem'). ' where 1';
            $list = pdo_fetchall($sql);
            $list = set_medias($list, 'cover');

            if ($list) {
                $data1['list'] = $list;
                return json_app(0, $data1, '成功');
            } else {
                return json_app(-1, $data, "无记录");
            }
        } else {
            return json_app(-1, $data, '无数据');
        }
    }

    /**
     * 确认卡项订单
     */
    public function confirmCardItemOrder()
    {
        global $_W;
        global $_GPC;

        $data = array();
        $facetoken = trim($_GPC['facetoken']);

        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken=:facetoken limit 1 ', array(':facetoken' => $facetoken));
        if (!$info['id']) {
            return json_app(-1, $data, "用户token错误，找不到该用户");
        }

        if ($_W['ispost']) {
            $data = array('uniacid' => $_W['uniacid']);

            if (empty($_GPC['card_items'])) {
                return json_app(-1, $data, '请选择卡项');
            }
            $_GPC['card_items'] = htmlspecialchars_decode($_GPC['card_items']);
            $cardItems = json_decode($_GPC['card_items'], true);

            $price = 0;
            $cards = [];
            $goodsnum = [];
            foreach ($cardItems as &$cardItem) {
                $card = pdo_fetch('select * from ' . tablename('ewei_shop_virtual_carditem') . ' where id=:id limit 1', array(':id' => $cardItem['id']));
                $price += $card['retail_price'] * $cardItem['quantity'];
                array_push($cards, $card);

                $servers = pdo_fetchall("SELECT server_id FROM  " . tablename('ewei_shop_virtual_carditem_server') . " where `card_id`=:card_id", array(":card_id" => $cardItem['id']));
                foreach ($servers as $server) {
                    $val = [
                        'server_id' => $server['server_id'],
                        'num' => $cardItem['quantity']
                    ];
                    array_push($goodsnum, $val);
                }
                unset($server);
                $cardItem['servers'] = $servers;
            }
            unset($cardItem);

            if (empty($goodsnum)) {
                return json_app(-1, $data, '服务不存在');
            }

            foreach ($goodsnum as $gnum) {
                if ($gnum['num'] == 0) {
                    return json_app(-1, $data, '请选择数量');
                }
            }
            unset($gnum);

            // 0=自提 1=快递
            $dtype = $_GPC['dtype'];
            $address = [];
            if ($dtype == 1) {
                $address = [
                    'uniacid' => $_W['uniacid'],
                    'openid' => $info['openid'],
                    'province' => $_GPC['province'],
                    'city' => $_GPC['city'],
                    'area' => $_GPC['area'],
                    'address' => $_GPC['address'],
                    'realname' => $_GPC['name'],
                    'mobile' => $_GPC['mobile']
                ];
                if (empty($address['province']) || $address['province'] == "请选择省份") {
                    return json_app(-1, $data, '请选择快递地址[省份]');
                }
                if (empty($address['city']) || $address['city'] == "请选择城市") {
                    return json_app(-1, $data, '请选择快递地址[城市]');
                }
                if (empty($address['area']) || $address['area'] == "请选择街道") {
                    return json_app(-1, $data, '请选择快递地址[街道]');
                }
                if (empty($address['mobile'])) {
                    return json_app(-1, $data, '请填写手机号');
                }
                if (empty($address['realname'])) {
                    return json_app(-1, $data, '请填写姓名');
                }
                if (empty($address['address'])) {
                    return json_app(-1, $data, '请填写详细地址');
                }

                $data['address'] = iserializer($address);
            }

            // 判断库存是否充足
            foreach ($cardItems as $cardItem) {
                foreach ($cardItem['servers'] as $value) {
                    $goods = pdo_fetch("SELECT * FROM  " . tablename('ewei_shop_goods') . " where `id`=:id", array(":id" => $value['server_id']));

                    if ($goods['total'] < $cardItem['quantity']) {
                        return json_app(-1, [], '商品' . $goods['title'] . "库存不足" . $value['num'] . "件.");
                    }
                }
            }

            $buymember = m('member')->getMember($info['openid']);

            $data["createtime"] = time();
            $data['openid'] = $buymember['openid'];

            //我的备注==补充订单缺少字段
            $data['uid'] = $buymember['id'];
            $data['agentarea'] = $buymember['agentarea'];
            $data['merchid'] = $buymember['merch_id'];

            $data['agentid'] = $buymember['agentid'];
            $data['ordersn'] = m("common")->createNO("order", "ordersn", "RL");
            $data['price'] = $price;
            $data['goodsprice'] = $price;
            $data['status'] = 0;

            $data['paytime'] = time();
            $data['agentarea'] = $buymember['agentarea'];
            if ($dtype == 1) {
                // 配送
                pdo_insert('ewei_shop_member_address', $address);
                $adid = pdo_insertid();
                $data['addressid'] = $adid;
                $data['address'] = serialize($address);
            } else {
                $data['dispatchtype'] = 1;
                $data['storeid'] = $_GPC['outstore'];
                $data["verifycode"] = random(8, true);
            }
            pdo_insert('ewei_shop_order', $data);
            $orderid = pdo_insertid();

            foreach ($cardItems as $key => $item) {
//                $card = pdo_fetch('select * from ' . tablename('ewei_shop_virtual_carditem') . ' where id=:id limit 1', array(':id' => $item['id']));
                $card = $cards[$key];
                $cardItemOrder = [
                    'order_id' => $orderid,
                    'card_id' => $card['id'],
                    'title' => $card['title'],
                    'retail_price' => $card['retail_price'],
                    'pv' => $card['pv'],
                    'valid_period' => $card['valid_period'],
                    'shop_type' => $card['shop_type'],
                    'shop_list' => $card['shop_list'],
                    'card_background' => $card['card_background'],
                    'background_image' => $card['background_image'],
                    'add_time' => time()
                ];
                // 订单卡项关联表
                pdo_insert('ewei_shop_order_carditem', $cardItemOrder);

                foreach ($item['servers'] as $server) {
                    // 订单商品关联表
                    $ogdata = array(
                        'uniacid' => $_W['uniacid']
                    );
                    $ogdata['orderid'] = $orderid;
                    $ogdata['goodsid'] = $server['server_id'];
                    $ogdata['price'] = 0;
                    $ogdata['total'] = $item['num'];
                    $ogdata['realprice'] = 0;
                    $ogdata["createtime"] = time();
                    $ogdata["pvprice"] = 0;
                    $ogdata['card_id'] = $card['id']; // 卡片id
                    $ogdata['disposable'] = $item['num']; // 可用次数
                    $ogdata['remain_disposable'] = $item['num']; // 剩余可用次数

                    pdo_insert('ewei_shop_order_goods', $ogdata);

                    // 卡项销量
                    pdo_update('ewei_shop_virtual_carditem', 'sales=sales+1', array('id' => $card['id']));

                    // 减少库存
                    pdo_fetch("UPDATE " . tablename('ewei_shop_goods') . " SET `total` = `total` - {$item['num']} WHERE  `id` =:id", array(":id" => $server['server_id']));
                }
            }

            p('commission')->checkOrderConfirm($orderid);
            m('verifygoods')->createverifyservers($orderid, true);

            return json_app(0, ['order_id' => $orderid, 'price' => $price], '确认订单');
        }
    }

    /**
     * 确认服务/商品订单
     */
    public function confirmGoodsOrder()
    {
        global $_W;
        global $_GPC;

        $data = array('uniacid' => $_W['uniacid']);
        $facetoken = trim($_GPC['facetoken']);

        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken=:facetoken limit 1 ', array(':facetoken' => $facetoken));
        if (empty($info)) {
            return json_app(-1, $data, "用户token错误，找不到该用户");
        }

        if ($_W['ispost']) {
            if (empty($_GPC['goods_items'])) {
                return json_app(-1, $data, '请选择需要的服务或商品');
            }
            $_GPC['goods_items'] = htmlspecialchars_decode($_GPC['goods_items']);
            $goodItems = json_decode($_GPC['goods_items'], true);

            $price = 0;
            $goods = [];
            foreach ($goodItems as $goodItem) {
                $goodId = intval($goodItem['goodsid']);
                $num = intval($goodItem['quantity']);

                if ($num <= 0) {
                    return json_app(-1, $data, '数量必须大于0');
                }

                if (isset($goodItem['id'])) {
                    $optionId = intval($goodItem['id']);
                    $option = pdo_fetch('select id,title,marketprice,productprice,liveprice,islive,presellprice,goodssn,productsn,`virtual`,stock,weight,specs,
                    `day`,allfullbackprice,fullbackprice,allfullbackratio,fullbackratio,isfullback
                    from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':goodsid' => $goodId, ':id' => $optionId));

                    if (empty($option)) {
                        return json_app(-1, $data, '该规格不存在');
                    }

                    $item['optionid'] = $optionId;
                    $item['title'] = $option['title'];
                    $item['pvprice'] = $option['productprice'];
                    $item['marketprice'] = $option['marketprice'];
                    $item['price'] = $option['marketprice'] * $num;
                } else {
                    $goodVal = pdo_fetch('select id,title,thumb,marketprice,total from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $_W['uniacid'] . ' and id = ' . $goodId . ' ');
                    $item['title'] = $goodVal['title'];
                    $item['pvprice'] = $goodVal['productprice'];
                    $item['marketprice'] = $goodVal['marketprice'];
                    $item['price'] = $goodVal['marketprice'] * $num;
                }

                $item['num'] = $num;
                $item['goodid'] = $goodId;
                $price += $item['marketprice'];

                if (!empty($option['specs'])) {
                    $thumb = m('goods')->getSpecThumb($option['specs']);

                    if (!empty($thumb)) {
                        $item['thumb'] = $thumb;
                    }
                }
                $goods[] = $item;
            }
            unset($goodItem);

            // 0=自提 1=快递
            $dtype = $_GPC['dtype'];
            $address = [];
            if ($dtype == 1) {
                $address = [
                    'uniacid' => $_W['uniacid'],
                    'openid' => $info['openid'],
                    'province' => $_GPC['province'],
                    'city' => $_GPC['city'],
                    'area' => $_GPC['area'],
                    'address' => $_GPC['address'],
                    'realname' => $_GPC['name'],
                    'mobile' => $_GPC['mobile']
                ];
                if (empty($address['province']) || $address['province'] == "请选择省份") {
                    return json_app(-1, $data, '请选择快递地址[省份]');
                }
                if (empty($address['city']) || $address['city'] == "请选择城市") {
                    return json_app(-1, $data, '请选择快递地址[城市]');
                }
                if (empty($address['area']) || $address['area'] == "请选择街道") {
                    return json_app(-1, $data, '请选择快递地址[街道]');
                }
                if (empty($address['mobile'])) {
                    return json_app(-1, $data, '请填写手机号');
                }
                if (empty($address['realname'])) {
                    return json_app(-1, $data, '请填写姓名');
                }
                if (empty($address['address'])) {
                    return json_app(-1, $data, '请填写详细地址');
                }

                $data['address'] = iserializer($address);
            }

            // 判断库存是否充足
            foreach ($goods as $value) {
                if ($value['optionid']) {
                    $option = pdo_fetch("SELECT * FROM  " . tablename('ewei_shop_goods_option') . " where `id`=:id", array(":id" => $value['optionid']));
                    $stock = $option ? $option['stock'] : 0;
                } else {
                    $tgood = pdo_fetch("SELECT * FROM  " . tablename('ewei_shop_goods') . " where `id`=:id", array(":id" => $value['goodid']));
                    $stock = $tgood ? $tgood['total'] : 0;
                }

                if ($stock < $value['num']) {
                    return json_app(-1, [], '商品' . $goods['title'] . "库存不足" . $value['num'] . "件.");
                }
            }

            $buymember = m('member')->getMember($info['openid']);

            $data["createtime"] = time();
            $data['openid'] = $buymember['openid'];

            //我的备注==补充订单缺少字段
            $data['uid'] = $buymember['id'];
            $data['agentarea'] = $buymember['agentarea'];
            $data['merchid'] = $buymember['merch_id'];

            $data['agentid'] = $buymember['agentid'];
            $data['ordersn'] = m("common")->createNO("order", "ordersn", "RL");
            $data['price'] = $price;
            $data['goodsprice'] = $price;
            $data['status'] = 0;

            $data['paytime'] = time();
            $data['agentarea'] = $buymember['agentarea'];
            if ($dtype == 1) {
                // 配送
                pdo_insert('ewei_shop_member_address', $address);
                $adid = pdo_insertid();
                $data['addressid'] = $adid;
                $data['address'] = serialize($address);
            } else {
                $data['dispatchtype'] = 1;
                $data['storeid'] = $_GPC['outstore'];
                $data["verifycode"] = random(8, true);
            }
            pdo_insert('ewei_shop_order', $data);
            $orderid = pdo_insertid();

            // 订单商品关联表
            foreach ($goods as $good) {
                $ogdata = array(
                    'uniacid' => $_W['uniacid']
                );
                $ogdata['orderid'] = $orderid;
                $ogdata['goodsid'] = $good['goodid'];
                $ogdata['optionid'] = $good['optionid'];
                $ogdata['oldprice'] = $ogdata['realprice'] = $ogdata['price'] = $good['price'];
                $ogdata['total'] = $good['num'];
                $ogdata['title'] = $good['title'];
                $ogdata['createtime'] = time();
                $ogdata['pvprice'] = $good['pvprice'];
                $ogdata['disposable'] = $good['num']; // 可用次数
                $ogdata['remain_disposable'] = $good['num']; // 剩余可用次数

                pdo_insert('ewei_shop_order_goods', $ogdata);

                // 减少库存
                if ($good['optionid']) {
                    pdo_fetch("UPDATE " . tablename('ewei_shop_goods_option') . "  SET  `stock` = `stock` - {$good['num']} WHERE  `id` =:id", array(":id" => $good['optionid']));
                } else {
                    pdo_fetch("UPDATE " . tablename('ewei_shop_goods') . "  SET  `total` = `total` - {$good['num']} WHERE  `id` =:id", array(":id" => $good['goodid']));
                }
            }

            p('commission')->checkOrderConfirm($orderid);
            m('verifygoods')->createverifyservers($orderid, true);

            return json_app(0, ['order_id' => $orderid, 'price' => $price], '确认订单');
        }
    }

    /**
     * 卡项订单支付
     */
    public function cardItemOrderPay()
    {
        global $_W;
        global $_GPC;

        $data = [];
        $uniacid = $_W['uniacid'];
        $orderId = $_GPC['order_id'];
        $type = $_GPC['type']; // 支付类型：1=余额支付 2=微信扫码支付 3=支付宝扫码支付

        if (!$type) {
            return json_app(-1, $data, "请选择支付方式!");
        }

        $facetoken = trim($_GPC['facetoken']);
        if (!$facetoken) {
            return json_app(-1, $data, "facetoken必传");
        }

        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken=:facetoken  limit 1 ', array(':facetoken' => $facetoken));
        if ($info && !$info['id']) {
            return json_app(-1, $data, "用户token错误，找不到该用户");
        }

        $order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderId, ':uniacid' => $uniacid));
        if (empty($order)) {
            return json_app(-1, $data, '订单未找到');
        }

        if ($type == 1) {
            $result = $this->creditPay($order, $info);
            if ($result[0] == -1) {
                // 支付失败
                return json_app(-1, $data, $result[1]);
            } else {
                return json_app(0, $data, $result[1]);
            }
        } else if ($type == 2) {
            $result = $this->wechatNativePay($order, $info);
            if ($result[0] == -1) {
                // 支付失败
                return json_app(-1, $data, $result[1]);
            } else {
                $data['pay_url'] = $result[1];
                return json_app(0, $data, '支付成功');
            }
        } else if ($type == 3) {
            $result = $this->aliPay();
        }
    }

    /**
     * 余额支付
     */
    public function creditPay($order, $info)
    {
        global $_W;
        global $_GPC;

        $data = [];
        $uniacid = $_W['uniacid'];
        $openid = $info['openid'];
        $muid = $info['uid'];

        $set = m('common')->getSysset(array('shop', 'pay'));
        if (empty($set["pay"]["credit"]) && 0 < $order["price"]) {
            return [-1, "未开启余额支付!"];
        }
        if ($order["price"] < 0) {
            return [-1, "金额错误"];
        }
        $credits = m("member")->getCredit($openid, "credit2");
        if ($credits < $order["price"]) {
            return [-1, "余额不足,请充值"];
        }

        $fee = floatval($order["price"]);
        // 扣除用户余额操作
        $result = m("member")->setCredit($openid, "credit2", 0 - $fee, array($muid, $_W["shopset"]["shop"]["name"] . "人脸识别消费" . $fee));
        if (is_error($result)) {
            return [-1, $result["message"]];
        }

        $time = time();
        $change_data = array();
        $change_data['status'] = 1;
        $change_data['tradestatus'] = 1;
        $change_data['paytime'] = $time;
        pdo_update('ewei_shop_order', $change_data, array('id' => $order['id']));

        return [1, '支付成功'];
    }

    /**
     * 微信支付
     */
    public function wechatNativePay($order, $info)
    {
        global $_W;
        global $_GPC;

        $set = m('common')->getSysset('pay');
        $payment = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_payment') . ' WHERE uniacid=:uniacid AND id=:id', array(':uniacid' => $_W['uniacid'], ':id' => $set['weixin_id']));
        $wechat = array('appid' => trim($payment['sub_appid']), 'mchid' => trim($payment['sub_mch_id']), 'apikey' => trim($payment['apikey']));

        $params = array();
        $params['tid'] = $order['ordersn'];
        $params['user'] = $info['openid'];
        $params['fee'] = floatval($order["price"]) * 100; // 分
        $params['title'] = '扫码支付单号:'. $order['ordersn'];

        load()->model('payment');
        $setting = uni_setting($_W['uniacid'], array('payment'));
        $options = array();

        if (is_array($setting['payment'])) {
            $options = $setting['payment']['wechat'];
            $options['appid'] = trim($wechat['appid']);
            $options['secret'] = trim($wechat['mchid']);
        }

        $wechat = m('common')->wechat_native_build($params, $options, 23);
        if ($wechat && $wechat['code_url']) {
            $url = m('qrcode')->createQrcode($wechat['code_url']);
            return [1, $url];
        }

        return [-1, '支付失败'];
    }

    public function wechatNativePay1($order, $info)
    {
        $set = m('common')->getSysset('pay');
        $payment = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_payment') . ' WHERE uniacid=:uniacid AND id=:id', array(':uniacid' => $_W['uniacid'], ':id' => $set['weixin_id']));
        $wechat = array('appid' => trim($payment['sub_appid']), 'mchid' => trim($payment['sub_mch_id']), 'apikey' => trim($payment['apikey']));

        $params = [
            'title' => '扫码支付单号:'. $order['ordersn'],
            'device_info' => $_GPC['device_info'] ? $_GPC['device_info'] : 'WEB',
            'tid' => $order['ordersn'],
            'fee' => floatval($order["price"]),
            'product_id' => $order['ordersn']
        ];

        $package = array();
        $package['appid'] = trim($wechat['appid']);
        $package['mch_id'] = trim($wechat['mchid']);
        $package['nonce_str'] = random(32);
        $package['body'] = trim($params['title']);
        $package['device_info'] = isset($params['device_info']) ? 'ewei_shopv2:' . $params['device_info'] : 'ewei_shopv2';
        $package['attach'] = (isset($params['uniacid']) ? $params['uniacid'] : $_W['uniacid']) . ':' . 23;
        $package['out_trade_no'] = trim($params['tid']);
        $package['total_fee'] = floatval($params['fee']) * 100;
        $package['spbill_create_ip'] = CLIENT_IP;
        $package['product_id'] = $params['tid'];

        if (!empty($params['goods_tag'])) {
            $package['goods_tag'] = trim($params['goods_tag']);
        }

        $package['time_start'] = date('YmdHis', TIMESTAMP);
        $package['time_expire'] = date('YmdHis', TIMESTAMP + 3600);
        $package['notify_url'] = empty($params['notify_url']) ? $_W['siteroot'] . 'addons/ewei_shopv2/payment/wechat/notify.php' : $params['notify_url'];
        $package['trade_type'] = 'NATIVE';
        ksort($package, SORT_STRING);
        $string1 = '';

        foreach ($package as $key => $v) {
            if (empty($v)) {
                continue;
            }

            $string1 .= $key . '=' . $v . '&';
        }

        $string1 .= 'key=' . $wechat['apikey'];
        $package['sign'] = strtoupper(md5(trim($string1)));
        $dat = array2xml($package);
        load()->func('communication');
        $response = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);

        if (is_error($response)) {
            return [-1, $response];
        }

        libxml_disable_entity_loader(true);
        $xml = simplexml_load_string(trim($response['content']), 'SimpleXMLElement', LIBXML_NOCDATA);

        if (strval($xml->return_code) == 'FAIL') {
            return [-1, strval($xml->return_msg)];
        }

        if (strval($xml->result_code) == 'FAIL') {
            return [-1, strval($xml->err_code) . ': ' . strval($xml->err_code_des)];
        }

        $result = json_decode(json_encode($xml), true);

        $url = m('qrcode')->createQrcode($result['code_url']);

        return [1, $url];
    }

    /**
     * 支付宝支付
     */
    public function aliPay()
    {
        global $_W;
        global $_GPC;

        $data = m('common')->getSysset('pay');

        if (0 < $data['alipay_id']) {
            $paymentalis = pdo_fetch('SELECT alipay_sec FROM ' . tablename('ewei_shop_payment') . ' WHERE id = :id and uniacid=:uniacid and paytype = 1 ', array(':uniacid' => $_W['uniacid'], ':id' => $data['alipay_id']));
            if (!empty($paymentalis) && !empty($paymentalis['alipay_sec'])) {
                $paymentalis['alipay_sec'] = iunserializer($paymentalis['alipay_sec']);

                $params = [
                    'app_id' => $paymentalis['alipay_sec']['appid'],
                    'sign_type' => $paymentalis['alipay_sec']['alipay_sign_type'],
                    'method' => 'alipay.trade.precreate',
                    'privatekey' => $paymentalis['alipay_sec']['private_key']
                ];

                $public = array(
                    'app_id' => $params['app_id'],
                    'method' => $params['method'],
                    'format' => 'JSON',
                    'charset' => 'utf-8',
                    'sign_type' => $params['sign_type'],
                    'timestamp' => date('Y-m-d H:i:s'),
                    'version' => '1.0'
                );

                if (!empty($params['return_url'])) {
                    $public['return_url'] = $params['return_url'];
                }

                if (!empty($params['app_auth_token'])) {
                    $public['app_auth_token'] = $params['app_auth_token'];
                }

                if (!empty($params['notify_url'])) {
                    $public['notify_url'] = $params['notify_url'];
                }

                if (!empty($params['biz_content'])) {
                    $public['biz_content'] = is_array($params['biz_content']) ? json_encode($params['biz_content']) : $params['biz_content'];
                }

                ksort($public);
                $string1 = '';

                foreach ($public as $key => $v) {
                    if (empty($v)) {
                        continue;
                    }

                    $string1 .= $key . '=' . $v . '&';
                }

                $string1 = rtrim($string1, '&');
                $pkeyid = openssl_pkey_get_private($this->chackKey($params['privatekey'], false));

                if ($pkeyid === false) {
                    return error(-1, '提供的私钥格式不对');
                }

                $signature = '';

                if ($params['sign_type'] == 'RSA') {
                    openssl_sign($string1, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
                } else {
                    if ($params['sign_type'] == 'RSA2') {
                        openssl_sign($string1, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
                    }
                }

                openssl_free_key($pkeyid);
                $signature = base64_encode($signature);

                if (empty($signature)) {
                    return error(-1, '签名不能为空！');
                }

                $public['sign'] = $signature;
                load()->func('communication');
                $url = EWEI_SHOPV2_DEBUG ? 'https://openapi.alipaydev.com/gateway.do' : 'https://openapi.alipay.com/gateway.do';

                $response = ihttp_post($url, $public);
                $result = json_decode(iconv('GBK', 'UTF-8//IGNORE', $response['content']), true);

                return $result;
            }
        }
    }

    /**
     * 获取订单详情
     */
    public function getOrderDetail()
    {
        global $_W;
        global $_GPC;

        $data = [];
        $orderId = $_GPC['order_id'];
        $order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderId, ':uniacid' => $_W['uniacid']));
        if (empty($order)) {
            return json_app(-1, $data, '订单未找到');
        }

        $order['address'] = iunserializer($order['address']);

        return json_app(0, $order, '订单详情');
    }

    /**
     * 检查订单知否支付成功
     */
    public function checkOrderPay()
    {
        global $_W;
        global $_GPC;

        $data = [];
        $orderId = $_GPC['order_id'];
        $order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderId, ':uniacid' => $_W['uniacid']));
        if (empty($order)) {
            return json_app(-1, $data, '订单未找到');
        }
        if ($order['status'] != 1) {
            return json_app(-1, $data, '未支付');
        }

        return json_app(0, $data, '已支付');
    }

    // 两个经纬度之间的距离
    public static function getDistance($lng1, $lat1, $lng2, $lat2)
    {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = round(2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000);

        return $s;
    }
}

?>
