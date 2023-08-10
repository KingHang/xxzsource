<?php

namespace app\common\enum\settings;

use MyCLabs\Enum\Enum;

/**
 * 商城设置枚举类
 */
class SettingEnum extends Enum
{
    // 商城设置
    const STORE = 'store';
    // 商城设置
    const MP_SERVICE = 'mp_service';
    // 交易设置
    const TRADE = 'trade';
    // 短信通知
    const SMS = 'sms';
    // 模板消息
    const TPL_MSG = 'tplMsg';
    // 上传设置
    const STORAGE = 'storage';
    // 小票打印
    const PRINTER = 'printer';
    // 满额包邮设置
    const FULL_FREE = 'full_free';
    // 充值设置
    const RECHARGE = 'recharge';
    // 积分设置
    const POINTS = 'exchangepurch';
    // 公众号设置
    const OFFICIA = 'officia';
    // 商品推荐
    const RECOMMEND = 'recommend';
    // 签到有礼
    const SIGN = 'clockin';
    // 首页推送
    const HOMEPUSH = 'homepush';
    // 引导收藏
    const COLLECTION = 'collection';
    // 好物圈
    const BASIC = 'basic';
    // 积分商城
    const POINTSMALL = 'pointsmall';
    // 限时秒杀
    const SECKILL = 'flashsell';
    // 限时拼团
    const ASSEMBLE = 'groupsell';
    // 限时砍价
    const BARGAIN = 'pricedown';
    // 获取手机号
    const GETPHOME = 'getPhone';
    // 直播设置
    const LIVE = 'live';
    // 底部导航设置
    const NAV = 'nav';
    // 邀请好友设置
    const APPSHARE = 'appshare';
    // 系统配置
    const SYS_CONFIG = 'sys_config';
    // 充值设置
    const BALANCE = 'balance';
    // h5支付宝支付设置
    const H5ALIPAY = 'h5Alipay';
    // 底部中间菜单设置
    const BOTTOMNAV = 'bottomnav';
    // 官网设置
    const WEBSITE = 'website';
    // 人脸设置
    const FACE = 'face';
    // 文章奖励设置
    const ARTICLEPROMOTION = 'articlepromotion';
    // 服务设置
    const SERVER = 'server';
    // 成长值设置
    const GROW = 'grow';
    // 人脸识别年龄折扣
    const FACEDISCOUNT = 'faceDiscount';
    // 抵扣设置
    const DEDUCT = 'deduct';
    //品牌设置
    const BRAND = 'brand';
    /**
     * 获取订单类型值
     */
    public static function data()
    {
        return [
            self::STORE => [
                'value' => self::STORE,
                'describe' => '商城设置',
            ],
            self::MP_SERVICE => [
                'value' => self::MP_SERVICE,
                'describe' => '客服设置',
            ],
            self::TRADE => [
                'value' => self::TRADE,
                'describe' => '交易设置',
            ],
            self::SMS => [
                'value' => self::SMS,
                'describe' => '短信通知',
            ],
            self::TPL_MSG => [
                'value' => self::TPL_MSG,
                'describe' => '模板消息',
            ],
            self::STORAGE => [
                'value' => self::STORAGE,
                'describe' => '上传设置',
            ],
            self::PRINTER => [
                'value' => self::PRINTER,
                'describe' => '小票打印',
            ],
            self::FULL_FREE => [
                'value' => self::FULL_FREE,
                'describe' => '满额包邮设置',
            ],
            self::RECHARGE => [
                'value' => self::RECHARGE,
                'describe' => '充值设置',
            ],
            self::POINTS => [
                'value' => self::POINTS,
                'describe' => '积分设置',
            ],
            self::OFFICIA => [
                'value' => self::OFFICIA,
                'describe' => '公众号设置',
            ],
            self::RECOMMEND => [
                'value' => self::RECOMMEND,
                'describe' => '商品推荐',
            ],
            self::SIGN => [
                'value' => self::SIGN,
                'describe' => '签到有礼',
            ],
            self::HOMEPUSH => [
                'value' => self::HOMEPUSH,
                'describe' => '首页推送',
            ],
            self::COLLECTION => [
                'value' => self::COLLECTION,
                'describe' => '引导收藏',
            ],
            self::BASIC => [
                'value' => self::BASIC,
                'describe' => '好物圈',
            ],
            self::POINTSMALL => [
                'value' => self::POINTSMALL,
                'describe' => '积分商城',
            ],
            self::SECKILL => [
                'value' => self::SECKILL,
                'describe' => '限时秒杀',
            ],
            self::ASSEMBLE => [
                'value' => self::ASSEMBLE,
                'describe' => '限时拼团',
            ],
            self::BARGAIN => [
                'value' => self::BARGAIN,
                'describe' => '限时砍价',
            ],
            self::GETPHOME => [
                'value' => self::GETPHOME,
                'describe' => '获取手机号',
            ],
            self::LIVE => [
                'value' => self::LIVE,
                'describe' => '直播设置',
            ],
            self::NAV => [
                'value' => self::NAV,
                'describe' => '底部导航设置',
            ],
            self::APPSHARE => [
                'value' => self::APPSHARE,
                'describe' => 'app分享',
            ],
            self::SYS_CONFIG => [
                'value' => self::SYS_CONFIG,
                'describe' => '系统设置',
            ],
            self::BALANCE => [
                'value' => self::BALANCE,
                'describe' => '充值设置',
            ],
            self::H5ALIPAY => [
                'value' => self::H5ALIPAY,
                'describe' => 'h5支付宝支付',
            ],
            self::BOTTOMNAV => [
                'value' => self::BOTTOMNAV,
                'describe' => '底部菜单',
            ],
            self::WEBSITE => [
                'value' => self::WEBSITE,
                'describe' => '官网设置',
            ],
            self::FACE => [
                'value' => self::FACE,
                'describe' => '人脸设置',
            ],
            self::ARTICLEPROMOTION => [
                'value' => self::ARTICLEPROMOTION,
                'describe' => '文章奖励设置',
            ],
            self::SERVER => [
                'value' => self::SERVER,
                'describe' => '服务设置',
            ],
            self::GROW => [
                'value' => self::GROW,
                'describe' => '成长值设置',
            ],
            self::FACEDISCOUNT => [
                'value' => self::FACEDISCOUNT,
                'describe' => '人脸识别年龄折扣',
            ],
            self::DEDUCT => [
                'value' => self::DEDUCT,
                'describe' => '抵扣设置',
            ],
            self::BRAND => [
                'value' => self::BRAND,
                'describe' => '品牌设置',
            ]
        ];
    }
}
