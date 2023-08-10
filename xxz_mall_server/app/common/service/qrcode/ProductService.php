<?php

namespace app\common\service\qrcode;

use app\common\model\setting\Setting as SettingModel;
use Grafika\Color;
use Grafika\Grafika;
use Endroid\QrCode\QrCode;

class ProductService extends Base
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
        10 => 'pages/goods/detail/detail',
        20 => 'pagesBrand/brand/detail',
//        20 => 'pages/goods/detail/detail',
        30 => 'pages/plugin/flashsell/detail/detail',
//        30 => 'pages/goods/detail/detail',
        40 => 'pages/plugin/groupsell/detail/detail ',
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
    public function getProductImage()
    {
        // 判断二维码文件存在则直接返回url
        if (file_exists($this->getPosterPath())) {
            return $this->getPosterUrl();
        }
        // 拼接请求地址
        $base_url = base_url();
        $param = $base_url . 'productDetail?id=' . $this->product['product_id'];
        $qrcode = new QrCode($param);
        $qrcode = $this->saveMpQrcode($qrcode, $this->product['product_id'], $this->product['product_id'], 'image_mp');
        return $this->saveProductPoster($qrcode);
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
        $appId = $this->product['app_id'];
        // 商品海报背景图
        $backdrop = __DIR__ . '/resource/product_bg1.png';
        // 下载商品首图
        $productUrl = $this->saveTempImage($appId, $this->product['image'][0]['file_path'], 'product');
        $qrcode = null;
        if($this->source == 'wx'){
            // 小程序码参数
            $scene = "gid:{$this->product['product_id']},uid:" . ($this->user_id ?: '');
            // 下载小程序码
            $qrcode = $this->saveQrcode($appId, $scene, $this->pages[$this->productType]);
        }else if($this->source == 'mp' || $this->source == 'h5'){
            $scene = "gid:{$this->product['product_id']},uid:" . ($this->user_id ?: '');
            $qrcode = new QrCode(base_url().'h5/pages/goods/detail/detail?product_id='.$this->product['product_id'].'&app_id='.$appId.'&referee_id='.$this->user_id ?: '');
            $qrcode = $this->saveMpQrcode($qrcode, $appId, $scene, 'image_mp');
        }
        // 拼接海报图
        return $this->savePoster($backdrop, $productUrl, $qrcode);
    }
    private function saveProductPoster($qrcode)
    {
        copy($qrcode, $this->getPosterPath());
        return $this->getPosterUrl();
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
        $editor->blend($backdropImage, $productImage, 'normal', 1.0, 'top-left', 65, 60);

        $priceBg = $this->saveTempImage($appId, 'https://img.dfhlyl.com/20220802103858c143d8808.png', 'priceBg');
        $editor->open($priceBgImage, $priceBg);
        $editor->resizeExact($priceBgImage, 360, 110);
        $editor->blend($backdropImage, $priceBgImage, 'normal', 1.0, 'top-left', 325, 570);

        // 商城名
        $fontSize = 26;
        $store = SettingModel::getItem('store');
        $this->product['store_name'] = $store['name'];
        $name = $this->wrapText($fontSize, 0, $fontPath, $this->product['store_name'], 850, 80);
        $editor->text($backdropImage, $name, $fontSize, 575, 1250, new Color('#FFFFFF'), $fontPath);

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
        $supplier = $this->saveTempImage($appId, $this->product['supplier']['logo']['file_path'], 'supplier');
        $this->circular($supplier, $supplier);
        $editor->open($shopImage, $supplier);
        $editor->resizeExact($shopImage, 90, 90);
        $editor->blend($backdropImage, $shopImage, 'normal', 1.0, 'top-left', 320, 715);
        $fontSize = 26;
        $shopName = $this->wrapText($fontSize, 0, $fontPath, $this->product['supplier']['name'], 850, 80);
        $editor->text($backdropImage, $shopName, $fontSize, 80, 830, new Color('#999999'), $fontPath);

        // 商品名称处理换行
        $fontSize = 30;
        $length = strlen($this->product['product_name']);
        $pName = mb_substr($this->product['product_name'],0,14,'utf-8');
        $this->product['product_name'] = $length > 14 ? $pName . '...' : $pName;
        $productName = $this->wrapText($fontSize, 0, $fontPath, $this->product['product_name'], 900, 80);
        // 写入商品名称
        $editor->text($backdropImage, $productName, $fontSize, 80, 900, new Color('#333333'), $fontPath);
        // 写入商品价格
        $fontSize = 24;
        $priceUnit = $this->wrapText($fontSize, 0, $fontPath, '￥', 850, 80);
        $editor->text($backdropImage, $priceUnit, $fontSize, 340, 620, new Color('#ffffff'), $fontPath);
        $fontSize = 48;
        $priceType = [10 => 'product_price',20 => 'product_price',30 => 'product_price',40 => 'product_price',50 => 'product_price'];
        $priceGood = $this->wrapText($fontSize, 0, $fontPath, $this->product['sku'][0][$priceType[$this->productType]], 850, 80);
        $editor->text($backdropImage, $priceGood, $fontSize, 365, 600, new Color('#ffffff'), $fontPath);
        // 打开小程序码
        $editor->open($qrcodeImage, $qrcode);
        // 重设小程序码宽高
        $editor->resizeExact($qrcodeImage, 140, 140);
        // 小程序码添加到背景图
        $editor->blend($backdropImage, $qrcodeImage, 'normal', 1.0, 'top-left', 80, 1000);

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
        return 'product_' . md5("{$this->user_id}_{$this->productType}_{$this->product['product_id']}") . '.png';
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