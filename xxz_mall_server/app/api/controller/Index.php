<?php

namespace app\api\controller;

use app\api\model\home\Home as AppPage;
use app\api\model\setting\Setting as SettingModel;
use app\common\enum\settings\SettingEnum;
use app\common\model\app\AppUpdate as AppUpdateModel;
use app\common\model\setting\Agreement as AgreementModel;
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
            $agentInfo = (object)[];
            $data['userInfo'] = $userInfo;
            $data['agentInfo'] = $agentInfo ? $agentInfo : (object)[];
            $data['is_agent'] = false;
            $data['is_applying'] = false;
        } else {
            $data['userInfo'] = (object)[];
            $data['agentInfo'] = (object)[];
            $data['is_agent'] = false;
            $data['is_applying'] = false;
        }
        $data['tip'] =  [
        'is_agent' => false,
        'is_applying' => false,
        'is_pop' => false,
        'name' => '',
        'type' => 0,
        'setmsg' => ''
    ];
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


}
