<?php

namespace app\api\model\plus\live;

use app\common\enum\user\GiftLogSceneEnum;
use app\common\model\plugin\live\Gift as GiftModel;
use app\api\model\user\User as UserModel;
use app\common\model\plugin\live\RoomGift as RoomGiftModel;
use app\common\model\user\GiftLog as GiftLogModel;
use app\common\model\plugin\live\Room as RoomModel;
use app\common\model\plugin\live\UserGift as UserGiftModel;
use app\api\model\supplier\Purveyor as SupplierModel;
/**
 * 礼物模型
 */
class Gift extends GiftModel
{

    /**
     * 获取礼物列表
     */
    public function getList()
    {
        return $this->with(['image'])
            ->where('is_delete', '=', 0)
            ->order(['sort' => 'asc', 'create_time' => 'asc'])
            ->select();
    }

    public function sendGift($user, $room_id, $gift_id){
        $gift = self::detail($gift_id);
        $room = RoomModel::detail($room_id);
        if(!$gift || $gift['is_delete'] == 1){
            $this->error = '礼物不存在';
            return false;
        }
        if($user['gift_money'] < $gift['price']){
            $this->error = '余额不足';
            return false;
        }
        $this->startTrans();
        try {
            // 扣除
            (new UserModel())->where('user_id', '=', $user['user_id'])
                ->dec('gift_money', $gift['price'])
                ->update();
            // 主播增加
            (new SupplierModel())->where('shop_supplier_id', '=', $room['shop_supplier_id'])
                ->inc('total_gift', $gift['price'])
                ->inc('gift_money', $gift['price'])
                ->update();
            // 房间增加
            (new RoomModel())->where('room_id', '=', $room_id)
                ->inc('gift_num', $gift['price'])
                ->update();
            // 房间用户礼物数增加
            $user_gift_model = UserGiftModel::detail($room_id, $user['user_id']);
            $user_gift_model->where('room_id', '=', $room_id)->where('user_id', '=', $user['user_id'])
                ->inc('gift_num', $gift['price'])->update();
            // 保存记录
            (new RoomGiftModel())->save([
                'room_id' => $room_id,
                'user_id' => $user['user_id'],
                'shop_supplier_id' => $room['shop_supplier_id'],
                'price' => $gift['price'],
                'gift_name' =>  $gift['gift_name'],
                'app_id' => self::$app_id
            ]);
            GiftLogModel::add(GiftLogSceneEnum::CONSUME, [
                'user_id' => $user['user_id'],
                'money' => -$gift['price'],
            ], ['gift_name' => $gift['gift_name']]);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
}