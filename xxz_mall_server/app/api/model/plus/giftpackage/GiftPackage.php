<?php

namespace app\api\model\plus\giftpackage;

use app\common\model\plugin\giftpackage\GiftPackage as GiftPackageModel;
use app\api\model\plus\coupon\Voucher;

/**
 * 礼包购模型
 */
class GiftPackage extends GiftPackageModel
{

    /**
     * 获取数据
     */
    public function getGiftPackage($id)
    {
        $data = self::detail($id);
        $data = $data->toArray();
        $data['value1'][] = $data['start_time']['text'];
        $data['value1'][] = $data['end_time']['text'];
        $data['grade_ids'] = explode(',', $data['grade_ids']);
        if ($data['is_coupon'] == '1') {
            $data['is_coupon'] = true;
        }
        if ($data['is_point'] == '1') {
            $data['is_point'] = true;
        }
        if ($data['coupon_ids'] != '') {
            $model = new Voucher();
            $coupon = $model->getCoupon($data['coupon_ids']);
            $data['coupon_list'] = $coupon->toArray();
            $data['coupon'] = explode(',', $data['coupon_ids']);
        }
        return $data;
    }
}