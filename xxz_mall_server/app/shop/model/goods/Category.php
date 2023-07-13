<?php

namespace app\shop\model\goods;

use think\facade\Cache;
use app\common\model\goods\Category as CategoryModel;

/**
 * 商品分类模型
 */
class Category extends CategoryModel
{
    /**
     * 添加新记录
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        $params = $data['params'];
        $params = explode(",",$params);
        if ($params){
            $arr = [[]];
            foreach ($params as $v=> $item)
            {
                $arr[$v]['params_name'] = $item;
                $arr[$v]['params_value'] = '';
            }
            $data['params'] = json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
        $this->deleteCache();
        return $this->save($data);
    }

    /**
     * 编辑记录
     */
    public function edit($data)
    {
        $params = $data['params'];
        $params = explode(",",$params);
        if ($params){
            $arr = [[]];
            foreach ($params as $v=> $item)
            {
                $arr[$v]['params_name'] = $item;
                $arr[$v]['params_value'] = '';
            }
            $data['params'] = json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
        // 验证：一级分类如果存在子类，则不允许移动
        if ($data['parent_id'] > 0 && static::hasSubCategory($this['category_id'])) {
            $this->error = '该分类下存在子分类，不可以移动';
            return false;
        }
        $this->deleteCache();
        !array_key_exists('image_id', $data) && $data['image_id'] = 0;
        return $this->save($data) !== false;
    }

    /**
     * 删除商品分类
     */
    public function remove($categoryId)
    {
        // 判断是否存在商品
        if ($productCount = (new Goods)->getProductTotal(['category_id' => $categoryId])) {
            $this->error = '该分类下存在' . $productCount . '个商品，不允许删除';
            return false;
        }
        // 判断是否存在子分类
        if (static::hasSubCategory($categoryId)) {
            $this->error = '该分类下存在子分类，请先删除';
            return false;
        }
        $this->deleteCache();
        return $this->delete();
    }

    /**
     * 删除缓存
     */
    private function deleteCache()
    {
        return Cache::delete('category_' . static::$app_id);
    }

}
