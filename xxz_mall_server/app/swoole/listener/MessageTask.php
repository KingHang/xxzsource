<?php
declare (strict_types=1);

namespace app\swoole\listener;

use Swoole\Server\Task;
use think\facade\Cache;
use app\api\model\plus\chat\Chat as ChatModel;
use app\common\service\message\MessageService;

class MessageTask
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle(Task $task)
    {
        var_dump('on task');
        var_dump($task->data);//task的data数据即server->task()传入的数据
        $Chat = new ChatModel;
        $Chat->add($task->data);
        $this->sendMessage($task->data);
        $task->finish($task->data);//这里必须手动执行finish,否则不会触发onFinish监听事件
        return;
    }

    public function sendMessage($data)
    {
        //给供应商发送未读消息
        if ($data['shop_supplier_id']) {
            //供应商缓存状态
            $status = Cache::get('message_' . $data['shop_supplier_id']);
            if (!$status) {
                //未读消息
                $count = ChatModel::where('shop_supplier_id', '=', $data['shop_supplier_id'])->where('status', '=', 0)->count();
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
}
