<?php

namespace app\api\controller;

use app\api\model\page\Home as AppPage;
use app\api\model\plus\agent\Apply as AgentApplyModel;
use app\api\model\plus\agent\User as AgentUserModel;
use app\api\model\settings\Settings as SettingModel;
use app\common\enum\settings\SettingEnum;
use app\common\model\app\AppUpdate as AppUpdateModel;
use app\common\model\settings\Agreement as AgreementModel;
use app\common\model\purveyor\Service as ServiceModel;
use app\api\model\plus\chat\Chat as ChatModel;
use app\common\model\plugin\agent\GradeLog;
use app\common\model\plugin\agent\Grade as AgentGrade;
use app\common\model\plugin\agent\OrderDetail;
use app\common\model\plugin\agent\OrderSettled;
use app\common\model\plugin\agent\Referee;
use app\api\model\order\Order as orderModel;
use app\api\model\plus\agent\Order as agentOrderModel;
use think\facade\Cache;
use app\common\model\plugin\agent\Capital;
use app\common\library\helper;
use app\common\model\user\User;
use app\common\model\plugin\agent\Apply as ApplyModel;
use app\common\model\plugin\agent\Setting as AgentSettingModel;
/**
 * 页面控制器
 */
class Index extends Controller
{
    /**
     * 首页
     */
    public function index($page_id = null, $url = '')
    {
        // 页面元素
        $data = AppPage::getPageData($this->getUser(false), $page_id);
        //消息条数
        $Chat = new ChatModel;
        $data['msgNum'] = $Chat->mCount($this->getUser(false));
        $data['setting'] = array(
            'collection' => SettingModel::getItem('collection'),
            'officia' => SettingModel::getItem('officia'),
            'homepush' => SettingModel::getItem('homepush'),
        );
        // 扫一扫参数
        $data['signPackage'] = $this->getScanParams($url)['signPackage'];
        // 当前用户信息
        $userInfo = $this->getUser(false);
        if ($userInfo) {
            $agentInfo = AgentUserModel::detail($userInfo['user_id']);
            $data['userInfo'] = $userInfo;
            $data['agentInfo'] = $agentInfo ? $agentInfo : (object)[];
            $data['is_agent'] = !!$agentInfo && !$agentInfo['is_delete'];
            $data['is_applying'] = AgentApplyModel::isApplying($userInfo['user_id']);
        } else {
            $data['userInfo'] = (object)[];
            $data['agentInfo'] = (object)[];
            $data['is_agent'] = false;
            $data['is_applying'] = false;
        }
        $data['tip'] = (new AgentApplyModel())->checkApply($userInfo);
        return $this->renderSuccess('', $data);
    }

    /**
     * 系统配置
     */
    public function base()
    {
        $config = SettingModel::getItem('store');
        $settings = [
            'shop_name' => $config['name'],
            'shop_logo' => $config['logo'],
            'shop_bg_img' => $config['supplier_image']
        ];
        // 获取协议标题
        $agreement = AgreementModel::detailByKeyword('register');
        $agreementTitle = $agreement ? $agreement['agreement_title'] : '';
        return $this->renderSuccess('', compact('settings', 'agreementTitle'));
    }

    /**
     * 公众号客服
     */
    public function mpService($shop_supplier_id)
    {
        $mp_service = ServiceModel::detail($shop_supplier_id);
        return $this->renderSuccess('', compact('mp_service'));
    }

    /**
     * 底部导航
     */
    public function nav()
    {
        $data['vars'] = SettingModel::getItem(SettingEnum::BOTTOMNAV);
        return $this->renderSuccess('', $data);
    }

    /**
     * app更新
     */
    public function update($name, $version, $platform)
    {
        $result = [
            'update' => false,
            'wgtUrl' => '',
            'pkgUrl' => '',
        ];
        try {
            $model = AppUpdateModel::getLast();
            // 这里简单判定下，不相等就是有更新。
            if ($model && $version != $model['version']) {
                $currentVersions = explode('.', $version);
                $resultVersions = explode('.', $model['version']);

                if ($currentVersions[0] < $resultVersions[0]) {
                    // 说明有大版本更新
                    $result['update'] = true;
                    $result['pkgUrl'] = $platform == 'android' ? $model['pkg_url_android'] : $model['pkg_url_ios'];
                } else {
                    // 其它情况均认为是小版本更新
                    $result['update'] = true;
                    $result['wgtUrl'] = $model['wgt_url'];
                }
            }
        } catch (\Exception $e) {

        }
        return $this->renderSuccess('', compact('result'));
    }
    public function agent_settled()
    {
        event('AgentOrderMonth', 10001);
        echo 111;die;
    }
    public function setAgentCache()
    {echo 11;die;
        set_time_limit(0);
        $list = (new AgentUserModel())->where('is_delete' , '=' , '0')->select();
        $i = 0;
        foreach ($list as $item) {
            $i ++;
            Cache::delete('agent_user_list_' . $item['user_id']);
            (new AgentUserModel())->getRefereeUserList($item);
        }
//        $data = AgentSettingModel::getAll();
//        $setting = $data['condition']['values'];
//        $gradeIds = AgentGrade::getTeamGradeList($setting['team_level']);
//        $list = (new AgentUserModel())->where('is_delete' , '=' , 0)->whereIn('grade_id',$gradeIds)->select();
//        $agent_setting = $data['venturebonus_basic']['values'];
//        $bonusGradeIds = AgentGrade::getTeamGradeList($agent_setting['grade_id']);
//        foreach ($list as $item) {
//            Cache::delete('direct_agent_user_list_' . $item['user_id']);
//            (new AgentUserModel())->getDirectUserIds($item);
//            Cache::delete('direct_agent_user_list_bonus_' . $item['user_id']);
//            (new AgentUserModel())->getDirectUserIds($item,0,'bonus');
//
//            Cache::delete('agent_low_list_' . $item['user_id'] . 'bonus');
//            Cache::delete('agent_low_list_' . $item['user_id']);
//            (new AgentUserModel())->getLowTeamIdWithGrad($bonusGradeIds,$item,0,[],'bonus');
//            (new AgentUserModel())->getLowTeamIdWithGrad($gradeIds,$item);
//        }
        return $this->renderSuccess('', $i);
    }
    // 分销商等级回滚
    public function agentGardRollback() {
        $list = (new GradeLog())->alias('a')
            ->field('au.real_name,au.mobile,a.old_grade_id,a.new_grade_id,a.user_id,ag.grade_id')
            ->join('agent_user au' , 'au.user_id = a.user_id')
            ->join('agent_grade ag' , 'ag.grade_id = au.grade_id')
            ->where(['change_type' => 20])
            ->whereRaw('old_grade_id != new_grade_id')
            ->select();

        foreach ($list as $item) {
            if ($item['new_grade_id'] == $item['grade_id']) {
                // 回滚等级
                (new AgentUserModel())->where('user_id' , '=' , $item['user_id'])->update([
                    'grade_id' => $item['old_grade_id']
                ]);
                // 更新缓存
                (new AgentUserModel())->getRefereeUserList(['user_id' => $item['user_id']],1);
                // 添加等级变动日志
                (new GradeLogModel)->save([
                    'old_grade_id' => $item['new_grade_id'],
                    'new_grade_id' => $item['old_grade_id'],
                    'change_type' => 10,
                    'user_id' => $item['user_id'],
                    'app_id' => 10001,
                    'remark' => '等级变更错误回滚'
                ]);
            }
        }
        return $this->renderSuccess('', $list);
    }
    public function bbb()
    {
        $list = (new OrderModel())->alias('o')
            ->join('agent_order a' , 'a.order_id = o.order_id' , 'left')
            ->where('o.pay_type' , '=' , 20)
            ->where('o.pay_status' , '=' , 20)
            ->whereIn('o.order_status',[10,30])     
            ->where('o.order_id' , '=' ,1408)
            ->whereNull('a.id')
            ->field('o.user_id,o.order_id,o.order_source')
            ->select();
    }
   
}
