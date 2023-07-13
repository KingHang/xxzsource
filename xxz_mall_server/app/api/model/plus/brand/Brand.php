<?php
declare (strict_types = 1);

namespace app\api\model\plus\brand;

use app\api\model\product\Category;
use app\common\model\file\UploadFile;
use app\common\model\plugin\brand\SignLog;
use think\Model;
use app\common\model\plugin\brand\Brand as BrandModel;

/**
 * @mixin \think\Model
 */
class Brand extends BrandModel
{

    public function brandCategory($params=[])
    {
        $page = $params['home'] ?? '';//页码
        $pageSize = $params['pageSize'] ?? 10;//页码
        $category_id = $params['category_id'] ?? '';//页码
        $search = $params['search'] ?? '';//页码
        $where = [];
        $where[] = ["brand_status", '=', 1];
        $where[] = ["brand.is_delete", '=', 0];
        $where[] = ["brand_verify", '=', 1];
        $where[] = ["branddaysign.sign_status", '=', 1];
        $where[] = ["branddaysign.is_delete", '=', 0];
        if (!empty($category_id)){
            $categoryIds = \app\common\model\goods\Category::getSubCategoryId($category_id);
            $where[] = ["brand.category_id", 'in', $categoryIds];
        }
        if (!empty($params['search'])){
            $where[] = ["brand_name | selling_points", 'like', "%$search%"];
        }

        $data = self::order('brand_id', 'desc')
            ->with(['image','supplier.supplierApply.servers'])
            ->withJoin(['branddaysign'],'right')
            ->where($where)
//            ->fetchSql(true)
//            ->select();
//        var_dump($data);die;
            ->paginate(['list_rows' => $pageSize, 'home' => $page])
            ->toArray();
        foreach ($data['data'] as $k =>$v)
        {
            $data['data'][$k]['product_down'] = SignLog::where('sign_id',$v['branddaysign']['sign_id'])->count();
        }
        return $data;
    }
}
