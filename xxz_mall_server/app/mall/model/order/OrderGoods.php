<?php

namespace app\mall\model\order;

use app\common\model\order\OrderGoods as OrderProductModel;
use app\common\service\qrcode\VerifyService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use app\mall\model\store\Store as StoreModel;
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
            $model = $model->where('user.user_id|user.nickName|user.mobile|user.realname|order.order_no|goods.product_name', 'like', '%' . trim($data['search']) . '%');
        }

        // 获取列表数据
        return $model->with(['image', 'order.user'])
            ->alias('goods')
            ->field('goods.*,goods.gift_amount as amount,log.amount as gift_amount,goods.already_stages as product_stages_number,log.stages_number as already_stages')
            ->join('giftcert_product_log log','goods.order_product_id = log.order_product_id' , 'left' )
            ->join('order', 'order.order_id = goods.order_id')
            ->join('user', 'user.user_id = goods.user_id', 'left')
            ->where('goods.is_gift', '=', 1)
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
        $model = $this;
        if (isset($data['search']) && $data['search']) {
            $model = $model->where('og.product_name|og.verify_code|o.order_no|u.mobile|u.nickName|u.realname', 'like', '%' . trim($data['search']) . '%');
        }
        $model = $model->where('product_type' , '=' , 3);
        $model = $model->where('o.pay_status' , '=' , 20);
        $model = $model->where('o.order_status' , '=' , 30);
        $list = $model->alias('og')->with(['image'])
            ->field('og.*,o.order_no,o.purveyor_id')
            ->join("order o" , 'og.order_id = o.order_id')
            ->join("user u" , 'o.user_id = u.user_id' , 'left')
            ->order(['og.create_time' => 'desc'])
            ->select();
        if (!empty($list)) {
            foreach ($list as &$item) {
                // 获取支持门店
                if ($item['store_ids'] != '') {
                    $store_list = (new StoreModel())->getListByIds(explode(',',$item['store_ids']));
                } else {
                    // 获取订单供应商id
                    $store_list = (new StoreModel())->getList(1,'','',false,[$item['purveyor_id']]);
                }
                $store_list = helper::getArrayColumn($store_list,'store_name');
                $item['store_list'] = !empty($store_list) ? implode($store_list , ',') : '';
                // 核销二维码
                $Qrcode = new VerifyService(
                    $item['app_id'],
                    $item['user_id'],
                    $item['order_product_id'],
                    $item['product_type'],
                    $item['verify_code'],
                    $item['product_type'] == 3 ? 1 : 2
                );
                $item['qrcode'] = $Qrcode->getImage();
            }
        }
        return $list;
    }
}
