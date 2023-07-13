<?php

namespace app\api\model\plus\live;

use app\common\model\plugin\live\Room as RoomModel;
use app\api\model\settings\Settings as SettingModel;
use app\api\model\supplier\Purveyor as SupplierModel;
use app\api\model\order\Order as OrderModel;
use app\common\model\plugin\live\RoomGift as RoomGiftModel;
use app\api\model\user\Favorite as FavoriteModel;

/**
 * 直播模型
 */
class Room extends RoomModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'is_delete',
        'app_id',
    ];

    /**
     * 获取直播列表
     */
    public function getList($data,$user=false)
    {   
        $model = $this;
        if(isset($data['category_id'])&&$data['category_id']){
            $model = $model->where('category_id','=',$data['category_id']);
        }
        if(isset($data['is_notice'])){
            $model = $model->where('is_notice', '=', $data['is_notice']);
        }
        if(isset($data['is_follow'])&&$data['is_follow']){
            if($user){
               $shop_supplier_id = (new FavoriteModel())->where('user_id','=',$user['user_id'])->where('type','=',10)->column('pid');
               $model = $model->where('shop_supplier_id', 'in', $shop_supplier_id);
            }
            
        }
        $model = $model->where('live_status','in','101,102,104');
        return $model->with(['share', 'user','cover','product.product.image.file'])->where('is_delete', '=', 0)
            ->order([ 'is_top' => 'desc','live_status' => 'asc', 'create_time' => 'desc'])
            ->paginate($data, false, [
                'query' => \request()->request()
            ]);
    }
    /**
     * 获取直播中数据
     */
    public function getLive($data)
    {   
        $model = $this;
        $model = $model->where('live_status','=',101);
        return $model->with(['share', 'user','cover','product.product.image.file'])->where('is_delete', '=', 0)
            ->order([ 'is_top' => 'desc','create_time' => 'desc'])
            ->limit(5)
            ->select();
    }
    /**
     * 获取diy直播列表
     */
    public function getDiyList($num)
    {   
        $model = $this;
        $model = $model->where('live_status','in',[101,102,104]);
        return $model->with(['share', 'user'])->where('is_delete', '=', 0)
            ->order(['is_top' => 'desc','live_status' => 'asc', 'create_time' => 'desc'])
            ->limit($num)
            ->select();
    }
    /**
     * 获取直播列表
     */
    public function getMyList($user, $data, $show_delete = false)
    {
        $model = $this;
        if(isset($data['is_notice'])){
            $model = $model->where('is_notice', '=', $data['is_notice']);
        }
        if(!$show_delete){
            $model = $model->where('is_delete', '=', 0);
        }
        if(isset($data['is_end'])&&$data['is_end']){
            $model = $model->where('live_status', 'in', '101,103');
        }
        return $model->with(['cover', 'share'])->where('user_id', '=', $user['user_id'])
            ->order(['create_time' => 'desc'])
            ->paginate($data, false, [
            'query' => \request()->request()
        ]);
    }


    /**
     * 保存
     */
    public function add($user, $data)
    {
        //保存直播
        $data['user_id'] = $user['user_id'];
        $live = SettingModel::getItem('live');
        $data['qcloud_room_id'] =  'room_' .$user['user_id'];
        $data['shop_supplier_id'] =  $user['supplierUser']['shop_supplier_id'];
        $data['app_id'] = self::$app_id;
        //判断是否需要审核
        $is_audit = $live['is_audit'];
        $data['live_status'] = $is_audit==1?0:102;
        //查询供应商类目
        $supplier = SupplierModel::detail($data['shop_supplier_id']);
        $data['category_id'] = $supplier['category_id'];
        return $this->save($data);
    }

    /**
     * 预告
     */
    public function notice($user, $data)
    {
        $this->startTrans();
        try {
            //保存直播
            $data['user_id'] = $user['user_id'];
            $live = SettingModel::getItem('live');
            $data['room_name'] =  'channel_' .$user['supplierUser']['shop_supplier_id'];
            $data['start_time'] = strtotime($data['start_time']);
            $data['is_notice'] = 1;
            $data['app_id'] = self::$app_id;
            $data['shop_supplier_id'] =  $user['supplierUser']['shop_supplier_id'];
            //判断是否需要审核
            $is_audit = $live['is_audit'];
            $data['live_status'] = $is_audit==1?0:102;
            //查询供应商类目
            $supplier = SupplierModel::detail($data['shop_supplier_id']);
            $data['category_id'] = $supplier['category_id'];
            $data['record_uid'] = time();
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }

    /**
     * 保存
     */
    public function edit($data)
    {
        //保存直播
        return $this->save($data);
    }

    /**
     * 真删
     */
    public function setDelete(){
        return $this->delete();
    }

    public static function getRoom($room_id, $user_id){
        return (new static())->where('room_id', '=', $room_id)
            ->where('user_id', '=', $user_id)
            ->find();
    }

    public function setProduct($product_id){
        return $this->save([
            'product_id' => $product_id
        ]);
    }

    public function setStatus($status){
        $data['live_status'] = $status;
        if($status == 101){
            $data['is_notice'] = 0;
            $data['real_start_time'] = time();
            $data['real_end_time'] = time();
        }
        if($status == 103){
            $data['real_end_time'] = time();
        }
        return $this->save($data);
    }

    public function digg($num){
        return $this->inc('digg_num',$num)->update();
    }
    //直播数据
    public function livedata($shop_supplier_id){
        //总收入
        $OrderModel = new OrderModel();
        $order = $OrderModel->where('shop_supplier_id','=',$shop_supplier_id)
                            ->where('room_id','>',0)
                            ->where('pay_status','=',20)
                            ->field("sum('order_id') as orderCount,sum(pay_price) as totalPrice")
                            ->find();
        //礼物收入
        $RoomGiftModel = new RoomGiftModel();
        $gifPrice = $RoomGiftModel->where('shop_supplier_id','=',$shop_supplier_id)->sum('price');
        $data['totalPrice'] = $order['totalPrice']?$order['totalPrice']:0;
        $data['orderCount'] = $order['orderCount']?$order['orderCount']:0;
        $data['gifPrice'] = $gifPrice;
        return $data;
    }

    /**
     * 更新结束时间
     */
    public function updateEndTime(){
        return $this->save([
            'real_end_time' => time()
        ]);
    }
    /**
     * 获取店铺直播列表
     */
    public function getStoreList($data)
    {
        $model = $this;
        if(isset($data['shop_supplier_id'])&&$data['shop_supplier_id']){
            $model = $model->where('shop_supplier_id', '=', $data['shop_supplier_id']);
        }
        return $model->with(['cover', 'share'])
            ->order([ 'is_top' => 'desc','live_status' => 'asc', 'create_time' => 'desc'])
            ->where('is_delete', '=', 0)
            ->where('live_status', 'in', '101,102,103,104')
            ->paginate($data, false, [
            'query' => \request()->request()
        ]);
    }
}