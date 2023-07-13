<?php

namespace app\api\model\product;

use app\common\model\plugin\voucher\Voucher;
use app\common\model\plugin\productgift\CouponProduct;
use app\common\model\plugin\productgift\Productgift;
use app\common\model\goods\Goods as ProductModel;
use app\common\service\product\BaseProductService;
use app\common\library\helper;
use app\api\model\supplier\Purveyor as SupplierModel;
use app\api\model\supplier\ServiceApply;
use app\common\model\purveyor\User as SupplierUserModel;
/**
 * 商品模型
 */
class Goods extends ProductModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'spec_rel',
        'delivery',
        'sales_initial',
        'sales_actual',
        'is_delete',
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * 商品详情：HTML实体转换回普通字符
     */
    public function getContentAttr($value)
    {
        return htmlspecialchars_decode($value);
    }

    /**
     * 获取商品列表
     */
    public function getList($param, $userInfo = false)
    {
        // 获取商品列表
        $data = (new ProductModel())->getList($param);

        // 隐藏api属性
        !$data->isEmpty() && $data->hidden(['category', 'content', 'image', 'sku']);
        // 整理列表数据并返回
        return $this->setProductListDataFromApi($data, true, ['userInfo' => $userInfo]);
    }

    /**
     * 商品详情
     */
    public static function detail($product_id)
    {
        // 商品详情
        $detail = parent::detail($product_id);
        // 多规格商品sku信息
        $detail['product_multi_spec'] = BaseProductService::getSpecData($detail);
        return $detail;
    }

    /**
     * 获取商品详情页面
     */
    public function getDetails($productId, $userInfo = false,$with=[])
    {
        // 获取商品详情
        $detail = $this->with(array_merge($with,[
            'category',
            'image' => ['file'],
            'contentImage' => ['file'],
            'sku' => ['image','productgift'=>['productgiftsku'=>['product'=>['image'=>['file'],'sku','spec_rel'=>['spec']]]]],
            'spec_rel' => ['spec'],
            'delivery' => ['rule'],
            'commentData' => function ($query) {
                $query->with('user')->where(['is_delete' => 0, 'status' => 1])->limit(2);
            },
            'video',
            'poster'
        ]))->withCount(['commentData' => function ($query) {
            $query->where(['is_delete' => 0, 'status' => 1]);
        }])
            ->where('product_id', '=', $productId)
            ->find();
        // 判断商品的状态
        if (empty($detail) || $detail['is_delete'] || $detail['product_status']['value'] != 10) {
            $this->error = '很抱歉，商品信息不存在或已下架';
            return false;
        }
        $SupplierModel = new SupplierModel;
        if ($detail['shop_supplier_id']) {
            $supplier = $SupplierModel::detail($detail['shop_supplier_id'], ['logo', 'category']);
            $supplier['logos'] = $supplier['logo']['file_path'];
            unset($supplier['logo']);
            $supplier['category_name'] = $supplier['category']['name'];
            unset($supplier['category']);
            $supplier['supplier_user_id'] = (new SupplierUserModel())->where('shop_supplier_id', '=', $detail['shop_supplier_id'])->value('supplier_user_id');
            $supplier->visible(['logos', 'category_name', 'name', 'shop_supplier_id', 'product_sales', 'server_score', 'store_type', 'user_id', 'supplier_user_id']);
            $server = (new ServiceApply())->getList($detail['shop_supplier_id']);
        } else {
            $supplier = [];
            $server = [];
        }
        $detail['supplier'] = $supplier;
        $detail['server'] = $server;
        //优惠券信息
        $detail['product_coupon_ids'] =$this->getProductCoupon($productId);
        if ($detail['is_open_gift']){
            $detail = $this->getGiftProductSku($detail);
        }
        // 更新访问数据
        $this->where('product_id', '=', $detail['product_id'])->inc('view_times')->update();
        // 设置商品展示的数据
        $detail = $this->setProductListDataFromApi($detail, false, ['userInfo' => $userInfo]);
        // 多规格商品sku信息
        $detail['product_multi_spec'] = BaseProductService::getSpecData($detail);
        //计算券后多少钱
        $after_coupon = self::getUserCouponList($detail['product_coupon_ids'],$detail['product_price']);
        $detail['after_coupon'] = $after_coupon;
        foreach ($detail['sku'] as $k => $v)
        {
            if ($v['product_price']-$after_coupon > 0){
                $detail['sku'][$k]['after_coupon'] = $v['product_price']-$after_coupon;

            }else{
                $detail['sku'][$k]['after_coupon'] = 0;
            }

        }
        return $detail;
    }
    //获取实际赠品的sku
    public function getGiftProductSku($detail)
    {

        //计算券后多少钱
        $after_coupon = self::getUserCouponList($detail['product_coupon_ids'],$detail['product_price']);
        $productgiftsku = [];
        if ($detail['sku'])
        {
            foreach ($detail['sku'] as $k => $arr)
            {
                if ($arr['product_price']-$after_coupon<0){
                    $arr['after_coupon'] = 0;
                }
                $detail['sku'][$k]['after_coupon'] = $arr['product_price']-$after_coupon;
                $arr['productgift'] = $arr['productgift']->toArray();
                if (isset($arr['productgift']) && !empty($arr['productgift']))
                    $productgiftsku = $arr['productgift'][0]['productgiftsku'];break;
            }
            foreach ($productgiftsku as $key =>$v)
            {
                $productgiftsku[$key]['giftSkuMsg'] = \app\common\model\goods\Goods::getProductSku($v['product'],$v['spec_sku_id']);
            }
        }
        $detail['productgiftsku'] =$productgiftsku;
        return $detail;
    }

    //判断该商品是否有可用优惠券并且自动领取是否开启
    public function getProductCoupon($productId)
    {
        $coupon = Voucher::where('is_delete', '=', 0)->where(['is_receive'=>1])
                ->order(['sort' => 'asc', 'create_time' => 'desc'])->select();
        if (!$coupon) return false;
        $product_coupon_ids ='';
        foreach ($coupon as $k => $v)
        {
            if ($v['total_num'] > -1 && $v['receive_num'] >= $v['total_num']) {
                continue;
            }
            if ($v['expire_type'] == 20 && ($v->getData('end_time') + 86400) < time()) {
                continue;
            }
            if ($v['apply_range'] == 10)
            {
                $product_coupon_ids.=$v['coupon_id'].',';
            }if ($v['apply_range'] == 20)
            {
                $v['products'] = explode(',', $v['product_ids']);
                    if (in_array($productId, $v['products'])) {
                        $product_coupon_ids.=$v['coupon_id'].',';
                    }

            }if ($v['apply_range'] == 30)
            {
                $v['products'] = explode(',', $v['product_ids']);
                    if (!in_array($productId, $v['products'])) {
                        $product_coupon_ids.=$v['coupon_id'].',';
                    }

            }
        }
        $product_coupon_ids = rtrim($product_coupon_ids, ",");
        if (empty($product_coupon_ids)){
            return '';
        }
        $product_coupon_ids = explode(",",$product_coupon_ids);
        return json_encode($product_coupon_ids);

    }
    /**
     * 订单结算优惠券列表
     */
    public static function getUserCouponList($coupon_ids, $orderPayPrice)
    {
        $coupon_ids = json_decode($coupon_ids,true);
        //     新增筛选条件: 最低消费金额
        // 获取用户可用的优惠券列表
        $list = Voucher::where('coupon_id','in',$coupon_ids)->select();
        if (!$list){
            return 0;
        }
        $data = [];
        foreach ($list as $key =>$coupon) {
            // 最低消费金额
            if ($orderPayPrice < $coupon['min_price']) continue;
            // 有效期范围内
            if ($coupon['start_time']['value'] > time()) continue;
            $data[$key] = [
                'name' => $coupon['name'],
                'color' => $coupon['color'],
                'coupon_type' => $coupon['coupon_type'],
                'reduce_price' => $coupon['reduce_price'],
                'discount' => $coupon['discount'],
                'min_price' => $coupon['min_price'],
                'expire_type' => $coupon['expire_type'],
                'start_time' => $coupon['start_time'],
                'end_time' => $coupon['end_time'],
                'expire_day' => $coupon['expire_day'],
                'free_limit' => $coupon['coupon']['free_limit'],
                'apply_range' => $coupon['coupon']['apply_range'],
                'product_ids' => $coupon['coupon']['product_ids'],
            ];
            // 计算打折金额
            if ($coupon['coupon_type']['value'] == 20) {
                $reducePrice = helper::bcmul($orderPayPrice, $coupon['discount'] / 100);
                $data[$key]['reduced_price'] = bcsub($orderPayPrice, $reducePrice, 2);
            } else
                $data[$key]['reduced_price'] = $coupon['reduce_price'];
        }
        if (!$data)
        {
            return 0;
        }
        // 根据折扣金额排序并返回
         $data = array_sort($data, 'reduced_price', true);
        $data = array_shift($data);
        return $data['reduced_price'];
    }

    /**
     * 根据商品id集获取商品列表
     */
    public function getListByIdsFromApi($productIds, $userInfo = false)
    {
        // 获取商品列表
        $data = (new ProductModel())->getListByIds($productIds, 10);
        // 整理列表数据并返回
        return $this->setProductListDataFromApi($data, true, ['userInfo' => $userInfo]);
    }


    /**
     * 设置商品展示的数据 api模块
     */
    public function setProductListDataFromApi(&$data, $isMultiple, $param)
    {
        return parent::setProductListData($data, $isMultiple, function ($product) use ($param) {
            // 计算并设置商品会员价
            $this->setProductGradeMoney($param['userInfo'], $product);
        });
    }

    /**
     * 设置商品的会员价
     */
    public function setProductGradeMoney($user, &$product)
    {
        // 会员等级状态
        $gradeStatus = (!empty($user) && $user['grade_id'] > 0 && !empty($user['grade']))
            && (!$user['grade']['is_delete']);
        // 判断商品是否参与会员折扣
        if (!$gradeStatus || !$product['is_enable_grade']) {
            $product['is_user_grade'] = false;
            return;
        }
        $alone_grade_type = 10;
        // 商品单独设置了会员折扣
        if ($product['is_alone_grade'] && isset($product['alone_grade_equity'][$user['grade_id']])) {
            if ($product['alone_grade_type'] == 10) {
                // 折扣比例
                $discountRatio = helper::bcdiv($product['alone_grade_equity'][$user['grade_id']], 100);
            } else {
                $alone_grade_type = 20;
                $discountRatio = helper::bcdiv($product['alone_grade_equity'][$user['grade_id']], $product['product_price'], 2);
            }
        } else {
            // 折扣比例
            $discountRatio = helper::bcdiv($user['grade']['equity'], 100);
        }
        if ($discountRatio < 1) {
            // 标记参与会员折扣
            $product['is_user_grade'] = true;
            // 会员折扣后的商品总金额
            if ($alone_grade_type == 20) {
                $product['product_price'] = $product['alone_grade_equity'][$user['grade_id']];
            } else {
                $product['product_price'] = helper::number2(helper::bcmul($product['product_price'], $discountRatio), true);
            }

            // 会员折扣价
            foreach ($product['sku'] as &$skuItem) {
                if ($alone_grade_type == 20) {
                    $skuItem['product_price'] = $product['alone_grade_equity'][$user['grade_id']];
                } else {
                    $skuItem['product_price'] = helper::number2(helper::bcmul($skuItem['product_price'], $discountRatio), true);
                }
            }
        } else {
            $product['is_user_grade'] = false;
        }
    }

    /**
     * 为你推荐
     */
    public function getRecommendProduct($params)
    {
        $model = $this;
        // 手动
        if ($params['choice'] == 1) {
            $product_id = array_column($params['product'], 'product_id');
            $model = $model->where('product_id', 'IN', $product_id);
            $list = $model->with(['category', 'image.file'])
                ->where('product_status', '=', 10)
                ->where('audit_status', '=', 10)
                ->where('is_delete', '=', 0)
                ->select();
            // 整理列表数据并返回
            return $this->setProductListData($list, true);
        } else {
            $sort = ['create_time' => 'desc'];
            if ($params['type'] == 10) {
                $sort = ['sales_actual' => 'desc'];
            } else if ($params['type'] == 30) {
                $sort = ['view_times' => 'desc'];
            }
            // 自动
            $sort = $params['type'] == 20 ? ['create_time' => 'desc'] : ['product_sales' => 'desc'];
            $list = $model->field(['*', '(sales_initial + sales_actual) as product_sales'])->with(['category', 'image.file'])
                ->where('product_status', '=', 10)
                ->where('audit_status', '=', 10)
                ->where('is_delete', '=', 0)
                ->limit($params['num'])
                ->order($sort)
                ->select();
            return $this->setProductListData($list, true);
        }
    }

    //商品上下架
    public function editStatus($data)
    {
        return $this->save(['product_status' => $data['product_status']]);
    }
}