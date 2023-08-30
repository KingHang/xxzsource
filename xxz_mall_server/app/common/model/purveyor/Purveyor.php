<?php

namespace app\common\model\purveyor;

use app\common\model\BaseModel;

/**
 * 商家供应商模型
 */
class Purveyor extends BaseModel
{
    protected $name = 'purveyor';
    protected $pk = 'purveyor_id';
    protected $append = ['purveyor_id'];
    public function getPurveyorIdAttr($value, $data)
    {
        return $data['purveyor_id'];
    }
    /**
     * 关联应用表
     */
    public function app()
    {
        return $this->belongsTo('app\\common\\model\\app\\App', 'app_id', 'app_id');
    }

    /**
     * 关联logo
     */
    public function logo()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'logo_id');
    }
    /**
     * 关联品牌类型
     */
    public function category()
    {
        return $this->hasOne('app\\common\\model\\purveyor\\Category', 'category_id', 'category_id');
    }
    public function supplierApply()
    {
        return $this->hasMany('app\\common\\model\\purveyor\\ServiceApply', 'purveyor_id', 'purveyor_id')->where('status',1);
    }
    /**
     * 关联business
     */
    public function business()
    {
        return $this->hasOne('app\\common\\model\\file\\UploadFile', 'file_id', 'business_id');
    }
    /**
     * 关联超管
     */
    public function superUser()
    {
        return $this->hasOne('app\\common\\model\\purveyor\\User', 'purveyor_id', 'purveyor_id')
            ->where('is_super','=', 1);
    }
    /**
     * 详情
     */
    public static function detail($shop_supplier_id, $with = [])
    {
        return (new static())->with($with)->find($shop_supplier_id);
    }

    /**
     * 累积供应商结算金额 (批量)
     */
    public function onBatchIncSupplierMoney($data)
    {
        foreach ($data as $supplierId => $supplierMoney) {
            $this->where(['purveyor_id' => $supplierId])
                ->inc('total_money', $supplierMoney)
                ->inc('money', $supplierMoney)
                ->update();
        }
        return true;
    }

    public function checkSupplierType($shop_supplier_id,$type = 20)
    {
        return $this->where('purveyor_id' , '=' , $shop_supplier_id)->where('store_type' , '=' , $type)->value('purveyor_id');
    }

}