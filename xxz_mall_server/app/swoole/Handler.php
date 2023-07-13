<?php

namespace app\swoole;

use Swoole\Server;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server as WebsocketServer;
use think\Config;
use think\Request;
use think\swoole\contract\websocket\HandlerInterface;
use think\swoole\Table;
use think\Container;
use think\swoole\websocket\socketio\Packet;
//Handler全部返回false,将逻辑全部交给WebsocketEvent处理
class Handler implements HandlerInterface
{
    /** @var WebsocketServer */
    protected $server;

    /** @var Config */
    protected $config;

    protected $table = null;
    protected $u2fd = null;
    protected $fd2u = null;
    protected $s2fd = null;
    protected $fd2s = null;

    public function __construct(Server $server, Config $config, Container $container)
    {
        $this->server = $server;
        $this->config = $config;
        $this->table = $container->get(Table::class);
        $this->u2fd = $this->table->u2fd;
        $this->fd2u = $this->table->fd2u;
        $this->s2fd = $this->table->s2fd;
        $this->fd2s = $this->table->fd2s;
    }

    /**
     * "onOpen" listener.
     *
     * @param int     $fd
     * @param Request $request
     */
    public function onOpen($fd, Request $request)
    {
        $data = $request->param();
        dump($data);
        if($data['usertype'] == 'user'){
            $this->table->u2fd->set((string)$data['user_id'], ['fd' => $fd]);
            $this->table->fd2u->set((string)$fd, ['uid' => $data['user_id']]);

            $toFd = $this->table->s2fd->get((string)$data['to'], 'fd');
            if ($toFd === false) {//不在线
                // 发送给用户供应商状态
                $toUd = $this->table->u2fd->get((string)$data['user_id'], 'fd');
                $this->server->push($toUd, json_encode(['type'=>'off']));

            }
        }else if($data['usertype'] == 'supplier'){
            $this->table->s2fd->set((string)$data['user_id'], ['fd' => $fd]);
            $this->table->fd2s->set((string)$fd, ['sid' => $data['user_id']]);
        }
    }

    /**
     * "onMessage" listener.
     *  only triggered when event handler not found
     *
     * @param Frame $frame
     * @return bool
     */
    public function onMessage(Frame $frame)
    {
        dump('----------------------message------------------');
        dump($frame);
        $data = json_decode($frame->data, true);
        dump($data);
        $data['status'] = 1;
        $data['time'] = date('Y-m-d H:i:s');
        // 发送给供应商
        if($data['usertype'] == 'ping'){
            $this->server->push($frame->fd, 'pong');
            return true;
        }else if($data['usertype'] == 'user'){
            foreach ($this->table->s2fd as $row) {
                dump($row['fd']);
            }
            $toFd = $this->table->s2fd->get((string)$data['to'], 'fd');
            dump('--------------sendFd='. $frame->fd);
            dump('--------------toFd='. $toFd);
            if ($toFd === false) {
                // 离线消息处理
                $data['status'] = 0;
            }
           
            $this->server->push($toFd, json_encode($data));
        } else if ($data['usertype'] == 'supplier'){
            // 发送给用户
            $toFd = $this->table->u2fd->get((string)$data['to'], 'fd');
            if ($toFd === false) {
                // 离线消息处理
                $data['status'] = 0;
            }
            $this->server->push($toFd, json_encode($data));
        }
        // 异步操作
        $this->server->task($data);

        return true;
    }

    /**
     * "onClose" listener.
     *
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($fd, $reactorId)
    {
        $uid = $this->table->fd2u->get((string)$fd, 'uid');
        if($uid){
            $this->table->u2fd->del((string)$uid);
            $this->table->fd2u->del((string)$fd);
        }
        $sid = $this->table->fd2s->get((string)$fd, 'sid');
        if($sid){
            $this->table->s2fd->del((string)$sid);
            $this->table->fd2s->del((string)$fd);
        }
    }

    protected function checkHeartbeat($fd, $packet)
    {
        $packetLength = strlen($packet);
        $payload      = '';

        if ($isPing = Packet::isSocketType($packet, 'ping')) {
            $payload .= Packet::PONG;
        }

        if ($isPing && $packetLength > 1) {
            $payload .= substr($packet, 1, $packetLength - 1);
        }

        if ($isPing) {
            $this->server->push($fd, $payload);
        }
    }
}
