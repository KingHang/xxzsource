<?php

namespace app\shop\model\page;
use app\common\model\home\CenterMenu as CenterMenuModel;
use think\facade\Cache;

/**
 * 模型
 */
class CenterMenu extends CenterMenuModel
{

    /**
     * 获取列表
     */
    public function getList($params)
    {
        $list = $this->order(['sort' => 'asc'])
            ->paginate($params);
        foreach ($list as $menus){
            if(strpos($menus['image_url'], 'http') !== 0){
                $menus['image_url'] = self::$base_url . $menus['image_url'];
            }
        }
        return $list;
    }
    /**
     * 添加新记录
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $this->deleteCache();
        return $this->save($data);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
//        if(isset($data['image_url']) && strpos($data['image_url'], 'image/menu') !== 0){
//            unset($data['image_url']);
//        }
        $this->deleteCache();
        return $this->save($data);
    }

    /**
     * 删除记录
     */
    public function remove()
    {
        $this->deleteCache();
        return $this->delete();
    }

    /**
     * 删除缓存
     */
    private function deleteCache()
    {
        return Cache::delete('center_menu_' . self::$app_id);
    }
}