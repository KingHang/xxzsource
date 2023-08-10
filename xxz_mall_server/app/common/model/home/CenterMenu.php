<?php

namespace app\common\model\home;

use think\facade\Cache;
use app\common\model\BaseModel;

/**
 * 个人中心菜单模型
 */
class CenterMenu extends BaseModel
{
    protected $name = 'center_menu';
    protected $pk = 'menu_id';

    /**
     * 详情
     */
    public static function detail($menu_id)
    {
        return (new static())->find($menu_id);
    }

    /**
     * 查询所有
     */
    public static function getAll(){
        $model = new static();
        if (!Cache::get('center_menu_' . $model::$app_id)) {
            $list = $model->order(['sort' => 'asc'])->select();
            if(count($list) == 0){
                $sys_menus = $model->getSysMenu();
                $save_data = [];
                foreach($sys_menus as $menu){
                    $save_data[] = array_merge($sys_menus[$menu['sys_tag']], [
                        'app_id' => self::$app_id
                    ]);
                }
                $model->saveAll($save_data);
                $list = $model->order(['sort' => 'asc'])->select();
            }
            Cache::tag('cache')->set('center_menu_' . $model::$app_id, $list);
        }
        return Cache::get('center_menu_' . $model::$app_id);
    }

    /**
     * 系统菜单
     */
    public static function getSysMenu(){
        return [
            'address' => [
                'sys_tag' => 'address',
                'title' => '收货地址',
                'link_url' => '/pages/user/address/address',
                'image_url' => 'image/menu/address.png'
            ],
            'voucher' => [
                'sys_tag' => 'voucher',
                'title' => '领券中心',
                'link_url' => '/pages/voucher/voucher',
                'image_url' => 'image/menu/voucher.png'
            ],
            'my_coupon' => [
                'sys_tag' => 'my_coupon',
                'title' => '我的优惠券',
                'link_url' => '/pages/user/my-voucher/my-voucher',
                'image_url' => 'image/menu/my_coupon.png'
            ],
            'agent' => [
                'sys_tag' => 'agent',
                'title' => '分销中心',
                'link_url' => '/pages/agent/index/index',
                'image_url' => 'image/menu/agent.png'
            ],
            'pricedown' => [
                'sys_tag' => 'pricedown',
                'title' => '我的砍价',
                'link_url' => '/pages/user/my-pricedown/my-pricedown',
                'image_url' => 'image/menu/pricedown.png'
            ],
            'mall' => [
                'sys_tag' => 'mall',
                'title' => '入驻店铺',
                'link_url' => '/pages/mall/register',
                'image_url' => 'image/menu/mall.png'
            ],
            'my_shop' => [
                'sys_tag' => 'my_shop',
                'title' => '我的店铺',
                'link_url' => '/pages/mall/register',
                'image_url' => 'image/menu/my_shop.png'
            ],
            'app_shop' => [
                'sys_tag' => 'app_shop',
                'title' => '我的店铺',
                'link_url' => '/pages/mall/register',
                'image_url' => 'image/menu/app_shop.png'
            ],
            'my_attention' => [
                'sys_tag' => 'my_attention',
                'title' => '我的收藏',
                'link_url' => '/pages/user/my_attention/my_attention',
                'image_url' => 'image/menu/my_attention.png'
            ],
            'my_collect' => [
                'sys_tag' => 'my_collect',
                'title' => '我的关注',
                'link_url' => '/pages/user/my_collect/my_collect',
                'image_url' => 'image/menu/my_collect.png'
            ],
            'signin' => [
                'sys_tag' => 'signin',
                'title' => '签到有礼',
                'link_url' => '/pages/plugin/signin/signin',
                'image_url' => 'image/menu/signin.png'
            ],
            'raffle' => [
                'sys_tag' => 'raffle',
                'title' => '我的抽奖',
                'link_url' => '/pages/plugin/raffle/raffle',
                'image_url' => 'image/menu/raffle.png'
            ],
            'setting' => [
                'sys_tag' => 'setting',
                'title' => '设置',
                'link_url' => '/pages/user/set/set',
                'image_url' => 'image/menu/setting.png'
            ],
        ];
    }

}