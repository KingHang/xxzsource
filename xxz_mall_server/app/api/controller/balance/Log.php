<?php

namespace app\api\controller\balance;

use app\api\controller\Controller;
use app\api\model\settings\Settings as SettingModel;
use app\api\model\user\BalanceLog as BalanceLogModel;
use app\common\exception\BaseException;
use think\db\exception\DbException;
use think\response\Json;

/**
 * 余额账单明细
 */
class Log extends Controller
{
    /**
     * 余额首页
     */
    public function index()
    {
        $user = $this->getUser();
        $list = (new BalanceLogModel)->getTop10($user['user_id'], $this->postData());
        // 余额
        $balance = $user['balance'];
        $bonus = $user['bonus'];
        // 充值功能是否开启
        $balance_setting = SettingModel::getItem('balance');
        $balance_open = intval($balance_setting['is_open']);
        return $this->renderSuccess('', compact('list', 'balance','bonus', 'balance_open'));
    }

    /**
     * 余额账单明细列表
     * @param string $type
     * @return Json
     * @throws BaseException
     * @throws DbException
     */
    public function lists($type = 'all')
    {
        $user = $this->getUser();
        $list = (new BalanceLogModel)->getList($user['user_id'], $type, $this->postData());
        return $this->renderSuccess('', compact('list'));
    }
}
