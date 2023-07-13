<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */

//declare(ticks=1);
namespace app\gateway;

use \GatewayWorker\Lib\Gateway;
use app\api\model\plus\chat\Chat as ChatModel;
use Workerman\Lib\Timer;
use Workerman\Worker;
use think\worker\Application;
use think\facade\Cache;
use app\common\service\message\MessageService;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * onWorkerStart 事件回调
     * 当businessWorker进程启动时触发。每个进程生命周期内都只会触发一次
     *
     * @access public
     * @param \Workerman\Worker $businessWorker
     * @return void
     */
    public static function onWorkerStart(Worker $businessWorker)
    {
        $app = new Application;
        $app->initialize();
        // 5秒执行一次定时任务
        Timer::add(5, function () use (&$task) {
            try {
                event('JobScheduler');
            } catch (\Throwable $e) {
                echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
            }
        });
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        // 向当前client_id发送数据
        $data['client_id'] = $client_id;
        $data['type'] = 'init';
        Gateway::sendToClient($client_id, json_encode($data));
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message)
    {
        $data = json_decode($message, 1);
        $data['status'] = 0;
        $to = 0;
        $from_id = 0;
        if (isset($data['msg_type']) && $data['msg_type'] == 2) {
            $to = 'supplier_' . $data['supplier_user_id'];
            $from_id = $data['user_id'];
        } else {
            $to = $data['user_id'];
            $from_id = 'supplier_' . $data['supplier_user_id'];
        }
        if ($data['type'] !== 'ping' && $data['type'] !== 'close') {//正常发送消息
            if (Gateway::isUidOnline($to)) {
                $data['status'] = 1;
                $data['time'] = date('Y-m-d H:i:s');
                Gateway::sendToUid($to, json_encode($data));
            }
            $Chat = new ChatModel;
            $Chat->add($data);
            self::sendMessage($data);
        } else if ($data['type'] == 'ping') {
            //心跳
            $data['Online'] = $to && Gateway::isUidOnline($to) ? 'on' : 'off';
            Gateway::sendToUid($from_id, json_encode($data));
        } else if ($data['type'] == 'close') {
            //断开链接
            Gateway::unbindUid($client_id, $from_id);
        }
    }

    private static function sendMessage($data)
    {
        //给供应商发送未读消息
        if (isset($data['shop_supplier_id']) && $data['shop_supplier_id']) {
            //供应商缓存状态
            $status = Cache::get('message_' . $data['shop_supplier_id']);
            if (!$status) {
                //未读消息
                $count = (new ChatModel())->where('shop_supplier_id', '=', $data['shop_supplier_id'])
                    ->where('status', '=', 0)
                    ->where('msg_type', '=', 2)
                    ->count();
                if ($count > 0) {
                    Cache::set('message_' . $data['shop_supplier_id'], 1, 7200);
                    // 发送模板消息
                    $send['create_time'] = time();
                    $send['send_user'] = $data['from_id'];
                    $send['message'] = $data['content'] . ",您还有{$count}条消息未读";
                    $send['user_id'] = $data['to'];
                    (new MessageService)->supplierMsg($send);
                }
            }
        }
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        // 向所有人发送
        //GateWay::sendToAll("$client_id logout\r\n");
    }
}
