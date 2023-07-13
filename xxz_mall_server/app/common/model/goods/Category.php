<?php

namespace app\common\model\goods;

use think\Collection;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;
use app\common\model\BaseModel;

/**
 * 产品分类模型
 */
class Category extends BaseModel
{
    protected $pk = 'category_id';
    protected $name = 'category';

    /**
     * 分类图片
     */
    public function images()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'image_id');
    }

    /**
     * 充值套餐详情
     */
    public static function detail($category_id)
    {
        return (new static())->find($category_id);
    }


    public function detailWithImage($where)
    {
        return $this->with(['image'])->where($where)->find();
    }

    /**
     * 所有分类
     */
    public static function getALL()
    {
        $model = new static;
        if (!Cache::get('category_' . $model::$app_id)) {
            $data = $model->with(['images'])->order(['sort' => 'asc', 'create_time' => 'asc'])->select();
            $all = !empty($data) ? $data->toArray() : [];
            $tree = [];
            $showTree = [];
            foreach ($all as $first) {
                if ($first['parent_id'] != 0) continue;
                $firstTemp = $first;
                $twoTree = [];//所有
                $twoShowTree = [];//显示的
                foreach ($all as $two) {
                    if ($two['parent_id'] != $first['category_id']) continue;
                    $twoTemp = $two;
                    $threeTree = [];//所有
                    $threeShowTree = [];//显示的
                    foreach ($all as $three) {
                        if ($three['parent_id'] == $two['category_id']) {
                            // 所有的
                            $threeTree[$three['category_id']] = $three;
                            // 显示的
                            if ($three['disabled'] == 0) {
                                $threeShowTree[$three['category_id']] = $three;
                            }
                        }
                    }
                    // 所有的
                    if (!empty($threeTree)) {
                        $two['child'] = $threeTree;
                    }
                    array_push($twoTree, $two);
                    // 显示的
                    if (!empty($threeShowTree)) {
                        $twoTemp['child'] = $threeShowTree;
                    }
                    if ($twoTemp['disabled'] == 0) {
                        array_push($twoShowTree, $twoTemp);
                    }
                }
                // 所有的
                if (!empty($twoTree)) {
                    $temp_two_tree = array_column($twoTree, 'sort');
                    array_multisort($temp_two_tree, SORT_ASC, $twoTree);
                    $first['child'] = $twoTree;
                }
                array_push($tree, $first);
                // 显示的
                if (!empty($twoShowTree)) {
                    $temp_two_show_tree = array_column($twoShowTree, 'sort');
                    array_multisort($temp_two_show_tree, SORT_ASC, $twoShowTree);
                    $firstTemp['child'] = $twoShowTree;
                }
                if ($firstTemp['disabled'] == 0) {
                    array_push($showTree, $firstTemp);
                }
            }
            Cache::tag('cache')->set('category_' . $model::$app_id, compact('all', 'tree', 'showTree'));
        }
        return Cache::get('category_' . $model::$app_id);
    }

    /**
     * 获取所有分类
     */
    public static function getCacheAll()
    {
        return self::getALL()['all'];
    }

    /**
     * 获取显示的所有分类(树状结构)
     */
    public static function getCacheTree()
    {
        return self::getALL()['showTree'];
    }

    /**
     * 获取所有分类(树状结构)
     */
    public static function getCacheAllTree()
    {
        return self::getALL()['tree'];
    }

    /**
     * 获取所有分类(树状结构)
     * @return string
     */
    public static function getCacheTreeJson()
    {
        return json_encode(static::getCacheTree());
    }

    /**
     * 获取指定分类下的所有子分类id
     */
    public static function getSubCategoryId($parent_id, $all = [])
    {
        $arrIds = [$parent_id];
        empty($all) && $all = self::getCacheAll();
        foreach ($all as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                unset($all[$key]);
                $subIds = self::getSubCategoryId($item['category_id'], $all);
                !empty($subIds) && $arrIds = array_merge($arrIds, $subIds);
            }
        }
        return $arrIds;
    }

    /**
     * 指定的分类下是否存在子分类
     */
    protected static function hasSubCategory($parentId)
    {
        $all = self::getCacheAll();
        foreach ($all as $item) {
            if ($item['parent_id'] == $parentId) {
                return true;
            }
        }
        return false;
    }

    /**
     * 关联图片
     */
    public function image()
    {
        return $this->belongsTo('app\common\model\file\UploadFile', 'image_id', 'file_id');
    }

    /**
     * 获取所有一级分类
     */
    public static function getFirstCategory()
    {
        return (new static())->where('disabled', '=', 0)
            ->where('parent_id', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }

    /**
     * 根据分类id获取一级分类
     * @param $categoryIds
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getFirstCategoryByIds($categoryIds)
    {
        $model = $this;

        if (!empty($categoryIds)) {
            $model = $model->orderRaw('field(category_id, ' . implode(',', $categoryIds) . ')');
        }

        return $model->where('disabled', '=', 0)
            ->where('parent_id', '=', 0)
            ->where('category_id', 'in', $categoryIds)
            ->select();
    }

    /**
     * 获取所有二级分类
     * @param $parentId
     * @return Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function getSecondCategory($parentId)
    {
        return (new static())->where('disabled', '=', 0)
            ->where('parent_id', '=', $parentId)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }
}
