<?php

//dezend by http://www.yunlu99.com/
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require_once EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Index_EweiShopV2Page extends AppMobilePage {

    public function __construct() {
        global $_W, $_GPC;
        if (trim($_GPC['token']) != trim($_W['shopset']['recognition']['hlbtoken'])) {
            $json['code'] = 500;
            $json['message'] = "手环Token校验失败";
            die(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        $_GPC['device_sn'] = trim($_GPC['device_sn']);
        $_GPC['mobile'] = trim($_GPC['mobile']);

        if (!isset($_GPC['operate_type'])) {
            $json['code'] = 500;
            $json['message'] = "缺少推送的绑定类型operate_type";
            die(json_encode($json, JSON_UNESCAPED_UNICODE));
        }

        if (empty($_GPC['device_sn'])) {
            $json['code'] = 500;
            $json['message'] = "推送设备编号不可以为空";
            die(json_encode($json, JSON_UNESCAPED_UNICODE));
        }

        if (!preg_match("/^1[34578]\d{9}$/", trim($_GPC['mobile']))) {
            $json['code'] = 500;
            $json['message'] = "推送手机号不可以为空";
            die(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        switch (intval($_GPC['operate_type'])) {
            case 1://绑定
                $this->Bindmobile();
                break;

            case 2://解绑
                $this->Unbindmobile();
                break;
        }
    }

    //手环绑定
    private function Bindmobile() {
        global $_GPC, $_W;
        $ckdevice = pdo_fetch("SELECT * FROM " . tablename("bracelet") . " where `device_sn`='{$_GPC['device_sn']}' and `uniacid`='{$_W['uniacid']}' ");

        if (!empty($ckdevice)) {//已存在设备信息更新流程
            $bradata['mobile'] = $_GPC['mobile'];
            $device = machinfo($ckdevice['device_sn']);
            $bradata['device_id'] = $device['id'] ? $device['id'] : 0;
            $bradata['device_name'] = $device['deviceName'] ? $device['deviceName'] : "";
            $bradata['eicon'] = $device['deviceE'] ? $device['deviceE'] : 0;
            $bradata['version'] = $device['firmwareSn'] ? $device['firmwareSn'] : "";
            pdo_update("bracelet", $bradata, array("id" => $ckdevice['id']));

            $log['device_sn'] = $_GPC['device_sn'];
            $log['mobile'] = $_GPC['mobile'];
            $log['isbind'] = 1;
            $log['uniacid'] = $_W['uniacid'];
            $log['date'] = date("Y-m-d H:i:s", time());
            pdo_insert("bracelet_log", $log);

            $json['code'] = 200;
            $json['message'] = "数据绑定成功";
            die(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        $data['uniacid'] = $_W['uniacid'];
        $data['device_sn'] = $_GPC['device_sn'];
        $data['mobile'] = $_GPC['mobile'];
        pdo_insert("bracelet", $data);
        $r1 = pdo_insertid();
        //写入日志
        $log['device_sn'] = $_GPC['device_sn'];
        $log['mobile'] = $_GPC['mobile'];
        $log['isbind'] = 1;
        $log['uniacid'] = $_W['uniacid'];
        $log['date'] = date("Y-m-d H:i:s", time());
        $r2 = pdo_insert("bracelet_log", $log);
        if ($r1 && $r2) {
            $device = machinfo($log['device_sn']);
            $bradata['device_id'] = $device['id'] ? $device['id'] : 0;
            $bradata['device_name'] = $device['deviceName'] ? $device['deviceName'] : "";
            $bradata['eicon'] = $device['deviceE'] ? $device['deviceE'] : 0;
            $bradata['version'] = $device['firmwareSn'] ? $device['firmwareSn'] : "";
            pdo_update("bracelet", $bradata, array("id" => $r1));
            $json['code'] = 200;
            $json['message'] = "数据绑定成功";
        } else {
            $json['code'] = 500;
            $json['message'] = "数据绑定失败";
        }
        die(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    //手环解绑
    public function Unbindmobile() {
        global $_GPC, $_W;
        $ckdevice = pdo_fetch("SELECT * FROM " . tablename("bracelet") . " where `device_sn`='{$_GPC['device_sn']}' and `mobile`='{$_GPC['mobile']}' and `uniacid`='{$_W['uniacid']}'");
        if (!empty($ckdevice)) {
            $bradata['mobile'] = "";
            $device = machinfo($ckdevice['device_sn']);
            $bradata['device_id'] = $device['id'] ? $device['id'] : 0;
            $bradata['device_name'] = $device['deviceName'] ? $device['deviceName'] : "";
            $bradata['eicon'] = $device['deviceE'] ? $device['deviceE'] : 0;
            $bradata['version'] = $device['firmwareSn'] ? $device['firmwareSn'] : "";
            $r1 = pdo_update("bracelet", $bradata, array("id" => $ckdevice['id']));

            //写入日志
            $log['device_sn'] = $_GPC['device_sn'];
            $log['mobile'] = $_GPC['mobile'];
            $log['isbind'] = 0;
            $log['uniacid'] = $_W['uniacid'];
            $log['date'] = date("Y-m-d H:i:s", time());
            $r2 = pdo_insert("bracelet_log", $log);
            if ($r1 && $r2) {
                $json['code'] = 200;
                $json['message'] = "数据已解绑成功";
            }
        } else {
            $this->Bindmobile();
            die();
        }
        die(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

}

?>
