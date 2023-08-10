<?php

namespace app\common\service\qrcode;

use app\common\model\setting\Setting as SettingModel;
use Endroid\QrCode\QrCode;

/**
 * 分销订单二维码
 */
class OrderService extends Base
{
    private $order_no;
    private $app_id;
    private $code_url;
    private $source;

    /**
     * 构造方法
     */
    public function __construct($order_no,$app_id,$code_url,$source)
    {
        parent::__construct();
        $this->order_no = $order_no;
        $this->app_id = $app_id;
        $this->code_url = $code_url;
        $this->source = $source;
    }

    /**
     * 获取支付二维码
     */
    public function getImage()
    {
        // 判断二维码文件存在则直接返回url
        if (file_exists($this->getPosterPath())) {
            return $this->getPosterUrl();
        }
        $qrcode = new QrCode($this->code_url);
//        $qrcode = $qrcode->setLogoPath($this->getLogopath());
        $qrcode = $this->savePayQrcode($qrcode, $this->app_id,$this->order_no,$this->source,$this->getLogopath());
        return $this->savePoster($qrcode);
    }

    private function savePoster($qrcode)
    {
        return $this->getPosterUrl();
    }
    private function getLogopath(){
        $web_path = $_SERVER['DOCUMENT_ROOT'];
        $logo_name = $this->source == 'wxpay' ? '20' : '30';
        $logo_name .= ".png";
        return $web_path . "/image/payment/" . $logo_name;
    }
    /**
     * 二维码文件路径
     */
    private function getPosterPath()
    {
        $web_path = $_SERVER['DOCUMENT_ROOT'];
        // 保存路径
        $tempPath = $web_path . "/temp/{$this->app_id}/{$this->source}/{$this->order_no}/";
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 二维码文件名称
     */
    private function getPosterName()
    {
        return 'qrcode_' . md5("{$this->order_no}_{$this->source}") . '.png';
    }

    /**
     * 二维码url
     */
    private function getPosterUrl()
    {
        return \base_url() . "temp/{$this->app_id}/{$this->source}/{$this->order_no}/{$this->getPosterName()}" . '?t=' . time();
    }
}