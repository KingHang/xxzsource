<?php
//dezend by http://www.yunlu99.com/
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require_once EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Uniapp_EweiShopV2Page extends AppMobilePage
{
	public function __construct()
	{
		global $_W;

	}

	public function wechath5(){
          global $_W;
          global $_GPC;
          $sec = m('common')->getSec();
          $sec = iunserializer($sec['sec']);
          $wxuser = $this->wxuser($_GPC['code'],'wxb7c02a45a4d31605', '2e6ac11dea90b6bd6085b1fdf532413c','snsapi_userinfo');





          return app_json(array('wxuser' => $wxuser));
    }
      public function wxuser($code,$appid, $secret, $snsapi = 'snsapi_base', $expired = '600') {
            global $_W;
            $wxuser = $_COOKIE[$_W['config']['cookie']['pre'] . $appid];

            if ($wxuser === NULL) {
                  $http = 'http://';
                  if (isset($_W['config']['setting']['https']) && !empty($_W['config']['setting']['https'])) {
                        $http = 'https://';
                  }

                  load()->func('communication');
                  $getOauthAccessToken = ihttp_get('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code');
                  $json = json_decode($getOauthAccessToken['content'], true);
                  if (!empty($json['errcode']) && ($json['errcode'] == '40029' || $json['errcode'] == '40163')) {
                        $url = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '' : '?');
                        $parse = parse_url($url);

                        if (isset($parse['query'])) {
                              parse_str($parse['query'], $params);
                              unset($params['code']);
                              unset($params['state']);
                              $url = $http . $_SERVER['HTTP_HOST'] . $parse['path'] . '?' . http_build_query($params);
                        }

                        $oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=' . $snsapi . '&state=wxbase#wechat_redirect';
                        header('Location: ' . $oauth_url);
                        exit();
                  }

                  if ($snsapi == 'snsapi_userinfo') {
                        $userinfo = ihttp_get('https://api.weixin.qq.com/sns/userinfo?access_token=' . $json['access_token'] . '&openid=' . $json['openid'] . '&lang=zh_CN');
                        $userinfo = $userinfo['content'];
                  } else {
                        if ($snsapi == 'snsapi_base') {
                              $userinfo = array();
                              $userinfo['openid'] = $json['openid'];
                        }
                  }

                  $userinfostr = json_encode($userinfo);
                  isetcookie($appid, $userinfostr, $expired);
                  return $userinfo;
            }

            return json_decode($wxuser, true);
      }

}

?>
