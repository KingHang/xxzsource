<?php

namespace app\shop\model\order;

use app\common\model\order\OrderGoods as OrderProductModel;
use app\common\service\qrcode\VerifyService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use app\shop\model\store\Store as StoreModel;
use app\common\library\helper;
use think\Model;

/**
 * 订单商品模型
 */
class OrderGoods extends OrderProductModel
{
    /**
     * 获取赠送明细列表
     * @param array $query
     * @return array
     */
    public function getList($data = [])
    {
        $model = $this;

        if (isset($data['time_start']) && $data['time_start']) {
            $model = $model->where('order.create_time', '>=', strtotime($data['time_start']));
        }

        if (isset($data['time_end']) && $data['time_end']) {
            $model = $model->where('order.create_time', '<', strtotime($data['time_end']) + 24 * 3600);
        }

        if (isset($data['search']) && $data['search']) {
            $model = $model->where('user.user_id|user.nickName|user.mobile|user.realname|order.order_no|product.product_name', 'like', '%' . trim($data['search']) . '%');
        }

        // 获取列表数据
        return $model->with(['image', 'order.user'])
            ->alias('product')
            ->field('product.*,product.gift_amount as amount,log.amount as gift_amount,product.already_stages as product_stages_number,log.stages_number as already_stages')
            ->join('giftcert_product_log log','product.order_product_id = log.order_product_id' , 'left' )
            ->join('order', 'order.order_id = product.order_id')
            ->join('user', 'user.user_id = product.user_id', 'left')
            ->where('product.is_gift', '=', 1)
            ->order(['log.log_id' => 'desc'])
            ->paginate($data);
    }

    /**
     * 订单商品详情
     * @param $order_id
     * @param $product_id
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getUserOrderProductDetail($order_id, $product_id)
    {
        return $this->with(['express'])
            ->where('order_id', '=', $order_id)
            ->where('product_id', '=', $product_id)
            ->find();
    }
    
    public function getVerifList($data = []) {
        $order = "og.create_time";
        $product_type = 3;
        if ($data['search_type'] == 2) {
            $product_type = 4;
            $order = "ot.create_time";
            $search = "og.product_name|ot.verify_code|o.order_no|u.mobile|u.nickName|u.realname|ot.name|ot.mobile|ot.id_card";
        } else {
            $search = "og.product_name|og.verify_code|o.order_no|u.mobile|u.nickName|u.realname";
        }
        $model = $this;
        if (isset($data['search']) && $data['search']) {
            $model = $model->where($search, 'like', '%' . trim($data['search']) . '%');
        }
        $model = $model->where('product_type' , '=' , $product_type);
        $model = $model->where('o.pay_status' , '=' , 20);
        $model = $model->where('o.order_status' , '=' , 30);
        $model = $model->alias('og')->with(['image']);
        // 旅游商品
        if ($data['search_type'] == 2) {
            $model = $model->field('og.*,o.order_no,o.shop_supplier_id,ot.verify_code as verify_code,ot.name,ot.mobile,ot.id_card,ot.status,ot.id as opt_id')
                ->join('order_travelers ot' , 'ot.order_product_id = og.order_product_id');
        } else {
            $model = $model->field('og.*,o.order_no,o.shop_supplier_id');
        }
        $list = $model->join("order o" , 'og.order_id = o.order_id')
            ->join("user u" , 'o.user_id = u.user_id' , 'left')
            ->order([$order => 'desc'])
            ->select()->toArray();
        if (!empty($list)) {
            foreach ($list as &$item) {
                // 获取支持门店
                if ($item['store_ids'] != '') {
                    $store_list = (new StoreModel())->getListByIds(explode(',',$item['store_ids']));
                } else {
                    // 获取订单供应商id
                    $store_list = (new StoreModel())->getList(1,'','',false,[$item['shop_supplier_id']]);
                }
                $store_list = helper::getArrayColumn($store_list,'store_name');
                $item['store_list_arr'] = !empty($store_list) ? implode($store_list , ',') : '';
                // 核销二维码

                if ($item['product_type'] == 4) {
                    $type = 3;
                } elseif ($item['product_type'] == 3) {
                    $type = 1;
                } else {
                    $type = 2;
                }
                $Qrcode = new VerifyService(
                    $item['app_id'],
                    $item['user_id'],
                    $item['order_product_id'],
                    $item['product_type'],
                    $item['verify_code'],
                    $type
                );
                $item['qrcode'] = $Qrcode->getImage();
            }
        }
        return $list;
    }
}
