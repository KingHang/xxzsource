<?php

namespace app\mall\controller;

use app\common\model\app\App;
use app\mall\model\mall\Sms as SmsModel;
use app\mall\model\mall\User;
use think\facade\Session;
use think\exception\ValidateException;
use app\mall\validate\shop\User as ValidateUser;
use think\response\Json;

/**
 * 商户认证
 */
class Passport extends Controller
{
    /**
     * 商户后台登录
     */
    public function login()
    {
        //登录前清空session
        session('xxzmall_store', null);
        $user = $this->postData();
        $user['password'] = salt_hash($user['password']);
        $model = new User();
        if ($model->checkLogin($user)) {
            $isTrade = $this->getTradeState();
            return $this->renderSuccess('登录成功', ['username' => $user['username'], 'isTrade' => $isTrade]);
        }
        return $this->renderError($model->getError()?:'登录失败');
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session('xxzmall_store', null);
        return $this->renderSuccess('退出成功');
    }

    /**
     * 修改密码
     */
    public function editPass()
    {
        $data = $this->postData();
        $result = $this->validateData($data,'code');

        if (!$result['check']) {
            return $this->renderError($result['msg']);
        }

        $model = new User();
        if ($model->editPassByCode($data, $this->store['user'])) {
            return $this->renderSuccess('修改成功');
        }
        return $this->renderError($model->getError()?:'修改失败');
    }

    /**
     * 获取验证码
     */
    public function sendCode()
    {
        $data = $this->postData();

        // 是否登录状态判断
        $isLogin = isset($data['isLogin']) && $data['isLogin'] == 1 ? 1 : 0;

        if (!$isLogin) {
            session('xxzmall_store', null);
        }

        $result = $this->validateData($data,'mobile');
        
        if (!$result['check']) {
           return $this->renderError($result['msg']);
        }

        $model = new SmsModel();
        if ($model->send($data['mobile'], $data['type'])) {
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError() ?: '发送失败');
    }

    /**
     * 短信注册
     */
    public function smsRegister()
	{
        session('xxzmall_store', null);
        $data = $this->postData();
        $result = $this->validateData($data,'code');
        
        if (!$result['check']) {
           return $this->renderError($result['msg']);
        }

        $model = new User();
 
        if ($model->smsregister($data)) {
            return $this->renderSuccess();
        }
        return $this->renderError($model->getError() ?: '登录失败');
    }

    /**
     * 短信登录
     */
    public function smsLogin()
	{
        //登录前清空session
        session('xxzmall_store', null);
        $user = $this->postData();
        $result = $this->validateData($user,'code');

        if (!$result['check']) {
           return $this->renderError($result['msg']);
        }

        $model = new User();

        if ($model->smslogin($user)) {
            $isTrade = $this->getTradeState();
            return $this->renderSuccess('登录成功', ['mobile' => $user['mobile'], 'isTrade' => $isTrade]);
        }
        
        return $this->renderError($model->getError() ?: '登录失败');
    }

    /**
     * 重设密码
     */
    public function resetPassword()
    {
        session('xxzmall_store', null);
        $data = $this->postData();
        $result = $this->validateData($data,'code');
        
        if (!$result['check']) {
           return $this->renderError($result['msg']);
        }

        $model = new User();
        if ($model->resetpassword($data)) {
            return $this->renderSuccess('设置成功');
        }
        return $this->renderError($model->getError() ?: '设置失败');
    }

    /**
     * 选择行业
     * @param $trade_id
     * @return Json
     */
    public function bindTrade($trade_id)
    {
        if (!$trade_id) return $this->renderError('请选择行业');

        $store = session('xxzmall_store');

        if (empty($store) || empty($store['user']) || !isset($store['user']['app_id'])) return $this->renderError('请先登录');

        if ($store['user']['app_id'] == '10001') return $this->renderError('无需选择行业');

        $model = App::detail($store['user']['app_id']);

        if (!$model) return $this->renderError('未找到应用信息');

        if ($model->save(['trade_id' => $trade_id])) return $this->renderSuccess('操作成功');

        return $this->renderError('操作失败');
    }

    /**
     * 验证
     * @param $data
     * @param string $scene
     * @return array
     */
    private function validateData($data, $scene = 'mobile')
    {
        $result = [];
        $result['check'] = false;
        
        try {
            validate(ValidateUser::class)
                ->scene($scene)
                ->check($data);
        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            $result['msg'] = ($e->getError());
            return  $result;
        }

        $result['check'] = true;
        return $result;
    }

    /**
     * 登录时获取是否选择行业
     */
    private function getTradeState()
    {
        $store = session('xxzmall_store');
        $user = !empty($store) ? $store['user'] : [];
        return App::checkTrade($user);
    }

    /**
     * 信息测试
     */
    public function info()
    {
        $arr['roles']= 'admin';
        $arr['introduction']= 'I am a super administrator';
        $arr['avatar']= 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif';
        $arr['name']= 'Super Admin';
        return $this->renderSuccess('success', $arr);
    }
}
