<?php
declare (strict_types = 1);

namespace app\mall\controller\datavisualization;

use app\common\model\order\VerifyServerLog;
use app\common\model\store\Store;
use app\XxzController;
use app\mall\controller\Controller;
use app\mall\model\user\User;
use think\facade\Db;
use think\Request;

class Bigview extends Controller
{

    //总服务人次：社区服务中心（人脸识别核销）+时间银行报名+活动报名
    public function order_count()
    {
        $verifyCount = VerifyServerLog::count();
        $timeCount = Timebankorder::where('status',1)->count();
        $activityCount = ActivityOrder::where('order_status',30)->count();
        return $verifyCount+$timeCount+$activityCount;
    }
    //服务志愿者
    public function memberserver()
    {
        $where = [];
        $where[] = ["is_delete", '=', "0"];
        $where[] = ["status", '=', 1];
        return (new Timebankserver())->where($where)->count();
    }
    //会员：会员列表所有会员
    public function member_count()
    {
        return (new User())->getUserTotal();
    }
    //派单数：时间银行（单人服务派单+多人服务接单）+社区服务订单
    public function dispatch()
    {
        $verifyCount = VerifyServerLog::count();
        $timeCount = Timebankorder::where('status',1)->count();
        return $verifyCount+$timeCount;
    }
    //实时数据：时间银行待服务和进行中的服务
    public function get_timebank_server()
    {
        $server['servedNum'] = Timebankorder::where(['server_type'=>1,'status'=>['-2','2']])->count();
        $server['servingNum'] = Timebankorder::where(['server_type'=>2,'status'=>['-1','2']])->count();
        return $server;
    }
    //会员年龄分布
    public function agedistri()
    {
        $data =[];
        $data[0]['name']="年龄不详";
        $data[0]['value']=100099;
        $data[1]['name']="18-39岁";
        $data[1]['value']=444;
        $data[2]['name']="40-49岁";
        $data[2]['value']=333;
        $data[3]['name']="50-59岁";
        $data[3]['value']=522;
        $data[4]['name']="60-69岁";
        $data[4]['value']=566;
        $data[5]['name']="70岁及以上";
        $data[5]['value']=888;
        return $this->renderSuccess('success',$data);
    }
    //按省份查找
    public function memberarea()
    {
        $area = Db::query("select * from xxzmall_screen_statistics where `key`='area'");
        $list = json_decode($area[0]['value'], true);
        $tmp = array();
        $dat = array();
        foreach ($list as $k => $v) {
            $tmp['name'] = $k;
            $tmp['value'] = $v;
            $dat[] = $tmp;
        }
        return $this->renderSuccess('success',$dat);
    }
    //性别数量
    public function memberinfo()
    {
        $data['woman'] = (new User())->where(['gender'=>0,'is_delete'=>0])->count();
        $data['man'] = (new User())->where(['gender'=>1,'is_delete'=>0])->count();
        return $data;
    }

    //今日新增数量
    public function mtimecount()
    {
        $day = date("Y-m-d 00:00:01", time());
        return (new User())->getUserTotal($day);
    }

    //老年大学课程热度：人脸识别中属于老年大学课程分类的服务热度
    public function serverCategoryCount()
    {
        $server_name = array();
        $server_value = array();
        $category = ServerCategory::field('category_id,name')->where('is_delete',0)->limit(6)->select();
        foreach ($category as $k =>$v)
        {
            $activity_id = Server::where('category_id',$v['category_id'])->count();
            $category[$k]['categoryCount']=$activity_id;
            $server_name[] = $v['name'];
            $server_value[] = $v['categoryCount'];
        }
        $res['name'] = $server_name;
        $res['value'] = $server_value;
        $res['all'] = $category;
        return $this->renderSuccess('success',$res);
    }
    //上门服务热度：时间银行单人服务热度
    public function singleCount()
    {
        $server_name = array();
        $server_value = array();
        $category = Timebankserverproject::field('project_id,project_title')->where(['is_delete'=>0])->limit(8)->select();
        foreach ($category as $k =>$v)
        {
            $activity_id = Timebanksingle::where('project_id',$v['project_id'])->count();
            $category[$k]['singleCount']=$activity_id;
            $server_name[] = $v['project_title'];
            $server_value[] = $v['singleCount'];
        }
        $res['name'] = $server_name;
        $res['value'] = $server_value;
        $res['all'] = $category;
        return $this->renderSuccess('success',$res);
    }
    //社区中心服务热度：人脸识别中的服务项目
    public function faceCount()
    {
        $category = \app\common\model\facerecognition\ServerCategory::where(['is_delete'=>0])->select();
        $server_name = array();
        $server_value = array();
        foreach ($category as $k =>$v)
        {
            $activity_id = Server::where('category_id',$v['category_id'])->count();
            $category[$k]['faceCount']=$activity_id;
            $server_name[] = $v['name'];
            $server_value[] = $v['faceCount'];
        }
        $res['name'] = $server_name;
        $res['value'] = $server_value;
        $res['all'] = $category;
        return $this->renderSuccess('success',$res);
    }
    public function morderCount()
    {
        $beginTime = strtotime(date("Y-m-d 00:00:01", time()));
        $endTime = strtotime(date("Y-m-d 23:59:59", time()));

        $todayOrderNum = Timebankorder::where('create_time', '>=', $beginTime)->where('create_time', '<=', $endTime)->count();
        return $todayOrderNum;
    }
    public function numInfo()
    {
        $supplier_store_count = $this->supplier_store_count();
        $orderCount = $this->order_count();
        $memberServer = $this->memberserver();
        $memberCount = $this->member_count();
        $dispatch = $this->dispatch();
        $getTimeBankServer = $this->get_timebank_server();
        $memberInfo = $this->memberinfo();
        $activityCount = $this->activityCount();
        $serverNumber = $this->servernumber();
        $mtimeCount = $this->mtimecount();
        $todayOrderNum = $this->morderCount();
        $server = $this->get_timebank_server();
        $timeNum = Timebankorder::where('status', '>', 0)->sum('timeget');
        return $this->renderSuccess('success',compact(
            'supplier_store_count','orderCount','memberCount','memberServer','dispatch','getTimeBankServer',
            'memberInfo','activityCount','serverNumber','mtimeCount','todayOrderNum','timeNum','server'
        ));
    }


}
