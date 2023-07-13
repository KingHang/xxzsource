<?php

namespace app\api\model\page;
use app\common\model\home\CenterMenu as CenterMenuModel;
use app\api\model\plus\agent\Setting as AgentSettingModel;
use app\common\model\store\Clerk as ClerkModel;
use think\facade\Cache;
use app\common\exception\BaseException;
use app\common\model\user\User as UserModel;
use app\api\model\plus\agent\Referee as RefereeModel;
use app\common\library\easywechat\AppWx;
use app\common\model\user\Grade as GradeModel;
/**
 * 菜单模型
 */
class CenterMenu extends CenterMenuModel
{

    /**
     * 获取列表
     */
    public static function getMenu($user, $source)
    {   
        $list =  self::withoutGlobalScope()->where('status','=',1)->order(['sort' => 'asc','create_time'=>'desc'])->select();
        foreach ($list as $key => &$menus) {
        	// 判断分销功能是否开启
            if (AgentSettingModel::isOpen()&&$menus['label']=="agent") {
                $menus['name'] = AgentSettingModel::getAgentTitle();
            } else if($menus['label']=="agent") {
                unset($list[$key]);
            }
            //判断是否入住店铺
            if($user['user_type']==1&&($menus['label']=="my_shop"||$menus['label']=="app_shop")){
                unset($list[$key]);unset($list[$key]);
            }else if($user['user_type']==2){
                // 申请中或者已入驻成功
                if($menus['label']=="shop"){
                    unset($list[$key]);
                }
                // 入驻成功
                if(UserModel::isSupplier($user['user_id'])){
                    if($menus['label']=="app_shop"){
                    unset($list[$key]);
                    }
                    
                } else{
                    if($menus['label']=="my_shop"){
                    unset($list[$key]);
                    }
                }
            }
            // 如果来源是app并且是店员，出现扫一扫
            if($source == 'app' && ClerkModel::detail(['user_id' => $user['user_id']])){

            } else {
                if($menus['label']=="scan"){
                    unset($list[$key]);
                    }
            }

        }
        return $list;
    }
}