<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
use app\mall\service\order\ExportService;

/**
 * 商品订单模型
 */
class VerifyGoodsLog  extends BaseModel
{
    protected $name = 'verify_goods_log';
    protected $pk = 'id';
    protected $append = [
        'verify_time'
    ];
    public function getVerifyTimeAttr($value, $data)
    {
        if (isset($data['verify_date'])){
            return date('Y-m-d H:i:s',$data['verify_date']);
        }
        return '';

    }
    /**
     * 关联门店表
     */
    public function store()
    {
        return $this->hasOne('app\\common\\model\\store\\Store', 'store_id', 'store_id')->bind(['store_name']);
    }
    /**
     * 关联店员表
     */
    public function clerk()
    {
        return $this->BelongsTo("app\\common\\model\\store\\Clerk", 'clerk_id', 'clerk_id');
    }
    /**
     * 关联供应商表
     */
    public function product()
    {
        return $this->belongsTo('app\\common\\model\\order\\OrderGoods', 'order_goods_id', 'order_goods_id');
    }
    /**
     * @param $order_product_id
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLogList($order_product_id)
    {
        return $this->with(['store'])->where('order_goods_id' , '=' , $order_product_id)->select();
    }

    public function getVerifLogsList($store_id, $search, $params)
    {
        $model = $this;
        if ($store_id > 0) {
            $model = $model->where('order.store_id', '=', (int)$store_id);
        }
        if (isset($params['verif_type']) && $params['verif_type'] > 0) {
            $verif_type = $params['verif_type'] == 1 ? 1 : 0;
            $model = $model->where('order.verify_type', '=', $verif_type);
        }
        if (isset($params['time']) && $params['time']) {
            $start_time = strtotime($params['time'][0]);
            $end_time = strtotime($params['time'][1]) + 86399;
            $model = $model->where('order.create_time', 'between', [$start_time, $end_time]);
        }
        if (!empty($search) && $params['type'] == 1) {
            $model = $model->where('clerk.real_name', 'like', '%' . $search . '%');
        }
        if (!empty($search) && $params['type'] == 2) {
            $model = $model->where('goods.product_name', 'like', '%' . $search . '%');
        }
        $data = $model->alias('order')
            ->field('order.*')
            ->with(['clerk'=>['store','user'],'product.order.supplier'])
            ->join('store_clerk clerk','clerk.clerk_id = order.clerk_id','left')
            ->join('order_goods goods','goods.order_goods_id = order.order_goods_id','left')
            ->order(['verify_date' => 'desc'])
           ->paginate($params);
        return $data;
    }

    public function exportList($data)
    {
        $model = $this;
        if ($data['store_id'] > 0) {
            $model = $model->where('store_id', '=', (int)$data['store_id']);
        }
        if (!empty($data['search']) && $data['type'] == 1) {
            $model = $model->where('clerk.real_name', 'like', '%' . $data['search'] . '%');
        }
        if (!empty($data['search']) && $data['type'] == 2) {
            $model = $model->where('goods.product_name', 'like', '%' . $data['search'] . '%');
        }
        if (isset($params['verif_type']) && $params['verif_type'] > 0) {
            $verif_type = $params['verif_type'] == 1 ? 1 : 0;
            $model = $model->where('order.verify_type', '=', $verif_type);
        }
        if (isset($params['time']) && $params['time']) {
            $start_time = strtotime($params['time'][0]);
            $end_time = strtotime($params['time'][1]) + 86399;
            $model = $model->where('order.create_time', 'between', [$start_time, $end_time]);
        }
        $data = $model->alias('order')
            ->field('order.*')
            ->with(['clerk'=>['store','user'],'product.order.purveyor'])
            ->join('store_clerk clerk','clerk.clerk_id = order.clerk_id','left')
            ->join('order_product goods','goods.order_product_id = order.order_product_id','left')
            ->order(['verify_date' => 'desc'])
            ->select();
        return (new Exportservice)->verifLogsList($data);
    }
}