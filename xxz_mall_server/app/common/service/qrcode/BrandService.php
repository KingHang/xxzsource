<?php

namespace app\common\service\qrcode;

use app\common\model\settings\Settings as SettingModel;
use Grafika\Color;
use Grafika\Grafika;
use Endroid\QrCode\QrCode;

class BrandService extends Base
{
    // 商品信息
    private $product;

    // 用户id
    private $user_id;

    // 商品类型：10商城商品 20拼团商品
    private $productType;

    // 来源，微信小程序，公众号
    private $source;

    // 小程序码链接
    private $pages = [
        10 => 'pages/product/detail/detail',
        70 => 'pages/product/detail/detail',
//        20 => 'pages/product/detail/detail',
        30 => 'pages/plugin/seckill/detail/detail',
        60 => 'pages/product/detail/detail',
        40 => 'pages/plugin/assemble/detail/detail ',
        50 => 'pages/plugin/pricedown/detail/detail',
    ];

    /**
     * 构造方法
     */
    public function __construct($product, $user,$source, $productType = 10)
    {
        parent::__construct();
        // 商品信息
        $this->product = $product;
        $this->user = $user;
        // 当前用户id
        $this->user_id = $user ? $user['user_id'] : 0;
        // 商品类型：10商城商品
        $this->productType = $productType;
        //来源
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {

        // 判断海报图文件存在则直接返回url
        // if (file_exists($this->getPosterPath())) {
        //     return $this->getPosterUrl();
        // }
        // 小程序id
        $appId = $this->product['app_id'];

        // 商品海报背景图
        $backdrop = __DIR__ . '/resource/product_bg2.png';
        // 下载商品首图
        $productUrl = $this->saveTempImage($appId, $this->product['brand']['image']['file_path'], 'product');

        $qrcode = null;
        if($this->source == 'wx'){

            // 小程序码参数
            $scene = "gid:{$this->product['sign_id']},uid:" . ($this->user_id ?: '');
            // 下载小程序码

            $qrcode = $this->saveQrcode($appId, $scene, $this->pages[$this->productType]);

        }else if($this->source == 'mp' || $this->source == 'h5'){
            $scene = "gid:{$this->product['sign_id']},uid:" . ($this->user_id ?: '');
            $qrcode = new QrCode(base_url().'h5/pages/product/detail/detail?product_id='.$this->product['sign_id'].'&app_id='.$appId.'&referee_id='.$this->user_id ?: '');
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'image_mp');
        }

        // 拼接海报图
        return $this->savePoster($backdrop, $productUrl, $qrcode);
    }

    /**
     * 拼接海报图
     */
    private function savePoster($backdrop, $productUrl, $qrcode)
    {
        // 小程序id
        $appId = $this->product['app_id'];
        // 实例化图像编辑器
        $editor = Grafika::createEditor(['Gd']);
        // 字体文件路径
        $fontPath = Grafika::fontsDir() . '/' . 'st-heiti-light.ttc';
        // 打开海报背景图
        $editor->open($backdropImage, $backdrop);
        // 打开商品图片
        $editor->open($productImage, $productUrl);
        // 重设商品图片宽高
        $editor->resizeExact($productImage, 620, 620);
        // 商品图片添加到背景图
        $editor->blend($backdropImage, $productImage, 'normal', 1.0, 'top-left', 65, 190);

        if ($this->product['signLog']) {
            $left = 0;
            $top = 820;
            foreach ($this->product['signLog'] as $key => $item) {

                if ($item['product'] && $key < 4) {
                    $pUrl = $this->saveTempImage($appId, $item['product']['image'][0]['file_path'], 'pUrl');
                    $editor->open($pImage, $pUrl);
                    // 重设商品图片宽高
                    $editor->resizeExact($pImage, 152, 154);
                    // 商品图片添加到背景图
                    $left = 65 + 155 * $key;
                    $editor->blend($backdropImage, $pImage, 'normal', 1.0, 'top-left', $left, $top);

                    $pBg = $this->saveTempImage($appId, 'https://img.dfhlyl.com/20220830175343bc7278870.png', 'pBg');
                    $editor->open($pBgImage, $pBg);
                    $editor->resizeExact($pBgImage, 152, 50);
                    $editor->blend($backdropImage, $pBgImage, 'normal', 1.0, 'top-left', $left, 920);

                    $fontSize = 24;
                    $pGood = $this->wrapText($fontSize, 0, $fontPath, '￥' . $item['product']['product_price'], 850, 80);
                    $editor->text($backdropImage, $pGood, $fontSize, $left+5, 933, new Color('#ffffff'), $fontPath);
                }
            }
        }
        // 价格图
        $priceBg = $this->saveTempImage($appId, 'https://img.dfhlyl.com/202208301752284474a7742.png', 'priceBg');
        $editor->open($priceBgImage, $priceBg);
        $editor->resizeExact($priceBgImage, 360, 110);
        $editor->blend($backdropImage, $priceBgImage, 'normal', 1.0, 'top-left', 68, 700);
        // 写入商品价格
        $fontSize = 30;
        $priceUnit = $this->wrapText($fontSize, 0, $fontPath, '￥', 900, 80);
        $editor->text($backdropImage, $priceUnit, $fontSize, 85, 745, new Color('#ffffff'), $fontPath);
        $fontSize = 30;
        $priceType = [10 => 'product_price',20 => 'product_price',30 => 'product_price',40 => 'product_price',50 => 'product_price',60=>'product_price',70=>'product_price'];
        $priceGood = $this->wrapText($fontSize, 0, $fontPath, $this->product['signLog'][0]['product']['sku'][0][$priceType[$this->productType]], 850, 80);
        $editor->text($backdropImage, $priceGood, $fontSize, 125, 745, new Color('#ffffff'), $fontPath);

        // 单品数量图
        $numBg = $this->saveTempImage($appId, 'https://img.dfhlyl.com/20220830175417092ea5257.png', 'numBg');
        $editor->open($numBgImage, $numBg);
        $editor->resizeExact($numBgImage, 228, 76);
        $editor->blend($backdropImage, $numBgImage, 'normal', 1.0, 'top-left', 430, 210);
        $fontSize = 30;
        $numGood = $this->wrapText($fontSize, 0, $fontPath, $this->product['product_down']."件单品", 850, 80);
        $editor->text($backdropImage, $numGood, $fontSize, 450, 230, new Color('#ffffff'), $fontPath);

        // 商城名
        // $fontSize = 26;
        // $store = SettingModel::getItem('store');
        // $this->product['store_name'] = $store['name'];
        // $name = $this->wrapText($fontSize, 0, $fontPath, $this->product['store_name'], 850, 80);
        // $editor->text($backdropImage, $name, $fontSize, 575, 1250, new Color('#FFFFFF'), $fontPath);

        // 头像和昵称
        if ($this->user) {
            $avatar = $this->saveTempImage($appId, $this->user['avatarUrl'], 'avatar');
            $editor->open($avatorImage, $avatar);
            $editor->resizeExact($avatorImage, 70, 70);
            $editor->blend($backdropImage, $avatorImage, 'normal', 1.0, 'top-left', 40, 1230);
            $fontSize = 26;
            $nickname = $this->wrapText($fontSize, 0, $fontPath, $this->user['nickName'] . ' 推荐', 850, 80);
            $editor->text($backdropImage, $nickname, $fontSize, 120, 1250, new Color('#FFFFFF'), $fontPath);
        }
        // 店铺头像和名称
        $supplier_logo = $this->product['supplier']['logo'] ? $this->product['supplier']['logo']['file_path'] : 'https://img.dfhlyl.com/20220831093556caa370944.png';
        $supplier = $this->saveTempImage($appId, $supplier_logo, 'supplier');
        $this->circular($supplier, $supplier);
        $editor->open($shopImage, $supplier);
        $editor->resizeExact($shopImage, 70, 70);
        $editor->blend($backdropImage, $shopImage, 'normal', 1.0, 'top-left', 495, 1230);
        $fontSize = 26;
        $shopName = $this->wrapText($fontSize, 0, $fontPath, $this->product['supplier']['name'], 300, 1230);
        $editor->text($backdropImage, $shopName, $fontSize, 570, 1250, new Color('#ffffff'), $fontPath);

        // 品牌日名称和结束时间
        $fontSize = 32;
        $length = strlen($this->product['brandday']['brand_day_name']);
        $pName = mb_substr($this->product['brandday']['brand_day_name'],0,14,'utf-8');
        $this->product['brandday']['brand_day_name'] = $length > 14 ? $pName . '...' : $pName;
        $productName = $this->wrapText($fontSize, 0, $fontPath, $this->product['brandday']['brand_day_name'], 900, 80);

        $editor->text($backdropImage, $productName, $fontSize, 200, 60, new Color('#333333'), $fontPath);
        $fontSize = 24;
        $length = strlen($this->product['brandday']['end_time_text']);
        $pName = mb_substr($this->product['brandday']['end_time_text'],0,14,'utf-8');
        $this->product['brandday']['brand_day_name'] = $length > 14 ? $pName . '...' : $pName;
        $productName = $this->wrapText($fontSize, 0, $fontPath, $this->product['brandday']['end_time_text']."结束", 900, 80);
        $editor->text($backdropImage, $productName, $fontSize, 200, 115, new Color('#999999'), $fontPath);

        $supplier = $this->saveTempImage($appId, $this->product['brand']['image']['file_path'], 'supplier');
        $editor->open($shopImage, $supplier);
        $editor->resizeExact($shopImage, 112, 112);
        $editor->blend($backdropImage, $shopImage, 'normal', 1.0, 'top-left', 70, 50);

        // $productName = $this->wrapText($fontSize, 0, $fontPath, "划线价".$this->product['product']['sku'][0]['line_price'], 900, 80);
        // $editor->text($backdropImage, $productName, $fontSize, 80, 100, new Color('#333333'), $fontPath);

        // 打开小程序码
        $editor->open($qrcodeImage, $qrcode);
        // 重设小程序码宽高
        $editor->resizeExact($qrcodeImage, 140, 140);
        // 小程序码添加到背景图
        $editor->blend($backdropImage, $qrcodeImage, 'normal', 1.0, 'top-left', 80, 1030);

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
        $tempPath = root_path('public') . 'temp' . '/' . $this->product['app_id'] . '/' . $this->source. '/';
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 海报图文件名称
     */
    private function getPosterName()
    {
        return 'product_' . md5("{$this->user_id}_{$this->productType}_{$this->product['sign_id']}") . '.png';
    }

    /**
     * 海报图url
     */
    private function getPosterUrl()
    {
        return \base_url() . 'temp/' . $this->product['app_id'] . '/' .$this->source . '/' . $this->getPosterName() . '?t=' . time();
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