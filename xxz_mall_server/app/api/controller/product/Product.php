<?php

namespace app\api\controller\product;

use app\api\model\product\Goods as ProductModel;
use app\api\model\order\Cart as CartModel;
use app\api\controller\Controller;
use app\api\model\settings\Settings as SettingModel;
use app\api\model\user\Visit as VisitModel;
use app\api\service\common\RecommendService;
use app\common\model\plugin\brand\BranddaySign;
use app\common\model\plugin\brand\SignLog;
use app\common\model\plugin\material\Material;
use app\common\service\qrcode\BrandService;
use app\common\service\qrcode\MaterialService;
use app\common\service\qrcode\ProductService;
use app\api\model\user\Favorite as FavoriteModel;
use app\api\model\plus\coupon\Voucher as CouponModel;
use app\common\model\purveyor\Service as ServiceModel;

/**
 * 商品控制器
 */
class Product extends Controller
{
    /**
     * 商品列表
     */
    public function lists()
    {
        // 整理请求的参数
        $param = array_merge($this->postData(), [
            'product_status' => 10,
            'audit_status' => 10
        ]);

        // 获取列表数据
        $model = new ProductModel;
        $list = $model->getList($param, $this->getUser(false));
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 商品列表
     */
    public function list()
    {
        // 整理请求的参数
        $param = array_merge($this->postData(), [
            'product_status' => 10,
            'audit_status' => 10
        ]);

        // 获取列表数据
        $model = new ProductModel;
        $list = $model->getList($param);
        return $this->renderSuccess('', compact('list'));
    }

    /**
     * 推荐产品
     */
    public function recommendProduct($location)
    {
        $recommend = SettingModel::getItem('recommend');
        $model = new ProductModel;
        $is_recommend = RecommendService::checkRecommend($recommend, $location);
        $list = [];
        if ($is_recommend) {
            $list = $model->getRecommendProduct($recommend);
        }
        return $this->renderSuccess('', compact('list', 'recommend', 'is_recommend'));
    }

    /**
     * 获取商品详情
     */
    public function detail($product_id, $url = '')
    {
        $params = $this->postData();
        // 用户信息
        $user = $this->getUser(false);
        // 商品详情
        $model = new ProductModel;
        $product = $model->getDetails($product_id, $this->getUser(false),['benefit']);
        if ($product === false) {
            return $this->renderError($model->getError() ?: '商品信息不存在');
        }
        // 多规格商品sku信息
        $specData = $product['spec_type'] == 20 ? $model->getManySpecData($product['spec_rel'], $product['sku'],$product['after_coupon']) : null;
        $isfollow = 0;
        if ($user) {
            if (FavoriteModel::detail($product_id, 20, $user['user_id'])) {
                $isfollow = 1;
            }
        }
        $product['isfollow'] = $isfollow;
        $dataCoupon['shop_supplier_id'] = $product['shop_supplier_id'];
        $model = new CouponModel;
        $couponList = $model->getWaitList($dataCoupon, $this->getUser(false), 1);
        // 购物车商品总数量
        $cartModel = new CartModel($user);
        $cartcount = $cartModel->getProductNum();
        // 访问记录
//        (new VisitModel())->addVisit($user, $product['supplier'], $params['visitcode'], $product);
        return $this->renderSuccess('', [
            // 购物车数量
            'cartcount' => $cartcount,
            // 商品详情
            'detail' => $product,
            // 购物车商品总数量
            'cart_total_num' => $user ? (new CartModel($user))->getProductNum() : 0,
            // 多规格商品sku信息
            'specData' => $specData,
            // 微信公众号分享参数
            'share' => $this->getShareParams($url, $product['product_name'], $product['product_name'], '/pages/product/detail/detail', $product['image'][0]['file_path']),
            'couponList' => $couponList,
            //是否显示店铺信息
            'store_open' => SettingModel::getStoreOpen(),
            //是否开启客服
            'service_open' => SettingModel::getSysConfig()['service_open'],
            //店铺客服信息
            'mp_service' => ServiceModel::detail($product['shop_supplier_id']),
        ]);
    }
    public function getImage($product_id)
    {
        $product['product_id'] = $product_id;
        $product['app_id'] = 10001;
        $Qrcode = new ProductService(
            $product,
            $user = '',
            $source ='',
            ''
        );
        return $this->renderSuccess('', [
            'url' => $Qrcode->getProductImage(),
        ]);
    }

    /**
     * 获取详情
     */
    public function getDetail($product_id)
    {
        // 商品详情
        $model = new ProductModel;
        $detail = $model->getDetails($product_id);
        if ($detail === false) {
            return $this->renderError($model->getError() ?: '商品信息不存在');
        }
        // 多规格商品sku信息
        $specData = $detail['spec_type'] == 20 ? $model->getManySpecData($detail['spec_rel'], $detail['sku']) : null;
        return $this->renderSuccess('', [
            // 商品详情
            'detail' => $detail,
            // 多规格商品sku信息
            'specData' => $specData,
        ]);
    }

    /**
     * 生成商品海报
     */
    public function poster($product_id, $source,$productType=10)
    {
        // 商品详情
        $detail = "";
        if ($productType==10){
            $detail = ProductModel::detail($product_id);
            $Qrcode = new ProductService($detail, $this->getUser(false), $source,$productType);
        }
        if ($productType==20){
            $log = SignLog::detail($product_id);//log_id
            $productId = $log->product_id;
            $detail = ProductModel::detail($productId);
            $detail->product_id = $product_id;
            $Qrcode = new ProductService($detail, $this->getUser(false), $source,$productType);
        }if ($productType==30){
            $log = \app\common\model\plugin\flashsell\Product::detail($product_id);//log_id
            $productId = $log->product_id;
            $detail = ProductModel::detail($productId);
            $detail->product_id = $product_id;
            $Qrcode = new ProductService($detail, $this->getUser(false), $source,$productType);
        }
        if ($productType==40){
            $log = \app\common\model\plugin\groupsell\Goods::detail($product_id);
            $productId = $log->product_id;
            $detail = ProductModel::detail($productId);
            $detail->product_id = $product_id;
            $Qrcode = new ProductService($detail, $this->getUser(false), $source,$productType);
        }
        if ($productType==50){
            $log = \app\common\model\plugin\pricedown\Product::detail($product_id);
            $productId = $log->product_id;
            $detail = ProductModel::detail($productId);
            $detail->product_id = $product_id;
            $Qrcode = new ProductService($detail, $this->getUser(false), $source,$productType);
        }
        if ($productType==60){//素材海报
            $detail = (new Material())->detail($product_id);
            $Qrcode = new MaterialService($detail, $this->getUser(false), $source,$productType);
        }if ($productType==70){//素材海报
             $detail = BranddaySign::detail($product_id);
            $Qrcode = new BrandService($detail, $this->getUser(false), $source,$productType);
        }
        return $this->renderSuccess('', [
            'qrcode' => $Qrcode->getImage(),
        ]);
    }
}