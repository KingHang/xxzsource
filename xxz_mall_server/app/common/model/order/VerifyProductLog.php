<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
use app\shop\service\order\ExportService;

/**
 * 商品订单模型
 */
class VerifyProductLog  extends BaseModel
{
    protected $name = 'verify_product_log';
    protected $pk = 'id';
    protected $append = [
        'verify_time',
        'verify_type_text'
    ];
    public function getVerifyTimeAttr($value, $data)
    {
        if (isset($data['verify_date'])){
            return date('Y-m-d H:i:s',$data['verify_date']);
        }
        return '';

    }
    public function getVerifyTypeTextAttr($value,$data)
    {
        return $data['verify_type'] == 0 ? '扫码核销' : '后台核销';
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
        return $this->belongsTo('app\\common\\model\\order\\OrderGoods', 'order_product_id', 'order_product_id');
    }
    public function OrderTravelers()
    {
        return $this->hasOne('app\\common\\model\\order\\OrderTravelers', 'id', 'order_product_id');
    }
    /**
     * @param $order_product_id
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLogList($order_product_id,$product_type = 3)
    {
        $type = 0;
        if ($product_type == 4) {
            $type = 1;
        }
        return $this->with(['store','clerk'])->where('type','=',$type)->where('order_product_id' , '=' , $order_product_id)->select();
    }
    /**
     * 获取核销记录
    */
    public function getVerifLogsList($store_id, $search, $params)
    {
        $search_type = isset($params['search_type']) ? $params['search_type'] : 1; // 1：计次商品,2旅游商品
        $field = $search_type == 1 ? "" : ",ot.name,ot.mobile,ot.id_card";
        $model = $this;
        // 核销门店
        if ($store_id > 0) {
            $model = $model->where('order.store_id', '=', (int)$store_id);
        }

        //核销类型
        if (isset($params['verif_type']) && $params['verif_type'] > 0) {
            $verif_type = $params['verif_type'] == 1 ? 1 : 0;
            $model = $model->where('order.verify_type', '=', $verif_type);
        }

        // 核销时间
        if (isset($params['time']) && $params['time']) {
            $start_time = strtotime($params['time'][0]);
            $end_time = strtotime($params['time'][1]) + 86399;
            $model = $model->where('order.create_time', 'between', [$start_time, $end_time]);
        }

        // 核销员姓名检索
        if (!empty($search) && $params['type'] == 1) {
            $model = $model->where('clerk.real_name', 'like', '%' . $search . '%');
        }

        // 商品名称检索
        if (!empty($search) && $params['type'] == 2) {
            $model = $model->where('product.product_name', 'like', '%' . $search . '%');
        }

        $model = $model->alias('order')
            ->field('order.*'.$field)
            ->with(['clerk'=>['store','user'],$search_type == 2 ? 'OrderTravelers.product.order.supplier' :'product.order.supplier'])
            ->join('store_clerk clerk','clerk.clerk_id = order.clerk_id','left');

        // 设置不同类型商品关联条件
        if ($search_type == 2) {
            // 旅游商品核销
            $model = $model->where('order.type', '=', 1);
            $model = $model->join('order_travelers ot' , 'ot.id = order.order_product_id');
            $model = $model->join('order_product product','product.order_product_id = ot.order_product_id','left');
        } else {
            // 计次商品核销
            $model = $model->where('order.type', '=', 0);
            $model = $model->join('order_product product','product.order_product_id = order.order_product_id','left');
        }
        $data = $model->order(['verify_date' => 'desc'])
           ->paginate($params);
        return $data;
    }

    /**
     * 导出
     * @param $data
     */
    public function exportList($params)
    {
        $search_type = isset($params['search_type']) ? $params['search_type'] : 1; // 1：计次商品,2旅游商品
        $field = $search_type == 1 ? "" : ",ot.name,ot.mobile,ot.id_card";
        $model = $this;
        // 核销门店
        if (isset($params['store_id']) && $params['store_id'] > 0) {
            $model = $model->where('order.store_id', '=', (int)$params['store_id']);
        }

        //核销类型
        if (isset($params['verif_type']) && $params['verif_type'] > 0) {
            $verif_type = $params['verif_type'] == 1 ? 1 : 0;
            $model = $model->where('order.verify_type', '=', $verif_type);
        }

        // 核销时间
        if (isset($params['time']) && $params['time']) {
            $start_time = strtotime($params['time'][0]);
            $end_time = strtotime($params['time'][1]) + 86399;
            $model = $model->where('order.create_time', 'between', [$start_time, $end_time]);
        }
        $search = isset($params['search']) ? $params['search'] : '';
        // 核销员姓名检索
        if (!empty($search) && $params['type'] == 1) {
            $model = $model->where('clerk.real_name', 'like', '%' . $search . '%');
        }

        // 商品名称检索
        if (!empty($search) && $params['type'] == 2) {
            $model = $model->where('product.product_name', 'like', '%' . $search . '%');
        }
        if ($search_type == 2) {
                $model = $model->alias('order')
                    ->field('order.*'.$field)
                    ->with(['clerk'=>['store','user'],'OrderTravelers'=>['cardProduct','product'=>['order.supplier','benefit']]])
                    ->join('store_clerk clerk','clerk.clerk_id = order.clerk_id','left');
        } else {
            $model = $model->alias('order')
                ->field('order.*'.$field)
                ->with(['clerk'=>['store','user'],'product.order.supplier'])
                ->join('store_clerk clerk','clerk.clerk_id = order.clerk_id','left');
        }


        // 设置不同类型商品关联条件
        if ($search_type == 2) {
            // 旅游商品核销
            $model = $model->where('order.type', '=', 1);
            $model = $model->join('order_travelers ot' , 'ot.id = order.order_product_id');
            $model = $model->join('order_product product','product.order_product_id = ot.order_product_id','left');
        } else {
            // 计次商品核销
            $model = $model->where('order.type', '=', 0);
            $model = $model->join('order_product product','product.order_product_id = order.order_product_id','left');
        }
        $data = $model->order(['verify_date' => 'desc'])
            ->select();
//        echo "<pre>";
//        print_r($data->toArray());die;
        return (new Exportservice)->verifLogsList($data,$search_type);
    }
}