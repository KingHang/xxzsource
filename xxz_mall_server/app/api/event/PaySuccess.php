<?php


namespace app\api\event;

use app\common\enum\order\OrderTypeEnum;
use app\common\service\message\MessageService;
use app\common\service\order\OrderPrinterService;
use app\api\model\order\Order;
use app\api\service\order\paysuccess\source\PaySourceSuccessFactory;
use app\common\model\user\User as UserModel;

class PaySuccess
{
    public $order;
    public $appId;
    public $orderType;


    public function handle(Order $order)
    {
        $this->order = $order;
        $this->appId = $order['app_id'];
        $this->orderType = OrderTypeEnum::MASTER;
        // 订单公共业务
        $this->onCommonEvent();
        // 订单来源回调业务
        $this->onSourceCallback();
        return true;
    }

    /**
     * 订单公共业务
     */
    private function onCommonEvent()
    {
        // 发送消息通知
        (new MessageService)->payment($this->order, $this->orderType);
        // 小票打印
        (new OrderPrinterService)->printTicket($this->order);
        // 累计用户消费金额
        (new UserModel())->incExpendMoney($this->order);
    }

    /**
     * 订单来源回调业务
     */
    private function onSourceCallback()
    {
        $model = PaySourceSuccessFactory::getFactory($this->order['order_source']);
        return $model->onPaySuccess($this->order);
    }
}