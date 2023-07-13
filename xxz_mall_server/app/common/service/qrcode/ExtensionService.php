<?php

namespace app\common\service\qrcode;

use app\common\enum\order\OrderTypeEnum;
use Endroid\QrCode\QrCode;

/**
 * 推广二维码
 */
class ExtensionService extends Base
{
    private $id;


    /**
     * 构造方法
     */
    public function __construct($id, $type)
    {
        parent::__construct();
        $this->id = $id;
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
        $scene = "oid:{$this->id},otype:" . ($this->id ?: '');
        if ($this->type == 'invitation') {
            $qrcode = new QrCode(base_url() . 'pages/plugin/giftpackage/giftpackage?invitation_gift_id=' . $this->id . '&order_type=' . $this->id ?: '');
        } elseif ($this->type == 'extension') {
            $qrcode = new QrCode(base_url() . 'pages/plugin/giftpackage/giftpackage?gift_package_id=' . $this->id . '&order_type=' . $this->id ?: '');
        }
        $qrcode = $this->saveMpQrcode($qrcode, $this->id, $scene);
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
        $tempPath = $web_path . "/{$this->type}/{$this->id}/";
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 二维码文件名称
     */
    private function getPosterName()
    {
        return 'clerk_' . md5("{$this->id}}") . '.png';
    }

    /**
     * 二维码url
     */
    private function getPosterUrl()
    {
        return \base_url() . $this->type . '/' . $this->id . '/' . $this->getPosterName() . '?t=' . time();
    }

}