<?php

namespace app\mall\model\mall;

use app\common\model\mall\LoginLog as LoginLogModel;
/**
 * 后台管理员登录日志模型
 */
class LoginLog extends LoginLogModel
{
    /**
     * 获取列表数据
     */
    public function getList($params)
    {
        $model = $this;
        // 查询条件：订单号
        if (isset($params['username']) && !empty($params['username'])) {
            $model = $model->where('username', 'like', "%{$params['username']}%");
        }
        // 查询列表数据
        return $model->order(['create_time' => 'desc'])
            ->paginate($params);
    }
}