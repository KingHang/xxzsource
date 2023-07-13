<?php
declare (strict_types = 1);

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\api\model\product\Goods;
use app\XxzController;
use think\Request;

class Cardvoucher extends Controller
{
    // 模型
    private $model;

    // 当前用户
    private $user;

    /**
     * 构造方法
     */
    public function initialize()
    {
        $this->model = new \app\api\model\order\OrderGoods();
        $this->user = $this->getUser();
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function list()
    {
        $list = $this->model->getList($this->postData(),$this->user['user_id']);
        return $this->renderSuccess('', $list);
    }

}
