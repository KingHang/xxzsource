<?php

namespace app\mall\model\setting;

use app\common\model\setting\Message as MessageModel;
use app\common\model\setting\MessageSettings as MessageSettingsModel;

/**
 * 退货地址模型
 */
class Message extends MessageModel
{
    /**
     * 获取全部收货地址
     */
    public function getAll($message_to)
    {
        //子查询先过滤条件
        $settings_model = new MessageSettingsModel;
        $subsql = $settings_model->where('app_id', '=', self::$app_id)->buildSql();

        return $this->withoutGlobalScope()->alias('message')->field(['message.*','setting.message_settings_id','setting.sms_status','setting.sms_template'
            ,'setting.mp_status','setting.mp_template','setting.wx_status','setting.wx_template'])
            ->where('message.message_to', '=', $message_to)
            ->where('message.is_delete', '=', 0)
            ->join([$subsql=> 'setting'], 'setting.message_id = message.message_id', 'left')
            ->order(['message.sort' => 'asc'])
            ->select();
    }
}