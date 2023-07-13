<?php

declare (strict_types = 1);

namespace app\common\model\admin;

use app\shop\model\shop\Access;
use think\Model;

/**
 * @mixin Model
 */
class TradeAccess extends Model
{
    protected $name = 'trade_access';

    protected $pk = 'id';

    /**
     * 获取指定角色的所有权限id
     * @param $tradeIds
     * @param string $type
     * @return array
     */
    public static function getAccessIds($tradeIds,$type='')
    {
        $roleIds = is_array($tradeIds) ? $tradeIds : [(int)$tradeIds];
        return (new self)->where('trade_id', 'in', $roleIds)->where('type',$type)->column('access_id');
    }

    public static function detail($trade_id){
        $supplier_access_ids = (new self())->where(['trade_id'=>$trade_id,'type'=>1])->select()->toArray();
        $shop_access_ids = (new self())->where(['trade_id'=>$trade_id,'type'=>0])->select()->toArray();
        return compact('shop_access_ids','supplier_access_ids');
    }

    public function editInfo($trade_id)
    {
            $model = self::detail($trade_id);
            $trade = \app\admin\model\Trade::detail(['trade_id'=>$trade_id]);
            $shop['select_menu'] = array_column($model['shop_access_ids'], 'access_id');
            $shop['menu'] = (new Access())->getList();
            $supplier['select_menu'] = array_column($model['supplier_access_ids'], 'access_id');
            $supplier['menu'] = (new \app\supplier\model\supplier\Access())->getList();
            return compact('trade', 'shop', 'supplier');
    }
}
