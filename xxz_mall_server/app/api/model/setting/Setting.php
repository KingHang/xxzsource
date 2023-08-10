<?php

namespace app\api\model\setting;

use app\common\model\setting\Setting as SettingModel;

/**
 * 设置模型
 */
class Setting extends SettingModel
{
    /**
     * 获取积分名称
     */
    public static function getPointsName()
    {
        return static::getItem('exchangepurch')['points_name'];
    }

    /**
     * 获取积分启用开关
     */
    public static function getIsPoints()
    {
        return static::getItem('exchangepurch')['is_points'];
    }

    /**
     * 获取成长值名称
     */
    public static function getGrowthName()
    {
        return static::getItem('grow')['growth_name'];
    }

    /**
     * 获取成长值启用开关
     */
    public static function getIsGrow()
    {
        return static::getItem('grow')['is_grow'];
    }

    /**
     * 获取签到成长值启用开关
     */
    public static function getIsSignGrow()
    {
        return static::getItem('grow')['is_grow'] && static::getItem('grow')['is_sign'] ? '1' : '0';
    }

    /**
     * 获取积分名称
     */
    public static function getBargain()
    {
        return static::getItem('pricedown');
    }
}
