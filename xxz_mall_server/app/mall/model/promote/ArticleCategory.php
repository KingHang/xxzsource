<?php
declare (strict_types = 1);

namespace app\mall\model\promote;

use think\Model;
use app\common\model\promote\ArticleCategory as ArticleCategoryModel;
use think\facade\Request;
use think\Response;

/**
 * @mixin \think\Model
 */
class ArticleCategory extends ArticleCategoryModel
{
    public static function typeList()
    {
        $params = Request::post();
        $page = $params['home'] ?? '';//页码
        $pageSize = $params['pageSize'] ?? 10;//页码
        $where = [];
        $where[] = ["is_delete", '=', "0"];
        $data = self::where($where)->order('sort', 'desc')->paginate(['list_rows' => $pageSize, 'home' => $page]);
        return $data;
    }

    public static function addType($params)
    {
        $res = self::create($params);
        return $res;
    }

    public static function typeDesc()
    {
        $params = Request::get('category_id');
        return self::where('category_id', $params)->find();
    }

    public static function typeUpd($params)
    {
        $category_id = $params['category_id'] ?? '';
        $res = self::where('category_id', $category_id)->find();
        if ($res) {
            return $res->save($params);
        } else {
            return false;
        }

    }

    public static function typeDelete($type_id, $is_delete)
    {
        $type_id = explode(',', $type_id);
        foreach ($type_id as $key => $v) {
            $res = self::where('category_id', $v)->find();
            if ($res) {
                $res->is_delete = $is_delete;
                $result = $res->save();
            }
        }
        return $result;
    }


    public static function appletTypeList()
    {
        $params = Request::post();
        $status = $params['status'] ?? '';
        $where = [];
        $data = self::where($where)->order('sort', 'desc')->select();
        return $data;
    }
}
