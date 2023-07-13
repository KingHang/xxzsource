<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
/**
 * 商品订单模型
 */
class VerifyServerLog  extends BaseModel
{
    protected $name = 'face_verify_server_log';
    protected $pk = 'id';

    public function verifyServerOrder()
    {
        return $this->hasOne(VerifyServerOrder::class,'id','verify_id');
    }
    /**
     * 关联门店表
     */
    public function store()
    {
        return $this->hasOne('app\\common\\model\\store\\Store', 'store_id', 'store_id')->bind(['store_name']);
    }
    /**
     * 获取服务通道消单记录
     * @param $data
     * @param $equipment
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getStatisticsTypedata($data,$equipment){
        //获取设备对应的通道列表
        $result['home'] = empty($data['home']) ? 1 : $data['home']; // 页数
        $result['pageSize'] = empty($data['pageSize']) ? 10 : $data['pageSize']; // 数量

        // 处理查询时间
        $week_start = strtotime('-7 day'); // 获取七天前时间
        $day_start = strtotime(date("Y-m-d",time())); // 获取当天起始时间
        $model = $this;
        // 处理查询条件
        $where = array();
        if ($data['statistics_type'] === 'store') {
            // 门店统计
            $model = $model->where('f.store_id', '=',   $equipment['store_id']);
        } elseif ($data['statistics_type'] === 'equipment'){
            // 设备统计
            $model = $model->where('f.equipment_id', '=',   $equipment['equipment_id']);
        } else {
            $this->setError('无效统计类型');
            return false;
        }
        //关键字筛选
        if (!empty($data['keyword'])) {
            $model = $model->where('og.product_name', 'like',   "%" . strtolower($data['keyword']) . "%");
        }
        // 分类筛选
        if (!empty($data['category_id'])) {
            $model = $model->where('category_id', '=',   $data['category_id']);
        }
        $field = ('og.product_name as server_name,og.product_id as server_id,SUM(CASE WHEN f.create_time > ' . $week_start . ' THEN 1 ELSE 0 END) as week_count,SUM(CASE WHEN f.create_time > ' . $day_start . ' THEN 1 ELSE 0 END) as day_count,COUNT(f.id) AS count');
        $sql = $model->alias('f')
            ->join('face_verify_server_order fv','fv.id=f.verify_id')
            ->join('order_product og','og.order_id=fv.exchange_order_id AND og.product_id = fv.server_id AND og.product_sku_id = fv.server_sku_id')
            ->group('og.product_id');

        $count = $sql->count();
        $list = $sql->field($field)->page( $result['home'], $result['pageSize'])->select();

        $page_count = ($count > 0) ? intval(ceil($count / $result['pageSize'])) : 1;

        $result['count'] = $count;
        $result['page_count'] = $page_count;
        $result['list'] = $list;
        $result['more']   = $result['home'] < $page_count ? 1 : 0;
        return $result;
    }

    // 获取核销记录
    public function getLogList($order_id , $verify_code)
    {
        return $this->alias('log')
            ->withJoin('verifyServerOrder' , 'left')
            ->with(['store'])
            ->where(['verifyServerOrder.exchange_order_id' => $order_id , 'verify_code' => $verify_code])
//            ->fetchSql()
            ->select();

    }
}