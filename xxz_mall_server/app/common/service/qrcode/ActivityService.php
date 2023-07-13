<?php

namespace app\common\service\qrcode;

use Endroid\QrCode\QrCode;
use think\facade\Request;

/**
 * 活动签到二维码
 */
class ActivityService extends Base
{
    private $appId;

    private $log_id;

    private $verify_code;

    private $user_id;
    private $type;
    /**
     * 构造方法
     */
    public function __construct($appId, $log_id, $verify_code,$user_id,$type=0)
    {
        parent::__construct();
        $this->appId = $appId;
        $this->log_id = $log_id;
        $this->verify_code = $verify_code;
        $this->user_id = $user_id;
        $this->type = $type;
    }

    /**
     * 获取小程序码
     */
    public function getImage()
    {
        // 判断二维码文件存在则直接返回url
        if (file_exists($this->getPosterPath())) {
            return $this->getPosterUrl();
        }
        // 拼接请求地址
        $base_url = base_url();
        if ($this->type == 1) {
            $param = $base_url . 'activitySign?id=' . $this->log_id;
        } elseif ($this->type == 2) {
            $param = $base_url . 'activityShare?id=' . $this->log_id;
        } else {
            $param = json_encode(['id'=>$this->log_id,'verify_code'=>$this->verify_code,'Interface'=>"plugin.activity.activityLog/sign",'token'=>'','app_id'=>'']);
        }
        $qrcode = new QrCode($param);
        $qrcode = $this->saveMpQrcode($qrcode, $this->appId, $this->log_id, 'image_mp'.$this->type);
        return $this->savePoster($qrcode);
    }

    private function savePoster($qrcode)
    {
        copy($qrcode, $this->getPosterPath());
        return $this->getPosterUrl();
    }

    /**
     * 二维码文件路径
     */
    private function getPosterPath()
    {
        $web_path = $_SERVER['DOCUMENT_ROOT'];
        // 保存路径
        $tempPath = $web_path . "/temp/{$this->appId}/{$this->log_id}/{$this->type}/";
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 二维码文件名称
     */
    private function getPosterName()
    {
        return 'clerk_' . md5("{$this->log_id}_{$this->user_id}}") . '.png';
    }

    /**
     * 二维码url
     */
    private function getPosterUrl()
    {
        return \base_url() . "temp/{$this->appId}/{$this->log_id}/{$this->type}/{$this->getPosterName()}" . '?t=' . time();
    }

}