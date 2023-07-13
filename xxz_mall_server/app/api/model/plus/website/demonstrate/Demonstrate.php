<?php

namespace app\api\model\plus\website\demonstrate;

use app\common\model\plugin\website\demonstrate\Demonstrate as DemonstrateModel;

/**
 * 预约演示模型
 */
class Demonstrate extends DemonstrateModel
{
    /**
     * 新增记录
     * @param string $phone 手机号
     * @param string $place 所在地区
     * @param string $intention 意向产品
     * @return bool
     */
    public function addDemonstrateLog($phone, $place, $intention)
    {
        //判断今日是否预约
        $exist = self::phoneExist($phone);

        if ($exist) {
            $this->error = '您今日已预约演示，请勿重复预约';
            return false;
        }

        $data['phone'] = $phone;
        $data['place'] = $place;
        $data['intention'] = $intention;
        $data['app_id'] = self::$app_id;

        return $this->save($data);
    }
}
