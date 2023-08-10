<?php

namespace app\common\service\qrcode;

use app\common\model\setting\Setting as SettingModel;
use Endroid\QrCode\QrCode;

/**
 * 分销二维码
 */
class AgentService extends Base
{
    // 分销商用户信息
    private $agent;
    // 来源
    private $source;

    /**
     * 构造方法
     */
    public function __construct($agent, $source)
    {
        parent::__construct();
        // 分销商用户信息
        $this->agent = $agent;
        $this->source = $source;
    }

    /**
     * 获取分销二维码
     */
    public function getImage($isShare = 0,$type = 0)
    {
        // 小程序id
        $appId = $this->agent['app_id'];
        $qrcode = null;
        if ($this->source == 'wx') {
            // 3. 下载小程序码
            $scene = $isShare == 1 ? 'share_id:' . $this->agent['user_id'] : 'uid:' . $this->agent['user_id'];
            $qrcode = $this->saveAgentQrcode($appId, $scene, 'pages/index/index',$type);
        } else if ($this->source == 'mp' || $this->source == 'h5') {
            $scene = $isShare == 1 ? 'share_id:' . $this->agent['user_id'] : 'uid:' . $this->agent['user_id'];
            $url = base_url() . 'h5/pages/index/index?referee_id=' . $this->agent['user_id'] . '&app_id=' . $appId;
            $qrcode = new QrCode($url);
            $qrcode = $this->saveAgentMpQrcode($qrcode, $appId, $scene, 'image_mp');
        } else if ($this->source == 'app') {
            $appshare = SettingModel::getItem('appshare');
            if ($appshare['type'] == 1) {
                $down_url = $appshare['open_site'] . '?app_id=' . $this->agent['app_id'] . '&referee_id=' . $this->agent['user_id'];
            } else {
                //下载页
                if ($appshare['bind_type'] == 1) {
                    $down_url = $appshare['down_url'];
                } else {
                    $down_url = base_url() . "/index.php/api/user.useropen/invite?app_id=" . $this->agent['app_id'] . "&referee_id=" . $this->agent['user_id'];
                }
            }
            $scene = $isShare == 1 ? 'share_id:' . $this->agent['user_id'] : 'uid:' . $this->agent['user_id'];
            $qrcode = new QrCode($down_url);
            $qrcode = $this->saveAgentMpQrcode($qrcode, $appId, $scene, 'image_app');
        }
        return base_url() . $qrcode;
    }
}