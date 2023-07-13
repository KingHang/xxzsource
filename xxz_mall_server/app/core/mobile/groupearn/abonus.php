<?php



//lcj 20190826
if (!defined('IN_IA')) {
    exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';

class abonus_EweiShopV2Page extends AppMobilePage {

    protected $member;

    public function __construct() {
        global $_W;
        parent::__construct();
        global $_GPC;
        $member = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid=:openid or openid_wa=:openid and uniacid=:uniacid   limit 1', array(':openid' => $_GPC['openid'], ':uniacid' => $_W[uniacid]));

        $member = set_medias($member, 'avatar');
        if(!empty($member['status']) && !empty($member['isagent'])){
            $agentLevel = p('abonus')->getLevel($member['openid']);
            $set = p('abonus')->getSet();
            if (empty($agentLevel['id'])) {
                $agentLevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname']);
            }
            $member['levelname']=$member['agentarea'];
        }
        $this->member = $member;
    }

    public function main() {
        global $_W;
        global $_GPC;
        $member = $this->member;



        $starttime  = strtotime(date('Y-m-1', time()));

        if (!empty($member['agentarea'])){
              $monthorders = pdo_fetchall('select * from ' . tablename('ewei_shop_order') . ' where status>0 and agentarea=:agentarea and uniacid=:uniacid and createtime>= '.$starttime, array(':agentarea' => $member['agentarea'], ':uniacid' => $_W[uniacid]));
              $orders = pdo_fetchall('select * from ' . tablename('ewei_shop_order') . ' where status>0 and agentarea=:agentarea and uniacid=:uniacid  ', array(':agentarea' => $member['agentarea'], ':uniacid' => $_W[uniacid]));

        }else{
              $monthorders=array();
              $orders=array();
        }

         $monthmoney = 0;
          foreach ($monthorders as $monthorder) {
                $monthmoney+=$monthorder['price'];
        }

          $money = 0;
          foreach ($orders as $order) {
                $money+=$order['price'];
          }

          if (!empty($member['agentarea'])){
                $downs = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where agentarea=:agentarea and uniacid=:uniacid ', array(':agentarea' => $member['agentarea'], ':uniacid' => $_W[uniacid]));
          }




          $year = date('Y');
          $month = intval(date('m'));
          $bonusall = p('abonus')->getBonusData($year, $month, 0, $member['openid']);


          $bonudata = p('abonus')->getBonus( $member['openid'],array('ok', 'lock', 'total'));


          $wlkdata =array();

          $wlkdata['bonus_wait'] = $bonusall['aagents'][$member ['id']]['bonusmoney1'] + $bonusall['aagents'][$member ['id']]['bonusmoney2'] + $bonusall['aagents'][$member ['id']]['bonusmoney3']+ $bonusall['aagents'][$member ['id']]['bonusmoney4']+ $bonusall['aagents'][$member ['id']]['bonusmoney5']+ $bonusall['aagents'][$member ['id']]['bonusmoney6'];
          $wlkdata['ok'] = $bonudata['ok'];
          $wlkdata['lock'] = $bonudata['lock'];
          $wlkdata['total'] = $bonudata['total'];


          $wlkdata['member'] = $member;
          $wlkdata['monthmoney'] = $monthmoney;
          $wlkdata['monthcount'] = count($monthorders);
          $wlkdata['count'] = count($orders);
          $wlkdata['money'] = $money;
          $wlkdata['downs'] = array_slice($downs,0,5);
          $wlkdata['downcount'] = count($downs);

          return app_json(array('wlkdata' => $wlkdata,'member'=>$member));

    }
    public function down(){
          global $_W;
          global $_GPC;
          $member = $this->member;
          $downs = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where agentarea=:agentarea and uniacid=:uniacid ', array(':agentarea' => $member['agentarea'], ':uniacid' => $_W[uniacid]));

          foreach ($downs as &$row) {
                $row['agentname'] = pdo_fetchcolumn('select nickname from ' . tablename('ewei_shop_member') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $row['agentid'], ':uniacid' => $_W[uniacid]));;
                $ordercount = pdo_fetchcolumn('select count(id) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
                $row['ordercount'] = number_format(intval($ordercount), 0);
                $moneycount = pdo_fetchcolumn('select sum(og.realprice) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id where o.openid=:openid  and o.status>=1 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
                $row['moneycount'] = number_format(floatval($moneycount), 2);
                $row['createtime'] = date('Y-m-d H:i', $row['createtime']);

                $agentLevel = p('commission')->getLevel($row['openid']);

                if (empty($agentLevel['id'])) {
                      $agentLevel = array('levelname' => empty($set['levelname']) ? '普通等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3']);
                }
                $row['levelname']=$agentLevel['levelname'];
          }
          return app_json(array('downs' => $downs,'member'=>$member));


    }
      public function log(){
            global $_W;
            global $_GPC;
            $member = $this->member;

            $year = $_GPC['year']?$_GPC['year']:date('Y');


            $starttime = strtotime($year.'-1-1');

            $endtime = strtotime("+1 year",$starttime);



            $bonus = pdo_fetchall('select *  from ' . tablename('ewei_shop_abonus_billp') . ' where openid=:openid  and uniacid=:uniacid and createtime >= ' . $starttime . ' and  createtime <=' . $endtime, array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));


            foreach ($bonus as &$b) {

                  $bill = pdo_fetch('select * from ' . tablename('ewei_shop_abonus_bill') . ' where id=:id and uniacid=:uniacid   limit 1', array(':id' => $b['billid'], ':uniacid' => $_W[uniacid]));


                  $b['total'] = $b['commoney'] +$b['paymoney1']+$b['paymoney2']+$b['paymoney3']+$b['paymoney4']+$b['paymoney5']+$b['paymoney6'];
                  $b['month'] = $bill['month'];
                  $b['createtime'] = date('Y-m-d H:i:s',$b['createtime']);
            }
            unset($b);
            return app_json(array('wlkdata' => $bonus,'member'=>$member,'year'=>$year));

      }

}

?>
