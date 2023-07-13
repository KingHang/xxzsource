<?php
declare (strict_types=1);

namespace app\swoole\listener;

use Swoole\Server;
use think\Container;
use think\swoole\Table;
use think\swoole\Websocket;

class Message
{
    public $websocket = null;
    public $table = null;

    public function __construct(Container $container, Server $server)
    {
        $this->websocket = $container->make(Websocket::class);
        $this->server = $container->make(Server::class);
        $this->table = $container->get(Table::class);
    }

    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
        dump('$event');
        //$event 为从客户端接收的数据
        $this->websocket->emit("testcallback", ['aaaaa' => 1, 'getdata' => 'hhhhhhhhhhhhhhhhhhhhhhh']);
        $this->server->task(['test'=>'test value']);
    }
}
