<?php

namespace app\common\enum\settings;

use MyCLabs\Enum\Enum;
/**
 * 配送方式枚举类
 */
class AgentOpenTypeEnum extends Enum
{
    // 销售奖
    const SALE = 'sale';
    // 差额奖
    const DIFF = 'diff';
    // 推荐奖
    const REFEREE = 'referee';
    // 团队奖
    const TEAM = 'team';
    // 级差奖
    const LEVEL = 'level';
    // 平级奖
    const SAME = 'same';
    // 越级奖
    const THAN = 'than';
    // 拓展奖
    const EXPAND = 'expand';
    // 区域奖
    const AREA = 'area';
    // 积分奖
    const POINTS = 'exchangepurch';
    /**
     * 获取枚举数据
     */
    public static function data()
    {
        return [
            self::SALE => [
                'name' => '销售奖',
                'value' => self::SALE,
            ],
            self::DIFF => [
                'name' => '差额奖',
                'value' => self::DIFF,
            ],
            self::REFEREE => [
                'name' => '推荐奖',
                'value' => self::REFEREE,
            ],
            self::TEAM => [
                'name' => '团队奖',
                'value' => self::TEAM,
            ],
            self::LEVEL => [
                'name' => '级差奖',
                'value' => self::LEVEL,
            ],
            self::SAME => [
                'name' => '平级奖',
                'value' => self::SAME,
            ],
            self::THAN => [
                'name' => '越级奖',
                'value' => self::THAN,
            ],
            self::EXPAND => [
                'name' => '拓展奖',
                'value' => self::EXPAND,
            ],
            self::AREA => [
                'name' => '区域奖',
                'value' => self::AREA,
            ],
            self::POINTS => [
                'name' => '积分奖',
                'value' => self::POINTS,
            ],
        ];
    }

    /**
     * 获取所有数组
     */
    public static function getAll()
    {
        $names = [];
        foreach (self::data() as $item){
            array_push($names, $item);
        }
        return $names;
    }

    /**
     * 获取所有数组
     */
    public static function getShow($open_type)
    {
        $str = '';
        foreach ($open_type as $key){
            $str .= self::data()[$key]['name'].'、';
        }
        return substr($str, 0, strlen($str) - 3);
    }

    /**
     * 获取所有值
     */
    public static function getAllValue()
    {
        $names = [];
        foreach (self::data() as $item){
            array_push($names, $item['value']);
        }
        return $names;
    }
}