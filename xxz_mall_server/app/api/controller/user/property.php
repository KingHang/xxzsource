<?php
declare (strict_types = 1);

namespace app\api\controller\user;

use app\api\controller\Controller;
use app\timebank\model\Common;
use app\timebank\model\Timebankmultiple;
use app\timebank\model\Timebanksingle;
use app\timebank\model\Timelog;
use think\Request;

class property extends \app\timebank\controller\applet\Controller
{
    public function getuser()
    {
        return $this->renderSuccess('数据获取成功', $this->getUserinfo($is_force=true,$is_ztservice=true));
    }
    //服务明细
    public function usertime_log()
    {
        $params = \think\facade\Request::post();
        $pagesize = $params['pagesize'] ?? 10;
        $page = $params['home'] ?? 1;
        $user_id = $this->getUserId();
        $where=[];
        $where[] = ["user_id", '=', "{$user_id}"];
        $date_search = $params['date_search'] ?? '';
        $server_type = $params['server_type'] ?? '';
        $trade = $params['trade'] ?? '';
        if (!empty($date_search))//搜索框名字/电话
        {
            $common = new Common();
            $first = $common->getCurMonthFirstDay($date_search);
            $last =  $common->getCurMonthLastDay($date_search);
            $where[] = ['create_time', 'between', [$first,$last]];
        }
        if (!empty($server_type))//审核状态条件
        {
            $where[] = ["server_type", '=', "{$server_type}"];
        }
        if (!empty($trade))//审核状态条件
        {
            $where[] = ["trade_type", 'in', "{$trade}"];
        }
        $timelog = Timelog::where($where)
            ->order('create_time', 'desc')
            ->paginate(['list_rows' => $pagesize, 'home' => $page])
            ->toArray();
        foreach ($timelog['data'] as $key => $v) {
            if ($v['single_id']) {
                $level = Timebanksingle::field('level_string')->where('id', $v['single_id'])->find();
                $level = json_decode($level->level_string, true);
                $timelog['data'][$key]['level_power'] = $level['level_power'];
            }
            if ($v['mult_id']) {
                $level = Timebankmultiple::field('level_string')->where('id', $v['mult_id'])->find();
                $level = json_decode($level->level_string, true);
                $timelog['data'][$key]['level_power'] = $level['level_power'];
            }

        }
        return $this->renderSuccess('数据获取成功', $timelog);
    }
}
