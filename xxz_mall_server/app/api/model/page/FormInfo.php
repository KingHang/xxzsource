<?php

namespace app\api\model\page;
use app\common\model\home\FormInfo as FormInfoModel;
/**
 * 用户信息模型
 */
class FormInfo extends FormInfoModel
{
    public function add($data)
    {
        return $this->save([
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'app_id' => self::$app_id
        ]);
    }
}