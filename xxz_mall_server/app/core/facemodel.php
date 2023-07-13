<?php

class FaceModel {

    function gettoken($grant_type, $client_id, $client_secret) {
        session_start();
        if (!empty($_SESSION['facetoken']) && time() <= ($_SESSION['expires_time'] + $_SESSION['expires_in'])) {
            return $_SESSION['facetoken'];
        }
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $post_data['grant_type'] = $grant_type;
        $post_data['client_id'] = $client_id;
        $post_data['client_secret'] = $client_secret;
        $time = time();
        $res = json_decode($this->request_post($url, $post_data, 1), true);
        if (!empty($res['access_token'])) {
            $_SESSION['facetoken'] = $res['access_token'];
            $_SESSION['expires_time'] = $time;
            $_SESSION['expires_in'] = $res['expires_in'];
            return $res['access_token'];
        }
        return "";
    }

    function request_post($url = '', $param = '', $utype = 0, $isjson = 0) {
        $postUrl = $url;
        $curlPost = $param;
        $curl = curl_init(); //初始化curl
        curl_setopt($curl, CURLOPT_URL, $postUrl); //抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0); //设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1); //post提交方式
        if (intval($utype) > 0) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if (intval($isjson) > 0) {
            $curlPost = json_encode($param);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($curl); //运行curl
        curl_close($curl);
        return $data;
    }

    function imgToBase64($img_file) {
        $img_base64 = '';
        if (file_exists($img_file)) {
            $app_img_file = $img_file; // 图片路径
            $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等
            $fp = fopen($app_img_file, "r"); // 图片是否可读权限

            if ($fp) {
                $filesize = filesize($app_img_file);
                $content = fread($fp, $filesize);
                $file_content = chunk_split(base64_encode($content)); // base64编码
                switch ($img_info[2]) {           //判读图片类型
                    case 1: $img_type = "gif";
                        break;
                    case 2: $img_type = "jpg";
                        break;
                    case 3: $img_type = "png";
                        break;
                }
                //$img_base64 = 'data:image/' . $img_type . ';base64,' . $file_content; //合成图片的base64编码
                $img_base64 = $file_content; //合成图片的base64编码
            }
            fclose($fp);
        }
        return $img_base64;
    }

}

?>
