<?php

namespace app\common\service\qrcode;

use app\common\model\setting\Setting as SettingModel;
use Grafika\Color;
use Grafika\Grafika;
use Endroid\QrCode\QrCode;

class RoomService extends Base
{
    // 房间信息
    private $room;

    // 来源，微信小程序，公众号
    private $source;

    // 分享用户信息
    private $user;

    // 小程序码链接
    //private $pages = 'pages/pagesLive/live/live';
    private $pages = 'pages/index/index';

    /**
     * 构造方法
     */
    public function __construct($room, $user, $source)
    {
        parent::__construct();
        // 商品信息
        $this->room = $room;
        //来源
        $this->source = $source;
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        // 判断海报图文件存在则直接返回url
        if (file_exists($this->getPosterPath())) {
            return $this->getPosterUrl();
        }
        // 小程序id
        $appId = $this->room['app_id'];
        // 商品海报背景图
        $backdrop = __DIR__ . '/resource/room_bg.png';
        // 下载推广图
        $shareUrl = $this->saveTempImage($appId, $this->room['share']['file_path'], 'temp');
        // 2. 下载用户头像
        $avatarUrl = $this->saveTempImage($appId, $this->room['user']['avatarUrl'], 'avatar');
        // 3. 下载分享用户头像
        $shareAvatarUrl = $this->saveTempImage($appId, $this->user['avatarUrl'], 'avatar');
        $qrcode = null;
        if($this->source == 'wx'){
            // 小程序码参数
            $scene = "rid:{$this->room['room_id']}";
            // 下载小程序码
            $qrcode = $this->saveQrcode($appId, $scene, $this->pages);
        }else if($this->source == 'mp' || $this->source == 'h5'){
            $scene = "rid:{$this->room['room_id']}";
            $qrcode = new QrCode(base_url().'h5/'.$this->pages.'?roomId='.$this->room['room_id']).'&app_id='.$appId;
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'image_mp');
        }else if($this->source == 'app'){
            $appshare = SettingModel::getItem('appshare');
            $down_url = '';
            //下载页
            if($appshare['bind_type'] == 1){
                $down_url = $appshare['down_url'];
            }else{
                $down_url = base_url(). "/index.php/api/user.useropen/invite?app_id=".$appId."&referee_id=" .$this->user['user_id'];
            }

            $scene = 'uid:' . $this->user['user_id'];
            $qrcode = new QrCode($down_url);
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'image_app');
        }
        // 拼接海报图
        return $this->savePoster($backdrop, $shareUrl, $qrcode, $avatarUrl, $shareAvatarUrl);
    }

    /**
     * 拼接海报图
     */
    private function savePoster($backdrop, $shareUrl, $qrcode, $avatarUrl, $shareAvatarUrl)
    {
        // 实例化图像编辑器
        $editor = Grafika::createEditor(['Gd']);
        // 字体文件路径
        $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
        // 打开海报背景图
        $editor->open($backdropImage, $backdrop);

        // 生成圆形用户头像
        $this->circular($avatarUrl, $avatarUrl);
        // 打开用户头像
        $editor->open($avatarImage, $avatarUrl);
        // 重设用户头像宽高
        $avatarWidth = 110;
        $editor->resizeExact($avatarImage, $avatarWidth, $avatarWidth);
        // 用户头像添加到背景图
        $editor->blend($backdropImage, $avatarImage, 'normal', 1.0, 'top-left', 30, 30);

        // 写入用户昵称
        $editor->text($backdropImage, $this->room['user']['nickName'], 28, 150, 50, new Color('#333333'), $fontPath);
        // 写入开播时间
        $editor->text($backdropImage, '开播时间：'.substr($this->room['create_time'], 0, 16), 24, 150, 100, new Color('#ff0000'), $fontPath);
        // 打开分享图
        $editor->open($shareImage, $shareUrl);
        // 重设分享图宽高
        $editor->resizeExact($shareImage, 690, 690);
        // 分享图添加到背景图
        $editor->blend($backdropImage, $shareImage, 'normal', 1.0, 'top-left', 30, 160);
        // 直播间名称处理换行
        $fontSize = 30;
        $productName = $this->wrapText($fontSize, 0, $fontPath, $this->room['name'], 680, 2);
        // 写入商品名称
        $editor->text($backdropImage, $productName, $fontSize, 30, 900, new Color('#333333'), $fontPath);
        // 打开小程序码
        $editor->open($qrcodeImage, $qrcode);
        // 重设小程序码宽高
        $editor->resizeExact($qrcodeImage, 140, 140);
        // 小程序码添加到背景图
        $editor->blend($backdropImage, $qrcodeImage, 'normal', 1.0, 'top-left', 570, 914);

        //分享用户信息
        // 生成圆形用户头像
        $this->circular($shareAvatarUrl, $shareAvatarUrl);
        // 打开用户头像
        $editor->open($shareAvatarImage, $shareAvatarUrl);
        // 重设用户头像宽高
        $avatarWidth = 60;
        $editor->resizeExact($shareAvatarImage, $avatarWidth, $avatarWidth);
        // 用户头像添加到背景图
        $editor->blend($backdropImage, $shareAvatarImage, 'normal', 1.0, 'top-left', 30, 1000);
        // 写入用户昵称
        $editor->text($backdropImage, $this->user['nickName'].'向你推荐', 18, 100, 1020, new Color('#333333'), $fontPath);

        // 保存图片
        $editor->save($backdropImage, $this->getPosterPath());
        return $this->getPosterUrl();
    }

    /**
     * 处理文字超出长度自动换行
     */
    private function wrapText($fontsize, $angle, $fontface, $string, $width, $max_line = null)
    {
        // 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
        $content = "";
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        $letter = [];
        for ($i = 0; $i < mb_strlen($string, 'UTF-8'); $i++) {
            $letter[] = mb_substr($string, $i, 1, 'UTF-8');
        }
        $line_count = 0;
        foreach ($letter as $l) {
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $content . ' ' . $l);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $line_count++;
                if ($max_line && $line_count >= $max_line) {
                    $content = mb_substr($content, 0, -1, 'UTF-8') . "...";
                    break;
                }
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }

    /**
     * 海报图文件路径
     */
    private function getPosterPath()
    {
        // 保存路径
        $tempPath = root_path('public') . 'temp' . '/' . $this->room['app_id'] . '/' . $this->source. '/';
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 海报图文件名称
     */
    private function getPosterName()
    {
        return 'room_' . md5("{$this->room['room_id']}}{$this->user['user_id']}}") . '.png';
    }

    /**
     * 海报图url
     */
    private function getPosterUrl()
    {
        return \base_url() . 'temp/' . $this->room['app_id'] . '/' .$this->source . '/' . $this->getPosterName() . '?t=' . time();
    }


    /**
     * 生成圆形图片
     */
    private function circular($imgpath, $saveName = '')
    {
        $srcImg = imagecreatefromstring(file_get_contents($imgpath));
        $w = imagesx($srcImg);
        $h = imagesy($srcImg);
        $w = $h = min($w, $h);
        $newImg = imagecreatetruecolor($w, $h);
        // 这一句一定要有
        imagesavealpha($newImg, true);
        // 拾取一个完全透明的颜色,最后一个参数127为全透明
        $bg = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefill($newImg, 0, 0, $bg);
        $r = $w / 2; //圆半径
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($srcImg, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($newImg, $x, $y, $rgbColor);
                }
            }
        }
        // 输出图片到文件
        imagepng($newImg, $saveName);
        // 释放空间
        imagedestroy($srcImg);
        imagedestroy($newImg);
    }
}