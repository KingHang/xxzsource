<?php

namespace app\common\service\qrcode;

use Endroid\QrCode\QrCode;

/**
 * 计次商品、服务核销二维码
 */
class VerifyService extends Base
{
    private $appId;

    //用户
    private $userId;

    private $order_product_id;

    private $verify_code;

    private $product_type;

    private $type; // 类型  0：自提核销 1：计次商品 2：服务,计次商品,4，旅游商品

    /**
     * 构造方法
     */
    public function __construct($appId, $userId, $order_product_id, $product_type, $verify_code,$type = 0)
    {
        parent::__construct();
        $this->appId = $appId;
        $this->userId = $userId;
        $this->order_product_id = $order_product_id;
        $this->product_type = $product_type;
        $this->verify_code = $verify_code;
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
        $qrcode = new QrCode(json_encode(['verify_code' => $this->verify_code , 'type' => $this->type]));
        $qrcode = $this->saveMpQrcode($qrcode, $this->appId, $this->order_product_id, 'image_mp');
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
        $tempPath = $web_path . "/temp/{$this->appId}/{$this->product_type}/";
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 二维码文件名称
     */
    private function getPosterName()
    {
        return 'clerk_' . md5("{$this->order_product_id}_{$this->userId}}") . '.png';
    }

    /**
     * 二维码url
     */
    private function getPosterUrl()
    {
        return \base_url() . "temp/{$this->appId}/{$this->product_type}/{$this->getPosterName()}" . '?t=' . time();
    }

}