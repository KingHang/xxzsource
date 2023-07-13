<?php

//dezend by http://www.yunlu99.com/
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require_once EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Index_EweiShopV2Page extends AppMobilePage {

    //扫脸录入
    public function WriteIn() {
        global $_W;
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = strtolower(substr($charid, 0, 8) . substr($charid, 8, 4) . substr($charid, 12, 4) . substr($charid, 16, 4) . substr($charid, 20, 12));
        $imgFile = $this->uploaders($_FILES, "imgFile", false);
        if ($imgFile['status'] != "success") {
            json_show(1, [], "人脸识别信息上传失败！");
        }
        $fileimg = "../attachment/{$imgFile['filename']}";
        $facemodel = new FaceModel();
        $imgdata = $facemodel->imgToBase64($fileimg);
        $url = 'https://aip.baidubce.com/rest/2.0/face/v3/faceset/user/add?access_token=' . $this->facetoken;
        //注册人脸前先查看人脸库是否有该人脸信息
        $ckface = $this->ckfacetoken($url, $imgdata);
        if ($ckface['code'] == 1) {//人脸库存在人脸信息
            $shop_member = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_member") . " where `facetoken`='{$ckface['uface_token']}' ");
            unlink($fileimg);
            if (empty($shop_member)) {
                json_show(0, ["uface_token" => $ckface['uface_token'], "faceimg" => $imgFile['url']], "SUCCESS");
            } else {
                json_show(1, ["member" => $shop_member], "该用户已绑定了人脸信息，无需重复录入！");
            }
        } else {//不存在直接走注册逻辑
            $regdata['image'] = $imgdata;
            $regdata['image_type'] = "BASE64";
            $regdata['group_id'] = "group_repeat";
            $regdata['user_id'] = md5(uniqid());
            $regdata['quality_control'] = "LOW";
            $regdata['liveness_control'] = "NORMAL";
            $regdata['user_info'] = $uuid;

            $res = $facemodel->request_post($url, $regdata, 1, 1);
            $rs = json_decode($res, true);
            unlink($fileimg);
            $result['status'] = 0;
            $result['msg'] = $rs['error_msg'];
            if ($rs['error_code'] == 0 && $rs['error_msg'] == "SUCCESS") {
                json_show(0, ["uface_token" => $regdata['user_id'], "faceimg" => $imgFile['url']], "SUCCESS");
            }
            json_show(1, [], "Filed");
        }
    }

    //注册之前先检测人脸库
    private function ckfacetoken($url, $imgdata) {
        $facemodel = new FaceModel();
        $url = "https://aip.baidubce.com/rest/2.0/face/v3/search?access_token=" . $this->facetoken;
        $regdata['image'] = $imgdata;
        $regdata['liveness_control'] = "NORMAL";
        $regdata['group_id_list'] = "group_repeat";
        $regdata['image_type'] = "BASE64";
        $regdata['quality_control'] = "LOW";
        $res = $facemodel->request_post($url, $regdata, 1, 1);
        $rs = json_decode($res, true);
        if ($rs['error_code'] == 0 && $rs['error_msg'] == "SUCCESS" && $rs['result']['user_list'][0]['score'] >= 80) {
            $result['code'] = 1;
            $result['uface_token'] = $rs['result']['user_list'][0]['user_id'];
        } else {
            $result['code'] = 0;
            $result['uface_token'] = "";
        }
        return $result;
    }

    //人脸检测
    public function ReadIn() {
        global $_W;
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid = strtolower(substr($charid, 0, 8) . substr($charid, 8, 4) . substr($charid, 12, 4) . substr($charid, 16, 4) . substr($charid, 20, 12));
        $facetemp = IA_ROOT . '/addons/ewei_shopv2/data/facetemp/' . $_W['uniacid'];
        if (!is_dir($facetemp)) {
            mkdir($facetemp, 0777, true);
        }
        $file = $_FILES['imgFile'];
        if (empty($file)) {
            json_show(1, [], "未检测到人脸录入信息");
        }
        if ($file['size'] > 10 * 1024 * 1024) {
            json_show(1, [], "人脸图像必须小于10M");
        }
        $imgtype = array("image/png", "image/jpg", "image/jpeg", "image/gif", "image/bmp");
        if (!in_array($file['type'], $imgtype)) {
            json_show(1, [], "人脸图像格式不正确");
        }
        $fnamearray = explode(".", $file['name']);
        $itype = $fnamearray[count($fnamearray) - 1];
        $fileimg = "../addons/ewei_shopv2/data/facetemp/{$_W['uniacid']}/" . $uuid . "." . $itype;
        move_uploaded_file($file['tmp_name'], $fileimg);

        $facemodel = new FaceModel();
        $imgdata = $facemodel->imgToBase64($fileimg);
        $url = "https://aip.baidubce.com/rest/2.0/face/v3/search?access_token=" . $this->facetoken;
        $regdata['image'] = $imgdata;
        $regdata['liveness_control'] = "NORMAL";
        $regdata['group_id_list'] = "group_repeat";
        $regdata['image_type'] = "BASE64";
        $regdata['quality_control'] = "LOW";
        $res = $facemodel->request_post($url, $regdata, 1, 1);
        $rs = json_decode($res, true);
        $result['status'] = 0;
        $result['msg'] = $rs['error_msg'];
        unlink($fileimg);
        if ($rs['error_code'] == 0 && $rs['error_msg'] == "SUCCESS" && $rs['result']['user_list'][0]['score'] >= 80) {
            $shop_member = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_member") . " where `facetoken`='{$rs['result']['user_list'][0]['user_id']}' ");
            if (empty($shop_member)) {
                json_show(1, ["uface_token" => $rs['result']['user_list'][0]['user_id']], "该用户未注册人脸信息");
            } else {
                json_show(0, ["uface_token" => $rs['result']['user_list'][0]['user_id'], "member" => $shop_member], $rs['error_msg']);
            }
        }
        json_show(1, [], "人脸库中未找到该用户");
    }

}

?>
