<?php
declare (strict_types = 1);

namespace app\common\model\qrcode;

use app\common\model\app\AppOpen as AppOpenModel;
use app\common\model\app\AppWx;
use app\common\model\settings\Settings as SettingModel;
use app\common\service\qrcode\Base;
use Endroid\QrCode\QrCode;
use think\Model;
use think\facade\Env;

/**
 * @mixin \think\Model
 */
class GetQrcode extends Base
{
    public  function getQrcode($type)
    {
        if ($type==1){
            $data = AppWx::detail();
            $APPID = $data->wxapp_id;
            $SECRET = $data->wxapp_secret;
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$APPID&secret=$SECRET";
            $data = json_decode(file_get_contents($url),true);
            $access_token = $data['access_token'];
            $urls = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$access_token";
            $path='pages/index/index';
            return curlPost($urls,json_encode(['path'=>$path]));
        }else{
            $scene = 111;
            $url = base_url()."wwww.baidu.com";
            $qrcode = new QrCode($url);
            $qrcode = $this->saveAgentMpQrcode($qrcode, 222, $scene, 'image_mp');
            return base_url().$qrcode;
        }

    }
}
