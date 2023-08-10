<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\OrderGoods;
use app\api\model\user\User as UserModel;
use app\api\model\order\Order as OrderModel;
use app\api\model\setting\Setting as SettingModel;
use app\common\enum\settings\GetPhoneTypeEnum;
use app\common\library\helper;
use think\facade\Cache;

/**
 * 个人中心主页
 */
class Index extends Controller
{
    /**
     * 获取当前用户信息
     */
    public function detail($source = 'wx')
    {
        //当前用户信息
        $user = $this->getUser(false);

        if ($user) {
            //店铺信息
            $user['is_recycle'] = $user['supplierUser']?SupplierModel::detail($user['supplierUser']['shop_supplier_id'])['is_recycle']:'';

            $coupon_model = new UserCouponModel();
            $coupon = count($coupon_model->getList($user['user_id'], -1, false, false));
            $cardvoucher = new OrderGoods();
            $cardvoucherNum = count($cardvoucher->getList($date_status=1,$user['user_id']));

            //订单总数
            $model = new OrderModel;
            $paymentNum = $model->getCount($user, 'payment');
            $deliveryNum = $model->getCount($user, 'delivery');
            $receivedNum = $model->getCount($user, 'received');
            $commentNum = $model->getCount($user, 'comment');

            //分销
            $agentInfo = (object)[];
            $is_agent = false;
            $is_applying = false;
        } else {
            $coupon = 0;
            $cardvoucherNum = 0;

            $paymentNum = 0;
            $deliveryNum = 0;
            $receivedNum = 0;
            $commentNum = 0;

            $agentInfo = (object)[];
            $is_agent = false;
            $is_applying = false;
        }

        //分销商基本设置
        $setting = [];

        //是否开启分销功能
        //$agent_open = $setting['is_open'];

        //商城设置
        $store = SettingModel::getItem('store');

        //供应商入住背景图
        $supplier_image = isset($store['supplier_image'])?$store['supplier_image']:'';

        // 充值功能是否开启
        $balance_setting = SettingModel::getItem('balance');
        $balance_open = intval($balance_setting['is_open']);

        if ($user && isset($user['mobile']) && $user['mobile']) {

            $user['time'] = '0.00';
            $user['cfp'] = '0.00';
        }

        return $this->renderSuccess('', [
            'coupon' => $coupon,
            'cardvoucherNum' => $cardvoucherNum,
            'userInfo' => $user,
            'agentInfo' => $agentInfo,
            'is_agent' => $is_agent,
            'is_applying' => $is_applying,
            'orderCount' => [
                'payment' => $paymentNum,
                'delivery' => $deliveryNum,
                'received' => $receivedNum,
                'comment' => $commentNum,
            ],
            'setting' => [
                'points_name' => SettingModel::getPointsName(),
                'is_points'   => SettingModel::getIsPoints(),
                'growth_name' => SettingModel::getGrowthName(),
                'is_grow'   => SettingModel::getIsGrow(),
                //'agent_open' => $agent_open,
                'supplier_image' => $supplier_image,
                'balance_open' => $balance_open
            ],
            'sign' => SettingModel::getItem('sign'),
            'getPhone' => $user ? $this->isGetPhone() : '',
            'msgcount' => $user ? (new ChatModel)->mCount($user) : 0,
            'menus' => UserModel::getMenus($user, $source)   // 个人中心菜单列表
        ]);
    }

    /**
     * 当前用户设置
     */
    public function setting()
    {
        // 当前用户信息
        $user = $this->getUser();

        return $this->renderSuccess('', [
            'userInfo' => $user
        ]);
    }

    private function isGetPhone(){
        $user = $this->getUser();
        if($user['mobile'] != ''){
            return false;
        }
        $settings = SettingModel::getItem('getPhone');
        if(in_array(GetPhoneTypeEnum::USER, $settings['area_type'])){
            // 缓存时间
            $key = 'get_phone_' . $user['user_id'];
            if (!$data = Cache::get($key)) {
                $settings['send_day'] > 0 && Cache::set($key, '1', 86400 * $settings['send_day']);
                return true;
            }
        }
        return false;
    }
}