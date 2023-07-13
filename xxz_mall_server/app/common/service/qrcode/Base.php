<?php

namespace app\common\service\qrcode;

use app\common\library\easywechat\AppWx;
/**
 * 二维码服务基类
 */
class Base
{
    /**
     * 构造方法
     */
    public function __construct()
    {
    }

    /**
     * 保存小程序码到文件
     */
    protected function saveQrcode($app_id, $scene, $page)
    {
        // 文件目录
        $dirPath = root_path('public') . "/temp/{$app_id}/image_wx";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene . $page) . '.png';
        // 文件路径
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $savePath;
        // 小程序配置信息
        $app = AppWx::getApp($app_id);
        // 请求api获取小程序码
        $response = $app->app_code->getUnlimit($scene, [
            'home' => $page,
            'width' => 430
        ]);
        // 保存小程序码到文件
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $response->saveAs($dirPath, $fileName);
        }
        return $savePath;
    }

    /**
     * 保存普通二维码到文件
     */
    protected function saveMpQrcode(\Endroid\QrCode\QrCode $qrcode, $app_id, $scene, $source)
    {
        // 文件目录
        $dirPath = root_path('public') ."/temp/{$app_id}/{$source}";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene) . '.png';
        // 文件路径
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $savePath;
        // 保存二维码到文件
        $qrcode->writeFile($savePath);
        return $savePath;
    }
    /**
     * 保存小程序码到文件
     */
    protected function saveAgentQrcode($app_id, $scene, $page,$type = 0)
    {
        // 文件目录
        $dirPath = root_path('public') . "/temp/{$app_id}/image_wx";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene . $page) . '.png';

        $file = "/temp/{$app_id}/image_wx/$fileName";
        // 文件路径
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $type == 1 ? $file : $savePath;
        // 小程序配置信息
        $app = AppWx::getApp($app_id);
        // 请求api获取小程序码
        $response = $app->app_code->getUnlimit($scene, [
            'home' => $page,
            'width' => 430
        ]);
        // 保存小程序码到文件
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $response->saveAs($dirPath, $fileName);
        }
        return $file;
    }

    /**
     * 保存普通二维码到文件
     */
    protected function saveAgentMpQrcode(\Endroid\QrCode\QrCode $qrcode, $app_id, $scene, $source)
    {
        // 文件目录
        $dirPath = root_path('public') ."/temp/{$app_id}/{$source}";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 文件名称
        $fileName = 'qrcode_' . md5($app_id . $scene) . '.png';
        // 文件路径
        // 文件路径
        $file = "/temp/{$app_id}/$source/$fileName";
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $file;
        // 保存二维码到文件
        $qrcode->writeFile($savePath);
        return $file;
    }
    /**
     * 保存支付二维码到文件
     */
    protected function savePayQrcode(\Endroid\QrCode\QrCode $qrcode, $app_id, $order_no, $source,$logo)
    {
        // 文件目录
        $dirPath = root_path('public') . "/temp/{$app_id}/{$source}/{$order_no}/";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 文件名称
        $fileName = 'qrcode_' . md5("{$order_no}_{$source}") . '.png';
        // 文件路径
        // 文件路径
        $file = "/temp/{$app_id}/$source/$fileName";
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $file;
//        $qrcode->setLogoPath($logo);
//        $qrcode->setLogoSize(40,40);
        // 保存二维码到文件
        $qrcode->writeFile($savePath);
        return $file;
    }
    /**
     * 获取网络图片到临时目录
     */
    protected function saveTempImage($app_id, $url, $mark = 'temp')
    {
        $dirPath = root_path('public') . "temp/{$app_id}/{$mark}";
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        $savePath = $dirPath . '/' . $mark . '_' . md5($url) . '.png';
        if (file_exists($savePath)) return $savePath;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $img = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($savePath, 'w');
        fwrite($fp, $img);
        fclose($fp);
        return $savePath;
    }

}