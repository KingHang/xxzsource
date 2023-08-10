<?php

namespace app\mall\controller\store;

use app\mall\controller\Controller;
use app\mall\model\store\Store as StoreModel;
use app\mall\model\store\Order as OrderModel;
use app\common\model\order\VerifyGoodsLog;

/**
 * 订单核销控制器
 */
class Order extends Controller
{
    /**
     * 订单核销记录列表
     */
    public function index($store_id = 0, $search = '')
    {
        $data = $this->postData();
        // 核销记录列表
        $model = new VerifyGoodsLog;
        $list = $model->getVerifLogsList($store_id, $search, $data);

        // 核销记录列表
//        $model = new OrderModel;
//        $list = $model->getList($store_id, $search, $data);
        // 门店列表
        $store_list = (new StoreModel)->getShopStoreList();
        return $this->renderSuccess('', compact('list', 'store_list'));
    }
    /**
     * 导出核销记录
     */
    public function export()
    {
        $model = new VerifyGoodsLog;
        return $model->exportList($this->postData());
    }
}
