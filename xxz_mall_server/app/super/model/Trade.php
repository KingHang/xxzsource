<?php
declare (strict_types = 1);

namespace app\super\model;

use app\common\model\super\TradeAccess;
use app\common\model\app\App;
use think\Model;

/**
 * @mixin Model
 */
class Trade extends \app\common\model\super\Trade
{
    public static function getALL()
    {
        $model = new static;
        return $model->order(['sort' => 'desc', 'create_time' => 'desc'])->select();
    }

    /**
     * 行业详情
     */
    public static function detail($where=[])
    {
        return (new static())->where($where)->find();
    }

    /**
     * 添加新记录
     */
    public function add($data)
    {
        $this->startTrans();
        $model = new TradeAccess();
        try {
            if (self::detail(['trade_name'=>$data['trade_name']])) {
                return ['status' => 1, 'msg' => '行业名称重复'];
            }
            $this->save($data);
            $arr1 = self::addAccess($this,$data);
            $model->saveAll($arr1);
            // 事务提交
            $this->commit();
            return ['status' => 0];
        } catch (\Exception $e) {
            $this->rollback();
            return ['status' => 1, 'msg' => $e->getMessage()];
        }
    }

    public static function addAccess($res,$data)
    {
        $arr1 = [];
        if (isset($data['shop_access_id'])){
            foreach ($data['shop_access_id'] as $val) {
                $arr1[] = [
                    'trade_id' => $res['trade_id'],
                    'access_id' => $val,
                    'type' => 0,
                ];
            }
            foreach ($data['supplier_access_id'] as $value) {
                $arr1[] = [
                    'trade_id' => $res['trade_id'],
                    'access_id' => $value,
                    'type' => 1,
                ];
            }

        }
        return $arr1;
    }

    /**
     * 编辑记录
     * @param $data
     * @return array|bool
     */
    public function edit($data)
    {
        $this->startTrans();
        $model = new TradeAccess();
        try {
            if ($trade_detail = self::detail(['trade_name'=>$data['trade_name']])) {
                if ($trade_detail['trade_id'] != $data['trade_id']) {
                    return ['status' => 1, 'msg' => '行业名称重复'];
                }
            }
            $this->save($data);
            $model->where(['trade_id' => $data['trade_id']])->delete();
            $arr1 = self::addAccess($this,$data);
            $model->saveAll($arr1);
            // 事务提交
            $this->commit();
            return ['status' => 0];
        } catch (\Exception $e) {
            $this->rollback();
            return ['status' => 1, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 删除
     */
    public function remove()
    {
        $this->startTrans();
        // 判断是否存在选择该行业的商城
        $tradeCount = App::where(['trade_id' => $this['trade_id']])->count();
        if ($tradeCount > 0) {
            return ['status' => 1, 'msg' => '该行业下存在' . $tradeCount . '个商户，不允许删除'];
        }
        try {
             $this->delete();
             (new TradeAccess())->where('trade_id',$this['trade_id'])->delete();
             $this->commit();
             return ['status' => 0];
        } catch (\Exception $e) {
            $this->rollback();
            return ['status' => 1, 'msg' => $e->getMessage()];
        }
    }
}
