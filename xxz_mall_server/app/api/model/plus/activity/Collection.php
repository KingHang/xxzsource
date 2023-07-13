<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\Collection as CollectionModel;

/**
 * 活动报名提醒模型
 */
class Collection extends CollectionModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'app_id',
        'update_time',
        'create_time',
    ];

    /**
     * 关注列表
     * @param $user_id
     * @param $data
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLists($user_id,$data)
    {
        $result['home'] = empty($data['home']) ? 1 : $data['home']; // 页数
        $result['pageSize'] = empty($data['pageSize']) ? 10 : $data['pageSize']; // 数量

        $model = $this;
        $model = $model->where('user_id','=',$user_id);
        $count = $model->count();
        $page_count = ($count > 0) ? intval(ceil($count / $result['pageSize'])) : 1;

        $list = $model->with(['activity' => function($query) {
            $query->with('image');
            $query->field('id,name as title,image_id,signup_time_start,signup_time_end,province,city,area,address');
        },
        ])->page($result['home'], $result['pageSize'])->select();

        // 处理返回时间格式
        if (!empty($list)) {
            foreach($list as $key=>$item){
                if (!empty($item['activity'])) {
                    $list[$key]['activity']['format_time'] =  date('m/d H:i', strtotime($item['activity']['signup_time_start'])) . ' - ' . date('m/d H:i', strtotime($item['activity']['signup_time_end']));
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

    public function add($activity_id,$user_id)
    {
        // 验证是否已设置
        if ($this->where(array('user_id' => $user_id , 'activity_id' => $activity_id))->count() > 0) {
            $this->error = '您已经收藏过了';
            return false;
        }
        $this->startTrans();
        try {
            $data = array(
                'user_id' => $user_id,
                'activity_id' => $activity_id,
                'app_id' => self::$app_id
            );
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 取消收藏
     * @param $activity_id
     * @return bool
     * @throws \think\exception\PDOException
     */
    public function cancel($activity_id ,$user_id)
    {
        $this->startTrans();
        try {
            $this->where('activity_id', 'in', $activity_id)->where('user_id' , '=' , $user_id)
                ->delete();
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}