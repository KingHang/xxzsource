<?php

namespace app\api\model\page;

use app\api\model\plus\benefit\BenefitCard;
use app\api\model\plus\brand\BrandDaySign;
use app\api\model\plus\live\WxLive;
use app\api\model\product\Goods as ProductModel;
use app\api\model\product\Category as CategoryModel;
use app\api\model\plus\article\News;
use app\common\model\home\Home as PageModel;
use app\api\model\plus\coupon\Voucher;
use app\api\model\plus\seckill\Product as SeckillProductModel;
use app\api\model\plus\seckill\Active as SeckillActiveModel;
use app\api\model\plus\assemble\Goods as AssembleProductModel;
use app\api\model\plus\assemble\Active as AssembleActiveModel;
use app\api\model\plus\bargain\Product as BargainProductModel;
use app\api\model\plus\bargain\Active as BargainActiveModel;
use app\api\model\plus\live\Room as RoomModel;
use app\common\model\plugin\agent\Setting;
use app\common\model\goods\Goods;
use app\shop\model\plugin\brand\Day as DayModel;
use app\shop\model\settings\Settings as SettingModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 首页模型
 */
class Home extends PageModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time'
    ];

    /**
     * DIY页面详情
     */
    public static function getPageData($user, $page_id = null)
    {
        // 页面详情
        $detail = $page_id > 0 ? parent::detail($page_id) : parent::getHomePage();

        // 页面diy元素
        $items = $detail['page_data']['items'];
//        var_dump($items);die;
        // 页面顶部导航
        isset($detail['page_data']['home']) && $items['home'] = $detail['page_data']['home'];

        // 获取动态数据
        $model = new self;

        foreach ($items as $key => $item) {
            unset($items[$key]['defaultData']);
            if ($item['type'] === 'window') {
                $items[$key]['data'] = array_values($item['data']);
            } else if ($item['type'] === 'product') {
                $items[$key]['data'] = $model->getProductList($user, $item);
            } else if ($item['type'] === 'category') {
                $items[$key]['data'] = $model->getCategoryList($item);
                $items[$key]['list'] = $model->getCategoryProductList($user, $item, 0);
            } else if ($item['type'] === 'coupon') {
                $items[$key]['data'] = $model->getCouponList($user, $item, true, 1);
            } else if ($item['type'] === 'article') {
                $items[$key]['data'] = $model->getArticleList($item);
            } else if ($item['type'] === 'special') {
                $items[$key]['data'] = $model->getSpecialList($item);
            } else if ($item['type'] === 'seckillProduct') {
                // 如果没有活动，则不显示
                $item_data = $model->getSeckillList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'assembleProduct') {
                // 如果没有活动，则不显示
                $item_data = $model->getAssembleList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'bargainProduct') {
                // 如果没有活动，则不显示
                $item_data = $model->getBargainList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'live') {
                $items[$key]['data'] = $model->getLiveList($item);
            } else if ($item['type'] === 'wxlive') {
                $items[$key]['data'] = $model->getWxLiveList($item);
            } else if ($item['type'] === 'bonusProduct') {
                // 如果没有活动，则不显示
                $item_data = $model->getBonusList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            } else if ($item['type'] === 'brandCategory') {
                // 如果没有活动，则不显示
                $item_data = $model->getBrandList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data['category'];
                    $items[$key]['list'] = $item_data['list'];
                    $items[$key]['setting'] = $item_data['setting'];
                }
            } else if ($item['type'] === 'card') {
                // 如果没有权益卡，则不显示
                $item_data = $model->getCardList($item);
                if (empty($item_data)) {
                    unset($items[$key]);
                } else {
                    $items[$key]['data'] = $item_data;
                }
            }
        }
        return ['home' => $items['home'], 'items' => $items];
    }

    /**
     * 商品组件：获取商品列表
     */
    private function getProductList($user, $item)
    {
        // 获取商品数据
        $model = new ProductModel;
        if ($item['params']['source'] === 'choice') {
            // 数据来源：手动
            $productIds = array_column($item['data'], 'product_id');
            $productList = $model->getListByIdsFromApi($productIds, $user);
        } else {
            // 数据来源：自动
            $productList = $model->getList([
                'type' => 'sell',
                'category_id' => $item['params']['auto']['category'],
                'sortType' => $item['params']['auto']['productSort'],
                'list_rows' => $item['params']['auto']['showNum'],
                'audit_status' => 10
            ], $user);
        }
        if ($productList->isEmpty()) return [];
        // 格式化商品列表
        $data = [];
        foreach ($productList as $product) {
            $show_sku = ProductModel::getShowSku($product);
            $data[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'selling_point' => $product['selling_point'],
                'image' => $product['image'][0]['file_path'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $show_sku['product_price'],
                'line_price' => $show_sku['line_price'],
                'product_sales' => $product['product_sales'],
            ];
            if (isset($product['show_deduct_money']))
                $data['show_deduct_money'] = $product['show_deduct_money'];
        }
        return $data;
    }

    /**
     * 商品分类组件：获取商品分类列表
     * @param $item
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    private function getCategoryList($item)
    {
        // 获取商品分类数据
        if ($item['params']['source'] === 'choice') {
            // 数据来源：手动
            $categoryIds = array_column($item['data'], 'category_id');
            $categoryList = (new CategoryModel)->getFirstCategoryByIds($categoryIds);
        } else {
            // 数据来源：自动
            $categoryList = CategoryModel::getFirstCategory();
        }

        if ($categoryList->isEmpty()) return [];

        // 格式化商品分类列表
        $data = [['category_id' => 0, 'name' => '全部', 'child' => []]];

        foreach ($categoryList as $category) {
            $data[] = [
                'category_id' => $category['category_id'],
                'name' => $category['name'],
                'child' => CategoryModel::getSecondCategory($category['category_id'])
            ];
        }

        return $data;
    }

    /**
     * 商品分类组件：获取商品列表
     * @param $user
     * @param $item
     * @param $category_id
     * @return array
     */
    private function getCategoryProductList($user, $item, $category_id)
    {
        // 获取商品数据
        $model = new ProductModel;
        $productList = $model->getList([
            'type' => 'sell',
            'category_id' => $category_id,
            'sortType' => $item['params']['productSort'],
            'list_rows' => $item['params']['showType'] == 'limit' ? $item['params']['showNum'] : 20,
            'audit_status' => 10
        ], $user);

        if ($productList->isEmpty()) return [];

        // 格式化商品列表
        $data = [];

        foreach ($productList as $product) {
            $show_sku = ProductModel::getShowSku($product);
            $data[] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'selling_point' => $product['selling_point'],
                'image' => $product['image'][0]['file_path'],
                'product_image' => $product['image'][0]['file_path'],
                'product_price' => $show_sku['product_price'],
                'line_price' => $show_sku['line_price'],
                'product_sales' => $product['product_sales'],
            ];
        }

        return $data;
    }

    /**
     * 优惠券组件：获取优惠券列表
     */
    private function getCouponList($user, $item)
    {
        // 获取优惠券数据
        return (new Voucher)->getList($user, $item['params']['limit'], true);
    }

    /**
     * 文章组件：获取文章列表
     */
    private function getArticleList($item)
    {
        // 获取文章数据
        $model = new News;
        $articleList = $model->getList($item['params']['auto']['category'], $item['params']['auto']['showNum']);
        return $articleList->isEmpty() ? [] : $articleList->toArray()['data'];
    }

    /**
     * 头条快报：获取头条列表
     */
    private function getSpecialList($item)
    {
        // 获取头条数据
        $model = new News;
        $articleList = $model->getList($item['params']['auto']['category'], $item['params']['auto']['showNum']);
        return $articleList->isEmpty() ? [] : $articleList->toArray()['data'];
    }

    /**
     * 获取限时秒杀
     */
    private function getSeckillList($item)
    {
        // 获取秒杀数据
        $seckill = SeckillActiveModel::getActive();
        if ($seckill) {
            $product_model = new SeckillProductModel;
            $seckill['product_list'] = $product_model->getProductList($seckill['seckill_activity_id'], $item['params']['showNum']);
        }
        return $seckill;
    }

    /**
     * 获取限时拼团
     */
    private function getAssembleList($item)
    {
        // 获取拼团数据
        $assemble = AssembleActiveModel::getActive();
        if ($assemble) {
            $assemble->visible(['assemble_activity_id', 'title', 'start_time', 'end_time']);
            $product_model = new AssembleProductModel;
            $assemble['product_list'] = $product_model->getProductList($assemble['assemble_activity_id'], $item['params']['showNum']);
        }
        return $assemble;
    }

    /**
     * 获取创业分红
     */
    private function getBonusList($item)
    {
        $model = new Setting();
        $data = $model->getItem('venturebonus_basic');
        $productIds = isset($data['product_ids']) ?? '';
        $end_time = isset($data['finsh_time']) ?? '' ;
        $data['is_open'] = isset($data['is_open']) ?? '' ;
        $lastday = strtotime("$end_time +1 month -1 day");
        $lastday = date("Y-m-d",$lastday);
        $lastday = $lastday." 23:59:59";
        $data['end_time'] = strtotime($lastday);
        if ($data['is_open'] == 1){
            if (date("Y-m")>=$data['start_time'] && date("Y-m")<=$data['finsh_time']){
                $data['product_list'] = (new Goods())->getListByIds($productIds);
            }
        }else{
            $data = [];
        }

        return $data;
    }

    /**
     * 获取限时砍价
     */
    private function getBargainList($item)
    {
        // 获取拼团数据
        $bargain = BargainActiveModel::getActive();
        if ($bargain) {
            $bargain->visible(['bargain_activity_id', 'title', 'start_time', 'end_time']);
            $product_model = new BargainProductModel;
            $bargain['product_list'] = $product_model->getProductList($bargain['bargain_activity_id'], $item['params']['showNum']);
        }
        return $bargain;
    }

    /**
     * 直播
     */
    private function getLiveList($item)
    {
        // 获取直播数据
        $model = new RoomModel();
        $liveList = $model->getDiyList($item['params']['showNum']);
        return $liveList->isEmpty() ? [] : $liveList->toArray();
    }

    /**
     * 微信直播
     */
    private function getWxLiveList($item)
    {
        // 获取头条数据
        $model = new WxLive();
        $liveList = $model->getList($item['params']['showNum']);
        return $liveList->isEmpty() ? [] : $liveList->toArray()['data'];
    }

    private function getBrandList($item)
    {
        $model = new DayModel();
        if ($item['params']['source'] === 'choice') {
            // 数据来源：手动
            $categoryIds = array_column($item['data'], 'category_id');
            $category = $model->dayList($categoryIds);
        } else {
            // 数据来源：自动

            $category = $model->dayList();
        }
        $data = [['brand_day_id' => 0, 'brand_day_name' => '全部']];

        foreach ($category as $category) {
            $data[] = [
                'brand_day_id' => $category['brand_day_id'],
                'brand_day_name' => $category['brand_day_name'],
//                'child' => CategoryModel::getSecondCategory($category['category_id'])
            ];
        }
        $category = $data;
        $modelBrand = new BrandDaySign();
        $params['pageSize'] = $item['params']['showType'] == 'limit' ? $item['params']['showNum'] : '全部';
        $list = $modelBrand->brandList($params)['data'];
        $setting  = SettingModel::getItem('brand');
        return compact('category','list','setting');
    }

    /**
     * 获取权益卡
     * @param array $item
     * @return
     */
    private function getCardList($item)
    {
        $model = new BenefitCard;
        if ($item['params']['source'] === 'choice') {
            // 数据来源：手动
            $cardIds = array_column($item['data'], 'card_id');
            $cardList = $model->getListByIds($cardIds);
        } else {
            // 数据来源：自动
            $cardList = $model->getHomeCardList([
                'sortType' => $item['params']['cardSort'],
                'list_rows' => $item['params']['showNum']
            ]);
        }
        if ($cardList->isEmpty()) return [];
        return $cardList;
    }
}
