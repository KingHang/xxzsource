<?php


namespace app\common\event;

use app\faceRecognition\model\user\User;
use app\common\service\ztservice\ZtService;
use think\facade\Cache;
class UserUpdate
{
    public $user;
    public $appId;
    public $model;
    public $updateData;

    public function handle(User $user)
    {
        if ($this->updateData = Cache::get('UpdateUserData'.$user['user_id'])) {
            $this->user = $user;
            $this->appId = $user['app_id'];
            $this->model = new ZtService();
            // 中台会员注册
            $this->ZtUserUpdate();
        }
        // 销毁会员更新缓存
        Cache::delete('UpdateUserData'.$user['user_id']);
        return true;
    }

    /**
     * 中台修改会员信息
     */
    private function ZtUserUpdate()
    {
        $data = [];
        if (isset($this->updateData['mobile'])) {
            $data['mobile'] = $this->updateData['mobile'];
        }
        if (isset($this->updateData['id_card'])) {
            $data['id_card'] = $this->updateData['id_card'];
        }
        if (isset($this->updateData['email'])) {
            $data['email'] = $this->updateData['email'];
        }
        if (isset($this->updateData['sex'])) {
            $data['gender'] = $this->updateData['gender'];
        }
        if (isset($this->updateData['avatar'])) {
            $data['avatar'] = $this->updateData['avatar'];
        }

        if (!empty($data)) {
            $data['access_token'] = $this->getZtAccessToken('access_token');
            $request = $this->model->UserRegister($data);
//                    $request = json_decode($request,true);
//        var_dump($request);die;
        }
    }

    /**
     * 获取请求token
     * @param string $access_token
     * @return mixed|string
     */
    public function getZtAccessToken($access_token = 'access_token')
    {
        if ($access_token = Cache::get($access_token)) {
            return $access_token;
        } else {
            $data = $this->model->getAccessToken();
            $data = json_decode($data, true);
            $access_token = $data['data']['access_token'];
            $expire_at = $data['data']['expire_at'] - time();
            Cache::tag('cache')->set('access_token', $access_token, $expire_at);
            return $access_token;
        }

    }
}