<?php

namespace app\mall\controller\data;

use app\mall\controller\Controller;
use app\timebank\model\Timebankmultiple;
use app\timebank\model\Timebankorder;
use app\timebank\model\Timebankserver;
use app\timebank\model\Timebanksingle;
use app\timebank\model\Timebankuser;

/**
 * 统计控制器
 */
class Statistics extends Controller
{
    /**
     * 获取数量
     */
    public function numInfo()
    {
        $beginTime = strtotime(date("Y-m-d 00:00:01", time()));
        $endTime = strtotime(date("Y-m-d 23:59:59", time()));

        //订单总数量
        $orderNum = Timebankorder::count();

        //今日新增订单数量
        $todayOrderNum = Timebankorder::where('create_time', '>=', $beginTime)->where('create_time', '<=', $endTime)->count();

        //会员总数量
        $userNum = Timebankuser::count();

        //今日新增会员数量
        $todayUserNum = Timebankuser::where('create_time', '>=', $beginTime)->where('create_time', '<=', $endTime)->count();

        //男性数量
        $manNum = Timebankuser::where('gender', '=', 1)->count();

        //女性数量
        $womanNum = Timebankuser::where('gender', '=', 0)->count();

        //服务者数量
        $servantNum = Timebankserver::count();

        //服务数量
        $singleNum = Timebanksingle::count();
        $multipleNum = Timebankmultiple::count();
        $serverNum = (int)$singleNum + (int)$multipleNum;

        //志愿者服务数量
        $volunteerServerNum = Timebankorder::where('server_type', '=', 2)->count();

        //累计时间
        $timeNum = Timebankorder::where('status', '>', 0)->sum('timeget');

        //待服务数量
        $servedNum = Timebankorder::where('status', 'in', [-2, -1])->count();

        //服务中数量
        $servingNum = Timebankorder::where('status', '=', 2)->count();

        return $this->renderSuccess('', compact(
            'orderNum',
            'todayOrderNum',
            'userNum',
            'todayUserNum',
            'manNum',
            'womanNum',
            'servantNum',
            'serverNum',
            'volunteerServerNum',
            'timeNum',
            'servedNum',
            'servingNum'
        ));
    }
}
