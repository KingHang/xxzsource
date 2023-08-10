<?php

namespace app\api\model\setting;

use app\common\model\setting\Message as MessageModel;
use app\common\model\setting\MessageSettings as MessageSettingsModel;

/**
 * 消息模型
 */
class Message extends MessageModel
{
    /**
     * 获取消息
     */
    public static function getMessageByNameArr($platform = '', $message_ename_arr)
    {
        $template_arr = [];
        //只适用于微信
        if($platform != 'wx'){
            return $template_arr;
        }
        $model = new self();
        //子查询先过滤条件
        $settings_model = new MessageSettingsModel;
        $subsql = $settings_model->where('wx_status', '=', 1)
            ->where('app_id', '=', self::$app_id)
            ->buildSql();

        $template_list = $model->withoutGlobalScope()->alias('message')->field(['message.*','setting.wx_status','setting.wx_template'])
            ->where('message.message_ename', 'in', $message_ename_arr)
            ->where('message.is_delete', '=', 0)
            ->join([$subsql=> 'setting'], 'setting.message_id = message.message_id', 'left')
            ->order(['message.sort' => 'asc'])
            ->select()->toArray();

        foreach($template_list as $template){
            if($template['wx_template']){
                $json = json_decode($template['wx_template'], true);
                array_push($template_arr, $json['template_id']);
            }
        }
        return $template_arr;
    }
}