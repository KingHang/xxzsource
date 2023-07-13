<?php

namespace app\api\controller\hlb;

use app\api\controller\Controller;
use app\shop\model\user\User as UserModel;
use app\timebank\model\Timebankuser;
use app\timebank\ztservice\Service;
use think\response\Json;

class User extends Controller
{
    /**
     * 会员充值
     * @return Json
     */
    public function recharge()
    {
        $postData = $this->postData();
        $mobile = $postData['mobile'];
        $source = (int)$postData['source'];
        $params = $postData['params'];
        $params = json_decode($params, true);

        // 用户详情
        $model = UserModel::detail(array('mobile' => $mobile));

        if ($model->recharge('admin', $source, $params)) {
            return $this->renderSuccess('操作成功');
        }

        return $this->renderError('操作失败');
    }

    /**
     * 会员注册
     */
    public function register()
    {
        $postData = $this->postData();

        $service = new Service();
        $accessToken = $service->getZtAccessToken('access_token');

        $user = new Timebankuser();

        if ($user->synchronizeUsers($postData, $accessToken)) {
            return $this->renderSuccess('操作成功');
        }

        return $this->renderError('操作失败');
    }

    /**
     * 商品同步
     */
    public function goods()
    {

    }
}
