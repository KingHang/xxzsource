<?php

namespace app\api\model\plus\chat;
use app\common\model\plugin\chat\ChatRelation as ChatRelationModel;


/**
 * 客服消息关系模型类
 */
class ChatRelation extends ChatRelationModel
{

    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time'
    ];

    
}
