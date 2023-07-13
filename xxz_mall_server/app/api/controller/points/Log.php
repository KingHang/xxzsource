<?php

namespace app\api\controller\points;

use app\api\controller\Controller;
use app\api\model\user\PointsLog as PointsLogModel;
use app\api\model\user\GrowthLog as GrowthLogModel;
use app\api\model\settings\Settings as SettingModel;
use app\common\exception\BaseException;
use think\db\exception\DbException;

/**
 * 积分明细控制器
 */
class Log extends Controller
{
    /**
     * 积分明细列表
     * @throws DbException|BaseException
     */
    public function index()
    {
        $user = $this->getUser();
        $points = $user['exchangepurch'];
        $points_name = SettingModel::getPointsName();
        $list = (new PointsLogModel)->getList($user['user_id'], $this->postData());
        //积分商城是否开放
        $is_open = SettingModel::getItem('pointsmall')['is_open'];
        return $this->renderSuccess('', compact('list', 'points', 'points_name', 'is_open'));
    }

    /**
     * 成长值明细列表
     * @throws DbException|BaseException
     */
    public function grow()
    {
        $user = $this->getUser();
        $growth_value = $user['growth_value'];
        $growth_name = SettingModel::getGrowthName();
        $list = (new GrowthLogModel)->getList($user['user_id'], $this->postData());
        return $this->renderSuccess('', compact('list', 'growth_value', 'growth_name'));
    }
}
