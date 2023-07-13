<?php

namespace app\common\service\qrcode;

use app\common\model\plugin\agent\Grade;
use app\common\model\settings\Settings as SettingModel;
use Endroid\QrCode\QrCode;
use Grafika\Color;
use Grafika\Grafika;
use app\common\model\plugin\agent\Setting;

/**
 * 分销二维码
 */
class PosterService extends Base
{
    // 分销商用户信息
    private $agent;
    // 分销商海报设置
    private $config;
    // 来源
    private $source;

    private $poster;
    private $gm;
    private $logId;

    /**
     * 构造方法
     */
    public function __construct($agent, $source='' ,$poster=0,$backgrd=null,$text='',$gm_title='',$gm=0,$logId=0,$grade=[],$certificate_num=0)
    {
        parent::__construct();
        // 分销商用户信息
        $this->agent = $agent;
        $this->source = $source;
        $this->poster = $poster;
        $this->gm = $gm;
        $this->gmId = $logId;

        // 分销商海报设置
        if ($poster == 0) {
            $this->config = Setting::getItem('qrcode', $agent['app_id']);
        } else if ($poster == 1) {
            // 海报
            $arr = [];
            $arr['backdrop']['src'] = $backgrd;
            $arr['nickName']['fontSize'] = 10;
            $arr['nickName']['color'] = "FFFFFF";
            $arr['nickName']['left'] = 120;
            $arr['nickName']['top'] = 1260;

            $arr['avatar']['width'] = 30;
            $arr['avatar']['style'] = "circle";
            $arr['avatar']['left'] = 44;
            $arr['avatar']['top'] = 1240;

            $arr['mobile']['fontSize'] = 8;
            $arr['mobile']['color'] = "FFFFFF";
            $arr['mobile']['left'] = 280;
            $arr['mobile']['top'] = 600;

            $arr['phonePng']['width'] = 13;
            $arr['phonePng']['style'] = "circle";
            $arr['phonePng']['left'] = 263;
            $arr['phonePng']['top'] = 597;
            $arr['phonePng']['src'] = "http://img.pighack.com/20220510154531619282327.png";

            $this->config = $arr;
        } else if ($poster == 2) {
            // 早安
            $arr = [];
            $arr['backdrop']['src'] = 'http://img.pighack.com/202205231531283c97d5468.jpg';
            $text1 = mb_substr($text,0,21);
            $text2 = mb_substr($text,21,21);
            $text3 = mb_substr($text,42,21);
            $text4 = mb_substr($text,63,21);
            $text5 = mb_substr($text,84,21);
            $text6 = mb_substr($text,105,21);
            $text7 = mb_substr($text,126,21);
            $text8 = mb_substr($text,147,21);
            $text9 = mb_substr($text,168,21);
            $text10 = mb_substr($text,189,21);
            $text11 = mb_substr($text,210,21);
            $text12 = mb_substr($text,231,21);

            $arr['headerBg']['src'] = $backgrd;
            $arr['headerBg']['width'] = 750;
            $arr['headerBg']['height'] = 560;
            $arr['headerBg']['style'] = "square";
            $arr['headerBg']['left'] = 0;
            $arr['headerBg']['top'] = 0;

            $arr['nickName']['fontSize'] = 10;
            $arr['nickName']['color'] = "999999";
            $arr['nickName']['left'] = 120;
            $arr['nickName']['top'] = 1240;

            $arr['mobile']['fontSize'] = 8;
            $arr['mobile']['color'] = "999999";
            $arr['mobile']['left'] = 60;
            $arr['mobile']['top'] = 600;

            $arr['avatar']['width'] = 30;
            $arr['avatar']['style'] = "circle";
            $arr['avatar']['left'] = 44;
            $arr['avatar']['top'] = 1240;

            $arr['tip']['fontSize'] = 9;
            $arr['tip']['color'] = "999999";
            $arr['tip']['left'] = 585;
            $arr['tip']['top'] = 1140;

            $arr['qrcode']['width'] = 135;
            $arr['qrcode']['height'] = 135;
            $arr['qrcode']['style'] = "square";
            $arr['qrcode']['left'] = 580;
            $arr['qrcode']['top'] = 1180;

            $arr['gm_text']['fontSize'] = 10;
            $arr['gm_text']['color'] = "999999";
            $arr['gm_text']['left'] = 46;
            $arr['gm_text']['top'] = 300;
            $arr['gm_text']['text'] = $text1;

            $arr['gm_text2']['fontSize'] = 10;
            $arr['gm_text2']['color'] = "999999";
            $arr['gm_text2']['left'] = 46;
            $arr['gm_text2']['top'] = 315;
            $arr['gm_text2']['text2'] = $text2;

            $arr['gm_text3']['fontSize'] = 10;
            $arr['gm_text3']['color'] = "999999";
            $arr['gm_text3']['left'] = 46;
            $arr['gm_text3']['top'] = 330;
            $arr['gm_text3']['text3'] = $text3;

            $arr['gm_text4']['fontSize'] = 10;
            $arr['gm_text4']['color'] = "999999";
            $arr['gm_text4']['left'] = 46;
            $arr['gm_text4']['top'] = 345;
            $arr['gm_text4']['text4'] = $text4;

            $arr['gm_text5']['fontSize'] = 10;
            $arr['gm_text5']['color'] = "999999";
            $arr['gm_text5']['left'] = 46;
            $arr['gm_text5']['top'] = 360;
            $arr['gm_text5']['text5'] = $text5;

            $arr['gm_text6']['fontSize'] = 10;
            $arr['gm_text6']['color'] = "999999";
            $arr['gm_text6']['left'] = 46;
            $arr['gm_text6']['top'] = 375;
            $arr['gm_text6']['text6'] = $text6;

            $arr['gm_text7']['fontSize'] = 10;
            $arr['gm_text7']['color'] = "999999";
            $arr['gm_text7']['left'] = 46;
            $arr['gm_text7']['top'] = 390;
            $arr['gm_text7']['text7'] = $text7;

            $arr['gm_text8']['fontSize'] = 10;
            $arr['gm_text8']['color'] = "999999";
            $arr['gm_text8']['left'] = 46;
            $arr['gm_text8']['top'] = 405;
            $arr['gm_text8']['text8'] = $text8;

            $arr['gm_text9']['fontSize'] = 10;
            $arr['gm_text9']['color'] = "999999";
            $arr['gm_text9']['left'] = 46;
            $arr['gm_text9']['top'] = 420;
            $arr['gm_text9']['text9'] = $text9;

            $arr['gm_text10']['fontSize'] = 10;
            $arr['gm_text10']['color'] = "999999";
            $arr['gm_text10']['left'] = 46;
            $arr['gm_text10']['top'] = 435;
            $arr['gm_text10']['text10'] = $text10;

            $arr['gm_text11']['fontSize'] = 10;
            $arr['gm_text11']['color'] = "999999";
            $arr['gm_text11']['left'] = 46;
            $arr['gm_text11']['top'] = 450;
            $arr['gm_text11']['text11'] = $text11;

            $arr['gm_text12']['fontSize'] = 10;
            $arr['gm_text12']['color'] = "999999";
            $arr['gm_text12']['left'] = 46;
            $arr['gm_text12']['top'] = 465;
            $arr['gm_text12']['text12'] = $text12 ? $text12 . '...' : '';

            $arr['gm_title']['fontSize'] = 13;
            $arr['gm_title']['color'] = "#000000";
            $arr['gm_title']['left'] = 50;
            $arr['gm_title']['top'] = 270;
            $arr['gm_title']['text'] = $gm_title;

            $this->config = $arr;
        } else if ($poster == 3) {
//            $level_num = 1;
//            $grade_array = Grade::field('weight,grade_id')->order('weight','asc')->select()->toArray();
//            foreach ($grade_array as $k =>$v){
//                if ($v['grade_id'] == $grade['grade_id']){
//                    $level_num = $k+1;
//                }
//            }
//            $level_num = $this->numToWord($level_num);
            // 证书
            $arr = [];
            $arr['backdrop']['src'] = $grade['certificate_bg'];
            $arr['nickName']['fontSize'] = 10;
            $arr['nickName']['color'] = "999999";
            $arr['nickName']['left'] = 120;
            $arr['nickName']['top'] = 1240;

            // 一星孝亲楷模
            $textColor = '';
            if ($grade['certificate_bg'] == 'https://img.dfhlyl.com/20220714092034437c89371.jpg') {
                $textColor = '441982';
            } else if ($grade['certificate_bg'] == 'https://img.dfhlyl.com/20220714092124866764125.jpg') {
                // 三星孝亲楷模
                $textColor = '831a5c';
            } else if ($grade['certificate_bg'] == 'https://img.dfhlyl.com/20220714092142d792d0436.jpg') {
                // 五星孝亲楷模
                $textColor = '82191e';
            }

            $arr['level_text1']['fontSize'] = 55;
            $arr['level_text1']['color'] = $textColor;
            $arr['level_text1']['left'] = 230;
            $arr['level_text1']['top'] = 920;

            $arr['level_text5']['fontSize'] = 40;
            $arr['level_text5']['color'] = $textColor;
            $arr['level_text5']['left'] = 430;
            $arr['level_text5']['top'] = 1210;
            $arr['level_text5']['text'] = $grade['name'];

            $arr['level_text6']['fontSize'] = 40;
            $arr['level_text6']['color'] = $textColor;
            $arr['level_text6']['left'] = 275;
            $arr['level_text6']['top'] = 1565;
            $arr['level_text6']['text'] = $certificate_num;

            $this->config = $arr;
        } else if ($poster == 4) {
            $this->config = Setting::getItem('qrcode', $agent['app_id']);

            $this->config['avatar']['width'] = 58;
            $this->config['avatar']['style'] = "square";
            $this->config['avatar']['left'] = 40;
            $this->config['avatar']['top'] = 60;

            $this->config['nickName']['fontSize'] = 20;
            $this->config['nickName']['color'] = "333333";
            $this->config['nickName']['left'] = 180;
            $this->config['nickName']['top'] = 70;

            $this->config['tip']['fontSize'] = 34;
            $this->config['tip']['color'] = "999999";
            $this->config['tip']['left'] = 180;
            $this->config['tip']['top'] = 130;
            $this->config['tip']['text'] = '精彩尽在 汇乐宝商城';

            $this->config['qrcode']['width'] = 90;
            $this->config['qrcode']['height'] = 90;
            $this->config['qrcode']['style'] = "circle";
            $this->config['qrcode']['left'] = 50;
            $this->config['qrcode']['top'] = 1080;
        }
    }

    /**
     * 获取分销二维码
     */
    public function getImage()
    {
        // if (file_exists($this->getPosterPath())) {
        //     return $this->getPosterUrl();
        // }
        // 小程序id
        $appId = $this->agent['app_id'];
        // 1. 下载背景图
        // 2. 下载用户头像
        $phonePng='';
        $mobile ='';
        $headerBg = '';
        $avatarUrl = '';
        if ($this->poster==1) {
            // 海报
            $avatarUrl = $this->saveTempImage($appId, $this->agent['avatarUrl'], 'avatar');
            $backdrop = $this->saveTempImage($appId, $this->config['backdrop']['src'], 'backdrop');
            $phonePng = $this->saveTempImage($appId, $this->config['phonePng']['src'], 'phonePng');
        } else if ($this->poster==2) {
            // 早安
            $avatarUrl = $this->saveTempImage($appId, $this->agent['avatarUrl'], 'avatar');
            $backdrop = $this->saveTempImage($appId, $this->config['backdrop']['src'], 'backdrop');
            $headerBg = $this->saveTempImage($appId, $this->config['headerBg']['src'], 'headerBg');
        } else if ($this->poster==3) {
            // 证书
            $backdrop = $this->saveTempImage($appId, $this->config['backdrop']['src'], 'backdrop');
        } else if ($this->poster==4) {
            // 店铺
            $src = 'https://img.dfhlyl.com/20220801135735b3ff72377.png'; // 汇乐宝
//            $src = 'https://img.dfhlyl.com/202208011427479f53f9167.png'; // 王牌特供
            $backdrop = $this->saveTempImage($appId, $src, 'backdrop');
            $avatarUrl = $this->saveTempImage($appId, $this->agent['user']['avatarUrl'], 'avatar');
        } else {
            // 分销
            $avatarUrl = $this->saveTempImage($appId, $this->agent['user']['avatarUrl'], 'avatar');
            $backdrop = $this->saveTempImage($appId, $this->config['backdrop']['src'], 'backdrop');
        }

        $qrcode = null;
        if ($this->source == 'wx') {
            // 3. 下载小程序码
            if ($this->poster == 4) {
                // 店铺海报
                $scene = 'share_id:' . $this->agent['user_id'];
                $qrcode = $this->saveAgentQrcode($appId, $scene, 'pages/index/index');
            } else {
                $scene = 'uid:' . $this->agent['user_id'];
                $qrcode = $this->saveQrcode($appId, $scene, 'pages/login/login');
            }
        } else if ($this->source == 'mp' || $this->source == 'h5') {
            $scene = 'uid:' . $this->agent['user_id'];
            $url = base_url().'h5/pages/index/index?referee_id='.$this->agent['user_id'].'&app_id='.$appId;
            $qrcode = new QrCode($url);
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'image_mp');

        } else if ($this->source == 'app') {
            $appshare = SettingModel::getItem('appshare');
            if($appshare['type'] == 1){
                $down_url = $appshare['open_site']. '?app_id='.$this->agent['app_id'].'&referee_id=' .$this->agent['user_id'];
            }else{
                //下载页
                if($appshare['bind_type'] == 1){
                    $down_url = $appshare['down_url'];
                }else{
                    $down_url = base_url(). "/index.php/api/user.useropen/invite?app_id=".$this->agent['app_id']."&referee_id=" .$this->agent['user_id'];
                }
            }

            $scene = 'uid:' . $this->agent['user_id'];
            $qrcode = new QrCode($down_url);
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'image_app');
        } else if ($this->source == 'gm') {
            $url = 'https://' . $_SERVER['HTTP_HOST'] . '/goodMorningRead?log_id=' . $this->logId;
            $qrcode = new QrCode($url);
            $scene = 'log_id:' . $this->logId;
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'image_mp');
        }

        // 4. 拼接海报图
        return $this->savePoster($backdrop, $avatarUrl, $qrcode,$phonePng, $headerBg);
    }

    /**
     * 海报图文件路径
     */
    private function getPosterPath()
    {
        // 保存路径
        $tempPath = root_path('public') . 'temp/' . $this->agent['app_id'] . '/' . $this->source. '/';

        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 海报图文件名称
     */
    private function getPosterName()
    {
        return 'poster_' . md5($this->agent['user_id']) . '.png';
    }

    /**
     * 海报图url
     */
    private function getPosterUrl()
    {
        return \base_url() . 'temp/' . $this->agent['app_id'] . '/' .$this->source . '/' . $this->getPosterName() . '?t=' . time();
    }

    /**
     * 拼接海报图
     */
    private function savePoster($backdrop, $avatarUrl, $qrcode,$phonePng, $headerBg='')
    {
        // 实例化图像编辑器
        $editor = Grafika::createEditor(['Gd']);
        // 打开海报背景图
        $editor->open($backdropImage, $backdrop);

        if ($avatarUrl) {
            // 生成圆形用户头像
            $this->config['avatar']['style'] === 'circle' && $this->circular($avatarUrl, $avatarUrl);
            // 打开用户头像
            $editor->open($avatarImage, $avatarUrl);
            // 重设用户头像宽高
            $avatarWidth = $this->config['avatar']['width'] * 2;
            $editor->resizeExact($avatarImage, $avatarWidth, $avatarWidth);
            // 用户头像添加到背景图
            if ($this->source == 'wx' && $this->poster != 4) {
                $avatarX = $this->config['avatar']['left'] * 2;
                $avatarY = $this->config['avatar']['top'] * 2 + 60;
            } else {
                $avatarX = $this->config['avatar']['left'];
                $avatarY = $this->config['avatar']['top'];
            }

            $editor->blend($backdropImage, $avatarImage, 'normal', 1.0, 'top-left', $avatarX, $avatarY);
        }

        // 生成圆形小程序码，仅小程序支持
        if ($this->source == 'wx') {
            $this->config['qrcode']['style'] === 'circle' && $this->circular($qrcode, $qrcode);
            // 打开小程序码
            $editor->open($qrcodeImage, $qrcode);
            // 重设小程序码宽高
            $qrcodeWidth = $this->config['qrcode']['width'] * 2;
            $editor->resizeExact($qrcodeImage, $qrcodeWidth, $qrcodeWidth);

            // 小程序码添加到背景图
            if ($this->poster == 4) {
                // 店铺海报
                $qrcodeX = $this->config['qrcode']['left'];
                $qrcodeY = $this->config['qrcode']['top'];
            } else {
                $qrcodeX = $this->config['qrcode']['left'] * 2;
                $qrcodeY = $this->config['qrcode']['top'] * 2 + 60;
            }
            $editor->blend($backdropImage, $qrcodeImage, 'normal', 1.0, 'top-left', $qrcodeX, $qrcodeY);
        }

        // 写入用户昵称
        $fontSize = $this->config['nickName']['fontSize'] * 2;
        if ($this->source == 'wx' && $this->poster != 4) {
            $fontX = $this->config['nickName']['left'] * 2;
            $fontY = $this->config['nickName']['top'] * 2 + 60;
        } else {
            $fontX = $this->config['nickName']['left'];
            $fontY = $this->config['nickName']['top'];
        }
        $Color = new Color($this->config['nickName']['color']);
        $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
        // 海报
        if ($this->poster == 1) {
            $editor->text($backdropImage, $this->agent['nickName'], $fontSize, $fontX, $fontY, $Color, $fontPath);
            $fontSize = $this->config['mobile']['fontSize'] * 2;
            $fontX = $this->config['mobile']['left'] * 2;
            $fontY = $this->config['mobile']['top'] * 2 + 60;
            $Color = new Color($this->config['mobile']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->agent['mobile'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            // 海报
            // 生成圆形手机图标
            $this->config['phonePng']['style'] === 'circle' && $this->circular($phonePng, $phonePng);
            $editor->open($phonePng, $phonePng);
            // 重设手机图标宽高
            $avatarWidth = $this->config['phonePng']['width'] * 2;
            $editor->resizeExact($phonePng, $avatarWidth, $avatarWidth);
            // 图标添加到背景图
            $avatarX = $this->config['phonePng']['left'] * 2;
            $avatarY = $this->config['phonePng']['top'] * 2 + 60;
            $editor->blend($backdropImage, $phonePng, 'normal', 1.0, 'top-left', $avatarX, $avatarY);
        } else if ($this->poster == 2) {
            // 早安
            $editor->text($backdropImage, $this->agent['nickName'], $fontSize, $fontX, $fontY, $Color, $fontPath);
            $fontSize = $this->config['mobile']['fontSize'] * 2;
            $fontX = $this->config['mobile']['left'] * 2;
            $fontY = $this->config['mobile']['top'] * 2 + 60;
            $Color = new Color($this->config['mobile']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->agent['mobile'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            if ($headerBg) {
                $editor->open($headerBgImage, $headerBg);
                $editor->resizeExact($headerBgImage, $this->config['headerBg']['width'], $this->config['headerBg']['height']);
                $qrcodeX = $this->config['headerBg']['left'];
                $qrcodeY = $this->config['headerBg']['top'];
                $editor->blend($backdropImage, $headerBgImage, 'normal', 1.0, 'top-left', $qrcodeX, $qrcodeY);
            }

            $editor->open($qrcodeImage, $qrcode);
            $editor->resizeExact($qrcodeImage, $this->config['qrcode']['width'], $this->config['qrcode']['height']);
            $editor->blend($backdropImage, $qrcodeImage, 'normal', 1.0, 'top-left', $this->config['qrcode']['left'], $this->config['qrcode']['top']);

            $fontSize = $this->config['tip']['fontSize'] * 2;
            $Color = new Color($this->config['tip']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, '扫码说早安', $fontSize, $this->config['tip']['left'], $this->config['tip']['top'], $Color, $fontPath);

            $fontSize = $this->config['gm_text']['fontSize'] * 2;
            $fontX = $this->config['gm_text']['left'] * 2;
            $fontY = $this->config['gm_text']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text']['text'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text2']['fontSize'] * 2;
            $fontX = $this->config['gm_text2']['left'] * 2;
            $fontY = $this->config['gm_text2']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text2']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text2']['text2'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text3']['fontSize'] * 2;
            $fontX = $this->config['gm_text3']['left'] * 2;
            $fontY = $this->config['gm_text3']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text3']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text3']['text3'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text4']['fontSize'] * 2;
            $fontX = $this->config['gm_text4']['left'] * 2;
            $fontY = $this->config['gm_text4']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text4']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text4']['text4'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text5']['fontSize'] * 2;
            $fontX = $this->config['gm_text5']['left'] * 2;
            $fontY = $this->config['gm_text5']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text5']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text5']['text5'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text6']['fontSize'] * 2;
            $fontX = $this->config['gm_text6']['left'] * 2;
            $fontY = $this->config['gm_text6']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text6']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text6']['text6'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text7']['fontSize'] * 2;
            $fontX = $this->config['gm_text7']['left'] * 2;
            $fontY = $this->config['gm_text7']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text7']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text7']['text7'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text8']['fontSize'] * 2;
            $fontX = $this->config['gm_text8']['left'] * 2;
            $fontY = $this->config['gm_text8']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text8']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text8']['text8'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text9']['fontSize'] * 2;
            $fontX = $this->config['gm_text9']['left'] * 2;
            $fontY = $this->config['gm_text9']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text9']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text9']['text9'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text10']['fontSize'] * 2;
            $fontX = $this->config['gm_text10']['left'] * 2;
            $fontY = $this->config['gm_text10']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text10']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text10']['text10'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text11']['fontSize'] * 2;
            $fontX = $this->config['gm_text11']['left'] * 2;
            $fontY = $this->config['gm_text11']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text11']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text11']['text11'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_text12']['fontSize'] * 2;
            $fontX = $this->config['gm_text12']['left'] * 2;
            $fontY = $this->config['gm_text12']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_text12']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_text12']['text12'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['gm_title']['fontSize'] * 2;
            $fontX = $this->config['gm_title']['left'] * 2;
            $fontY = $this->config['gm_title']['top'] * 2 + 60;
            $Color = new Color($this->config['gm_title']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['gm_title']['text'], $fontSize, $fontX, $fontY, $Color, $fontPath);
        } else if ($this->poster == 3) {
            // 证书
//            $fontSize = $this->config['level_text']['fontSize'];
//            $Color = new Color($this->config['level_text']['color']);
//            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
//            $editor->text($backdropImage, $this->config['level_text']['text'], $fontSize, $this->config['level_text']['left'], $this->config['level_text']['top'], $Color, $fontPath);

            $fontSize = $this->config['level_text1']['fontSize'];
            $fontX = $this->config['level_text1']['left'];
            $fontY = $this->config['level_text1']['top'];
            $Color = new Color($this->config['level_text1']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->agent['real_name'] . '：', $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['level_text5']['fontSize'];
            $fontX = $this->config['level_text5']['left'];
            $fontY = $this->config['level_text5']['top'];
            $Color = new Color($this->config['level_text5']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['level_text5']['text'], $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['level_text6']['fontSize'];
            $fontX = $this->config['level_text6']['left'];
            $fontY = $this->config['level_text6']['top'];
            $Color = new Color($this->config['level_text6']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['level_text6']['text'], $fontSize, $fontX, $fontY, $Color, $fontPath);
        } else if ($this->poster == 4) {
            // 店铺海报
            $editor->text($backdropImage, $this->agent['user']['nickName'] . '的店铺', $fontSize, $fontX, $fontY, $Color, $fontPath);

            $fontSize = $this->config['tip']['fontSize'];
            $fontX = $this->config['tip']['left'];
            $fontY = $this->config['tip']['top'];
            $Color = new Color($this->config['tip']['color']);
            $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
            $editor->text($backdropImage, $this->config['tip']['text'] . '：', $fontSize, $fontX, $fontY, $Color, $fontPath);
        } else {
            $editor->text($backdropImage, $this->agent['user']['nickName'], $fontSize, $fontX, $fontY, $Color, $fontPath);
        }

        // 保存图片
        $editor->save($backdropImage, $this->getPosterPath());
        return $this->getPosterUrl();
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
    //数字转成汉字
    function numToWord($num){
        $chiNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        $chiUni = array('','十', '百', '千', '万', '亿', '十', '百', '千');
        $chiStr = '';
        $num_str = (string)$num;
        $count = strlen($num_str);
        $last_flag = true; //上一个 是否为0
        $zero_flag = true; //是否第一个
        $temp_num = null; //临时数字
        $chiStr = '';//拼接结果
        if ($count == 2) {//两位数
            $temp_num = $num_str[0];
            $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num].$chiUni[1];
            $temp_num = $num_str[1];
            $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
        }else if($count > 2){
            $index = 0;
            for ($i=$count-1; $i >= 0 ; $i--) {
                $temp_num = $num_str[$i];
                if ($temp_num == 0) {
                    if (!$zero_flag && !$last_flag ) {
                        $chiStr = $chiNum[$temp_num]. $chiStr;
                        $last_flag = true;
                    }
                }else{
                    $chiStr = $chiNum[$temp_num].$chiUni[$index%9] .$chiStr;
                    $zero_flag = false;
                    $last_flag = false;
                }
                $index ++;
            }
        }else{
            $chiStr = $chiNum[$num_str[0]];
        }
        return $chiStr;
    }

}