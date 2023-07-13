<?php

namespace app\api\model\page;

use app\common\model\home\Ad as AdModel;

/**
 * 广告模型
 */
class Ad extends AdModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'create_time',
        'update_time',
        'name',
        'sort',
        'image_id',
        'category_id'
    ];
    public function getIndex($where,$limit){
        return parent::getLists($where,$limit);
    }
  


    
}