<?php

namespace app\job\event;

use think\facade\Cache;
use app\job\model\plus\agent\Month as AgentMonthModel;
use app\api\model\plus\agent\Order as AgentOrderModel;
/**
 * 分佣订单月结
 *
 */
class AgentOrderMonth
{
    // 模型
    private $model;

    private $AgentModel;

    // 应用id
    private $appId;
    /**
     * 执行函数
     */
    public function handle($app_id)
    {
        try {
            $this->appId = $app_id;
            $this->model = new AgentMonthModel();
            $this->AgentModel = new AgentOrderModel();
//            $cacheKey = "task_space_AgentOrderMonth";
//            if (!Cache::has($cacheKey)) {
                $this->model->startTrans();
                try {
                    // 重新计算分销奖项
                    $this->AgentModel->setMonthlyAward($this->appId);
                    // 统计创业分红入围人数和参与分红数量
                    (new AgentMonthModel())->setBonusMonthly($this->appId);

                    $this->bounsMoney();
                    // 发放分销订单佣金
                    $this->grantMoney();
                    // 发放创业拓展奖
                    // 月结
                    $this->grantSalemoneyByMonth($this->appId);
                    // 周结
                    $this->model->grantBonusmoney($this->appId);
                    $this->model->commit();
                } catch (\Exception $e) {
                    $this->model->rollback();
                }
                //Cache::set($cacheKey, time(), 60);
            //}
        } catch (\Throwable $e) {
            echo 'ERROR AgentOrderMonth: ' . $e->getMessage() . PHP_EOL;
            log_write('AgentOrderMonth TASK : ' . '__ ' . $e->getMessage(), 'task');
        }
        return true;
    }

    /**
     * 发放月结创业拓展奖
     * @return bool
     */
    private function grantSalemoneyByMonth()
    {
        // 获取一个未结算的用户
        $detail = $this->model->getSaleNoSettled($this->appId);
        if (!$detail) return false;
        $detail->settledSaleMoney($this->appId);
        // 记录日志
        $this->dologs('grantSalemoneyByMonth', ['user_id' => $detail['user_id']]);
        return true;
    }
    /**
     * 发放分销月结佣金
     */
    private function grantMoney()
    {
        // 获取一个未结算的用户
        $detail = $this->model->getNoSettled($this->appId);
        if (!$detail) return false;
        $detail->settledMoney($this->appId);
        // 记录日志
        $this->dologs('settledMonth', ['user_id' => $detail['user_id']]);
        return true;
    }
    /**
     * 发放创业分红
     * type 创业分红
     */
    private function bounsMoney()
    {
        // 获取一个未结算的用户
        $detail = $this->model->getNoBounsSettled($this->appId);
        if (!$detail) return false;
        $detail->settledMoney($this->appId,$type=1);
        // 记录日志
        $this->dologs('settledMonth', ['user_id' => $detail['user_id']]);
        return true;
    }

    /**
     * 记录日志
     */
    public function dologs($method, $params = [])
    {
        $value = 'behavior AgentOrderMonth --' . $method;
        foreach ($params as $key => $val) {
            $value .= ' --' . $key . ' ' . (is_array($val) ? json_encode($val) : $val);
        }
        return log_write($value, 'task');
    }

}