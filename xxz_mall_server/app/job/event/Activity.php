<?php

namespace app\job\event;

use app\job\service\ActivityOrderService;
use think\facade\Cache;
/**
 * 活动事件
 */
class Activity
{
    // 模型
    private $model;

    private $service;
    // 应用id
    private $appId;
    /**
     * 执行函数
     */
    public function handle($app_id)
    {
        $this->appId = $app_id;
        $this->service = new ActivityOrderService();
        try {
            // 订单行为管理
            $this->master();
            // 未支付订单自动关闭
            $this->close();
            // 报名通知
            $this->service->sendActivityMsg($this->appId);
        } catch (\Throwable $e) {
            echo 'ERROR ORDER: ' . $e->getMessage() . PHP_EOL;
            log_write('ORDER TASK : ' . $app_id . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }
    /**
     * 普通订单行为管理
     * 1小时执行一次
     */
    private function master()
    {
        $key = "task_space__order__{$this->appId}";
        if (Cache::has($key)) return true;
        // 活动结束后自动完成订单和结算
        $orderIds = $this->service->settled($this->appId);
        // 记录日志
        $this->dologs('settled', [
            'orderIds' => json_encode($orderIds),
        ]);
        Cache::set($key, time(), 60);
        return true;
    }
    /**
     * 未支付订单自动关闭
     */
    private function close()
    {
        // 执行自动关闭
        $this->service ->close($this->appId);
        // 记录日志
        $this->dologs('close', [
            'orderIds' => json_encode($this->service ->getCloseOrderIds()),
        ]);
        return true;
    }
    /**
     * 记录日志
     */
    private function dologs($method, $params = [])
    {
        $value = 'ActivityOrderClose --' . $method;
        foreach ($params as $key => $val)
            $value .= ' --' . $key . ' ' . $val;
        return log_write($value, 'task');
    }

}
