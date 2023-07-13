<?php

namespace app\api\model\plus\website\phone;

use app\common\model\plugin\website\phone\Phone as PhoneModel;

/**
 * 电话咨询模型
 */
class Phone extends PhoneModel
{
    /**
     * 新增记录
     * @param string $phone 手机号
     * @return bool
     */
    public function addReservationLog($phone)
    {
        //判断今日是否预约
        $exist = self::phoneExist($phone);

        if ($exist) {
            $this->error = '您今日已预约，请勿重复预约';
            return false;
        }

        $data['phone'] = $phone;
        $data['app_id'] = self::$app_id;

        return $this->save($data);
    }
}
