<?php

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\order\OrderGoods;
use app\api\model\plus\agent\Apply as AgentApplyModel;
use app\api\model\plus\agent\Setting;
use app\api\model\plus\agent\User as AgentUserModel;
use app\api\model\user\User as UserModel;
use app\api\model\order\Order as OrderModel;
use app\api\model\settings\Settings as SettingModel;
use app\api\model\plus\coupon\UserVoucher as UserCouponModel;
use app\common\enum\settings\GetPhoneTypeEnum;
use app\common\library\helper;
use app\timebank\ztservice\Service;
use think\facade\Cache;
use app\api\model\supplier\Purveyor as SupplierModel;
use app\api\model\plus\chat\Chat as ChatModel;

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
            $cardvoucherNum = $cardvoucher->getList(['data_status'=>1],$user['user_id'],1);

            //订单总数
            $model = new OrderModel;
            $paymentNum = $model->getCount($user, 'payment');
            $deliveryNum = $model->getCount($user, 'delivery');
            $receivedNum = $model->getCount($user, 'received');
            $commentNum = $model->getCount($user, 'comment');

            //分销
            $agentInfo = AgentUserModel::detail($user['user_id']);
            $is_agent = !!$agentInfo && !$agentInfo['is_delete'];
            $is_applying = AgentApplyModel::isApplying($user['user_id']);
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
        $setting = Setting::getItem('basic');

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
            // 获取中台用户time
            $service = new Service();
            $currentTime = $service->blockchainTimebankUserInfo($user['mobile'], 1);
            if ($currentTime
                && isset($currentTime['data'])
                && isset($currentTime['data']['blockUser'])
                && isset($currentTime['data']['blockUser']['memberWallet'])
            ) {
                $user['time'] = helper::number2($currentTime['data']['blockUser']['memberWallet']['balance'] + $currentTime['data']['blockUser']['memberWallet']['lockBalance']);
            } else {
                $user['time'] = '0.00';
            }

            // 获取中台cfp
            $currentCfp = $service->blockchainTimebankUserInfo($user['mobile'], 2);
            if ($currentCfp
                && isset($currentCfp['data'])
                && isset($currentCfp['data']['blockUser'])
                && isset($currentCfp['data']['blockUser']['memberWallet'])
            ) {
                $user['cfp'] = helper::number2($currentCfp['data']['blockUser']['memberWallet']['balance']);
            } else {
                $user['cfp'] = '0.00';
            }
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