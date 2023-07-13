<?php

namespace app\api\model\plus\website\matter;

use app\common\model\plugin\website\matter\Matter as MatterModel;

/**
 * 文章模型
 */
class Matter extends MatterModel
{
    /**
     * 获取文章列表
     */
 
   public function matterlist()
   {
       
        $list = $this->where('is_delete', '=', 0)
         ->where('matter_status', '=', 1)->field('matter_id,matter_title,matter_content')
         ->order(['matter_id' => 'desc'])->select();
         return $list;
   }

    /**
     * 新增记录
     */
    public function add($data)
    {
        if (empty($data['matter_title'])) {
            $this->error = '请输入标题';
            return false;
        }

        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 更新记录
     */
    public function edit($data)
    {
        if (empty($data['matter_title'])) {
            $this->error = '请输入标题';
            return false;
        }
        return $this->save($data);
    }

    /**
     * 软删除
     */
    public function setDelete()
    {
        return $this->save(['is_delete' => 1]);
    }

 
}