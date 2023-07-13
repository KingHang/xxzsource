<?php

namespace app\api\model\plus\activity;

use app\api\model\plus\activity\ActivityHostRember as ActivityHostRemberModel;
use app\common\model\plugin\activity\ActivityLog as ActivityLogModel;
use app\api\model\plus\activity\ActivityOrder as ActivityOrderModel;
use app\api\model\plus\activity\Activity as ActivityModel;
use app\common\service\qrcode\ActivityService;
use app\common\model\user\User as UserModel;
use app\api\model\settings\Settings as SettingModel;

/**
 * 活动报名记录
 */
class ActivityLog extends ActivityLogModel
{
    /**
     * 获取用户签到二维码
     * @param $value
     * @param $data
     * @return string
     */
    public function getQrCodeAttr($value, $data)
    {
        $Qrcode = new ActivityService(
            $data['app_id'],
            $data['id'],
            $data['verify_code'],
            $data['user_id']
        );
        return $Qrcode->getImage();
    }

    /**
     * 获取活动列表
     * @param $user_id
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function LogList($user_id, $data)
    {
        $result['home'] = empty($data['home']) ? 1 : $data['home']; // 页数
        $result['pageSize'] = empty($data['pageSize']) ? 10 : $data['pageSize']; // 数量

        $model = $this;
        $model = $model->where('user_id', '=', $user_id);

        if (in_array($data['status'], array(0, 1, 2, 3, 4, 5))) {
            $this->error = '非法状态';
        }
        switch ($data['status']) {
            case 0:

                break;
            case 1:
                // 待审核
                $model = $model->where('status', '=', 0);
                break;
            case 2:
                // 待使用
                $model = $model->where('status', '=', 1);
                break;
            case 3:
                // 取消
                $model = $model->where('status', '=', 2);
                break;
            case 4:
                // 已签到
                $model = $model->where('status', '=', 3);
                break;
            case 5:
                // 已签到
                $model = $model->where('status', '=', 4);
                break;
        }
        $count = $model->count();
        $page_count = ($count > 0) ? intval(ceil($count / $result['pageSize'])) : 1;

        $list = $model->with([
            'activity' => function ($query) {
                $query->with('image');
                $query->field('id,name as title,image_id,activity_time_start,signup_time_start,signup_time_end,activity_time_end,province,city,area,address,sponsor,total_pay_price,charge_type');
            },
        ])->order(['create_time' => 'desc', 'id' => 'desc'])->page($result['home'], $result['pageSize'])->select();

        // 处理返回信息
        if (!empty($list)) {
            foreach ($list as &$item) {
                $item['can_cancel'] = 0;
                // 计算距离结束时间
                if (($item['activity']['activity_time_status'] - time()) / 3600 > 5) {
                    $item['can_cancel'] = 1;
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

    /**
     * 根据订单获取报名记录信息
     * @param $order_id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getInfoWithOrder($order_id)
    {
        $info = $this->field('id,name,mobile,activity_id,order_detail_id,order_id,verify_code,status,seat_number,app_id,user_id,sign_number,app_id,remarks,is_receive,charge_type,charge')
            ->where(['order_id' => $order_id])->find();

        if (empty($info)) {
            return [];
        }
        // 处理座位号
        $info['seat_number'] = $info['status'] == 3 ? $info['seat_number'] : '随机';

        return $info;
    }
    /**
     * 报名详情
     * @param $id
     * @param $user_id
     * @return array|bool|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function LogDetail($where)
    {
        $info = $this->field('id,name,mobile,activity_id,order_detail_id,order_id,verify_code,status,seat_number,app_id,user_id,sign_number,app_id,remarks,is_receive,charge_type,charge')->where($where)->with([
            'activity' => function ($query) {
                $query->with('image');
                $query->field('type,id,name as title,activity_time_start,signup_time_start,signup_time_end,activity_time_end,province,city,area,address,host_id,longitude,latitude,sign_field');
            },
        ])->find();

        if (empty($info)) {
            $this->error = '报名记录不存在';
            return false;
        }
        if (empty($info['activity'])) {
            $this->error = '数据异常';
            return false;
        }
        // 处理座位号
        $info['seat_number'] = $info['status'] == 3 ? $info['seat_number'] : '随机';

        return $info;
    }

    /**
     * 活动签到
     * @param $user_id
     * @param $data
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\PDOException
     */
    public function sign($user_id, $data)
    {
        $source = isset($data['source']) ? $data['source'] : 0; // 来源 0: 核销员扫码，1:会员扫码

        if ($source == 0) {
            // 主办方扫码
            if (!isset($data['id'])) {
                $this->error = '签到目标必填';
                return false;
            }
            $where = ['id' => $data['id']];
            $info = $this->LogDetail($where);
            // 获取活动信息
            $activity_model = (new ActivityModel());
            $activity = $activity_model->detail($info['activity_id']);
            // 验证核销员权限
            if (!(new ActivityHostRemberModel())->checkUserActivityAuth($user_id,$activity['host_id'])) {
                $this->error = '您暂无权限进行此操作';
                return false;
            }
            if (empty($info)) {
                return false;
            }
        } else {
            // 获取活动信息
            $activity_model = (new ActivityModel());
            $activity = $activity_model->detail($data['activity_id']);

            // 用户扫码
            if (!isset($data['activity_id'])) {
                $this->error = '签到目标必传';
                return false;
            }

            // 获取当前会员可签到报名记录
            $where = ['activity_id' => $data['activity_id'],'user_id' => $user_id];
            $info = $this->LogDetail($where);

            // 不存在报名记录 判断是否可以现场报名
            if (empty($info)) {
                if (!$activity) {
                    $this->error = $activity_model->getError();
                    return false;
                }
                // 判断是否可以现场报名 现场报名条件：1、活动支持现场报名，2活动免费
                if ($activity['charge_type']['value'] == 0 && $activity['is_local_sign'] == 1) {
                    // 返回报名填写字段
                    return [
                        'status' => 2,
                        'info' => [],
                        'activity' => [
                            'sign_field' => $activity['sign_field'],
                            'activity_id' => $activity['id'],
                            'name' => $activity['name']
                        ],
                    ];
                } else {
                    return [
                        'status' => 3,
                        'info' => [],
                        'activity' => [
                            'sign_field' => $activity['sign_field'],
                            'activity_id' => $activity['id'],
                            'name' => $activity['name']
                        ],
                    ];
                }
            }
        }

        $return = [
            'log_id' => $info['id'],
            'title' => $info['activity']['title'],
            'order_id' => $info['order_id'],
            'activity_id' => $info['activity_id'],
            'status' => $info['status']['status'],
            'sign_number' => $info['sign_number'],
        ];

        // 签到验证
        if (!$this->checkSign($info)) {
            return [
                'status' => 0,
                'info' => $return,
                'activity' => [
                    'sign_field' => $info['activity']['sign_field'],
                    'activity_id' => $info['activity_id'],
                    'name' => $info['activity']['title'],
                ]
            ];
        }

        // 更新签到状态
        $sign_info = [
            'sign_number' => $this->getSignNumber($info['activity_id']),
            'status' => 3,
            'sign_time' => time()
        ];

        // 获取用户信息
        $user = UserModel::detail($info['user_id']);

        // 获取配置参数
        $pointsSetting = SettingModel::getItem('exchangepurch');
        $growSetting = SettingModel::getItem('grow');
        $isPoints = isset($pointsSetting['is_points']) && $pointsSetting['is_points'] == '1' ? 1 : 0;//是否开启积分
        $isGrow = isset($growSetting['is_grow']) && $growSetting['is_grow'] == '1' ? 1 : 0;//是否开启成长值

        // 开启事务
        $this->startTrans();
        try {
            $info->save($sign_info);

            // 活动报名签到赠奖励
            if ($user && isset($activity) && $activity) {
                // 赠积分
                if ($isPoints && $activity['is_gift'] == '1' && $activity['exchangepurch'] > 0) {
                    $user->setIncPoints($activity['exchangepurch'], '活动报名签到发放奖励', 12);
                }

                // 赠成长值
                if ($isGrow && $activity['is_gift'] == '1' && $activity['growth_value'] > 0) {
                    $user->setIncGrowthValue($activity['growth_value'], '活动报名签到发放奖励', 8);
                }
            }

            $this->commit();
            $return['status'] = 3;
            $return['sign_number'] = $sign_info['sign_number'];
            $this->error = '签到成功';
            return [
                'status' => 1,
                'info' => $return,
                'activity' => [
                    'sign_field' => $info['activity']['sign_field'],
                    'activity_id' => $info['activity_id'],
                    'name' => $info['activity']['title'],
                ]
            ];
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 获取最新签到号
     * @param $activity_id
     * @return int|mixed
     */
    public function getSignNumber($activity_id)
    {
        return $this->where('activity_id', '=', $activity_id)->max('sign_number') + 1;
    }

    /**
     * 活动签到验证
     * @param $info
     * @return bool
     */
    public function checkSign($info)
    {
        // 验证报名状态
        if ($info['status']['status'] == 0) {
            $this->error = '报名审核中，暂不能签到';
            return false;
        }
        if ($info['status']['status'] == 2) {
            $this->error = '报名已取消';
            return false;
        }
        if ($info['status']['status'] == 4) {
            $this->error = '报名已过期';
            return false;
        }
        if ($info['status']['status'] == 3) {
            $this->error = '已经签到过了，不能重复签到';
            return false;
        }
        if ($info['activity']['activity_time_start'] > time()) {
            $this->error = '活动未开始';
            return false;
        }
        if ($info['activity']['activity_time_end'] < time()) {
            $this->error = '活动已结束';
            return false;
        }
        return true;
    }

    /**
     * 取消
     * @param $user_id
     * @param $data
     */
    public function cancel($user_id, $data)
    {
        if (!isset($data['id'])) {
            $this->error = '请选择活动';
            return false;
        }

        $logInfo = $this->LogDetail(['id' => $data['id'] , 'user_id' => $user_id]);
        if (empty($logInfo)) {
            return false;
        }
        // 验证状态
        if (!in_array($logInfo['status']['status'],[0,1])) {
            $this->error = '当前状态不能取消';
            return false;
        }
        // 距离活动开始5小时内不能取消
        if (($logInfo['activity']['activity_time_start'] - time()) / 3600 < 5) {
            $this->error = '距离活动开始小于5小时无法取消';
            return false;
        }

        // 退款
        $ActivityOrderModel = new ActivityOrderModel();
        if (!$ActivityOrderModel->refund($logInfo)) {
            $this->error = $ActivityOrderModel->getError() ? $ActivityOrderModel->getError() : '操作失败';
            return false;
        }
            return true;
    }

    /**
     * 伴手礼核销
     * @param $user_id
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\PDOException
     */
    public function ReceiveVerification($user_id,$data)
    {
        // 验参开始
        if (!isset($data['activity_id'])) {
            $this->error = '请选择活动';
            return false;
        }
        if (!isset($data['sign_number']) && intval($data['sign_number']) == 0) {
            $this->error = '请填写座位号';
            return false;
        }
        $logInfo = $this->LogDetail(['activity_id' => $data['activity_id'] , 'sign_number' => $data['sign_number']]);
        if (empty($logInfo)) {
            return false;
        }
       // 验证核销员权限
        if (!(new ActivityHostRemberModel())->checkUserActivityAuth($user_id,$logInfo['activity']['host_id'])) {
            $this->error = '您暂无权限进行此操作';
            return false;
        }
        // 活动状态
        if ($logInfo['status']['status'] != 3) {
            $this->error = '请签到后再进行此操作';
            return false;
        }
        // 验证领取状态
        if ($logInfo['is_receive'] ==  1) {
            $this->error = '您已经领取了，不能重复领取 ';
            return false;
        }
        // 领取伴手礼
        $this->startTrans();
        try {
            $logInfo->save(['is_receive' => 1 , 'receive_time' => time()]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}