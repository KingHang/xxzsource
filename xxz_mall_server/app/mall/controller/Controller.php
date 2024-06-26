<?php

namespace app\mall\controller;

use app\common\exception\BaseException;
use app\common\model\setting\Setting;
use app\common\model\mall\OptLog as OptLogModel;
use app\XxzController;
use app\mall\service\AuthService;

/**
 * 商户后台控制器基类
 */
class Controller extends XxzController
{
    /** @var array $store 商家登录信息 */
    protected $store;

    /** @var string $route 当前控制器名称 */
    protected $controller = '';

    /** @var string $route 当前方法名称 */
    protected $action = '';

    /** @var string $route 当前路由uri */
    protected $routeUri = '';

    /** @var string $route 当前路由：分组名称 */
    protected $group = '';

    /** @var string $route 当前路由：分组名称 */
    protected $menu = '';

    /** @var array $allowAllAction 登录验证白名单 */
    protected $allowAllAction = [
        // 登录页面
        '/passport/login',
        /*登录信息*/
        '/index/base',
        /*获取验证码*/
        '/passport/sendCode',
        /*短信注册*/
        '/passport/smsRegister',
        /*短信登录*/
        '/passport/smsLogin',
        /*重设密码*/
        '/passport/resetPassword',
        /*统计-数量显示*/
        '/data/statistics/numInfo'
    ];

    /** @var array $allowPrivilegeAction 权限验证白名单 */
    protected $allowPrivilegeAction = [
        /*服务*/
        '/store/order/export',
        '/user/balance/log',
        '/user/balance/setting',
        '/user/plan/index',
        '/user/plan/log'
    ];

    /**
     * 后台初始化
     */
    public function initialize()
    {
        // 商家登录信息
        $this->store = session('xxzmall_store');
        // 当前路由信息
        $this->getRouteinfo();
        //  验证登录状态
        $this->checkLogin();
        // 写入操作日志
        $this->saveOptLog();
        // 验证当前页面权限
        $this->checkPrivilege();
    }

    /**
     * 验证当前页面权限
     */
    private function checkPrivilege()
    {
        if ($this->store == null) {
            return false;
        }
        // 验证当前请求是否在白名单
        if (in_array($this->routeUri, $this->allowPrivilegeAction)) {
            return true;
        }
        // 活动报名后台暂时不验证权限
        if (strpos($this->routeUri, '/plugin/activity') !== false) {
            return true;
        }
        if (!AuthService::getInstance()->checkPrivilege($this->routeUri)) {
            throw new BaseException(['msg' => '很抱歉，没有访问权限']);
        }
        return true;
    }

    /**
     * 解析当前路由参数 （分组名称、控制器名称、方法名）
     */
    protected function getRouteinfo()
    {
        // 控制器名称
        $this->controller = strtolower($this->request->controller());
        $this->controller = str_replace(".","/",$this->controller);
        // 方法名称
        $this->action = Request()->action();
        // 控制器分组 (用于定义所属模块)
        $groupstr = strstr($this->controller, '.', true);
        $this->group = $groupstr !== false ? $groupstr : $this->controller;
        // 当前uri
        $this->routeUri =  '/' . $this->controller . '/' . $this->action;
    }

    /**
     * 验证登录状态
     */
    private function checkLogin()
    {
        // 验证当前请求是否在白名单
        if (in_array($this->routeUri, $this->allowAllAction)) {
            return true;
        }
        // 验证登录状态
        if ($this->store != null && $this->store['is_login'] == 1) {
            return true;
        }
        throw new BaseException(['code' => -1, 'msg' => 'not_login']);
    }

    /**
     * 操作日志
     */
    private function saveOptLog(){
        if($this->store == null){
            return;
        }
        $mall_user_id = $this->store['user']['mall_user_id'];
        if(!$mall_user_id){
            return;
        }
        // 如果不记录查询日志
        $config = Setting::getItem('store');
        if(!$config || !$config['is_get_log']){
            return;
        }
        $model = new OptLogModel();
        $model->save([
            'mall_user_id' => $mall_user_id,
            'ip' => \request()->ip(),
            'request_type' => $this->request->isGet()?'Get':'Post',
            'url' => $this->routeUri,
            'content' => json_encode($this->request->param(), JSON_UNESCAPED_UNICODE),
            'browser' => get_client_browser(),
            'agent' => $_SERVER['HTTP_USER_AGENT'],
            'title' => AuthService::getAccessNameByPath($this->routeUri, $this->store['app']['app_id']),
            'app_id' => $this->store['app']['app_id']
        ]);
    }


}
