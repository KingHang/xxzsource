<?php

namespace app\api\model\plus\activity;

use app\common\model\plugin\activity\History as HistoryModel;

/**
 * 活动幻灯片
 */
class History extends HistoryModel
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
     * 获取检索记录签20条
     * @param $user_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList($user_id)
    {
        return $this->where(['is_delete' => 0 , 'app_id' => self::$app_id , 'user_id' => $user_id])
            ->order(['update_time' => 'desc'])
            ->limit(20)
            ->select();
    }

    /**
     * 清空
     * @param $user_id
     * @return History
     */
    public function remove($user_id)
    {
        return $this->where(['user_id'=>$user_id])->update(['is_delete'=>1]);
    }

    public function add($user_id,$keyword)
    {
        $info = $this->where(array('user_id' => $user_id , 'keyword' => $keyword))->find();
        $data = array(
            'user_id' => $user_id,
            'is_delete' => 0,
            'keyword' => $keyword,
            'num' => 1,
            'app_id' => self::$app_id
        );
        if ($info) {
            $data['num'] = $info['num'] + 1;
            $info->save($data);
        } else {
            $this->save($data);
        }
    }
}