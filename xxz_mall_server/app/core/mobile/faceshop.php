<?php

//lcj 20200507
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Faceshop_EweiShopV2Page extends AppMobilePage {

    protected $member;

    public function __construct() {
        parent::__construct();
        $this->member = m('member')->getInfo($_W['openid']);
        if ($this->iswxapp) {
            $needbind = false;
            if (empty($_W['shopset']['app']['isclose']) && !empty($_W['shopset']['app']['openbind']) || !empty($_W['shopset']['wap']['open'])) {
                $needbind = true;
            }

            if (!$needbind) {
                return app_error(AppError::$BindNotOpen);
            }
        }
    }

    //根据分类id获取商品列表
    public function categoods() {
        global $_W, $_GPC;
        $data=array();
        $page = intval($_GPC['home']) ? intval($_GPC['home']) : 1;
        $categoryid = $_GPC['categoryid'];
        $cateids = array();
        $check = true;
        while($check){
        	$sql = "SELECT id,parentid FROM " . tablename("ewei_shop_category") . " WHERE `uniacid`='{$_W['uniacid']}' and id='{$categoryid}'";
        	$arr = pdo_fetch($sql);
        	$cateids[] = $arr['id'];
        	if($arr['parentid']>0){
        		$categoryid = $arr['parentid'];
        	}else{
        		$check = false;
        	}
        }
        if ($page <= 1) {
            $page = 1;
        }
        $pageindex = ($page - 1) * pagesize();
        $where = " and pcate in (".implode(",",$cateids).")";
        $where .=" or ccate in (".implode(",",$cateids).")";
        $where .=" or tcate in (".implode(",",$cateids).")";
        $where .=" or ccates in (".implode(",",$cateids).")";
        $where .=" or pcates in (".implode(",",$cateids).")";
        $where .=" or tcates in (".implode(",",$cateids).")";
        $where .=" or cates in (".implode(",",$cateids).")";
        $sql = "SELECT * FROM " . tablename("ewei_shop_goods") . " WHERE `uniacid`='{$_W['uniacid']}' " . $where . " order by id desc limit {$pageindex}," . pagesize();
    	$data['list'] = pdo_fetchall($sql);
    	$sql = "SELECT count(*) FROM " . tablename("ewei_shop_goods") . " WHERE `uniacid`='{$_W['uniacid']}' " . $where . " order by id desc limit {$pageindex}," . pagesize();
    	$data['total'] = pdo_fetchcolumn($sql);
        return json_app(0, $data, '成功');
    }
    
    //获取全部分类以及商品
    public function allgoods(){
    	global $_W, $_GPC;
        $data['catlevel'] = $_W['shopset']['shop']['catlevel']; //0-无分类，1-一级分类，2-二层分类，3-三层分类
        if (intval($_GPC['categoryid']) > 0) {//获取父类下的子分类
            $data['list'] = pdo_fetchall("SELECT * FROM " . tablename("ewei_shop_category") . " where `parentid`='{$_GPC['categoryid']}' and `uniacid`='{$_W['uniacid']}' order by id asc");
            return json_app(1, $data, "fail");
        }
        $categorieslist = pdo_fetchall("SELECT id,name FROM " . tablename("ewei_shop_category") . " where `parentid`=0 and `uniacid`='{$_W['uniacid']}' order by id asc");
       	$allgoods=array();
       	foreach($categorieslist as $categoryarr){
       		$cgoods = array();
	        $cateids = array();
	        $categoryid = $categoryarr['id'];
	        $check = true;
	        while($check){
	        	$sql = "SELECT id,parentid FROM " . tablename("ewei_shop_category") . " WHERE `uniacid`='{$_W['uniacid']}' and id='{$categoryid}'";
	        	$arr = pdo_fetch($sql);
	        	$cateids[] = $arr['id'];
	        	if($arr['parentid']>0){
	        		$categoryid = $arr['parentid'];
	        	}else{
	        		$check = false;
	        	}
	        }
	        if ($page <= 1) {
	            $page = 1;
	        }
	        $pageindex = ($page - 1) * pagesize();
	        $where = " and pcate in (".implode(",",$cateids).")";
	        $where .=" or ccate in (".implode(",",$cateids).")";
	        $where .=" or tcate in (".implode(",",$cateids).")";
	        $where .=" or ccates in (".implode(",",$cateids).")";
	        $where .=" or pcates in (".implode(",",$cateids).")";
	        $where .=" or tcates in (".implode(",",$cateids).")";
	        $where .=" or cates in (".implode(",",$cateids).")";
	        $sql = "SELECT * FROM " . tablename("ewei_shop_goods") . " WHERE `uniacid`='{$_W['uniacid']}' " . $where . " order by id desc limit {$pageindex}," . pagesize();
	    	$goods = pdo_fetchall($sql);
	    	$cgoods['name'] = $categoryarr['name'];
    		$cgoods['foods'] = $goods;
    		$allgoods[] = $cgoods;
       	}
       	$data['list'] = $allgoods;
       return json_app(0, $data, '成功');
    }
    
    //商城登录验证身份
    public function doshoplogin(){
    	global $_W;
        global $_GPC;
        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken=:facetoken  limit 1 ', array(':facetoken' => $_GPC['facetoken']));
        if (!$info['id']) {
            return json_app(-1, $info, "用户token错误");
        }
        return json_app(0, $info, '成功');
    }
    
    public function doshoppay() {
        global $_W;
        global $_GPC;
        $data = array();
        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE facetoken=:facetoken  limit 1 ', array(':facetoken' => $_GPC['facetoken']));
        if (!$info['id']) {
            return json_app(-1, $data, "人脸不存在");
        }
        /*
        $ordersn = $_GPC['ordersn'];
        $uniacid = $_W['uniacid'];

        $openid = $info['openid'];
        $muid = $info['uid'];
        
        $set = m('common')->getSysset(array('shop', 'pay'));

        $order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where ordersn=:ordersn and uniacid=:uniacid limit 1', array(':ordersn' => $ordersn, ':uniacid' => $uniacid));

        if (empty($order)) {
                return json_app(-1, $data,'订单未找到');
        }

                if (empty($set["pay"]["credit"]) && 0 < $order["price"]) {
                      return   json_app(-1, $data,"未开启余额支付!");
                }
                if ($order["price"] < 0) {
                      return   json_app(-1,$data, "金额错误");
                }
                $credits = m("member")->getCredit($openid, "credit2");
                if ($credits < $order["price"]) {
                      return   json_app(-1, $data,"余额不足,请充值");
                }

                $fee = floatval($order["price"]);
                //扣除用户余额操作
                $result = m("member")->setCredit($openid, "credit2", 0 - $fee, array($muid, $_W["shopset"]["shop"]["name"] . "消费" . $fee));
                if (is_error($result)) {
                     return  json_app(-1,$data, $result["message"]);
                }
                
                $time = time();
                $change_data = array();
                $change_data['status'] = 1;
                $change_data['tradestatus'] = 1;
                $change_data['paytime'] = $time;
                pdo_update('ewei_shop_order', $change_data, array('ordersn' => $ordersn));
		*/
              return  json_app(0,$data);
               
    }
    
    /*20210219*/
    public function fetchGoodsinfo(){
    	global $_W;
        global $_GPC;
        $info = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_goods') . ' WHERE id=:goods_id  limit 1 ', array(':goods_id' => $_GPC['goods_id']));
        if (!$info['id']) {
            return json_app(-1, $info, "商品不存在");
        }
        return json_app(0, $info, '成功');
    }
    
    public function getCategory(){
    	global $_W, $_GPC;
    	$data=array();
        $data['list'] = pdo_fetchall("SELECT id,name FROM " . tablename("ewei_shop_category") . " where `parentid`=0 and enabled=1 and `uniacid`='{$_W['uniacid']}' order by id asc");
       return json_app(0, $data, '成功');
    }
    
    public function getCategoryGoods(){
    	global $_W, $_GPC;
       	$allgoods=array();
       	$categoryid = $_GPC['catid'];
       		$cgoods = array();
	        $cateids = array();
	        $check = true;
	        $cateids[] = $categoryid;
	        while($check){
	        	$sql = "SELECT id,name FROM " . tablename("ewei_shop_category") . " WHERE `uniacid`='{$_W['uniacid']}' and parentid='{$categoryid}'";
	        	$arr = pdo_fetch($sql);
	        	if($arr){
	        		$cateids[] = $arr['id'];
	        		$categoryid = $arr['id'];
	        		$cgoods['category_id'] = $arr['id'];
	        		$cgoods['name'] = $arr['name'];
	        	}else{
	        		$check = false;
	        	}
	        }
	        if ($page <= 1) {
	            $page = 1;
	        }
	        $pageindex = ($page - 1) * pagesize();
	        $where = " and pcate in (".implode(",",$cateids).")";
	        $where .=" or ccate in (".implode(",",$cateids).")";
	        $where .=" or tcate in (".implode(",",$cateids).")";
	        $where .=" or ccates in (".implode(",",$cateids).")";
	        $where .=" or pcates in (".implode(",",$cateids).")";
	        $where .=" or tcates in (".implode(",",$cateids).")";
	        $where .=" or cates in (".implode(",",$cateids).")";
	     //   $sql = "SELECT thumb as cover,title as name,id as goodsid FROM " . tablename("ewei_shop_goods") . " WHERE `uniacid`='{$_W['uniacid']}' " . $where . " order by id desc limit {$pageindex}," . pagesize();
	     	$sql = "SELECT thumb as cover,title as name,id as goodsid,marketprice FROM " . tablename("ewei_shop_goods") . " WHERE `uniacid`='{$_W['uniacid']}' " . $where . " order by id desc";
	    	$goods = pdo_fetchall($sql);
	    	foreach($goods as &$go){
	    		$go['cover'] = tomedia($go['cover']);
	    		$sql = "SELECT count(*) as count FROM " . tablename("ewei_shop_goods_option") . " WHERE goodsid=".$go['goodsid']." and `uniacid`='{$_W['uniacid']}' order by id desc";
	    		$count = pdo_fetchcolumn($sql);
	    		$go['count'] = $count;
	    	}
	    	unset($go);
	    	$cgoods['child'] = $goods;
    		$allgoods[] = $cgoods;
       		return json_app(0, $allgoods, '成功');
    }
    
    public function getGuige(){
    	global $_W, $_GPC;
    	$data=array();
    	$goods_id = $_GPC['goods_id'];
    	$sql = "SELECT thumb as cover,title as name,id as goodsid,marketprice FROM " . tablename("ewei_shop_goods") . " WHERE `uniacid`='{$_W['uniacid']}' and id=".$goods_id;
    	$goods_info = pdo_fetch($sql);
    	$goods_info['markid'] = 'g'.$goods_info['goodsid'];
    	$goods_info['cover'] = tomedia($goods_info['cover']);
    	$goods_info['quantity'] = 1;
    	$goods_info['unitprice'] = $goods_info['marketprice'];
    	$sql = "SELECT id,goodsid,title,marketprice,specs FROM " . tablename("ewei_shop_goods_option") . " WHERE goodsid=".$goods_id." and `uniacid`='{$_W['uniacid']}' order by id desc";
	    $goods_option = pdo_fetchall($sql);
	    $data['goods_info'] = $goods_info;
	    if(!empty($goods_option)){
		    foreach($goods_option as &$gop){
		    	$gop['cover'] = tomedia($goods_info['cover']);
		    	$gop['name'] = $goods_info['name'].'('.$gop['title'].')';
		    	$gop['markid'] = 'g'.$goods_info['goodsid'].'op'.$gop['id'];
    			$gop['quantity'] = 1;
    			$gop['unitprice'] = $gop['marketprice'];
    			$gop['specs'] = $gop['specs'];
		    }
		    unset($gop);
		}
	    $data['goods_option'] = $goods_option;
	    
	    $sql = "SELECT id,title FROM " . tablename("ewei_shop_goods_spec") . " WHERE goodsid=".$goods_id." and `uniacid`='{$_W['uniacid']}' order by id asc";
	    $arrs = pdo_fetchall($sql);
	    $spec = array();
	    if(!empty($arrs)){
	    	foreach($arrs as $k=>$v){
	    		$sql = "SELECT id,title FROM " . tablename("ewei_shop_goods_spec_item") . " WHERE specid=".$v['id']." and `uniacid`='{$_W['uniacid']}' order by id asc";
	    		$items = pdo_fetchall($sql);
	    		$spec[$k]['title'] = $v['title'];
	    		$spec[$k]['items'] = $items;
	    	}
	    }
	    $data['goods_spec'] = $spec;
	    return json_app(0, $data, '成功');
    }
    
    public function getGoodInfo(){
    	global $_W, $_GPC;
    	$data=array();
    	$goods_id = $_GPC['goods_id'];
    	$sql = "SELECT total,thumb as cover,title as name,id as goodsid,marketprice,productprice,sales,salesreal,description FROM " . tablename("ewei_shop_goods") . " WHERE `uniacid`='{$_W['uniacid']}' and id=".$goods_id;
    	$goods_info = pdo_fetch($sql);
    	$goods_info['markid'] = 'g'.$goods_info['goodsid'];
    	$goods_info['cover'] = tomedia($goods_info['cover']);
    	$goods_info['quantity'] = 1;
    	$goods_info['salenum'] = $goods_info['sales']+$goods_info['salesreal'];
    	$goods_info['unitprice'] = $goods_info['marketprice'];
	    $data['goods_info'] = $goods_info;
	    return json_app(0, $data, '成功');
    }
}
