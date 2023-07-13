<?php

namespace app\job\event;

use think\facade\Cache;
use app\job\model\plus\agent\Points as AgentPointsModel;
/**
 * 年结积分
 */
class AgentOrderPoints
{
    // 模型
    private $model;
    // 应用id
    private $appId;
    /**
     * 执行函数
     */
    public function handle($app_id)
    {
        try {
            $this->appId = $app_id;
            $this->model = new AgentPointsModel();
            $cacheKey = "task_space_AgentOrderPoints";
            if (!Cache::has($cacheKey)) {
                $this->model->startTrans();
                try {
                    // 发放分销订单佣金
                    $this->grantMoney();
                    $this->model->commit();
                } catch (\Exception $e) {
                    $this->model->rollback();
                }
                Cache::set($cacheKey, time(), 600);
            }
        } catch (\Throwable $e) {
            echo 'ERROR AgentOrderPoints: ' . $e->getMessage() . PHP_EOL;
            log_write('AgentOrderPoints TASK : ' . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 发放分销月结佣金
     */
    private function grantMoney()
    {
        // 获取100个未结算的用户
        $list = $this->model->getNoSettled($this->appId);
        if (!$list || count($list) == 0) return false;
        $this->model->settledMoney($list);
        // 记录日志
        $this->dologs('settledPoints', ['count' => count($list)]);
        return true;
    }

    /**
     * 记录日志
     */
    private function dologs($method, $params = [])
    {
        $value = 'behavior AgentOrderPoints --' . $method;
        foreach ($params as $key => $val) {
            $value .= ' --' . $key . ' ' . (is_array($val) ? json_encode($val) : $val);
        }
        return log_write($value, 'task');
    }

}