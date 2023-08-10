<?php

namespace app\api\model\goods;

use app\common\model\goods\CommentImage as CommentImageModel;

/**
 * 商品图片模型
 */
class CommentImage extends CommentImageModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'create_time',
    ];

}
