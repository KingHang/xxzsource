<?php

namespace app\api\controller\user;

use app\api\model\user\User as UserModel;
use app\api\controller\Controller;
use app\api\model\settings\Settings as SettingModel;
use app\common\exception\BaseException;
use app\api\model\user\Timelog;

/**
 * 用户管理模型
 */
class User extends Controller
{
    /**
     * 用户自动登录,默认微信小程序
     */
    public function login()
    {
        $model = new UserModel;
        $user_id = $model->login($this->request->post());
        if (!$user_id){
            return $this->renderError('登陆失败');
        }
        $token = $model->getToken();
        $userInfo = $model->getUser($token);
        return $this->renderSuccess('',[
            'user_id' => $user_id,
            'token' => $token,
            'mobile' => $userInfo ? $userInfo['mobile'] : ''
        ]);
    }

    public function byteLogin()
    {
        $model = new UserModel;
        $user_id = $model->byteLogin($this->request->post());
        if ($user_id) {
            return $this->renderSuccess('',[
                'user_id' => $user_id,
                'token' => $model->getToken()
            ]);
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 当前用户详情
     */
    public function detail()
    {
        // 当前用户信息
        $userInfo = $this->getUser();
        $gift_name = SettingModel::getItem('live')['gift_name'];
        return $this->renderSuccess('',compact('userInfo', 'gift_name'));
    }

    /**
     * 绑定手机号
     */
    public function bindMobile()
    {
        $model = $this->getUser();
        if($model->bindMobile($this->request->post())){
            return $this->renderSuccess('');
        }
        return $this->renderError('绑定失败');
    }

    /**
     * 修改用户信息
     * @throws BaseException
     */
    public function updateUser()
    {
        $model = $this->getUser();

        if ($model->updateUser($this->request->post())) {
            return $this->renderSuccess('修改成功');
        }

        return $this->renderError('修改失败');
    }

    /**
     * 字节小程序绑定手机号
     */
    public function bindmobileByte()
    {
        $user = $this->getUser();
        $mobile = $this->request->param('mobile');
        if ($user) {
            $model = new UserModel;
            $result = $model->getByMobile($mobile);
            if ($result && $result['user_id'] != $user['user_id']) {
                return $this->renderError('绑定失败');
            }
            $user->save([
                'mobile' => $mobile
            ]);
            return $this->renderSuccess('');
        } else {
            return $this->renderError('绑定失败');
        }
    }
    public function getCertificateNumber()
    {
        $user = $this->getUser();
        $user_id = $user['user_id'];
        return $this->renderSuccess('',\app\common\model\plugin\agent\User::details($user_id));
    }

    /**
     * 获取CFP日志
     * @return \think\response\Json
     * @throws BaseException
     */
    public function getUserTimeLog()
    {
        $user = $this->getUser();
        $detail = (new Timelog())->getList($this->request->param(),$user);
        return $this->renderSuccess('', compact('detail'));
    }
}