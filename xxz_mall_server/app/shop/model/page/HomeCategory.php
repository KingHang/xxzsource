<?php

namespace app\shop\model\page;

use app\common\model\home\HomeCategory as PageCategoryModel;

/**
 * app分类页模板模型
 */
class HomeCategory extends PageCategoryModel
{
    /**
     * 编辑记录
     */
    public function edit($data)
    {
        return $this->save($data);
    }

}
