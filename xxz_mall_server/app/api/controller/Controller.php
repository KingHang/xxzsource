<?php

namespace app\api\controller;

use app\api\model\user\User as UserModel;
use app\api\model\App as AppModel;
use app\common\exception\BaseException;
use app\common\library\easywechat\AppMp;
use app\XxzController;
use think\facade\Env;
use think\facade\Cache;

/**
 * API控制器基类
 */
class Controller extends XxzController
{

    // app_id
    protected $app_id;

    //白名单
    protected $routeUri = '';
    protected $allowAllAction = [
        // 首页
        '/index/index',
        //我的
        'user.index/detail',
    ];

    /**
     * 后台初始化
     */
    public function initialize()
    {
        // 当前路由信息
        $this->getRouteinfo();
        // 当前小程序id
        $this->app_id = $this->getAppId();
        // 验证当前小程序状态
        $this->checkWxapp();
//        $this->checkLogin();

    }
    /**
     * 验证登录状态
     */
    private function checkLogin()
    {
        // var_dump(in_array($this->allowAllAction,$allowAllAction));die;
        // 验证当前请求是否在白名单
        if (in_array($this->routeUri, $this->allowAllAction)) {
            return true;
        }
        // 验证登录状态
        if (!empty($this->getUser())) {
            return true;
        }
        throw new BaseException(['code' => -1, 'msg' => '请登录~']);
        return false;
    }
    /**
     * 解析当前路由参数 （分组名称、控制器名称、方法名）
     */
    protected function getRouteinfo()
    {
        // 控制器名称
        $this->controller = strtolower($this->request->controller());
        $this->controller = str_replace(".", "/", $this->controller);
        // 方法名称
        $this->action = Request()->action();
        // 控制器分组 (用于定义所属模块)
        $groupstr = strstr($this->controller, '.', true);
        $this->group = $groupstr !== false ? $groupstr : $this->controller;
        // 当前uri
        $this->routeUri = '/' . $this->controller . '/' . $this->action;
    }

    /**
     * 获取当前应用ID
     */
    private function getAppId()
    {
        if (!$app_id = $this->request->param('app_id')) {
            throw new BaseException(['msg' => '缺少必要的参数：app_id']);
        }
        return $app_id;
    }

    /**
     * 验证当前小程序状态
     */
    private function checkWxapp()
    {
        $app = AppModel::detail($this->app_id);
        if (empty($app)) {
            throw new BaseException(['msg' => '当前应用信息不存在']);
        }
        if ($app['is_recycle'] || $app['is_delete']) {
            throw new BaseException(['msg' => '当前应用已删除']);
        }
    }

    /**
     * 获取当前用户信息
     */
    protected function getUser($is_force = true,$field='',$with='')
    {
        if (!$token = $this->request->param('token')) {
            if ($is_force) {
                throw new BaseException(['msg' => '缺少必要的参数：token', 'code' => -1]);
            }
            return false;
        }
        if (!$user = UserModel::getUser($token,$field,$with)) {
            if ($is_force) {
                throw new BaseException(['msg' => '没有找到用户信息', 'code' => -1]);
            }
            return false;
        }
        if ($user['is_delete'] == 1) {
            throw new BaseException(['msg' => '没有找到用户信息', 'code' => -2]);
            Cache::delete($token);
        }
        return $user;
    }
    /**
     * 获取当前用户信息
     */
    protected function getUser_index($is_force = true,$field='',$with='')
    {
        if (!$token = $this->request->param('token')) {
            if ($is_force) {
                throw new BaseException(['msg' => '缺少必要的参数：token', 'code' => -1]);
            }
            return true;
        }
        if (!$user = UserModel::getUser($token,$field,$with)) {
            if ($is_force) {
                throw new BaseException(['msg' => '没有找到用户信息', 'code' => -1]);
            }
            return true;
        }
        if ($user['is_delete'] == 1) {
            throw new BaseException(['msg' => '没有找到用户信息', 'code' => -2]);
            Cache::delete($token);
        }
        return $user;
    }

    /**
     * 获取当前用户信息
     */
    protected function getSupplierUser($user)
    {
        if (!$user['supplierUser']) {
            throw new BaseException(['msg' => '非法请求', 'code' => -1]);
        }
        return $user['supplierUser'];
    }


    protected function getShareParams($url, $title = '', $desc = '', $link = '', $imgUrl = '')
    {
        $signPackage = '';
        $shareParams = '';
        if (Env::get('APP_DEBUG')) {
            return [
                'signPackage' => $signPackage,
                'shareParams' => $shareParams
            ];
        }
        if ($url != '') {
            $app = AppMp::getApp($this->app_id);
            $app->jssdk->setUrl($url);
            $signPackage = $app->jssdk->buildConfig(array('updateAppMessageShareData', 'updateTimelineShareData'), false);
            $shareParams = [
                'title' => $title,
                'desc' => $desc,
                'link' => $link,
                'imgUrl' => $imgUrl,
            ];
        }
        return [
            'signPackage' => $signPackage,
            'shareParams' => $shareParams
        ];
    }

    protected function getScanParams($url)
    {
        $signPackage = '';
        if (Env::get('APP_DEBUG')) {
            return [
                'signPackage' => $signPackage
            ];
        }
        if ($url != '') {
            $app = AppMp::getApp($this->app_id);
            $app->jssdk->setUrl($url);
            $signPackage = $app->jssdk->buildConfig(array('scanQRCode'), false);
        }
        return [
            'signPackage' => $signPackage
        ];
    }
}
