<?php

namespace app\mall\model\mall;

use app\common\model\mall\OptLog as OptLogModel;
/**
 * 后台管理员登录日志模型
 */
class OptLog extends OptLogModel
{
    /**
     * 获取列表数据
     */
    public function getList($params)
    {
        $model = $this;
        // 查询条件：订单号
        if (isset($params['username']) && !empty($params['username'])) {
            $model = $model->where('user.user_name|user.real_name', 'like', "%{$params['username']}%");
        }
        // 查询列表数据
        return $model->alias('log')->field(['log.*','user.user_name','user.real_name'])
            ->join('mall_user user', 'user.mall_user_id = log.mall_user_id','left')
            ->order(['log.create_time' => 'desc'])
            ->paginate($params);
    }
}