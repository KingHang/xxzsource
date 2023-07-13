<?php



//lcj 20190826
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Index_EweiShopV2Page extends AppMobilePage {

    protected $member;

    public function __construct() {
        global $_W;
        parent::__construct();
        global $_GPC;
        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid=:openid or openid_wa=:openid and uniacid=:uniacid   limit 1', array(':openid' => $_GPC['openid'], ':uniacid' => $_W[uniacid]));

        $member = set_medias($member, 'avatar');
        if(!empty($member['status']) && !empty($member['isagent'])){
            $agentLevel = p('commission')->getLevel($member['openid']);
            $set = p('commission')->getSet();
            if (empty($agentLevel['id'])) {
                $agentLevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
            }
            $member['levelname']=$agentLevel['levelname'];
        }
        $this->member = $member;
    }

    //微信h5sdk
    public function wechatjssdk(){
          global $_W;
          global $_GPC;
          $account_api = WeAccount::create($_W['acid']);
          $jssdkconfig = $account_api->getJssdkConfig($_GPC['url']);
          



          return app_json(array('jssdkconfig'=>$jssdkconfig));
    }



    public function getmember() {
        global $_W;
        global $_GPC;
        $member = $this->member;


        return app_json($member);
    }
      public function getordernum() {
            global $_W;
            global $_GPC;
            $member = $this->member;

            //获取用户的订单信息
            $ordernum[0] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupbuy=1 and status=0  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            $ordernum[1] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupbuy=1 and status=1  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            $ordernum[2] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupbuy=1 and status=2  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));
            $ordernum[3] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and isgroupbuy=1 and status=3  ', array(':openid' => $member['openid'], ':uniacid' => $_W[uniacid]));

            return app_json(array('ordernum'=>$ordernum));
      }



      /**
       * 创建订单
       */

      public function create_order()
      {
            global $_W;
            global $_GPC;
            $openid = empty($_W['openid']) ? $_W['openid'] : $_W['openid'];
            if (!isset($openid) || empty($openid)) {
                  $openid = $_GPC['openid'];
            }

            if (empty($openid)) {
                  return app_error(AppError::$ParamsError);
            }

            $uniacid = $_W['uniacid'];
            $isverify = false;
            $goodid = intval($_GPC['id']);
            $groups_option_id = intval($_GPC['option_id']);
            $ladder_id = intval($_GPC['ladder_id']);
            $is_ladder = 0;
            $type = $_GPC['type'];
            $heads = intval($_GPC['heads']);
            $teamid = intval($_GPC['teamid']);
            $originalOpenid = substr($openid, strripos($openid, '_') + 1);

            if (!$originalOpenid) {
                  return app_error(-1, '授权后才可进行操作');
            }

            $member = m('member')->getMember($openid);
            $credit = array();
            $ladder = array();
            $groups_option = array();
            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . '
				where id = :id and uniacid = :uniacid and deleted = 0 order by displayorder desc', array(':id' => $goodid, ':uniacid' => $uniacid));

            if (empty($goods['status'])) {
                  return app_error(1, '您选择的商品已经下架，请浏览其他商品或联系商家！');
            }

            $groups_option = pdo_get('ewei_shop_groupbuy_goods_option', array('id' => $groups_option_id));
            $ordernum = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' as o
			where openid = :openid and status >= :status and goodid = :goodid and uniacid = :uniacid and is_team = 1', array(':openid' => $openid, ':status' => 0, ':goodid' => $goodid, ':uniacid' => $uniacid));
            if (!empty($goods['purchaselimit']) && $goods['purchaselimit'] <= $ordernum) {
                  return app_error(1, '您已到达此商品购买上限，请浏览其他商品或联系商家！');
            }

            if ($goods['is_ladder'] == 1 && $type == 'groups') {
                  if (empty($ladder_id) && empty($teamid)) {
                        return app_error(1, '缺少阶梯团ID');
                  }

                  $is_ladder = 1;
                  $ladder = pdo_get('ewei_shop_groupbuy_ladder', array('id' => $ladder_id));
                  $sql = 'select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' as o
			where openid = :openid and status > :status and goodid = :goodid and uniacid = :uniacid  and ladder_id=:ladder_id';
                  $params = array(':openid' => $openid, ':status' => 0, ':goodid' => $goodid, ':uniacid' => $uniacid, ':ladder_id' => $ladder_id);

                  if (!empty($teamid)) {
                        $sql .= ' and teamid = :team_id';
                        $params['team_id'] = $teamid;
                  }

                  $ladder_ordernum = pdo_fetchcolumn($sql, $params);
            }

            $order = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_order') . '
					where goodid = :goodid and status >= 0 and  openid = :openid and uniacid = :uniacid and success = 0 and deleted = 0 ', array(':goodid' => $goodid, ':openid' => $openid, ':uniacid' => $uniacid));
            if ($order && $order['status'] == 0) {
//                  return app_error(1, '您的订单已存在，请尽快完成支付！');
            }

            if ($order && $order['is_team'] == 1 && $type != 'single' && $order['status'] == 1) {
//                  return app_error(1, '您已经参与了该团，请等待拼团结束后再进行购买！');
            }

            if ($goods['is_ladder'] == 0) {
                  $teamordernum = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' as o
			            where teamid = :teamid and status > :status and goodid = :goodid and uniacid = :uniacid and is_team = 1', array(':teamid' => $teamid, ':status' => 0, ':goodid' => $goodid, ':uniacid' => $uniacid));
                  if ($order && $order['groupnum'] <= $teamordernum && $order['is_team'] == 1 && $type != 'single') {
                        return app_error(1, '该团人数已达上限，请浏览其他商品或联系商家！(1)');
                  }
            }
            else {
                  if ($order && $ladder['ladder_num'] <= $ladder_ordernum && $order['is_team'] == 1 && $type != 'single') {
                        return app_error(1, '该团人数已达上限，请浏览其他商品或联系商家！(2)');
                  }
            }

            if (!empty($teamid)) {
                  $orders = pdo_fetchall('select * from ' . tablename('ewei_shop_groupbuy_order') . '
					where teamid = :teamid and uniacid = :uniacid ', array(':teamid' => $teamid, ':uniacid' => $uniacid));

                  foreach ($orders as $key => $value) {
                        if ($orders && $value['success'] == -1) {
                              return app_error(1, '该活动已过期，请浏览其他商品或联系商家！');
                        }

                        if ($orders && $value['success'] == 1) {
                              return app_error(1, '该活动已结束，请浏览其他商品或联系商家！');
                        }
                  }

                  if ($goods['is_ladder'] == 0) {
                        $num = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' where teamid = :teamid and status > :status and goodid = :goodid and uniacid = :uniacid ', array(':teamid' => $teamid, ':status' => 0, ':goodid' => $goods['id'], ':uniacid' => $uniacid));

                        if ($goods['groupnum'] <= $num) {
                              return app_error(1, '该活动已成功组团，请浏览其他商品或联系商家！');
                        }
                  }
                  else {
                        $num = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' where teamid = :teamid and status > :status and goodid = :goodid and uniacid = :uniacid and ladder_id = :ladder_id', array(':teamid' => $teamid, ':status' => 0, ':goodid' => $goods['id'], ':uniacid' => $uniacid, ':ladder_id' => $ladder_id));

                        if ($ladder['ladder_num'] <= $num) {
                              return app_error(1, '该活动已成功组团，请浏览其他商品或联系商家！');
                        }
                  }
            }

            if ($type == 'groups' && $goods['more_spec'] == 0 && $is_ladder == 0) {
                  if ($goods['stock'] <= 0) {
                        return app_error(1, '您选择的商品库存不足，请浏览其他商品或联系商家！');
                  }

                  $price = $goods['groupbuyprice'];
                  $groupnum = intval($goods['groupnum']);
                  $is_team = 1;
            }
            else {
                  if ($type == 'groups' && $goods['more_spec'] == 1 && $is_ladder == 0) {
                        if ($groups_option['stock'] <= 0) {
                              return app_error(1, '您选择的商品库存不足，请浏览其他商品或联系商家！(1)');
                        }

                        $goods['groupbuyprice'] = $groups_option['price'];
                        $price = $groups_option['price'];
                        $groupnum = intval($goods['groupnum']);
                        $is_team = 1;
                  }
                  else {
                        if ($type == 'groups' && $goods['more_spec'] == 0 && $is_ladder == 1) {
                              $goods['groupbuyprice'] = $ladder['ladder_price'];
                              $price = $ladder['ladder_price'];
                              $groupnum = intval($ladder['ladder_num']);
                              $is_team = 1;
                        }
                        else {
                              if ($type == 'single' && $goods['more_spec'] == 0) {
                                    if ($goods['stock'] <= 0) {
                                          return app_error(1, '您选择的商品库存不足，请浏览其他商品或联系商家！');
                                    }

                                    $goods['groupbuyprice'] = $goods['singleprice'];
                                    $price = $goods['singleprice'];
                                    $groupnum = 1;
                                    $is_team = 0;
                                    $teamid = 0;
                              }
                              else {
                                    if ($groups_option['stock'] <= 0) {
                                          return app_error(1, '您选择的商品库存不足，请浏览其他商品或联系商家！(2)');
                                    }

                                    $price = $groups_option['single_price'];
                                    $groupnum = 1;
                                    $is_team = 0;
                                    $teamid = 0;
                              }
                        }
                  }
            }



            $goods_price = $price;
            $set = pdo_fetch('select discount,headstype,headsmoney,headsdiscount from ' . tablename('ewei_shop_groupbuy_set') . '
					where uniacid = :uniacid ', array(':uniacid' => $uniacid));
            if (!empty($set['discount']) && $heads == 1) {
                  if (!empty($goods['discount'])) {
                        if (!empty($goods['headstype'])) {
                              if (0 < $goods['headsdiscount']) {
                                    if ($goods['headsdiscount'] == 100) {
                                          $goods['headsmoney'] = $goods['groupbuyprice'];
                                    }
                                    else {
                                          $goods['headsmoney'] = $goods['groupbuyprice'] - price_format($goods['groupbuyprice'] * $goods['headsdiscount'] / 100, 2);
                                    }
                              }
                        }
                  }
                  else {
                        if (empty($set['headstype'])) {
                              $goods['headsmoney'] = $set['headsmoney'];
                        }
                        else {
                              if (0 < $set['headsdiscount']) {
                                    if ($set['headsdiscount'] == 100) {
                                          $goods['headsmoney'] = $goods['groupbuyprice'];
                                    }
                                    else {
                                          $goods['headsmoney'] = $goods['groupbuyprice'] - price_format($goods['groupbuyprice'] * $set['headsdiscount'] / 100, 2);
                                    }
                              }
                        }

                        $goods['headstype'] = $set['headstype'];
                        $goods['headsdiscount'] = $set['headsdiscount'];
                  }

                  if ($goods['groupbuyprice'] < $goods['headsmoney']) {
                        $goods['headsmoney'] = $goods['groupbuyprice'];
                  }

                  $price = $price - $goods['headsmoney'];

                  if ($price < 0) {
                        $price = 0;
                  }
            }
            else {
                  $goods['headsmoney'] = 0;
            }

            if (!empty($goods['isverify'])) {
                  $isverify = true;
                  $goods['freight'] = 0;
                  $storeids = array();
                  $merchid = 0;

                  if (!empty($goods['storeids'])) {
                        $merchid = $goods['merchid'];
                        $storeids = array_merge(explode(',', $goods['storeids']), $storeids);
                  }

                  if (empty($storeids)) {
                        if (0 < $merchid) {
                              $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                        }
                        else {
                              $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                        }
                  }
                  else if (0 < $merchid) {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                  }
                  else {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                  }

                  $verifycode = 'PT' . random(8, true);

                  while (1) {
                        $count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_groupbuy_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(':verifycode' => $verifycode, ':uniacid' => $_W['uniacid']));

                        if ($count <= 0) {
                              break;
                        }

                        $verifycode = 'PT' . random(8, true);
                  }

                  $verifynum = !empty($goods['verifytype']) ? $verifynum = $goods['verifynum'] : 1;
            }
            else {
                  $address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . '
				where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(':uniacid' => $uniacid, ':openid' => $openid));
            }

            $creditdeduct = pdo_fetch('SELECT creditdeduct,groupsdeduct,credit,groupsmoney FROM' . tablename('ewei_shop_groupbuy_set') . 'WHERE uniacid = :uniacid ', array(':uniacid' => $uniacid));

            if (intval($creditdeduct['creditdeduct'])) {
                  if (intval($creditdeduct['groupsdeduct'])) {
                        if (0 < $goods['deduct']) {
                              $credit['deductprice'] = round(intval($member['credit1']) * $creditdeduct['groupsmoney'], 2);

                              if ($price <= $credit['deductprice']) {
                                    $credit['deductprice'] = $price;
                              }

                              if ($goods['deduct'] <= $credit['deductprice']) {
                                    $credit['deductprice'] = $goods['deduct'];
                              }

                              $credit['credit'] = floor($credit['deductprice'] / $creditdeduct['groupsmoney']);

                              if ($credit['credit'] < 1) {
                                    $credit['credit'] = 0;
                                    $credit['deductprice'] = 0;
                              }

                              $credit['deductprice'] = $credit['credit'] * $creditdeduct['groupsmoney'];
                        }
                        else {
                              $credit['deductprice'] = 0;
                        }
                  }
                  else {
                        $sys_data = m('common')->getPluginset('sale');

                        if (0 < $goods['deduct']) {
                              $credit['deductprice'] = round(intval($member['credit1']) * $sys_data['money'], 2);

                              if ($price <= $credit['deductprice']) {
                                    $credit['deductprice'] = $price;
                              }

                              if ($goods['deduct'] <= $credit['deductprice']) {
                                    $credit['deductprice'] = $goods['deduct'];
                              }

                              $credit['credit'] = floor($credit['deductprice'] / $sys_data['money']);

                              if ($credit['credit'] < 1) {
                                    $credit['credit'] = 0;
                                    $credit['deductprice'] = 0;
                              }

                              $credit['deductprice'] = $credit['credit'] * $sys_data['money'];
                        }
                        else {
                              $credit['deductprice'] = 0;
                        }
                  }
            }

            $diyform_plugin = p('diyform');
            $formInfo = false;
            $set_config = false;
            $diyform_id = 0;
            $fields = array();
            $f_data = array();

            if ($diyform_plugin) {
                  $set_config = $diyform_plugin->getSet();
                  $groups_diyform_open = $set_config['groups_diyform_open'];

                  if ($groups_diyform_open == 1) {
                        $diyform_id = $set_config['groups_diyform'];

                        if (!empty($diyform_id)) {
                              $formInfo = $diyform_plugin->getDiyformInfo($diyform_id);
                              $fields = $formInfo['fields'];
                              $diyform_data = array();
                              $f_data = $diyform_plugin->getDiyformData($diyform_data, $fields, $member);
                        }
                  }
            }

            $appDatas = array();

            if ($diyform_plugin) {
                  $appDatas = $diyform_plugin->wxApp($fields, $f_data, $this->member);
                  $fields = $appDatas['fields'];
            }

            $ordersn = m('common')->createNO('groups_order', 'orderno', 'XP');

            if ($_W['ispost'] == true) {
                  if (empty($_GPC['aid']) && !$isverify) {
                        return app_error(1, '请选择收货地址！');
                  }


                  if ($isverify) {
                        if (empty($_GPC['realname']) || empty($_GPC['mobile'])) {
                              return app_error(1, '联系人或联系电话不能为空！');
                        }
                  }

                  if (0 < intval($_GPC['aid']) && !$isverify) {
                        $order_address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid   limit 1', array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => intval($_GPC['aid'])));

                        if (empty($order_address)) {
                              return app_error(1, '未找到地址');
                        }

                        if (empty($order_address['province']) || empty($order_address['city'])) {
                              return app_error(1, '地址请选择省市信息');
                        }
                  }

                  $data = array('uniacid' => $_W['uniacid'], 'goods_option_id' => $groups_option['id'],'groupnum' => $groupnum, 'openid' => $openid, 'paytime' => 0, 'orderno' => $ordersn, 'credit' => $_GPC['deduct'] == 'true' ? $_GPC['deduct'] : 0, 'creditmoney' => $_GPC['deduct'] == 'true' ? $credit['deductprice'] : 0, 'price' => $price, 'freight' => $goods['freight'], 'status' => 0, 'goodid' => $goodid, 'is_ladder' => $is_ladder, 'ladder_id' => $ladder_id, 'more_spec' => $goods['more_spec'], 'teamid' => $teamid, 'is_team' => $is_team, 'heads' => $heads, 'discount' => !empty($heads) ? $goods['headsmoney'] : 0, 'addressid' => intval($_GPC['aid']), 'address' => iserializer($order_address), 'message' => trim($_GPC['message']), 'realname' => $isverify ? trim($_GPC['realname']) : '', 'mobile' => $isverify ? trim($_GPC['mobile']) : '', 'endtime' => $goods['endtime'], 'isverify' => intval($goods['isverify']), 'verifytype' => intval($goods['verifytype']), 'verifycode' => !empty($verifycode) ? $verifycode : 0, 'verifynum' => !empty($verifynum) ? $verifynum : 1, 'createtime' => TIMESTAMP, 'source' => 1, 'goods_price' => $goods_price);

                  if ($diyform_plugin) {
                        $diydata = $_GPC['diydata'];

                        if (is_string($diydata)) {
                              $diyformdatastring = htmlspecialchars_decode(str_replace('\\', '', $diydata));
                              $diydata = @json_decode($diyformdatastring, true);
                        }

                        if (is_array($diydata) && !empty($formInfo)) {
                              $diyform_data = $diyform_plugin->getInsertData($fields, $diydata, true);
                              $idata = $diyform_data['data'];
                              $data['diyformfields'] = $diyform_plugin->getInsertFields($fields);
                              $data['diyformdata'] = $idata;
                              $data['diyformid'] = $formInfo['id'];
                        }
                  }
                  if ($goods['gecoupon']==1&&$goods['gecoupon_need']>0){
                        $gecoupon = m('member')->getCredit($member['openid'],'gecoupon');
                        $member['gecoupon'] = $gecoupon;
                        if ($member['gecoupon']<$goods['gecoupon_need']){
                              return app_error(1, '开团券不足');
                        }
                        m('member')->setCredit($member['openid'], 'gecoupon', -$goods['gecoupon_need'], array(0, '消费拼团开团消耗券点'));

                  }

                  $order_insert = pdo_insert('ewei_shop_groupbuy_order', $data);

                  if (!$order_insert) {
                        return app_error(1, '生成订单失败！');
                  }



                  $orderid = pdo_insertid();
                  if (empty($teamid) && $type == 'groups') {
                        pdo_update('ewei_shop_groupbuy_order', array('teamid' => $orderid), array('id' => $orderid));
                  }

                  if (!empty($goods['more_spec'])) {
                        $groups_order_goods = array('uniacid' => $_W['uniacid'], 'goods_id' => $groups_option['goodsid'], 'groups_goods_id' => $groups_option['groups_goods_id'], 'groups_goods_option_id' => $groups_option['id'], 'groups_order_id' => $orderid, 'price' => $type == 'groups' ? $groups_option['price'] : $groups_option['single_price'], 'option_name' => $groups_option['title'], 'create_time' => TIMESTAMP);
                        pdo_insert('ewei_shop_groupbuy_order_goods', $groups_order_goods);
                  }


                  //插入普通商城订单
                  $oridata = array('uniacid' => $_W['uniacid'],
                      'openid' => $openid,
                      'ordersn' => $ordersn,
                      'price' => $price,
                      'goodsprice' => $goods_price,
                      'dispatchprice' => $goods['freight'],
                      'status' => 0,
                      'addressid' => intval($_GPC['aid']),
                      'address' => iserializer($order_address),
                      'isverify' => intval($goods['isverify']),
                      'verifytype' => intval($goods['verifytype']),
                      'verifycodes' => !empty($verifycode) ? $verifycode : 0,
                      'createtime' => TIMESTAMP,
                      'remark'=>$_GPC['remark'],
                      'isgroupearn'=>2,//groupbuy 订单
                      'agentid'=>$member['agentid'],
                      'agentarea'=>$member['agentarea'],
                  );

                  $order_insert = pdo_insert('ewei_shop_order', $oridata);

                  $oriorderid = pdo_insertid();



                  $pvprice = pdo_fetchcolumn('select productprice from ' . tablename('ewei_shop_goods') . ' where id=:id    limit 1', array(':id' => $goods['gid']));
                  $groups_order_goods = array(
                      'uniacid' => $_W['uniacid'],
                      'goodsid' => $goodid,
                      'groupearngoodsid' => $goods['gid'],
                      'total' => 1,
                      'orderid' => $oriorderid,
                      'optionid'=>$groups_option['id'],
                      'optionname'=>$groups_option['title'],
                      'price' => $goods['groupbuyprice'],
                      'pvprice' => $pvprice,
                      'realprice' => $goods['groupbuyprice'],
                      'createtime' => TIMESTAMP,
                  );
                  pdo_insert('ewei_shop_order_goods', $groups_order_goods);

                  pdo_update('ewei_shop_groupbuy_order', array('oriorderid' => $oriorderid), array('id' => $orderid));
                  p('commission')->groupbuycalculate($oriorderid);




                  $order = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_order') . '
						where id = :id and uniacid = :uniacid ', array(':id' => $orderid, ':uniacid' => $uniacid));
                  return app_json(array('teamid' => empty($teamid) ? $order['teamid'] : $teamid, 'orderid' => $orderid));
            }


            $new_goods = $goods;
            $new_goods['thumb'] = tomedia($goods['thumb']);


            if ($goods['more_spec'] == 1) {
                  if ($type == 'single') {
                        $new_goods['price'] = $groups_option['single_price'];
                  }
                  else {
                        $new_goods['price'] = $groups_option['price'];
                  }

                  $new_goods['spec_name'] = $groups_option['title'];
            }
            else {
                  $new_goods['price'] = $goods['groupbuyprice'];
                  $new_goods['spec_name'] = '';
            }

            $data = array('is_verify' => $isverify ? 1 : 0, 'is_ladder' => $is_ladder, 'is_more_spec' => $goods['more_spec'], 'type' => $type, 'teamid' => $teamid, 'heads' => $heads, 'headsmoney' => $goods['headsmoney'], 'credit' => $credit, 'ladder' => $ladder, 'goods' => $new_goods, 'stores' => $stores, 'address' => $address, 'freight' => $goods['freight'], 'price' => round(floatval($price + $goods['freight']), 2), 'f_data' => $appDatas['f_data'], 'fields' => $appDatas['fields']);
            return app_json(array('data' => $data));
      }
      public function orderdetail()
      {
            global $_W;
            global $_GPC;
            $openid = $_W['openid'];
            $uniacid = $_W['uniacid'];
            $orderid = intval($_GPC['orderid']);
            $order = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_order') . '
				where openid=:openid  and uniacid=:uniacid and id = :orderid order by createtime desc ', array(':uniacid' => $uniacid, ':openid' => $openid, ':orderid' => $orderid));
            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . '
					where id = :id and status = :status and uniacid = :uniacid and deleted = 0 order by displayorder desc', array(':id' => $order['goodid'], ':uniacid' => $uniacid, ':status' => 1));
            $goods = set_medias($goods, 'thumb');

            if (!empty($order['isverify'])) {
                  $storeids = array();
                  $merchid = 0;

                  if (!empty($goods['storeids'])) {
                        $merchid = $goods['merchid'];
                        $storeids = array_merge(explode(',', $goods['storeids']), $storeids);
                  }

                  if (empty($storeids)) {
                        if (0 < $merchid) {
                              $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                        }
                        else {
                              $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                        }
                  }
                  else if (0 < $merchid) {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                  }
                  else {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                  }

                  $verifytotal = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_verify') . ' where orderid = :orderid and openid = :openid and uniacid = :uniacid and verifycode = :verifycode ', array(':orderid' => $order['id'], ':openid' => $order['openid'], ':uniacid' => $order['uniacid'], ':verifycode' => $order['verifycode']));

                  if ($order['verifytype'] == 0) {
                        $verify = pdo_fetch('select isverify from ' . tablename('ewei_shop_groupbuy_verify') . ' where orderid = :orderid and openid = :openid and uniacid = :uniacid and verifycode = :verifycode ', array(':orderid' => $order['id'], ':openid' => $order['openid'], ':uniacid' => $order['uniacid'], ':verifycode' => $order['verifycode']));
                  }

                  $verifynum = $order['verifynum'] - $verifytotal;

                  if ($verifynum < 0) {
                        $verifynum = 0;
                  }
            }
            else {
                  $address = false;

                  if (!empty($order['addressid'])) {
                        $address = iunserializer($order['address']);

                        if (!is_array($address)) {
                              $address = pdo_fetch('select * from  ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
                        }
                  }
            }

            if (!empty($order['diyformfields']) && !empty($order['diyformdata'])) {
                  $order_fields = iunserializer($order['diyformfields']);
                  $order_data = iunserializer($order['diyformdata']);
            }

            $newFields = array();
            if (is_array($order_fields) && !empty($order_fields)) {
                  foreach ($order_fields as $k => $v) {
                        $v['diy_type'] = $k;
                        $newFields[] = $v;
                        if ($v['data_type'] == 5 && !empty($order_data[$k]) && is_array($order_data[$k])) {
                              $order_data[$k] = set_medias($order_data[$k]);
                        }
                  }
            }

            $order['diyformfields'] = empty($newFields) ? array() : $newFields;
            $order['diyformdata'] = empty($order_data) ? array() : $order_data;
            $carrier = @iunserializer($order['carrier']);
            if (!is_array($carrier) || empty($carrier)) {
                  $carrier = false;
            }

            $oriorder = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $order['oriorderid'], ':uniacid' => $_W[uniacid]));

            $order['oriorder'] = $oriorder;

            $order['status_supplement'] = '';
            $order['residualtime'] = '';

            switch ($oriorder['status']) {
                  case '-1':
                        $order['status_str'] = '交易关闭';
                        break;

                  case '0':
                        if ($order['paytype'] == 3) {
                              $order['status_str'] = '货到付款，等待发货';
                        }
                        else {
                              $order['status_str'] = '等待付款';
                        }

                        break;

                  case '1':
                        if ($order['iswin']==-1) {
                              $order['status_str'] = '拼团未中奖';
                              $order['status_supplement'] = '您的付款将会在24小时内原路退回账户';
                        } else {
                              if ($order['success']==1){
                                    if ($order['iswin']==1) {
                                          $order['status_str'] = '待发货';
                                          $order['status_supplement'] = '拼团成功';
                                    } else {
                                          $order['status_str'] = '支付成功，拼团完成';
                                    }
                              } elseif ($order['success']==-1){
                                    $order['status_str'] = '拼团失败';
                                    $order['status_supplement'] = '您的付款将会在24小时内原路退回账户';
                              } else {
                                    $order['status_str'] = '拼团中';
                                    $order['residualtime'] = $order['paytime'] + $goods['endtime'] * 60 * 60 - time();
                              }
                        }
                        break;

                  case '2':
                        $order['status_str'] = '拼团成功，已发货';
                        break;

                  case '3':
                        $order['status_str'] = '交易完成';
                        break;

            }

            if (0 < $order['refundstate']) {
                  $order['refundtext'] = '商品维权中';
            }
            else {
                  $order['refundtext'] = '已维权';
            }

            $spec = array();

            if ($order['more_spec'] == 1) {
                  $order['option'] = pdo_get('ewei_shop_groupbuy_order_goods', array('groups_order_id' => $order['id']));
            }

            if ($order['is_ladder'] == 1) {
                  $order['ladder'] = pdo_get('ewei_shop_groupbuy_ladder', array('id' => $order['ladder_id']));
            }

            $order['createtime'] = date('Y-m-d H:i:s', $order['createtime']);
            $order['paytime'] = $order['paytime'] ? date('Y-m-d H:i:s', $order['paytime']) : '';
            $order['sendtime'] = $order['sendtime'] ? date('Y-m-d H:i:s', $order['sendtime']) : '';
            $order['finishtime'] = $order['finishtime'] ? date('Y-m-d H:i:s', $order['finishtime']) : '';
            $money = price_format($order['price'] - $order['creditmoney'] + $order['freight'], 2);
            $order['money'] = 0 < $money ? $money : 0;
//            $express = m('util')->getExpressList($order['express'], $order['expresssn']);
            $express =array();
            $goodRefund = false;
            $groupsSet = pdo_fetch('select goodsid,refundday from ' . tablename('ewei_shop_groupbuy_set') . 'where uniacid = :uniacid ', array(':uniacid' => $uniacid));

            if (in_array($order['goodid'], explode(',', $groupsSet['goodsid']))) {
                  $goodRefund = true;
            }
            switch ($order['pay_type']){
                  case 'wechat':
                        $order['pay_str'] = '微信支付';
                        break;
                  case 'credit':
                        $order['pay_str'] = '余额支付';
                        break;
            }

            //已有拼团情况
            $teams = pdo_fetchall('select o.paytime,o.id,o.goodid,o.teamid,m.nickname,m.realname,m.mobile,m.avatar,g.endtime,g.groupnum,g.thumb_url from ' . tablename('ewei_shop_groupbuy_order') . ' as o
				left join ' . tablename('ewei_shop_member') . ' as m on m.openid=o.openid and m.uniacid =  o.uniacid
				left join ' . tablename('ewei_shop_groupbuy_goods') . ' as g on g.id = o.goodid
				where o.goodid = :goodid and o.uniacid = :uniacid  and o.deleted = 0 and o.teamid = :teamid and o.paytime > 0   limit 10 ', array(':teamid' => $order['teamid'],':goodid' => $goods['id'], ':uniacid' => $_W['uniacid']));




            return app_json(array('teams' => $teams,'express' => $express, 'goodRefund' => $goodRefund, 'order' => $order, 'address' => $address, 'store' => $stores, 'verifytotal' => $verifytotal, 'verifynum' => $verifynum, 'carrier' => $carrier, 'verify' => $verify, 'goods' => $goods));

      }
      public function groupdetail(){
            global $_W;
            global $_GPC;
//            $openid = $_W['openid'];
            $uniacid = $_W['uniacid'];
            $orderid = intval($_GPC['orderid']);
            $order = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_order') . '
				where 1  and uniacid=:uniacid and id = :orderid order by createtime desc ', array(':uniacid' => $uniacid,  ':orderid' => $orderid));
            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . '
					where id = :id and status = :status and uniacid = :uniacid and deleted = 0 order by displayorder desc', array(':id' => $order['goodid'], ':uniacid' => $uniacid, ':status' => 1));
            $goods = set_medias($goods, 'thumb');

            $goods['thumb2'] = iunserializer($goods['thumb_url']);
            $goods['images'][0]['src']=$goods['thumb'];
           /* foreach ($goods['thumb2'] as $good) {
                  $img['src'] = tomedia($good);
                  $goods['images'][] = $img;
            }*/
            $specs = array();

            if (!empty($goods['more_spec'])) {
                  $specArr = pdo_getall('ewei_shop_goods_spec', array('goodsid' => $goods['gid'], 'uniacid' => $_W['uniacid']), array('id', 'title'), '', array('displayorder' => 'desc'));
                  foreach ($specArr as $k => $v) {
                        $specArr[$k]['item'] = pdo_getall('ewei_shop_goods_spec_item', array('uniacid' => $_W['uniacid'], 'specid' => $v['id']), array('id', 'specid', 'title', 'thumb'), '', array('displayorder' => 'desc'));
                        foreach ($specArr[$k]['item'] as &$item) {
                              $item['status']='';
                        }
                        unset($item);

                  }
                  $goods['specArr'] = $specArr;
            }


            $ordermember = m('member')->getMember($order['openid']);

            $order['realname'] = $ordermember['nickname']?$ordermember['nickname']:$ordermember['realname'];

            if ($goods['is_ladder'] == 1) {
                  $ladder = pdo_getall('ewei_shop_groupbuy_ladder', array('goods_id' => $goods['id'], 'uniacid' => $_W['uniacid']));
                  $goods['ladder'] = $ladder;
            }


            //已有拼团情况
            $teams = pdo_fetchall('select o.paytime,o.id,o.goodid,o.teamid,m.nickname,m.realname,m.mobile,m.avatar,g.endtime,g.groupnum,g.thumb_url from ' . tablename('ewei_shop_groupbuy_order') . ' as o
				left join ' . tablename('ewei_shop_member') . ' as m on m.openid=o.openid and m.uniacid =  o.uniacid
				left join ' . tablename('ewei_shop_groupbuy_goods') . ' as g on g.id = o.goodid
				where o.goodid = :goodid and o.uniacid = :uniacid  and o.deleted = 0 and o.teamid = :teamid and o.paytime > 0   ', array(':teamid' => $order['teamid'],':goodid' => $goods['id'], ':uniacid' => $_W['uniacid']));



            $num = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' where uniacid = :uniacid and deleted = 0 and teamid = :teamid and status > 0 ', array(':teamid' => $order['teamid'], ':uniacid' => $_W['uniacid']));

            if (!empty($order['ladder_id']>0)) {
                  $ladder = pdo_get('ewei_shop_groupbuy_ladder', array('id' => $order['ladder_id']));
                  $lastnum = $ladder['ladder_num'] - $num;
            } else {
                  $lastnum = $order['groupnum'] - $num;
            }
            $order['lastnum'] = $lastnum;




            $order['residualtime'] = $order['paytime'] + $goods['endtime'] * 60 * 60 - time();

            //抽奖拼团明细
            if ($goods['lottery']==1){
                  $winners = pdo_fetchall('select * from ' . tablename('ewei_shop_groupbuy_lotterywin') . ' where goods_id=:goodsid and uniacid=:uniacid ', array(':goodsid' => $goods['id'], ':uniacid' => $_W[uniacid]));
                  foreach ($winners as &$winner) {
                        $winner['member'] = m('member')->getMember($winner['openid']);
                  }
                  unset($winner);
                  $goods['winners'] = $winners;
            }


            $condition = '  and status=:status and uniacid=:uniacid and group_time_end >= ' . time() . ' and  group_time_start <=' . time();

            $params = array(':status' => 1, ':uniacid' => $_W['uniacid']);


            $list =   pdo_fetchall('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where 1 ' .$condition. '  LIMIT 5', $params);
            foreach ($list as &$item){
                  $item['thumb']=tomedia($item['thumb']);
                  $item['price'] = pdo_fetchcolumn('select marketprice from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['gid'], ':uniacid' => $_W[uniacid]));
                  $item['counttime'] = $item['group_time_end']-time();
            }
            unset($item);


            return app_json(array('teams' => $teams, 'order' => $order,  'goods' => $goods,'list'=>$list));



      }
      public function getorder(){
            global $_W;
            global $_GPC;
            $member = $this->member;
            $status = $_GPC['status'];
            $pindex = max(1, intval($_GPC['home']));
            $psize = 5;
            $condition = ' and openid=:openid  and uniacid=:uniacid and isgroupbuy=:isgroupbuy';
            $params = array(':openid' => $member['openid'], ':uniacid' => $_W['uniacid'],':isgroupbuy' => 1);
            if($status!='all'){
                  $condition.=' and status=:status';
                  $params[':status']=$status;
            }
            $list =   pdo_fetchall('select * from ' . tablename('ewei_shop_order') . ' where 1 ' .$condition. ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
            $total = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_order') . ' where 1 ' . $condition . ' order by createtime desc  ', $params);
            foreach ($list as &$item){
                  $ordergoods = pdo_fetchall('select * from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid ', array(':orderid' => $item['id'], ':uniacid' => $_W[uniacid]));
                  foreach ($ordergoods as $g){
                        $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $g['goodsid'], ':uniacid' => $_W[uniacid]));
                        $goodslist['title']=  $goods['title'];
                        $goodslist['thumb']= tomedia($goods['thumb']);
                        $goodslist['price']= $g['price'];
                        $goodslist['total']= $g['total'];
                        $item['goodslist'][]=$goodslist;
                  }
                  switch ($item['status']){
                        case -1;
                              $item['statustext']='已关闭';
                              $item['statuscolor']='#333333';
                              break;
                        case 0;
                              $item['statustext']='待付款';
                              $item['statuscolor']='#F54423';
                              break;
                        case 1;
                              $item['statustext']='买家已付款';
                              $item['statuscolor']='#333333';
                              break;
                        case 2;
                              $item['statustext']='卖家已发货';
                              $item['statuscolor']='#F54423';
                              break;
                        case 3;
                              $item['statustext']='已完成';
                              $item['statuscolor']='#333333';
                              break;
                        case 4;
                              $item['statustext']='退款中';
                              $item['statuscolor']='#333333';
                              break;
                  }
            }
            unset($item);
            return app_json(array('list' => $list, 'total' => $total, 'pagesize' => $psize));
      }

     
     
      public function express()
      {
            global $_W;
            global $_GPC;
            $openid = $_W['openid'];
            $uniacid = $_W['uniacid'];
            $orderid = intval($_GPC['id']);
            $sendtype = intval($_GPC['sendtype']);
            $bundle = trim($_GPC['bundle']);
            $cycelid = intval($_GPC['cycelid']);

            if (empty($orderid)) {
                  return app_error(AppError::$OrderNotFound);
            }

            if (!empty($cycelid)) {
                  $order = pdo_fetch('select expresscom,expresssn,addressid,status,express,sendtype from ' . tablename('ewei_shop_cycelbuy_periods') . ' where id=:id and uniacid=:uniacid', array(':id' => $cycelid, ':uniacid' => $uniacid));
            }
            else {
                  $order = pdo_fetch('select expresscom,expresssn,addressid,status,express,sendtype from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

                  if (empty($order)) {
                        return app_error(AppError::$OrderNotFound);
                  }

                  if (empty($order['addressid'])) {
                        return app_error(AppError::$OrderNoExpress);
                  }

                  if ($order['status'] < 2) {
                        return app_error(AppError::$OrderNoExpress);
                  }
            }

            $bundlelist = array();
            if (!empty($order['sendtype']) && $sendtype == 0) {
                  $i = 1;

                  while ($i <= intval($order['sendtype'])) {
                        $bundlelist[$i]['code'] = chr($i + 64);
                        $bundlelist[$i]['sendtype'] = $i;
                        $bundlelist[$i]['orderid'] = $orderid;
                        $goods_arr = pdo_fetchall('select g.title,g.thumb,og.total,og.optionname as optiontitle,og.expresssn,og.express,
                    og.sendtype,og.expresscom,og.sendtime from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.sendtype = ' . $i . ' and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));

                        foreach ($goods_arr as &$goods) {
                              $goods['thumb'] = tomedia($goods['thumb']);
                        }

                        unset($goods);
                        $bundlelist[$i]['goods'] = $goods_arr;

                        if (empty($bundlelist[$i]['goods'])) {
                              unset($bundlelist[$i]);
                        }

                        ++$i;
                  }

                  $bundlelist = array_values($bundlelist);
            }

            $condition = '';

            if (0 < $sendtype) {
                  $condition = ' and og.sendtype = ' . $sendtype;
            }

            $goods = pdo_fetchall('select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,og.expresssn,og.express,
            og.sendtype,og.expresscom,og.sendtime,g.storeids from ' . tablename('ewei_shop_order_goods') . ' og ' .' left join ' . tablename("ewei_shop_groupbuy_goods") . ' geg on geg.id=og.goodsid  left join ' . tablename('ewei_shop_goods') . ' g on g.id=geg.gid ' . ' where og.orderid=:orderid and og.single_refundtime=0 ' . $condition . ' and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));

            if (0 < $sendtype) {
                  $order['express'] = $goods[0]['express'];
                  $order['expresssn'] = $goods[0]['expresssn'];
                  $order['expresscom'] = $goods[0]['expresscom'];
            }

            $expresslist = m('util')->getExpressList($order['express'], $order['expresssn']);
            $status = '';

            if (!empty($expresslist)) {
                  if (strexists($expresslist[0]['step'], '已签收')) {
                        $status = '已签收';
                  }
                  else if (count($expresslist) <= 2) {
                        $status = '备货中';
                  }
                  else {
                        $status = '配送中';
                  }
            }

            return app_json(array('com' => $order['expresscom'], 'sn' => $order['expresssn'], 'status' => $status, 'count' => count($goods), 'thumb' => tomedia($goods[0]['thumb']),'title'=>$goods[0]['title'], 'expresslist' => $expresslist, 'bundlelist' => $bundlelist));
      }


      public function get_option()
      {
            global $_W;
            global $_GPC;
            $specArr = $_GPC['spec_id'];
            $specArr =explode(',',$specArr);
            asort($specArr);
            $groups_goods_id = $_GPC['groups_goods_id'];

            if (!empty($specArr)) {
                  $spec_id = implode('_', $specArr);
                  $goods_option = pdo_get('ewei_shop_groupbuy_goods_option', array('groups_goods_id' => $groups_goods_id, 'specs' => $spec_id, 'uniacid' => $_W['uniacid']));
                  return app_json(array('data' => $goods_option));
            }
      }


      public function goodsdetail(){
            global $_W;
            global $_GPC;
            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $_GPC['id'], ':uniacid' => $_W[uniacid]));
//            $goods['thumb'] = tomedia($goods['thumb']);
            $goods['thumb'] = iunserializer($goods['thumb_url']);
            foreach ($goods['thumb'] as $good) {
                  $img['src'] = tomedia($good);
                  $goods['images'][] = $img;
            }
            unset($good);
            $data = pdo_fetch('select id,deleted from ' . tablename('ewei_shop_member_groupbuy_favorite') . ' where uniacid=:uniacid and goodsid=:id and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':id' => $_GPC['id']));
            if(empty($data)){
                  $goods['isfavorite']=false;
            }else{
                  if(empty($data['deleted'])){
                        $goods['isfavorite']=true;
                  }else{
                        $goods['isfavorite']=false;
                  }
            }

            $shopgoods = pdo_fetch('select * from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $goods['gid'], ':uniacid' => $_W[uniacid]));

            $goods['shopgoods'] = $shopgoods;

            $goods['counttime'] = $goods['group_time_end']-time();

            $member = $this->member;

            $gecoupon = m('member')->getCredit($member['openid'],'gecoupon');
            $member['gecoupon'] = $gecoupon;

            if ($goods['is_ladder'] == 1) {
                  $ladder = pdo_getall('ewei_shop_groupbuy_ladder', array('goods_id' => $goods['id'], 'uniacid' => $_W['uniacid']));
                  $goods['ladder'] = $ladder;
            }

            $specs = array();

            if (!empty($goods['more_spec'])) {
                  $specArr = pdo_getall('ewei_shop_goods_spec', array('goodsid' => $goods['gid'], 'uniacid' => $_W['uniacid']), array('id', 'title'), '', array('displayorder' => 'desc'));
                  foreach ($specArr as $k => $v) {
                        $specArr[$k]['item'] = pdo_getall('ewei_shop_goods_spec_item', array('uniacid' => $_W['uniacid'], 'specid' => $v['id']), array('id', 'specid', 'title', 'thumb'), '', array('displayorder' => 'desc'));
                        foreach ($specArr[$k]['item'] as &$item) {
                              $item['status']='';
                        }
                        unset($item);

                  }
                  $goods['specArr'] = $specArr;
            }




            //购买数量


            $goods['fightnum'] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' where goodid = :goodid and uniacid = :uniacid and deleted = 0 and is_team = 1 and status > 0 ', array(':goodid' => $goods['id'], ':uniacid' => $_W['uniacid']));
            $goods['fightnum'] = $goods['teamnum'] + $goods['fightnum'];

            //已有拼团情况
            $teams = pdo_fetchall('select o.paytime,o.id,o.goodid,o.goods_option_id,o.ladder_id,o.teamid,m.nickname,m.realname,m.mobile,m.avatar,g.endtime,g.groupnum,g.thumb_url from ' . tablename('ewei_shop_groupbuy_order') . ' as o
				left join ' . tablename('ewei_shop_member') . ' as m on m.openid=o.openid and m.uniacid =  o.uniacid
				left join ' . tablename('ewei_shop_groupbuy_goods') . ' as g on g.id = o.goodid
				where o.goodid = :goodid and o.uniacid = :uniacid  and o.deleted = 0 and o.heads = 1 and o.paytime > 0 and o.success = 0   ', array(':goodid' => $goods['id'], ':uniacid' => $_W['uniacid']));

            $choseteam=array();
            foreach ($teams as $key => $value) {
                  $ladder_id = $value['ladder_id'];
                  $num = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' where uniacid = :uniacid and deleted = 0 and teamid = :teamid and status > 0 ', array(':teamid' => $value['teamid'], ':uniacid' => $_W['uniacid']));

                  if (!empty($ladder_id)) {
                        $ladder = pdo_get('ewei_shop_groupbuy_ladder', array('id' => $ladder_id));
                        $value['num'] = $ladder['ladder_num'] - $num;
                  }
                  else {
                        $value['num'] = $value['groupnum'] - $num;
                  }

                  $value['residualtime'] = $value['paytime'] + $value['endtime'] * 60 * 60 - time();


                        $teams[$key] = $value;

                        if ($value['id']==$_GPC['groupid']){
                              $choseteam = $value;
                        }



            }
            foreach ($teams as $key => $value) {
                  if ($value['residualtime']<=0){
                        unset($teams[$key]);
                  }
            }
            $teams = array_merge($teams,array());

            //抽奖拼团明细
            if ($goods['lottery']==1){
                  $winners = pdo_fetchall('select * from ' . tablename('ewei_shop_groupbuy_lotterywin') . ' where goods_id=:goodsid and uniacid=:uniacid ', array(':goodsid' => $goods['id'], ':uniacid' => $_W[uniacid]));
                  foreach ($winners as &$winner) {
                        $winner['member'] = m('member')->getMember($winner['openid']);
                  }
                  unset($winner);
                  $goods['winners'] = $winners;

            }

            //拼团开始判断
            if ($goods['group_time_start']>time()){
                  $goods['nostart'] =1;
            }
            $prealert = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_prealert') . ' where goods_id=:id and openid=:openid   limit 1', array(':id' => $goods['id'], ':openid' => $_W['openid']));
            if (!empty($prealert)){
                  $goods['alertdone'] =1;
            }





            return app_json(array('goods' => $goods,'member'=>$member,'teams'=>$teams,'choseteam'=>$choseteam));

      }
      public function agent(){
            global $_W;
            global $_GPC;
            $agent = m('member')->getMember($_GPC['agentid']);
            $member = $this->member;
            if ($member['agentid']>0){
                  return app_error('已有上级');
            }
            if ($member['id'] == $agent['id']){
                  return app_error('上级不能是自己');
            }
            if ($agent['status']!=1){
                  return app_error('上级不是分销商');
            }
            pdo_update('ewei_shop_member', array('agentid' => $agent['id']), array('id' => $member['id'],'uniacid' => $_W['uniacid']));
            return app_json(1);

      }

      public function category(){
            global $_W;
            global $_GPC;
            $cate =   pdo_fetchall('select * from ' . tablename('ewei_shop_groupbuy_category') . ' where enabled=:enabled and uniacid=:uniacid  ', array(':enabled' => 1, ':uniacid' => $_W[uniacid]));
            $list[]=array('state'=>-1,'text'=>'推荐');
            foreach ($cate as $c){
                  $item['state'] =$c['id'];
                  $item['text'] =$c['name'];
                  $list[]=$item;
            }
            $adv = $this->adv();

            $block=array();
            $condition = '  and status=:status and uniacid=:uniacid and group_time_end >= ' . time() . ' and  group_time_start <=' . time();

            $block[0] = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where 1 '.$condition.'   limit 1', array( ':status' => 1,':uniacid' => $_W[uniacid]));
            $block[0]['counttime'] = $block[0]['group_time_end']-time();
            $block[1] = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where 1 and preadv=1 and status=:status and uniacid=:uniacid and preadv=1 and  group_time_start >=' . time().'   limit 1', array(':status' => 1, ':uniacid' => $_W[uniacid]));
            $block[2] = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where 1 and gecoupon=1 '.$condition.'   limit 1', array( ':status' => 1,':uniacid' => $_W[uniacid]));

            $block = set_medias($block,'thumb');

            //抽奖拼团
            $lotterys = pdo_fetchall('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where 1 and lottery=1 '.$condition.'  ', array( ':status' => 1,':uniacid' => $_W[uniacid]));
            $lotterys = set_medias($lotterys,'thumb');

            foreach ($lotterys as &$lottery){
                  $lottery['price'] = pdo_fetchcolumn('select marketprice from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $lottery['gid'], ':uniacid' => $_W[uniacid]));
                  $lottery['counttime'] = $lottery['group_time_end']-time();
            }
            unset($lottery);


            return app_json(array('block'=>$block,'lotterys'=>$lotterys,'cate' => $list,'adv'=>$adv));
    }
      public function adv(){
            global $_W;
            global $_GPC;
            $adv =   pdo_fetchall('select * from ' . tablename('ewei_shop_groupbuy_adv') . ' where enabled=:enabled and uniacid=:uniacid  ', array(':enabled' => 1, ':uniacid' => $_W[uniacid]));
            foreach ($adv as $c){
                  $item['image'] =tomedia($c['thumb']);
                  $item['title'] =$c['title'];
                  $item['link'] =$c['link'];
                  $list[]=$item;
            }
            return $list;
      }
      public function getgoods(){
            global $_W;
            global $_GPC;
            $pindex = max(1, intval($_GPC['home']));
            $psize = 5;

            $timer = time();

          $condition = '  and status=:status and uniacid=:uniacid and group_time_end >= ' . time() . ' and  group_time_start <=' . time();
            if ($_GPC['adv']==1){
                  $condition = '  and status=:status and uniacid=:uniacid and preadv=1   and  group_time_start >=' . time();
            }
          $params = array(':status' => 1, ':uniacid' => $_W['uniacid']);


          if (!empty($_GPC['keyword'])) {
              $_GPC['keyword'] = trim($_GPC['keyword']);
              $condition .= ' and title  like :keyword';
              $params[':keyword'] = '%' . $_GPC['keyword'] . '%';
          }elseif(!empty($_GPC['cateid'])){
                if ($_GPC['cateid']==-1){
                      $orderby=' order by displayorder desc, sales desc,createtime desc';
                }else{
                      $cateid = $_GPC['cateid'];
                      $condition.=' and category=:category';
                      $params['category']=$cateid;
                      $orderby =' order by createtime desc';
                }

          }
          if ($_GPC['type']>0){
                switch ($_GPC['type']){
                      case 1:
                            $orderby =' order by sales desc';
                            break;
                      case 2:
                            $orderby =' order by groupbuyprice asc';
                            break;
                }
          }

          //搜索页面兼容
            if ($_GPC['searchtype']=='lottery'){
                  $condition .= ' and lottery=1 ';
            }
            if ($_GPC['searchtype']=='gecoupon'){
                  $condition .= ' and gecoupon=1 ';
            }




            $list =   pdo_fetchall('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where 1 ' .$condition.$orderby. '  LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
            $total = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_goods') . ' where 1 ' . $condition . ' order by createtime desc  ', $params);

            foreach ($list as &$item){
                  $item['thumb']=tomedia($item['thumb']);
                  $item['price'] = pdo_fetchcolumn('select marketprice from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['gid'], ':uniacid' => $_W[uniacid]));
                  $item['counttime'] = $item['group_time_end']-time();
                  if ($_GPC['adv']==1){
                        $item['startgrouptime'] = $item['group_time_start']-time();
                  $prealert = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_prealert') . ' where goods_id=:id and openid=:openid   limit 1', array(':id' => $item['id'], ':openid' => $_W['openid']));
                  if (!empty($prealert)){
                        $item['alertdone'] =1;
                  }

                  }
                  $item['fightnum'] = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_groupbuy_order') . ' where goodid = :goodid and uniacid = :uniacid and deleted = 0 and is_team = 1 and status > 0 ', array(':goodid' => $item['id'], ':uniacid' => $_W['uniacid']));
                  $item['fightnum'] = $item['teamnum'] + $item['fightnum'];
            }
            unset($item);



          return app_json(array('list' => $list, 'total' => $total, 'pagesize' => $psize));
      }
      //设置提醒
      public function prealert()
      {
            global $_W;
            global $_GPC;
            $id = intval($_GPC['id']);
            pdo_insert('ewei_shop_groupbuy_prealert', array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid'],'goods_id'=>$_GPC['id']));
            show_json(1);
      }




      //喜欢商品
      public function toggle()
      {
            global $_W;
            global $_GPC;
            $id = intval($_GPC['id']);
            $isfavorite = number_format($_GPC['isfavorite']);
            $goods = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

            if (empty($goods)) {
                  show_json(0, '商品未找到');
            }

            $data = pdo_fetch('select id,deleted from ' . tablename('ewei_shop_member_groupbuy_favorite') . ' where uniacid=:uniacid and goodsid=:id and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':id' => $id));

            if (empty($data)) {
                  if (!empty($isfavorite)) {
                        $data = array('uniacid' => $_W['uniacid'], 'goodsid' => $id, 'openid' => $_W['openid'], 'createtime' => time());
                        pdo_insert('ewei_shop_member_groupbuy_favorite', $data);
                  }
            }
            else {
                  pdo_update('ewei_shop_member_groupbuy_favorite', array('deleted' => $isfavorite ? 0 : 1), array('id' => $data['id'], 'uniacid' => $_W['uniacid']));
            }

            show_json(1, array('isfavorite' => $isfavorite));
      }
      public function getfavoritelist(){
            global $_W;
            global $_GPC;
            $pindex = max(1, intval($_GPC['home']));
            $psize = 5;

            $condition = '  and uniacid=:uniacid and deleted=0 and openid=:openid';
            $params = array(':openid' => $_W['openid'],':uniacid' => $_W['uniacid']);

            $list =   pdo_fetchall('select * from ' . tablename('ewei_shop_member_groupbuy_favorite') . ' where 1 ' .$condition. ' order by createtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $params);
            $total = pdo_fetchcolumn('select count(1) from ' . tablename('ewei_shop_member_groupbuy_favorite') . ' where 1 ' . $condition . ' order by createtime desc  ', $params);
            $goods=[];
            foreach ($list as &$item){
                $g = pdo_fetch('select * from ' . tablename('ewei_shop_groupbuy_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $item['goodsid'], ':uniacid' => $_W[uniacid]));
                  $g['thumb']=tomedia($g['thumb']);
                  $goods[]=$g;
            }
            unset($item);
            return app_json(array('list' => $goods, 'total' => $total, 'pagesize' => $psize));
      }
      public function removefavorite()
      {
            global $_W;
            global $_GPC;
            $id = $_GPC['id'];
            pdo_update('ewei_shop_member_groupbuy_favorite', array('deleted' => 1), array('openid' => $_W['openid'],'uniacid' => $_W['uniacid'],'goodsid'=>$id));
            app_json(1);
      }

}

?>
