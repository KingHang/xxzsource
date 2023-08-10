<?php
declare (strict_types=1);

namespace app\api\model\user;

use app\common\model\BaseModel;
use think\Model;
use app\common\library\helper;

/**
 * @mixin Model
 */
class Timelog extends BaseModel
{
    protected $table = 'xxzmall_usertime_log';
    protected $autoWriteTimestamp = true;
    protected $readonly = ['create_time', 'update_time'];

    /**
     * 类型
     */
    public function getTradeTypeAttr($value)
    {
        $type = [0 =>'未知',2 => '转账给-', 1 => '转账来自-', 3 => '兑换', 4 => '发布个人服务',5 => '完成多人项目',6 => '完成个人项目',
            7=>'CFP/TIME',8=>'TIME/CFP',9=>'HXL/CFP(转出)',10=>'HXL/CFP(转入)',11=> '商品CFP抵扣',12 => '商品CFP退回',
            13 => '商品CFP赠送',14 =>'time/CNY(转出)'];
        return ['text' => $type[$value], 'value' => $value];
    }
    public function timebankUser()
    {
        //hasOne表示一对一关联，参数一表示附表，参数二表示外键，参数三表示主键
        return $this->hasOne("app\\timebank\\model\\Timebankuser", 'user_id', 'user_id');
    }
    public function getList($data = [],$user)
    {
        $model = $this::withoutGlobalScope();
        //搜索订单号
        if (isset($data['search']) && $data['search'] != '') {
            $model = $model->where('timelog.show_name|timelog.show_mobile|timelog.orderNo|timelog.serial_number|timelog.cfp_address', 'like', '%' . trim($data['search']) . '%');
        }
        //搜索配送方式
        if (isset($data['trade_type']) && $data['trade_type'] > 0) {
            $model = $model->where('timelog.server_type', '=', $data['trade_type']);
        }
        if (isset($data['type']) && $data['type'] != '') {
            if ($data['type'] == 2){//time
                $model = $model->where('timelog.trade_type', 'in', [7,8]);
            }
        }
        $model = $model->where('timelog.user_id' , '=' , $user['user_id']);
        // 时间
        if ($data['month'] && $data['month'] != '' && $data['month'] != 'undefined') {
            $startOnMonth = date('Y-m-01', strtotime($data['month']));
            $endOnMonth = strtotime(date('Y-m-d', strtotime("$startOnMonth +1 month -1 day"))) + 86399;
            $startOnMonth = strtotime($startOnMonth);
            $model = $model->where('timelog.create_time', 'between', [$startOnMonth, $endOnMonth]);
        }

        //搜索时间段
        if (isset($data['create_time']) && $data['create_time'] != '') {
            $sta_time = array_shift($data['create_time']);
            $end_time = array_pop($data['create_time']);
            $model = $model->whereBetweenTime('timelog.create_time', $sta_time, $end_time);
        }
        // 获取数据列表
        $list = $model
            ->withJoin(['timebankuser'=>['nickName','avatarUrl','mobile']])
            ->field("timelog.*,FROM_UNIXTIME(timelog.create_time,'%m月%d日 %H:%i') as time,FROM_UNIXTIME(timelog.create_time,'%Y年%m月') as months")
            ->order('timelog.create_time DESC')
            ->paginate($data);
        // 获取月份集合
        $mothIds = array_unique(helper::getArrayColumn($list, 'months'));
        $monthList = [];
        //根据月份分组
        foreach ($mothIds as $key => &$item) {
            $lists = [];
            foreach ($list as $value) {
                if ($item == $value['months']) {
                    $lists[] = $value;
                }
            }
            $month['months'] = $item;
            $month['lists'] = $lists;
            $monthList[] = $month;
        }
        $info['list'] = $list;
        $info['monthList'] = $monthList;
        return $info;
    }
}
