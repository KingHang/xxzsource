<?php

namespace app\common\model\goods;

use app\common\model\BaseModel;
/**
 * 商品图片模型
 */
class GoodsImage extends BaseModel
{
    protected $name = 'goods_image';
    protected $updateTime = false;

    /**
     * 关联文件库
     */
    public function file()
    {
        return $this->belongsTo('app\\common\\model\\file\\UploadFile', 'image_id', 'file_id')
            ->bind(['file_path', 'file_name', 'file_url']);
    }

}
