<?php

//lcj 20191120
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class Timebankshop_EweiShopV2Page extends AppMobilePage {

    protected $member;

    public function __construct() {
        global $_W;
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

    public function main() {
        global $_W;
        $member = $this->member;
        $key_time = '__ewei_shopv2_member_verifycodesendtime_' . $_W['uniacid'];
        $sendtime = m('cache')->get($key_time);
        if (empty($sendtime) || $sendtime + 60 < time()) {
            $endtime = 0;
        } else {
            $endtime = 60 - (time() - $sendtime);
        }

        $memberArr = array('mobile' => $member['mobile']);
        $wapset = m('common')->getSysset('wap');
        $domain = 'https://' . $_SERVER['HTTP_HOST'];
        $verifycode_img = $domain . '/app/ewei_shopv2_api.php?i=' . $_W['uniacid'] . '&r=sms.captcha&time=' . time() . '&openid=' . $_W['openid'];
        return app_json(array('member' => $memberArr, 'binded' => !empty($member['mobile']) && !empty($member['mobileverify']) ? 1 : 0, 'endtime' => $endtime, 'smsimgcode' => $wapset['smsimgcode'], 'verifycode_img' => $verifycode_img));
    }

    /**
     * 创建订单
     */
    public function timebankshop_create() {
		global $_W;
        global $_GPC;
        $num = max(1, $_GPC['num']);
		$params2 = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition2 = '';
        $sql2 = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition2;
        $openidid = pdo_fetch($sql2, $params2);
		
        $openid = $openidid['openid'];
        $uniacid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $merch_plugin = p('merch');
        $merch_data = m('common')->getPluginset('merch');
        if ($merch_plugin && $merch_data['is_openmerch']) {
            $is_openmerch = 1;
        } else {
            $is_openmerch = 0;
        }

        $merchid = intval($_GPC['merchid']);
        $optionid = intval($_GPC['optionid']);
        $shop = m('common')->getSysset('shop');
        $member = m('member')->getMember($openid);
        //$goods = $this->model->getGoods($id, $member, $optionid, $num);
		$credit = $member['credit1'];
			$money = $member['credit2'];
			$optionid = intval($optionid);
			$merchid = $_W['merchid'];
			$condition = ' and uniacid=:uniacid ';

			if (0 < $merchid) {
				$condition .= ' and merchid = ' . $merchid . ' ';
			}

			if (empty($id)) {
				return NULL;
			}

			$goods = pdo_fetch('select * from ' . tablename('ewei_shop_timebankshop_goods') . ' where id=:id ' . $condition . ' limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

			if (empty($goods)) {
				return false;
			}

			if (!empty($goods['status']) && empty($goods['status'])) {
				return array('canbuy' => false, 'buymsg' => '已下架');
			}

			$goods = set_medias($goods, 'thumb');
			if (0 < $goods['credit'] && 0 < $goods['money']) {
				$goods['acttype'] = 0;
			}
			else if (0 < $goods['credit']) {
				$goods['acttype'] = 1;
			}
			else if (0 < $goods['money']) {
				$goods['acttype'] = 2;
			}
			else {
				$goods['acttype'] = 3;
			}

			if (intval($goods['isendtime']) == 1) {
				$goods['endtime_str'] = date('Y-m-d H:i', $goods['endtime']);
			}

			$goods['timestart_str'] = date('Y-m-d H:i', $goods['timestart']);
			$goods['timeend_str'] = date('Y-m-d H:i', $goods['timeend']);
			$goods['timestate'] = '';
			$goods['canbuy'] = !empty($goods['status']) && empty($goods['deleted']);

			if (empty($goods['canbuy'])) {
				$goods['buymsg'] = '已下架';
			}
			else {
				if ($goods['goodstype'] == 3) {
					if ($goods['packetsurplus'] <= 0 || $goods['surplusmoney'] <= $goods['packetlimit'] || $goods['surplusmoney'] < $goods['minpacketmoney']) {
						$goods['canbuy'] = false;
						$goods['buymsg'] = empty($goods['type']) ? '已兑完' : '已抽完';
					}
				}
				else if ($num - 1 < $goods['total']) {
					$logcount = pdo_fetchcolumn('select sum(goods_num) from ' . tablename('ewei_shop_timebankshop_log') . '  where goodsid=:goodsid and status>=2  and uniacid=:uniacid  ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));
					$goods['logcount'] = $logcount;

					if ($goods['joins'] < $logcount) {
						pdo_update('ewei_shop_timebankshop_goods', array('joins' => $logcount), array('id' => $id));
					}
				}
				else {
					$goods['canbuy'] = false;
					$goods['buymsg'] = empty($goods['type']) ? '已兑完' : '已抽完';
				}

				if ($goods['hasoption'] && $optionid) {
					$option = pdo_fetch('select total,credit,money,title as optiontitle,weight from ' . tablename('ewei_shop_timebankshop_option') . ' where uniacid = ' . $_W['uniacid'] . ' and id = ' . $optionid . ' and goodsid = ' . $id . ' ');
					$goods['credit'] = $option['credit'];
					$goods['money'] = $option['money'];
					$goods['weight'] = $option['weight'];
					$goods['total'] = $option['total'];
					$goods['optiontitle'] = $option['optiontitle'];

					if ($option['total'] <= $num - 1) {
						$goods['canbuy'] = false;
						$goods['buymsg'] = empty($goods['type']) ? '已兑完' : '已抽完';
					}
				}

				if ($goods['isverify'] == 0) {
					if ($goods['dispatchtype'] == 1) {
						if (empty($goods['dispatchid'])) {
							$dispatch = m('dispatch')->getDefaultDispatch($goods['merchid']);
						}
						else {
							$dispatch = m('dispatch')->getOneDispatch($goods['dispatchid']);
						}

						if (empty($dispatch)) {
							$dispatch = m('dispatch')->getNewDispatch($goods['merchid']);
						}

						$areas = iunserializer($dispatch['areas']);
						if (!empty($areas) && is_array($areas)) {
							$firstprice = array();

							foreach ($areas as $val) {
								if (empty($dispatch['calculatetype'])) {
									$firstprice[] = $val['firstprice'];
								}
								else {
									$firstprice[] = $val['firstnumprice'];
								}
							}

							array_push($firstprice, m('dispatch')->getDispatchPrice($num, $dispatch));
							$ret = array('min' => round(min($firstprice), 2), 'max' => round(max($firstprice), 2));
							$goods['areas'] = $ret;
						}
						else {
							$ret = m('dispatch')->getDispatchPrice($num, $dispatch);
						}

						$goods['dispatch'] = is_array($ret) ? $ret['min'] : $ret;
					}
				}
				else {
					$goods['dispatch'] = 0;
				}

				if ($goods['canbuy']) {
					if (0 < $goods['totalday']) {
						$logcount = pdo_fetchcolumn('select sum(goods_num)  from ' . tablename('ewei_shop_timebankshop_log') . '  where goodsid=:goodsid and status>=2 and  date_format(from_UNIXTIME(`createtime`),\'%Y-%m-%d\') = date_format(now(),\'%Y-%m-%d\') and uniacid=:uniacid  ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid']));

						if ($goods['totalday'] <= $logcount) {
							$goods['canbuy'] = false;
							$goods['buymsg'] = empty($goods['type']) ? '今日已兑完' : '今日已抽完';
						}
					}
				}

				if ($goods['canbuy']) {
					if ($num - 1 < $goods['chanceday']) {
						$logcount = pdo_fetchcolumn('select sum(goods_num)  from ' . tablename('ewei_shop_timebankshop_log') . '  where goodsid=:goodsid and openid=:openid and status>0 and  date_format(from_UNIXTIME(`createtime`),\'%Y-%m-%d\') = date_format(now(),\'%Y-%m-%d\') and uniacid=:uniacid  ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));

						if ($goods['chanceday'] <= $logcount) {
							$goods['canbuy'] = false;
							$goods['buymsg'] = empty($goods['type']) ? '今日已兑换' : '今日已抽奖';
						}
					}
				}

				if ($goods['canbuy']) {
					if ($num - 1 < $goods['chance']) {
						$logcount = pdo_fetchcolumn('select sum(goods_num)  from ' . tablename('ewei_shop_timebankshop_log') . '  where goodsid=:goodsid and openid=:openid and status>0 and  uniacid=:uniacid  ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));

						if ($goods['chance'] <= $logcount) {
							$goods['canbuy'] = false;
							$goods['buymsg'] = empty($goods['type']) ? '已兑换' : '已抽奖';
						}
					}
				}

				if ($goods['canbuy']) {
					if ($num - 1 < $goods['usermaxbuy']) {
						$logcount = pdo_fetchcolumn('select ifnull(sum(total),0)  from ' . tablename('ewei_shop_timebankshop_log') . '  where goodsid=:goodsid and openid=:openid  and uniacid=:uniacid ', array(':goodsid' => $id, ':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));

						if ($goods['chance'] <= $logcount) {
							$goods['canbuy'] = false;
							$goods['buymsg'] = '已参加';
						}
					}
				}

				if ($goods['canbuy']) {
					$credit_text = empty($_W['shopset']['trade']['credittext']) ? '积分' : $_W['shopset']['trade']['credittext'];
					if ($credit < $goods['credit'] * $num && 0 < $goods['credit']) {
						$goods['canbuy'] = false;
						$goods['buymsg'] = $credit_text . '不足';
					}
				}

				if ($goods['canbuy']) {
					if ($goods['istime'] == 1) {
						if (time() < $goods['timestart']) {
							$goods['canbuy'] = false;
							$goods['timestate'] = 'before';
							$goods['buymsg'] = '活动未开始';
						}
						else if ($goods['timeend'] < time()) {
							$goods['canbuy'] = false;
							$goods['buymsg'] = '活动已结束';
						}
						else {
							$goods['timestate'] = 'after';
						}
					}
				}

				if ($goods['canbuy']) {
					if ($goods['isendtime'] == 1 && $goods['isverify']) {
						if ($goods['endtime'] < time()) {
							$goods['canbuy'] = false;
							$goods['buymsg'] = '活动已结束(超出兑换期)';
						}
					}
				}

				$levelid = $member['level'];
				$groupid = $member['groupid'];

				if ($goods['canbuy']) {
					if ($goods['buylevels'] != '') {
						$buylevels = explode(',', $goods['buylevels']);

						if (!in_array($levelid, $buylevels)) {
							$goods['canbuy'] = false;
							$goods['buymsg'] = '无会员特权';
						}
					}
				}

				if ($goods['canbuy']) {
					if ($goods['buygroups'] != '') {
						$buygroups = explode(',', $goods['buygroups']);

						if (!in_array($groupid, $buygroups)) {
							$goods['canbuy'] = false;
							$goods['buymsg'] = '无会员特权';
						}
					}
				}
			}

			$goods['followtext'] = empty($goods['followtext']) ? '您必须关注我们的公众帐号，才能参加活动哦!' : $goods['followtext'];
			$set = $this->getSet();
			$goods['followurl'] = $set['followurl'];

			if (empty($goods['followurl'])) {
				$share = m('common')->getSysset('share');
				$goods['followurl'] = $share['followurl'];
			}

			$goods['money'] = price_format($goods['money'], 2);
			$goods['minmoney'] = price_format($goods['minmoney'], 2);
			$goods['minmoney'] = price_format($goods['minmoney'], 2);
			//return $goods;

        if (empty($goods)) {
            $this->message('商品已下架或被删除!', mobileUrl('creditshop'), 'error');
        }

        $pay = m('common')->getSysset('pay');
        $pay['weixin'] = !empty($pay['weixin_sub']) ? 1 : $pay['weixin'];
        $pay['weixin_jie'] = !empty($pay['weixin_jie_sub']) ? 1 : $pay['weixin_jie'];
        $pay['app_alipay'] = 0;
        $goods['jie'] = intval($pay['weixin_jie']);
        $set = m('common')->getPluginset('creditshop');
        $goods['followed'] = m('user')->followed($openid);
        $chance = empty($goods['chance']) ? 0 : $goods['chance'];

        if ($goods['goodstype'] == 0) {
            $stores = array();

            if (!empty($goods['isverify'])) {
                $storeids = array();

                if (!empty($goods['storeids'])) {
                    $storeids = array_merge(explode(',', $goods['storeids']), $storeids);
                }

                if (empty($storeids)) {
                    if (0 < $merchid) {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                    } else {
                        $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                    }
                } else if (0 < $merchid) {
                    $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
                } else {
                    $stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
                }
            }
        }

        $sysSet = m('common')->getSysset();
        $sec = m('common')->getSec();
        $sec = iunserializer($sec['sec']);
        $payinfo = array('wechat' => !empty($sec['app_wechat']['merchname']) && !empty($sysSet['pay']['app_wechat']) && !empty($sec['app_wechat']['appid']) && !empty($sec['app_wechat']['appsecret']) && !empty($sec['app_wechat']['merchid']) && !empty($sec['app_wechat']['apikey']) ? true : false, 'alipay' => false, 'mcname' => $sec['app_wechat']['merchname'], 'aliname' => empty($_W['shopset']['shop']['name']) ? $sec['app_wechat']['merchname'] : $_W['shopset']['shop']['name'], 'attach' => $_W['uniacid'] . ':2', 'type' => 7);

        if ($_W['ispost']) {
            show_json(1, $goods);
        }

        return app_json(array('list' => $goods));
		
	}

   

   /**
     * 幻灯片
     */
    public function timebankshop_adv() {
		global $_W;
		global $_GPC;
		$list[] = array();
		$pindex = max(1, intval($_GPC['home']));
		$psize = 20;
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		
		$condition .= ' and enabled = 1';
		

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and advname  like :keyword';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_timebankshop_adv') . (' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC limit ') . ($pindex - 1) * $psize . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(1) FROM ' . tablename('ewei_shop_timebankshop_adv') . (' WHERE 1 ' . $condition), $params);
		$pager = pagination2($total, $pindex, $psize);
		$list = set_medias($list, 'thumb');
		return app_json(array('list' => $list));
	}
	/**
     * 全部分类
     */
    public function timebankshop_category() {
		global $_W;
		global $_GPC;
		$list[] = array();
		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_timebankshop_category') . (' WHERE enabled = 1 and uniacid = \'' . $_W['uniacid'] . '\' ORDER BY displayorder DESC'));
		$list = set_medias($list, 'thumb');
		return app_json(array('list' => $list));
	}
	/**
     * 首页-分类
     */
	 public function timebankshop_scategory() {
		global $_W;
		global $_GPC;
		$list[] = array();
		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_timebankshop_category') . (' WHERE enabled = 1 and uniacid = \'' . $_W['uniacid'] . '\' ORDER BY displayorder DESC limit 4'));
		$list = set_medias($list, 'thumb');
		return app_json(array('list' => $list));
	 }
	 
	/**
     * 商品列表
     */
    public function timebankshop_goodlist() {
		global $_W;
		global $_GPC;
		$params2 = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition2 = '';
        $sql2 = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition2;
        $openidid = pdo_fetch($sql2, $params2);
		
		$openid = $openidid['openid'];
		$member = m('member')->getMember($openid);
		$shop = m('common')->getSysset('shop');
		$uniacid = $_W['uniacid'];
		$cateid = intval($_GPC['cate']);
		$merchid = intval($_GPC['merchid']);
		$cate = pdo_fetch('select id,name from ' . tablename('ewei_shop_timebankshop_category') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $cateid, ':uniacid' => $uniacid));
		$pindex = max(1, intval($_GPC['home']));
		$psize = 6;
		if ($_GPC['limit']>0){
              $psize =   $_GPC['limit'];
        }
		$condition = ' and uniacid = :uniacid and status=1 and deleted=0 and type!=3 ';

		if (0 < $merchid) {
			$condition .= ' and merchid = ' . $merchid . ' ';
		}

		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($cate)) {
			$condition .= ' and cate=' . $cateid;
		}

		if (!empty($_GPC['goodstype']) || $_GPC['goodstype']==0 ) {
			$condition .= ' and goodstype=' . $_GPC['goodstype'];
		}

		$keywords = trim($_GPC['keywords']);

		if (!empty($keywords)) {
			$condition .= ' AND title like \'%' . $keywords . '%\' ';
		}

		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_timebankshop_goods') . (' where 1 ' . $condition);
		$total = pdo_fetchcolumn($sql, $params);
		$list[] = array();

		if (!empty($total)) {
			$sql = 'SELECT id,title,thumb,subtitle,`type`,price,credit,money,goodstype,joins FROM ' . tablename('ewei_shop_timebankshop_goods') . '
            		where 1 ' . $condition . ' ORDER BY displayorder desc,id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql, $params);
			$list = set_medias($list, 'thumb');

			foreach ($list as &$row) {
				if (0 < $row['credit'] & 0 < $row['money']) {
					$row['acttype'] = 0;
					$row['priceinfo'] = $row['credit'].'积分+'.$row['money'].'惠乐时';
				}
				else if (0 < $row['credit']) {
					$row['acttype'] = 1;
                      $row['priceinfo'] = $row['credit'].'积分';

                }
				else {
					if (0 < $row['money']) {
						$row['acttype'] = 2;
                          $row['priceinfo'] = $row['money'].'惠乐时';

                    }
				}

				if (intval($row['money']) - $row['money'] == 0) {
					$row['money'] = intval($row['money']);
				}

			}

			unset($row);
		}

		return app_json(array('list' => $list));
	
	}
	
	/**
     * 全部商品列表
     */
    public function timebankshop_allgoodlist() {
		global $_W;
		global $_GPC;
		$params2 = array(':openid' => $_GPC['openid'], ':openid_wa' => $_GPC['openid']);
        $condition2 = '';
        $sql2 = 'select  *  from ' . tablename('ewei_shop_member') . '   where openid=:openid or openid_wa=:openid_wa' . $condition2;
        $openidid = pdo_fetch($sql2, $params2);
		
		$openid = $openidid['openid'];
		$member = m('member')->getMember($openid);
		$shop = m('common')->getSysset('shop');
		$uniacid = $_W['uniacid'];
		$cateid = intval($_GPC['cate']);
		$merchid = intval($_GPC['merchid']);
		$cate = pdo_fetch('select id,name from ' . tablename('ewei_shop_timebankshop_category') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $cateid, ':uniacid' => $uniacid));
		$pindex = max(1, intval($_GPC['home']));
		$psize = 10;
		$condition = ' and uniacid = :uniacid and status=1 and deleted=0 and type!=3 ';

		if (0 < $merchid) {
			$condition .= ' and merchid = ' . $merchid . ' ';
		}
		

		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($cate)) {
			$condition .= ' and cate=' . $cateid;
		}

		$keywords = trim($_GPC['keywords']);

		if (!empty($keywords)) {
			$condition .= ' AND title like \'%' . $keywords . '%\' ';
		}

		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_timebankshop_goods') . (' where 1 ' . $condition);
		$total = pdo_fetchcolumn($sql, $params);
		$list[] = array();

		if (!empty($total)) {
			$sql = 'SELECT id,title,thumb,subtitle,`type`,price,credit,money,goodstype,joins FROM ' . tablename('ewei_shop_timebankshop_goods') . '
            		where 1 ' . $condition . ' ORDER BY displayorder desc,id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql, $params);
			$list = set_medias($list, 'thumb');

			foreach ($list as &$row) {
                  if (0 < $row['credit'] & 0 < $row['money']) {
                        $row['acttype'] = 0;
                        $row['priceinfo'] = $row['credit'].'积分+'.$row['money'].'惠乐时';
                  }
                  else if (0 < $row['credit']) {
                        $row['acttype'] = 1;
                        $row['priceinfo'] = $row['credit'].'积分';

                  }
                  else {
                        if (0 < $row['money']) {
                              $row['acttype'] = 2;
                              $row['priceinfo'] = $row['money'].'惠乐时';

                        }
                  }
				if (intval($row['money']) - $row['money'] == 0) {
					$row['money'] = intval($row['money']);
				}
			}

			unset($row);
		}
        return app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'next_page' => ceil($total / $psize)));
		//return app_json(array('list' => $list));
	
	}
	public function goodsdetail(){
          global $_W;
          global $_GPC;
          $goods = pdo_fetch('select * from ' . tablename('ewei_shop_timebankshop_goods') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $_GPC['id'], ':uniacid' => $_W[uniacid]));
          $goods['thumb'] = tomedia($goods['thumb']);
          if (0 < $goods['credit'] & 0 < $goods['money']) {
                $goods['acttype'] = 0;
                $goods['priceinfo'] = $goods['credit'].'积分+'.$goods['money'].'惠乐时';
          }
          else if (0 < $goods['credit']) {
                $goods['acttype'] = 1;
                $goods['priceinfo'] = $goods['credit'].'积分';

          }
          else {
                if (0 < $goods['money']) {
                      $goods['acttype'] = 2;
                      $goods['priceinfo'] = $goods['money'].'惠乐时';

                }
          }
          return app_json(array('goods' => $goods));

    }

}

?>
