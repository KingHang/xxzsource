<?php
declare (strict_types = 1);

namespace app\api\model\plus\brand;

use app\common\model\file\UploadFile;
use app\common\model\plugin\brand\SignLog;
use think\Model;
use app\common\model\plugin\brand\BranddaySign as BranddaySignModel;

/**
 * @mixin \think\Model
 */
class BrandDaySign extends BranddaySignModel
{
    public function brandList($params=[],$type=0)
    {
        $page = $params['home'] ?? '';//页码
        $pageSize = $params['pageSize'] ?? 10;//页码
        $brand_day_id = $params['brand_day_id'] ?? '';//页码
        $search = $params['search'] ?? '';//页码
        $where = [];
        $where[] = ["sign_status", '=', 1];
        $where[] = ["brand_day_sign.is_delete", '=', 0];
        $where[] = ["activity_start_at", '<', time()];
        $where[] = ["activity_end_at", '>', time()];
        $where[] = ["status", '=', 1];
        $where[] = ["brand.brand_verify", '=', 1];
        $where[] = ["brand.brand_status", '=', 1];
        $where[] = ["brand.is_delete", '=', 0];
        $where[] = ["brandday.is_delete", '=', 0];
        $where[] = ["brandday.status", '=', 1];

        if (!empty($brand_day_id)){
            $where[] = ["brandday.brand_day_id", '=', $brand_day_id];
        }

        if (!empty($params['search'])){
            $where[] = ["brand.brand_name", 'like', "%$search%"];
        }

        $data = self::order('sign_id', 'desc')
            ->with(['brand','signLog'=>['product.image.file','brandSku.productSku'],'supplier.supplierApply.servers'])
            ->withJoin(['brandday','brand'],'left')
            ->where($where);

        if ($pageSize == '全部') {
            $list['data'] = $data->select()->toArray();
        } else {
            $list = $data->paginate(['list_rows' => $pageSize, 'home' => $page])->toArray();
        }

        foreach ($list['data'] as $K => $v) {
            $list['data'][$K]['countdown'] = $v['brandday']['activity_end_at']-time();
            $list['data'][$K]['product_down'] = SignLog::where('sign_id',$v['sign_id'])->count();
            $list['data'][$K]['brand']['image'] = (new UploadFile())->where('file_id',$v['brand']['brand_logo'])->find();
        }

        return $list;
    }
}
