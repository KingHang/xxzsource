<?php
//dezend by http://www.yunlu99.com/
function app_error($errcode = 0, $message = '')
{
	return json_encode(array('error' => $errcode, 'message' => empty($message) ? AppError::getError($errcode) : $message));
}

function app_json($result = NULL, $openid)
{
	global $_GPC;
	global $_W;
	$ret = array();

	if (!is_array($result)) {
		$result = array();
	}

	$ret['error'] = 0;
	$key = time() . '@' . $openid;
	$auth = array('authkey' => base64_encode(authcode($key, 'ENCODE', 'ewei_shopv2_wxapp')));
	m('cache')->set($auth['authkey'], 1);
	return json_encode(array_merge($ret, $auth, $result));
}

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/error_code.php';
require EWEI_SHOPV2_PLUGIN . 'app/core/wxapp/wxBizDataCrypt.php';
class Wxapp_EweiShopV2Page extends Page
{
	protected $appid;
	protected $appsecret;

	public function __construct()
	{
		$data = m('common')->getSysset('app');
		$this->appid = $data['appid'];
		$this->appsecret = $data['secret'];
	}

	public function login()
	{
		global $_GPC;
		global $_W;
		$code = trim($_GPC['code']);

		if (empty($code)) {
			return app_error(AppError::$ParamsError);
		}

		$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->appid . '&secret=' . $this->appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
		load()->func('communication');
		$resp = ihttp_request($url);

		if (is_error($resp)) {
			return app_error(AppError::$SystemError, $resp['message']);
		}

		$arr = @json_decode($resp['content'], true);
		$arr['isclose'] = $_W['shopset']['app']['isclose'];

		if (!empty($_W['shopset']['app']['isclose'])) {
			$arr['closetext'] = $_W['shopset']['app']['closetext'];
		}

		if (!is_array($arr) || !isset($arr['openid'])) {
			return app_error(AppError::$WxAppLoginError);
		}

        $member = pdo_fetch('select id from ' . tablename('ewei_shop_member') . ' where openid_wa=:openid_wa and uniacid=:uniacid order by openid_wa desc limit 1', array(':openid_wa' => $arr['openid'], ':uniacid' => $_W['uniacid']));
		$arr['authBefore'] = $member ? 1 : 0;

		return app_json($arr, $arr['openid']);
	}

	/**
     * 微信小程序登录
     */
	public function auth()
	{
		global $_GPC;
		global $_W;
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
			$this->refine($data['openId']);
			$member = m('member')->getMember('sns_wa_' . $data['openId']);

			if (empty($member)) {
				$member = array('uniacid' => $_W['uniacid'], 'uid' => 0, 'openid' => 'sns_wa_' . $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1', 'openid_wa' => $data['openId'], 'comefrom' => 'sns_wa', 'createtime' => time(), 'status' => 0);
				pdo_insert('ewei_shop_member', $member);
				$id = pdo_insertid();
				$data['id'] = $id;
				$data['uniacid'] = $_W['uniacid'];

				if (method_exists(m('member'), 'memberRadisCountDelete')) {
					m('member')->memberRadisCountDelete();
				}
			}
			else {
				$updateData = array('nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1');
				pdo_update('ewei_shop_member', $updateData, array('id' => $member['id'], 'uniacid' => $member['uniacid']));
				$data['id'] = $member['id'];
				$data['uniacid'] = $member['uniacid'];
				$data['isblack'] = $member['isblack'];
			}

			if (p('commission')) {
				p('commission')->checkAgent($member['openid']);
			}

			return app_json($data, $data['openId']);
		}

		return app_error(AppError::$WxAppError, '登录错误, 错误代码: ' . $errCode);
	}

	//针对 uniapp的用户信息录入
    public function uniappauth()
    {
        global $_GPC;
        global $_W;
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
              $member= pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid order by openid_wa desc limit 1', array(':mobile' => $_GPC['account'], ':uniacid' => $_W[uniacid]));
              if (empty($member)) {
                    return app_error(-1, '会员未找到 ');

                    $member = array('uniacid' => $_W['uniacid'], 'uid' => 0, 'openid' => 'sns_wa_' . $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1', 'openid_wa' => $data['openId'], 'comefrom' => 'sns_wa', 'createtime' => time(), 'status' => 0);
                    pdo_insert('ewei_shop_member', $member);
                    $id = pdo_insertid();
                    $data['id'] = $id;
                    $data['uniacid'] = $_W['uniacid'];

                    if (method_exists(m('member'), 'memberRadisCountDelete')) {
                          m('member')->memberRadisCountDelete();
                    }
              }
              else {
                    $updateData = array('openid_wa' => $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1','openid_wa' => $data['openId']);
                    pdo_update('ewei_shop_member', $updateData, array('id' => $member['id'], 'uniacid' => $member['uniacid']));
                    $data['id'] = $member['id'];
                    $data['uniacid'] = $member['uniacid'];
                    $data['isblack'] = $member['isblack'];
              }

              if (p('commission')) {
                  p('commission')->checkAgent($member['openid']);
              }

              return app_json($data, $data['openId']);
        }

        return app_error(-1, '登录错误, 错误代码: ' . $errCode);
    }

    // 时间银行微信登录
    public function uniappauthTimebank()
    {
        global $_GPC;
        global $_W;

        $openId = trim($_GPC['openId']);
        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid_wa=:openid_wa and uniacid=:uniacid limit 1', array(':openid_wa' => $openId, ':uniacid' => $_W['uniacid']));

        if (empty($member)) {
            return app_error(-1, '会员未找到，请先去注册');
        }

        if ($_GPC['isDirect'] && $member) {
            $data['error'] = 0;
            $data['id'] = $member['id'];
            $data['mobile'] = $member['mobile'];
            $data['openid'] = $member['openid_wa'];
            $data['uniacid'] = $member['uniacid'];
            $data['isblack'] = $member['isblack'];

            return app_json($data, $data['openid']);
        } else {
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

                if (empty($member)) {
                    $member = array('uniacid' => $_W['uniacid'], 'uid' => 0, 'openid' => 'sns_wa_' . $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1', 'openid_wa' => $data['openId'], 'comefrom' => 'sns_wa', 'createtime' => time(), 'status' => 0);
                    pdo_insert('ewei_shop_member', $member);
                    $id = pdo_insertid();
                    $data['id'] = $id;
                    $data['uniacid'] = $_W['uniacid'];

                    if (method_exists(m('member'), 'memberRadisCountDelete')) {
                        m('member')->memberRadisCountDelete();
                    }
                } else {
                    $updateData = array('openid_wa' => $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1','openid_wa' => $data['openId']);
                    pdo_update('ewei_shop_member', $updateData, array('id' => $member['id'], 'uniacid' => $member['uniacid']));
                    $data['error'] = 0;
                    $data['id'] = $member['id'];
                    $data['mobile'] = $member['mobile'];
                    $data['openid'] = $member['openid_wa'];
                    $data['uniacid'] = $member['uniacid'];
                    $data['isblack'] = $member['isblack'];
                }

                if (p('commission')) {
                    p('commission')->checkAgent($member['openid']);
                }

                return app_json($data, $data['openId']);
            }
        }

        return app_error(-1, '登录错误, 错误代码: ' . $errCode);
    }

      //针对 uniapp的用户信息录入
      public function groupearnauth()
      {
            global $_GPC;
            global $_W;
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
                  $this->refine($data['openId']);
                  $data['openid'] = 'sns_wa_' .$data['openId'];
                  $member = m('member')->getMember('sns_wa_' . $data['openId']);

                  if (empty($member)) {
                        $member = array('uniacid' => $_W['uniacid'], 'uid' => 0, 'openid' => 'sns_wa_' . $data['openId'], 'nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1', 'openid_wa' => $data['openId'], 'comefrom' => 'sns_wa', 'createtime' => time(), 'status' => 0);
                        pdo_insert('ewei_shop_member', $member);
                        $id = pdo_insertid();
                        $data['id'] = $id;
                        $data['uniacid'] = $_W['uniacid'];

                        if (method_exists(m('member'), 'memberRadisCountDelete')) {
                              m('member')->memberRadisCountDelete();
                        }
                  }
                  else {
                        $updateData = array('nickname' => !empty($data['nickName']) ? $data['nickName'] : '', 'avatar' => !empty($data['avatarUrl']) ? $data['avatarUrl'] : '', 'gender' => !empty($data['gender']) ? $data['gender'] : '-1','openid_wa' => $data['openId']);
                        pdo_update('ewei_shop_member', $updateData, array('id' => $member['id'], 'uniacid' => $member['uniacid']));
                        $data['id'] = $member['id'];
                        $data['uniacid'] = $member['uniacid'];
                        $data['isblack'] = $member['isblack'];
                  }

                  if (p('commission')) {
                        p('commission')->checkAgent($member['openid']);
                  }

                  return app_json($data, $data['openId']);
            }

            return app_error(-1, '登录错误, 错误代码: ' . $errCode);
      }

      public function getmobile(){
            global $_GPC;
            global $_W;

            $encryptedData = trim($_GPC['data']);
            $iv = trim($_GPC['iv']);
            $sessionKey = trim($_GPC['sessionKey']);
            if (empty($encryptedData) || empty($iv)) {
                  return app_error(AppError::$ParamsError);
            }

            $pc = new WXBizDataCrypt($this->appid, $sessionKey);
            $errCode = $pc->decryptData($encryptedData, $iv, $data);
            if ($errCode == 0) {
                  $data=json_decode($data,true);

                  $member = m('member')->getMember($_GPC['openid']);

                  pdo_update('ewei_shop_member', array('mobile' => $data['phoneNumber'],'mobileverify'=>1), array('id' => $member['id'],'uniacid' => $_W['uniacid']));

                  return app_json(array(
                      'res' => $data,
                  ));
            } else {
                  return app_error(AppError::$WxAppError, '错误, 错误代码: ' . $errCode);
            }
      }

	/**
     * 处理注册用户openid多生成一个sns_wa_前缀的问题
     * 例如:
     *   正常:sns_wa_oX-v90Cdn4BpQSByrQZgS8dKLK_w
     *   异常:sns_wa_sns_wa_oX-v90Cdn4BpQSByrQZgS8dKLK_w
     * @param $openid string
     */
	protected function refine(&$openid)
	{
		if (substr($openid, 0, 7) == 'sns_wa_') {
			$openid = substr($openid, 7);
		}
	}
}

?>
