<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\Activity as ActivityModel;
use app\common\model\plugin\activity\ActivityLog;
use app\common\model\store\Store AS StoreModel;
use app\api\model\plus\activity\History AS HistoryModel;

/**
 * 活动模型
 */
class Activity extends ActivityModel
{
    public $error = '';
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'app_id',
        'update_time',
        'create_time',
    ];

    /**
     * 活动列表
     * @param $data
     * @param $user_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($data,$user_id,$host_id = '')
    {
        $result['home'] = empty($data['home']) ? 1 : $data['home']; // 页数
        $result['pageSize'] = empty($data['pageSize']) ? 10 : $data['pageSize']; // 数量
        $where = [];
        $model = $this;
        // 设置查询条件
        if (isset($data['keyword']) && $data['keyword']) {
            $where[] = ['name|address' , 'like' , "%" . strtolower($data['keyword']) . "%"];
            // 新增检索记录
            (new HistoryModel())->add($user_id,$data['keyword']);
        }
        if (isset($data['category_id']) && $data['category_id'] > 0) {
            $where[] = ['category_id' , '=' ,  $data['category_id']];
        }

        if ($host_id != '') {
            $where[] = ['host_id' , 'in' ,  $host_id];
        }
        $field = '0 as distance,';
        $order =['is_end' => 'DESC', 'signup_time_start' => 'ASC'];
        if (isset($data['latitude']) && isset($data['longitude'])) {
            $latitude = $data['latitude'];
            $longitude = $data['longitude'];
            $field = "ROUND(6378.138*2*ASIN(SQRT(POW(SIN(($latitude*PI()/180-latitude*PI()/180)/2),2)+COS($latitude*PI()/180)*COS(latitude*PI()/180)*POW(SIN(($longitude*PI()/180-longitude*PI()/180)/2),2)))*1000) AS distance,";
            $order =['is_end' => 'DESC', 'distance' => 'asc'];
        }

        $where[] = ['is_delete' , '=' , 0];
        //获取总数
        $count = $model
            ->where($where)
            ->where(function ($query) use ($data) {
                if (isset($data['city']) && $data['city'] > 0) {
                    // 城市筛选  如果是线上活动不受影响
                    $query->whereRaw("IF(type = 1 , city = '" . $data['city'] . "','1')");
                }
            })
            ->count();
        $page_count = ($count > 0) ? intval(ceil($count / $result['pageSize'])) : 1;
        $list = $model->field($field . '(case when signup_time_end < unix_timestamp() then 0 else 1 end) AS is_end,id,type,name,image_id,charge_type,charge,activity_time_start,activity_time_end,signup_time_start,signup_time_end,province,city,area,address,longitude,latitude')
            ->where($where)
            ->where(function ($query) use ($data) {
                if (isset($data['city']) && $data['city'] > 0) {
                    // 城市筛选  如果是线上活动不受影响
                    $query->whereRaw("IF(type = 1 , city = '" . $data['city'] . "','1')");
                }
            })
            ->order($order)
            ->with(['image','remind'])->page($result['home'], $result['pageSize'])
            ->select();

        if (!empty($list)) {
            foreach ($list as $key=>$value) {
                $list[$key]['is_remind'] = 0;
                if (!empty($value['remind'])) {
                    $list[$key]['is_remind'] = 1;
                }
                unset($list[$key]['remind']);
                $list[$key]['signup_time_start_stamp'] = strtotime($value['signup_time_start']);
                $list[$key]['activity_time_start_stamp'] = strtotime($value['activity_time_start']);
                $list[$key]['activity_time_end_stamp'] = strtotime($value['activity_time_end']);
                $list[$key]['signup_time_end_stamp'] = strtotime($value['signup_time_end']);
                $list[$key]['activity_time_format'] = date('m/d H:i', strtotime($value['activity_time_start'])) . ' - ' . date('m/d H:i' , strtotime($value['activity_time_end']));
                if ($value['distance'] >= 1000) {
                    $value['distance'] = bcdiv($value['distance'], 1000, 2);
                    $list[$key]['distance_unit'] = $value['distance'] . 'km';
                } else {
                    $list[$key]['distance_unit'] = $value['distance'] . 'm';
                }
            }
        }
        // 格式返回参数
        $result['count'] = $count; //服务总数
        $result['page_count'] = $page_count; //服务总页数
        $result['list'] = $list;
        $result['more'] = $result['home'] < $page_count ? 1 : 0; //是否存在下一页

        return $result;
    }
    public function getActivityWithId($activity_id,$with)
    {
        return $info = $this
            ->with($with)
            ->where(['id' => $activity_id , 'is_delete' =>0] )
            ->find();
    }
    /**
     * 活动详情
     * @param int $activity_id
     * @param int $user_id
     * @return array|bool|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail ($activity_id = 0,$user_id = 0)
    {
        $info = $this->getActivityWithId($activity_id,['image','remind','collection','poster','host']);
        if (!$info) {
            $this->setError('活动不存在');
            return false;
        }
        $info['text']=$info['content'];
        // 是否设置提醒
        $info['is_remind'] = 0;
        if (!empty($info['remind'])) {
            $info['is_remind'] = 1;
        }
        unset($info['remind']);
        // 是否收藏
        $info['is_collection'] = 0;
        if (!empty($info['collection'])) {
            $info['is_collection'] = 1;
        }
        unset($info['collection']);
        // 格式化时间
        $info['activity_time_format'] = date('m/d H:i', strtotime($info['activity_time_start'])) . ' - ' . date('m/d H:i' , strtotime($info['activity_time_end']));
        // 处理海报显示信息
        $info['poster_data'] = json_decode($info['poster_data'],true);
        // 处理登记信息
        $sign_field = array();
        // 姓名
        $sign_field['need_name'] = array(
            'custom' => 0,
            'data_type' => '0',
            'tp_name' => '姓名',
            'tp_must' => $info['need_name'] == 1 ? 1 : 0, // 是否必填 0：否 1：是
            'tp_default' => '姓名', // 默认值
            'placeholder' => '姓名必填', // 提示语
            'value' => '',
        );
        // 手机号
        $sign_field['need_mobile'] = array(
            'custom' => 0,
            'data_type' => '0',
            'tp_name' => '手机号',
            'tp_must' => $info['need_mobile'] == 1 ? 1 : 0, // 是否必填 0：否 1：是
            'tp_default' => '手机号', // 默认值
            'placeholder' => '手机号必填', // 提示语
            'value' => '',
        );
        if (!empty($info['sign_field'])) {
            foreach($info['sign_field'] as $key=>$item) {
                $item['value'] = '';
                $item['custom'] = 1;
                $sign_field[$key] = $item;
            }
        }
        $info['sign_field'] = serialize($sign_field);
        // 获取当前会员购买数量
        $user_count = $user_id > 0 ? (new ActivityLog())->getUserByNumber($user_id,$info['id']) : 0;
        // 判断当前会员是否可以继续购买
        $can_buy = 1;
        if ($info['limit_buy'] > 0 && $user_count >= $info['limit_buy'])  {
            $can_buy = 0;
        }
        // 主办方名称
        $info['sponsor'] = !empty($info['host']) ? $info['host']['name'] : '';
        unset($info['host']);
        // 获取实际报名总数
        $actual_sign_count = (new ActivityLog())->getSignCount($info['id']);
        // 已报名人数
        $info['count_sign'] = $info['fictitious_num'] + $actual_sign_count;
        $info['actual_sign_count'] = $actual_sign_count;
        $info['can_buy'] = $can_buy;
        $info['user_count'] = $user_count;
        return $info;
    }
    /**
     * 设置错误信息
     */
    protected function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在错误
     */
    public function hasError()
    {
        return !empty($this->error);
    }
}