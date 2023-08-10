<?php

namespace app\common\library\easywechat;

use app\mall\model\setting\Setting as SettingModel;
use EasyWeChat\Factory;
use app\common\exception\BaseException;
use app\common\model\app\AppWx as AppWxModel;

/**
 * 微信小程序
 */
class AppWx
{
    public static function getApp($app_id = null){
        // 获取当前小程序信息
        $wxConfig = AppWxModel::getAppWxCache($app_id);
        // 验证appid和appsecret是否填写
        if (empty($wxConfig['wxapp_id']) || empty($wxConfig['wxapp_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-应用-小程序设置] 填写appid 和 appsecret']);
        }
        $config = [
            'app_id' => $wxConfig['wxapp_id'],
            'secret' => $wxConfig['wxapp_secret'],
            'response_type' => 'array',
        ];
        return Factory::miniProgram($config);
    }

    /**
     * 获取客多多小程序配置
     */
    public static function getKddApp($app_id = null){
        $setting = SettingModel::getItem('articlepromotion');
        // 验证appid和appsecret是否填写
        if (empty($setting['app_id']) || empty($setting['app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-插件-文章推广-设置] 填写appid和appsecret']);
        }
        $config = [
            'app_id' => $setting['app_id'],
            'secret' => $setting['app_secret'],
            'response_type' => 'array',
        ];
        return Factory::miniProgram($config);
    }
    public static function getFapp($app_id = null)
    {
        // 获取当前小程序信息
        $wxConfig = AppWxModel::getAppWxCache($app_id);
        // 验证appid和appsecret是否填写
        if (empty($wxConfig['fwxapp_id']) || empty($wxConfig['fwxapp_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-应用-小程序设置] 填写appid 和 appsecret']);
        }
        $config = [
            'app_id' => $wxConfig['fwxapp_id'],
            'secret' => $wxConfig['fwxapp_secret'],
            'response_type' => 'array',
        ];
        return Factory::miniProgram($config);
    }
    public static function getWxPayApp($app_id){
        // 获取当前小程序信息
        $wxConfig = AppWxModel::getAppWxCache($app_id);
        // 验证appid和appsecret是否填写
        if (empty($wxConfig['wxapp_id']) || empty($wxConfig['wxapp_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-应用-小程序设置] 填写appid 和 appsecret']);
        }

        if (empty($wxConfig['cert_pem']) || empty($wxConfig['key_pem'])) {
            throw new BaseException(['msg' => '请先到后台小程序设置填写微信支付证书文件']);
        }
        // cert目录
        $filePath = root_path() . 'runtime/cert/appwx/' . $wxConfig['app_id'] . '/';

        $config = [
            'app_id' => $wxConfig['wxapp_id'],
            'mch_id'             => $wxConfig['mchid'],
            'key'                => $wxConfig['apikey'],   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          => $filePath . 'cert.pem',
            'key_path'           => $filePath . 'key.pem',
            'sandbox' => false, // 设置为 false 或注释则关闭沙箱模式
        ];
        return Factory::payment($config);
    }

    public static function getFwxPayApp($app_id)
    {
        // 获取当前小程序信息
        $wxConfig = AppWxModel::getAppWxCache($app_id);
        // 验证appid和appsecret是否填写
        if (empty($wxConfig['fwxapp_id']) || empty($wxConfig['fwxapp_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-应用-小程序设置] 填写appid 和 appsecret']);
        }

        if (empty($wxConfig['cert_pem']) || empty($wxConfig['key_pem'])) {
            throw new BaseException(['msg' => '请先到后台小程序设置填写微信支付证书文件']);
        }
        // cert目录
        $filePath = root_path() . 'runtime/cert/appwx/' . $wxConfig['app_id'] . '/';

        $config = [
            'app_id' => $wxConfig['fwxapp_id'],
            'mch_id' => $wxConfig['mchid'],
            'key' => $wxConfig['apikey'],   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path' => $filePath . 'cert.pem',
            'key_path' => $filePath . 'key.pem',
            'sandbox' => false, // 设置为 false 或注释则关闭沙箱模式
        ];
        return Factory::payment($config);
    }
}
