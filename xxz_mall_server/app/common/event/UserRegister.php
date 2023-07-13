<?php


namespace app\common\event;

use app\faceRecognition\model\user\User;
use app\common\service\ztservice\ZtService;
use think\facade\Cache;
class UserRegister
{
    public $user;
    public $appId;
    public $model;

    public function handle(User $user)
    {
        $this->user = $user;
        $this->appId = $user['app_id'];
        $this->model = new ZtService();
        // 中台会员注册
        $this->ZtRegister();
        return true;
    }

    /**
     * 中台会员注册
     */
    private function ZtRegister()
    {
       $data = array(
           'username' => $this->user['mobile'],
           'avatar'     => $this->user['avatarUrl'],
           'gender'        => $this->user['gender'],
           'mobile'     => $this->user['mobile'],
           'id_card'    => $this->user['id_card'],
           'regSource'  => $this->user['reg_source'],
       );
        $data['access_token'] = $this->getZtAccessToken('access_token');
        $this->model->UserRegister($data);
//        $request = json_decode($request,true);
//        var_dump($request);die;
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