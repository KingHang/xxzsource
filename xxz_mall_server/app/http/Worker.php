<?php
declare (strict_types = 1);

namespace app\http;

use think\facade\Db;
use think\worker\Server;
use Workerman\Lib\Timer;

// define('HEARTBEAT_TIME', 30);// 心跳间隔
class Worker extends Server
{
    protected $socket = 'websocket://0.0.0.0:2345';

    protected static $heartbeat_time = 50;

    public function onMessage($connection,$data)
    {
        #最后接收消息时间
        $connection->lastMessageTime = time();


        $msg_data = json_decode($data,true);
        if (!$msg_data){
            return;
        }

        #群聊
        if ($msg_data['type'] == 'text' && $msg_data['mode'] == 'group'){
            #实际项目通过群号查询群里有哪些用户
            $group_user = [10009,10010,10011,10012,10013,10014,10015,10016,10017];
            foreach ($group_user as $key => $val){
                if (isset($this->worker->uidConnections[$val])){
                    $conn = $this->worker->uidConnections[$val];
                    $conn->send($data);
                }
            }

        }

        // #向所有用户发送消息
        // foreach ($this->worker->connections as $key => $con){
        // 	$con->send($data);
        // }

        // $connection->send(json_encode($data));
        // $connection->send($data);
    }


}
