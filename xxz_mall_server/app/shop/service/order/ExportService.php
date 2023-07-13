<?php

namespace app\shop\service\order;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\common\model\plugin\agent\Referee as RefereeModel;
use app\common\model\plugin\agent\User as UserModel;
use app\common\model\plugin\agent\OrderDetail as OrderDetailModel;

/**
 * 订单导出服务类
 */
class ExportService
{
    /**
     * 订单导出
     */
    public function orderList($list)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 0);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列宽
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);

        //设置工作表标题名称
        $sheet->setTitle('订单明细');

        $sheet->setCellValue('A1', '订单号');
        $sheet->setCellValue('B1', '商品名称');
        $sheet->setCellValue('C1', '商品规格');
        $sheet->setCellValue('D1', '商品数量');
        $sheet->setCellValue('E1', '商品价格');
        $sheet->setCellValue('F1', '订单总额');
        $sheet->setCellValue('G1', '优惠券抵扣');
        $sheet->setCellValue('H1', '积分抵扣');
        $sheet->setCellValue('I1', '运费金额');
        $sheet->setCellValue('J1', '后台改价');
        $sheet->setCellValue('K1', '实付款金额');
        $sheet->setCellValue('L1', '支付方式');
        $sheet->setCellValue('M1', '下单时间');
        $sheet->setCellValue('N1', '买家');
        $sheet->setCellValue('O1', '买家留言');
        $sheet->setCellValue('P1', '卖家备注');
        $sheet->setCellValue('Q1', '配送方式');
        $sheet->setCellValue('R1', '自提门店名称');
        $sheet->setCellValue('S1', '自提联系人');
        $sheet->setCellValue('T1', '自提联系电话');
        $sheet->setCellValue('U1', '收货人姓名');
        $sheet->setCellValue('V1', '联系电话');
        $sheet->setCellValue('W1', '收货人地址');
        $sheet->setCellValue('X1', '物流公司');
        $sheet->setCellValue('Y1', '物流单号');
        $sheet->setCellValue('Z1', '付款状态');
        $sheet->setCellValue('AA1', '付款时间');
        $sheet->setCellValue('AB1', '发货状态');
        $sheet->setCellValue('AC1', '发货时间');
        $sheet->setCellValue('AD1', '收货状态');
        $sheet->setCellValue('AE1', '收货时间');
        $sheet->setCellValue('AF1', '订单状态');
        $sheet->setCellValue('AG1', '微信支付交易号');
        $sheet->setCellValue('AH1', '是否已评价');

        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $address = $order['address'];
            foreach ($order['product'] as $key => $product) {
                if ($key > 0) {
                    $sheet->setCellValue('A' . ($index + 2), '');
                    $sheet->setCellValue('B' . ($index + 2), $product['product_name']);
                    $sheet->setCellValue('C' . ($index + 2), !empty($product['product_attr']) ? $product['product_attr'] : '');
                    $sheet->setCellValue('D' . ($index + 2), $product['total_num']);
                    $sheet->setCellValue('E' . ($index + 2), $product['total_price']);
                    $sheet->setCellValue('F' . ($index + 2), '');
                    $sheet->setCellValue('G' . ($index + 2), '');
                    $sheet->setCellValue('H' . ($index + 2), '');
                    $sheet->setCellValue('I' . ($index + 2), '');
                    $sheet->setCellValue('J' . ($index + 2), '');
                    $sheet->setCellValue('K' . ($index + 2), '');
                    $sheet->setCellValue('L' . ($index + 2), '');
                    $sheet->setCellValue('M' . ($index + 2), '');
                    $sheet->setCellValue('N' . ($index + 2), '');
                    $sheet->setCellValue('O' . ($index + 2), '');
                    $sheet->setCellValue('P' . ($index + 2), '');
                    $sheet->setCellValue('Q' . ($index + 2), '');
                    $sheet->setCellValue('R' . ($index + 2), '');
                    $sheet->setCellValue('S' . ($index + 2), '');
                    $sheet->setCellValue('T' . ($index + 2), '');
                    $sheet->setCellValue('U' . ($index + 2), '');
                    $sheet->setCellValue('V' . ($index + 2), '');
                    $sheet->setCellValue('W' . ($index + 2), '');
                    $sheet->setCellValue('X' . ($index + 2), '');
                    $sheet->setCellValue('Y' . ($index + 2), '');
                    $sheet->setCellValue('Z' . ($index + 2), '');
                    $sheet->setCellValue('AA' . ($index + 2), '');
                    $sheet->setCellValue('AB' . ($index + 2), '');
                    $sheet->setCellValue('AC' . ($index + 2), '');
                    $sheet->setCellValue('AD' . ($index + 2), '');
                    $sheet->setCellValue('AE' . ($index + 2), '');
                    $sheet->setCellValue('AF' . ($index + 2), '');
                    $sheet->setCellValue('AG' . ($index + 2), '');
                    $sheet->setCellValue('AH' . ($index + 2), '');
                    $index++;
                    continue;
                }
                $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order_no'] . "\t");
                $sheet->setCellValue('B' . ($index + 2), $product['product_name']);
                $sheet->setCellValue('C' . ($index + 2), !empty($product['product_attr']) ? $product['product_attr'] : '');
                $sheet->setCellValue('D' . ($index + 2), $product['total_num']);
                $sheet->setCellValue('E' . ($index + 2), $product['total_price']);
                $sheet->setCellValue('F' . ($index + 2), $order['total_price']);
                $sheet->setCellValue('G' . ($index + 2), $order['coupon_money']);
                $sheet->setCellValue('H' . ($index + 2), $order['points_money']);
                $sheet->setCellValue('I' . ($index + 2), $order['express_price']);
                $sheet->setCellValue('J' . ($index + 2), "{$order['update_price']['symbol']}{$order['update_price']['value']}");
                $sheet->setCellValue('K' . ($index + 2), $order['pay_price']);
                $sheet->setCellValue('L' . ($index + 2), $order['pay_type']['text']);
                $sheet->setCellValue('M' . ($index + 2), $order['create_time']);
                $sheet->setCellValue('N' . ($index + 2), $order['user']['nickName']);
                $sheet->setCellValue('O' . ($index + 2), $order['buyer_remark']);
                $sheet->setCellValue('P' . ($index + 2), $order['order_remark']);
                $sheet->setCellValue('Q' . ($index + 2), $order['delivery_type']['text']);
                $sheet->setCellValue('R' . ($index + 2), !empty($order['extract_store']) ? $order['extract_store']['shop_name'] : '');
                $sheet->setCellValue('S' . ($index + 2), !empty($order['extract']) ? $order['extract']['linkman'] : '');
                $sheet->setCellValue('T' . ($index + 2), !empty($order['extract']) ? $order['extract']['phone'] : '');
                $sheet->setCellValue('U' . ($index + 2), $order['address']['name']);
                $sheet->setCellValue('V' . ($index + 2), $order['address']['phone']);
                $sheet->setCellValue('W' . ($index + 2), $address ? $address->getFullAddress() : '');
                $sheet->setCellValue('X' . ($index + 2), $order['express']['express_name']);
                $sheet->setCellValue('Y' . ($index + 2), $order['express_no']);
                $sheet->setCellValue('Z' . ($index + 2), $order['pay_status']['text']);
                $sheet->setCellValue('AA' . ($index + 2), $this->filterTime($order['pay_time']));
                $sheet->setCellValue('AB' . ($index + 2), $order['delivery_status']['text']);
                $sheet->setCellValue('AC' . ($index + 2), $this->filterTime($order['delivery_time']));
                $sheet->setCellValue('AD' . ($index + 2), $order['receipt_status']['text']);
                $sheet->setCellValue('AE' . ($index + 2), $this->filterTime($order['receipt_time']));
                $sheet->setCellValue('AF' . ($index + 2), $order['order_status']['text']);
                $sheet->setCellValue('AG' . ($index + 2), $order['transaction_id']);
                $sheet->setCellValue('AH' . ($index + 2), $order['is_comment'] ? '是' : '否');
                $index++;
            }
        }

        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '订单') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 奖励订单导出
     */
    public function awardList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('奖励明细');
        $sheet->setCellValue('A1', '订单号');
        $sheet->setCellValue('B1', '分销商名称');
        $sheet->setCellValue('C1', '分销商电话');
        $sheet->setCellValue('D1', '买家昵称');
        $sheet->setCellValue('E1', '买家电话');
        $sheet->setCellValue('F1', '订单状态');
        $sheet->setCellValue('G1', '订单金额');
        $sheet->setCellValue('H1', '订单PV');
        $sheet->setCellValue('I1', '奖励金额');
        $sheet->setCellValue('J1', '结算状态');
        $sheet->setCellValue('K1', '结算单号');
        $sheet->setCellValue('L1', '所属团队长');
        $sheet->setCellValue('M1', '所属团队长电话');
        $sheet->setCellValue('N1', '时间');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order']['order_no'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['agent_user']['real_name']);
            $sheet->setCellValue('C' . ($index + 2), "\t" . $order['agent_user']['mobile'] . "\t");
            $sheet->setCellValue('D' . ($index + 2), $order['nickName']);
            $sheet->setCellValue('E' . ($index + 2), "\t" . $order['mobile'] . "\t");
            $sheet->setCellValue('F' . ($index + 2), $order['order']['state_text']);
            $sheet->setCellValue('G' . ($index + 2), $order['order']['pay_price']);
            $sheet->setCellValue('H' . ($index + 2), $order['order']['total_pv']);
            $sheet->setCellValue('I' . ($index + 2), $order['money']);
            $sheet->setCellValue('J' . ($index + 2), $order['is_settled'] ? '已结算' : '未结算');
            $sheet->setCellValue('K' . ($index + 2), $order['settled']['settled_no']);
            $sheet->setCellValue('L' . ($index + 2), $order['teamUser']['real_name']);
            $sheet->setCellValue('M' . ($index + 2), $order['teamUser']['mobile']);
            $sheet->setCellValue('N' . ($index + 2), $order['create_time']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '奖励明细') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 奖励订单导出
     */
    public function awardLists($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('奖励明细');
        $sheet->setCellValue('A1', '结算月');
        $sheet->setCellValue('B1', '分销商名称');
        $sheet->setCellValue('C1', '分销商电话');
        $sheet->setCellValue('D1', '分销商等级');
        $sheet->setCellValue('E1', '个人消费');
        $sheet->setCellValue('F1', '团队业绩');
//        $sheet->setCellValue('G1', '奖励比例');
        $sheet->setCellValue('H1', '奖励金额');
        $sheet->setCellValue('I1', '结算状态');
        $sheet->setCellValue('J1', '结算单号');
        $sheet->setCellValue('K1', '时间');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $status = "";
            if ($order['status'] == 0) {
                $status = "结算失败";
            } elseif ($order['status'] == 1) {
                $status = "结算成功";
            } else {
                $status = "待结算";
            }
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['month_text'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['agent_user']['real_name']);
            $sheet->setCellValue('C' . ($index + 2), "\t" . $order['agent_user']['mobile'] . "\t");
            $sheet->setCellValue('D' . ($index + 2), $order['agent_user']['grade']['name']);
            $sheet->setCellValue('E' . ($index + 2), $order['buy_money']);
            $sheet->setCellValue('F' . ($index + 2), $order['team_money']);
//            $sheet->setCellValue('G' . ($index + 2), $order['percent']);
            $sheet->setCellValue('H' . ($index + 2), $order['money']);
            $sheet->setCellValue('I' . ($index + 2), $status);
            $sheet->setCellValue('J' . ($index + 2), $order['settled_no']);
            $sheet->setCellValue('K' . ($index + 2), $order['create_time']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '奖励明细') . '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 提现订单导出
     */
    public function cashList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('提现明细');
        $sheet->setCellValue('A1', '提现订单号');
        $sheet->setCellValue('B1', '分销商名称');
        $sheet->setCellValue('C1', '分销商电话');
        $sheet->setCellValue('D1', '提现方式');
        $sheet->setCellValue('E1', '提现金额');
        $sheet->setCellValue('F1', '提现状态');
        $sheet->setCellValue('G1', '时间');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order_no'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['user']['real_name']);
            $sheet->setCellValue('C' . ($index + 2), "\t" . $order['user']['mobile'] . "\t");
            $sheet->setCellValue('D' . ($index + 2), $order['pay_type']['text']);
            $sheet->setCellValue('E' . ($index + 2), $order['money']);
            $sheet->setCellValue('F' . ($index + 2), $order['apply_status']['text']);
            $sheet->setCellValue('G' . ($index + 2), $order['create_time']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '提现明细') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    /**
     * 时间银行兑换订单导出
     */
    public function ExchangeList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('兑换记录');
        $sheet->setCellValue('A1', '兑换商品');
        $sheet->setCellValue('B1', '买家');
        $sheet->setCellValue('C1', '支付方式/配送');
        $sheet->setCellValue('D1', '总价');
        $sheet->setCellValue('E1', '状态');
        $sheet->setCellValue('F1', '兑换订单号');
        $sheet->setCellValue('G1', '兑换时间');
        $sheet->setCellValue('H1', '用户姓名');
        $sheet->setCellValue('I1', '数量');
        $sheet->setCellValue('J1', '所属运营商');
        $sheet->setCellValue('K1', '用户手机号');
        $sheet->setCellValue('L1', '用户收货地址');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            //10余额支付 20微信支付 30支付宝
            if ($order['pay_type']==10){
                $type = "余额支付";
            }if ($order['pay_type']==20){
                $type = "微信支付";
            }if ($order['pay_type']==30){
                $type = "支付宝支付";
            }
//            var_dump($order['total_time']."CFP.""￥".$order['total_price']);die;
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['orderproduct'][0]['product_name'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['timebankuser']['nickName']);
            $sheet->setCellValue('C' . ($index + 2), "\t" . $type . "\t");
            $sheet->setCellValue('D' . ($index + 2), $order['total_time']."CFP"."￥".$order['total_price']);
            $sheet->setCellValue('E' . ($index + 2), $order['status_desc']);
            $sheet->setCellValue('F' . ($index + 2), $order['order_no']);
            $sheet->setCellValue('G' . ($index + 2), $order['create_time']);
            $sheet->setCellValue('H' . ($index + 2), $order['address']['name']);
            $sheet->setCellValue('I' . ($index + 2), $order['orderproduct'][0]['total_num']);
            $sheet->setCellValue('J' . ($index + 2), $order['org_name']);
            $sheet->setCellValue('K' . ($index + 2), $order['timebankuser']['mobile']);
            $sheet->setCellValue('L' . ($index + 2), $order['address']['detail']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '兑换记录') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    /**
     *活动列表导出
     */
    public function ActivityList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('活动列表');
        $sheet->setCellValue('A1', '活动标题');
        $sheet->setCellValue('B1', '活动开始时间');
        $sheet->setCellValue('C1', '活动地点');
        $sheet->setCellValue('D1', '活动状态');
        $sheet->setCellValue('E1', '已报名/限额');
        $sheet->setCellValue('F1', '已收款');
        $sheet->setCellValue('G1', '发布时间');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
//            var_dump($order['status_desc']);die;
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['name'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['activity_time_start']);
            $sheet->setCellValue('C' . ($index + 2), $order['type']['text']);
            $sheet->setCellValue('D' . ($index + 2), $order['signup_status_text']['text']);
            $sheet->setCellValue('E' . ($index + 2), $order['activitylog_count']."/".$order['total']);
            $sheet->setCellValue('F' . ($index + 2), $order['activityorder_sum']);
            $sheet->setCellValue('G' . ($index + 2), $order['create_time']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '活动列表') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    /**
     *活动报名列表导出
     */
    public function ActivityLogList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('活动列表');
        $sheet->setCellValue('A1', '报名信息');
        $sheet->setCellValue('B1', '邀请人');
        $sheet->setCellValue('C1', '票种');
        $sheet->setCellValue('D1', '实付金额');
        $sheet->setCellValue('E1', '报名状态');
        $sheet->setCellValue('F1', '报名类型');
        $sheet->setCellValue('G1', '签到号');
        $sheet->setCellValue('H1', '伴手礼状态');
        $sheet->setCellValue('I1', '报名时间');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            if ($order['verify_come'] == 0)
            {
                $type ='现场报名';
            }if ($order['verify_come'] == 1){
                $type ='后台报名';
            }if ($order['verify_come'] == 2)
            {
                $type = '自主报名';
            }
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['name'] ."/".$order['mobile'] ."\t");
            $sheet->setCellValue('B' . ($index + 2), $order['referee']['nickName']);
            $sheet->setCellValue('C' . ($index + 2), $order['charge_type']['text']);
            $sheet->setCellValue('D' . ($index + 2), $order['activityOrder']['order_price']);
            $sheet->setCellValue('E' . ($index + 2), $order['status']['text']);
            $sheet->setCellValue('F' . ($index + 2), $type);
            $sheet->setCellValue('G' . ($index + 2), $order['sign_number']);
            $sheet->setCellValue('H' . ($index + 2), $order['is_receive'] == 1 ? '是':'否');
            $sheet->setCellValue('I' . ($index + 2), $order['create_time']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '活动报名列表') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    /**
     *活动订单列表导出
     */
    public function ActivityOrderList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('活动订单列表');
        $sheet->setCellValue('A1', '活动名称/票种');
        $sheet->setCellValue('B1', '实付款');
        $sheet->setCellValue('C1', '下单时间/订单号');
        $sheet->setCellValue('D1', '买家');
        $sheet->setCellValue('E1', '支付方式');
        $sheet->setCellValue('F1', '订单状态');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['activity'][0]['name'] ."/".$order['activity'][0]['charge_type']['text'] ."\t");
            $sheet->setCellValue('B' . ($index + 2), $order['order_price']);
            $sheet->setCellValue('C' . ($index + 2), $order['create_time']."/".$order['order_no']);
            $sheet->setCellValue('D' . ($index + 2), $order['user']['nickName']);
            $sheet->setCellValue('E' . ($index + 2), $order['pay_type']['text']);
            $sheet->setCellValue('F' . ($index + 2), $order['order_status']['text']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '活动订单列表') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 分销商导出
     */
    public function agentUserList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('分销商明细');
        $sheet->setCellValue('A1', '姓名');
        $sheet->setCellValue('B1', '手机号');
        $sheet->setCellValue('C1', '等级');
        $sheet->setCellValue('D1', '邀请人');
        $sheet->setCellValue('E1', '累计佣金');
        $sheet->setCellValue('F1', '可提现佣金');
        $sheet->setCellValue('G1', '待结算佣金');
        $sheet->setCellValue('H1', '累积推广金额');
        $sheet->setCellValue('I1', '累积消费金额');
        $sheet->setCellValue('J1', '客户数');
        $sheet->setCellValue('K1', '邀请分销商');
        $sheet->setCellValue('L1', '加入方式');
        $sheet->setCellValue('M1', '累积提现');
        $sheet->setCellValue('N1', '积分余额');
        $sheet->setCellValue('O1', '加入时间');
        //填充数据
        $index = 0;
        foreach ($list as $user) {
//            $user['totalMoney'] = sprintf('%.2f', $user['money'] + $user['freeze_money'] + $user['total_money']);
            $pushMoney = (new OrderDetailModel())->where('user_id', '=', $user['user_id'])->where('is_settled', '=', 1)->sum('money');
            $user['pushMoney'] = $pushMoney ? $pushMoney : 0;
            $agentNum = 0;
            $userID = (new RefereeModel())->where('agent_id', $user['user_id'])->column('user_id');
            if ($userID) {
                $agentNum = (new UserModel())->where('user_id', 'in', $userID)->count();
            }
            $user['agentNum'] = $agentNum;
            $user['total_num'] = $user['first_num'] + $user['second_num'] + $user['third_num'];
            $sheet->setCellValue('A' . ($index + 2), $user['real_name']);
            $sheet->setCellValue('B' . ($index + 2), "\t" . $user['mobile'] . "\t");
            $sheet->setCellValue('C' . ($index + 2), $user['grade']['name']);
            $sheet->setCellValue('D' . ($index + 2), $user['referee']['nickName']);
            $sheet->setCellValue('E' . ($index + 2), $user['totalMoney']);
            $sheet->setCellValue('F' . ($index + 2), $user['money']);
            $sheet->setCellValue('G' . ($index + 2), $user['notSettledMoney']);
            $sheet->setCellValue('H' . ($index + 2), $user['pushMoney']);
            $sheet->setCellValue('I' . ($index + 2), $user['user']['expend_money']);
            $sheet->setCellValue('J' . ($index + 2), $user['total_num']);
            $sheet->setCellValue('K' . ($index + 2), $user['agentNum']);
            $sheet->setCellValue('L' . ($index + 2), $user['add_type'] == 1 ? '前端申请' : '后台导入');
            $sheet->setCellValue('M' . ($index + 2), $user['total_money']);
            $sheet->setCellValue('N' . ($index + 2), $user['user']['exchangepurch']);
            $sheet->setCellValue('O' . ($index + 2), $user['create_time']);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '分销商明细') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 分销商订单导出
     */
    public function agentOrderList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列宽
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);

        //设置工作表标题名称
        $sheet->setTitle('分销商订单明细');

        $sheet->setCellValue('A1', '订单号');
        $sheet->setCellValue('B1', '商品信息');
        $sheet->setCellValue('C1', '订单总额');
        $sheet->setCellValue('D1', '一级分销商');
        $sheet->setCellValue('E1', '二级分销商');
        $sheet->setCellValue('F1', '三级分销商');
        $sheet->setCellValue('G1', '是否结算');
        $sheet->setCellValue('H1', '实付款金额');
        $sheet->setCellValue('I1', '支付方式');
        $sheet->setCellValue('J1', '下单时间');
        $sheet->setCellValue('K1', '买家');
        $sheet->setCellValue('L1', '买家留言');
        $sheet->setCellValue('M1', '配送方式');
        $sheet->setCellValue('N1', '自提门店名称');
        $sheet->setCellValue('O1', '自提联系人');
        $sheet->setCellValue('P1', '自提联系电话');
        $sheet->setCellValue('Q1', '收货人姓名');
        $sheet->setCellValue('R1', '联系电话');
        $sheet->setCellValue('S1', '收货人地址');
        $sheet->setCellValue('T1', '物流公司');
        $sheet->setCellValue('U1', '物流单号');
        $sheet->setCellValue('V1', '付款状态');
        $sheet->setCellValue('W1', '付款时间');
        $sheet->setCellValue('X1', '发货状态');
        $sheet->setCellValue('Y1', '发货时间');
        $sheet->setCellValue('Z1', '收货状态');
        $sheet->setCellValue('AA1', '收货时间');
        $sheet->setCellValue('AB1', '订单状态');
        $sheet->setCellValue('AC1', '微信支付交易号');
        $sheet->setCellValue('AD1', '是否已评价');

        //填充数据
        $index = 0;
        foreach ($list as $item) {
            $order = $item['order_master'];
            $address = $order['address'];
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['order_no'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $this->filterProductInfo($order));
            $sheet->setCellValue('C' . ($index + 2), $order['total_price']);
            $sheet->setCellValue('D' . ($index + 2), $this->agentUser($item['first_money'], $item['agent_first']));
            $sheet->setCellValue('E' . ($index + 2), $this->agentUser($item['second_money'], $item['agent_second']));
            $sheet->setCellValue('F' . ($index + 2), $this->agentUser($item['third_money'], $item['agent_third']));
            $sheet->setCellValue('G' . ($index + 2), "{$order['update_price']['symbol']}{$order['update_price']['value']}");
            $sheet->setCellValue('H' . ($index + 2), $order['pay_price']);
            $sheet->setCellValue('I' . ($index + 2), $order['pay_type']['text']);
            $sheet->setCellValue('J' . ($index + 2), $order['create_time']);
            $sheet->setCellValue('K' . ($index + 2), $order['user']['nickName']);
            $sheet->setCellValue('L' . ($index + 2), $order['buyer_remark']);
            $sheet->setCellValue('M' . ($index + 2), $order['delivery_type']['text']);
            $sheet->setCellValue('N' . ($index + 2), !empty($order['extract_store']) ? $order['extract_store']['shop_name'] : '');
            $sheet->setCellValue('O' . ($index + 2), !empty($order['extract']) ? $order['extract']['linkman'] : '');
            $sheet->setCellValue('P' . ($index + 2), !empty($order['extract']) ? $order['extract']['phone'] : '');
            $sheet->setCellValue('Q' . ($index + 2), $order['address']['name']);
            $sheet->setCellValue('R' . ($index + 2), $order['address']['phone']);
            $sheet->setCellValue('S' . ($index + 2), $address ? $address->getFullAddress() : '');
            $sheet->setCellValue('T' . ($index + 2), $order['express']['express_name']);
            $sheet->setCellValue('U' . ($index + 2), $order['express_no']);
            $sheet->setCellValue('V' . ($index + 2), $order['pay_status']['text']);
            $sheet->setCellValue('W' . ($index + 2), $this->filterTime($order['pay_time']));
            $sheet->setCellValue('X' . ($index + 2), $order['delivery_status']['text']);
            $sheet->setCellValue('Y' . ($index + 2), $this->filterTime($order['delivery_time']));
            $sheet->setCellValue('Z' . ($index + 2), $order['receipt_status']['text']);
            $sheet->setCellValue('AA' . ($index + 2), $this->filterTime($order['receipt_time']));
            $sheet->setCellValue('AB' . ($index + 2), $order['order_status']['text']);
            $sheet->setCellValue('AC' . ($index + 2), $order['transaction_id']);
            $sheet->setCellValue('AD' . ($index + 2), $order['is_comment'] ? '是' : '否');
            $index++;
        }

        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '分销商订单') . '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    /**
     * 奖励订单导出
     */
    public function bounsLists($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('分红明细');
        $sheet->setCellValue('A1', '结算月');
        $sheet->setCellValue('B1', '分销商名称');
        $sheet->setCellValue('C1', '分销商电话');
        $sheet->setCellValue('D1', '分销商等级');
        $sheet->setCellValue('E1', '自购金额');
        $sheet->setCellValue('F1', '团队推广业绩');
        $sheet->setCellValue('G1', '直属团队推广业绩');
        $sheet->setCellValue('H1', '项目创业分红');
        $sheet->setCellValue('I1', '结算状态');
        $sheet->setCellValue('J1', '结算单号');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $status = "";
            $money = '';
            $settled_no = '';
            if (!isset($order['settled'])){
                if ($order['settled']['status'] == 0) {
                    $status = "结算失败";
                } elseif ($order['settled']['status'] == 1) {
                    $status = "结算成功";
                } elseif ($order['settled']['status'] == 10) {
                    $status = "待结算";
                }
                $money = $order['settled']['money'];
                $settled_no = $order['settled']['settled_no'];
            }
            $sheet->setCellValue('A' . ($index + 2), "\t" . $order['month'] . "\t");
            $sheet->setCellValue('B' . ($index + 2), $order['user']['real_name']);
            $sheet->setCellValue('C' . ($index + 2), "\t" . $order['user']['mobile'] . "\t");
            $sheet->setCellValue('D' . ($index + 2), $order['user']['grade']['name']);
            $sheet->setCellValue('E' . ($index + 2), $order['venturebonus_buy_money']);
            $sheet->setCellValue('F' . ($index + 2), $order['venturebonus_team_money']);
            $sheet->setCellValue('G' . ($index + 2), $order['venturebonus_direct_team_money']);
            $sheet->setCellValue('H' . ($index + 2), $money);
            $sheet->setCellValue('I' . ($index + 2), $status);
            $sheet->setCellValue('J' . ($index + 2), $settled_no);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '分红明细') . '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    /**
     *长者列表导出
     */
    public function ElderList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('长者列表');
        $sheet->setCellValue('A1', '姓名');
        $sheet->setCellValue('B1', '年龄');
        $sheet->setCellValue('C1', '加入时长');
        $sheet->setCellValue('D1', '状态');
        $sheet->setCellValue('E1', '身份证号');
        $sheet->setCellValue('F1', '电话');
        $sheet->setCellValue('G1', '街道社区');
        $sheet->setCellValue('H1', '地址');
        $sheet->setCellValue('I1', '紧急联系人');
        $sheet->setCellValue('J1', '标签');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
//            1正常2生病3失能4失智
            $statusMsg = '';
            if ($order['status']==1)
            {
                $statusMsg = '正常';
            }
            if ($order['status']==2)
            {
                $statusMsg = '生病';
            }
            if ($order['status']==3)
            {
                $statusMsg = '失能';
            }
            if ($order['status']==4)
            {
                $statusMsg = '失智';
            }
            $contectMsg = json_decode($order['contect_msg'],true);
            $contectMsg = $contectMsg['contect_name']."-".$contectMsg['contect_mobile'];
            $label='';

            foreach ($order['elderLabel'] as $labels)
            {
                $label.= $labels['label']['label_name']."|";
            }
            $sheet->setCellValue('A' . ($index + 2), $order['name']);
            $sheet->setCellValue('B' . ($index + 2), $order['age']);
            $sheet->setCellValue('C' . ($index + 2), $order['join_time']);
            $sheet->setCellValue('D' . ($index + 2), $statusMsg);
            $sheet->setCellValue('E' . ($index + 2), $order['id_card']);
            $sheet->setCellValue('F' . ($index + 2), $order['mobile']);
            $sheet->setCellValue('G' . ($index + 2), $order['street']);
            $sheet->setCellValue('H' . ($index + 2), $order['address']);
            $sheet->setCellValue('I' . ($index + 2), $contectMsg);
            $sheet->setCellValue('J' . ($index + 2), $label);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '长者列表') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    /**
     *关怀列表导出
     */
    public function CareList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('A')->setWidth(30);
        //设置工作表标题名称
        $sheet->setTitle('关怀列表');
        $sheet->setCellValue('A1', '时间');
        $sheet->setCellValue('B1', '关怀类型');
        $sheet->setCellValue('C1', '长者姓名');
        $sheet->setCellValue('D1', '关怀项目');
        $sheet->setCellValue('E1', '关怀执行人员');
        //填充数据
        $index = 0;
        foreach ($list as $order) {
            $label='';
            foreach ($order['carestaff'] as $labels)
            {
                $label.= $labels['staff']['name'].",";
            }
            $sheet->setCellValue('A' . ($index + 2), $order['care_time']);
            $sheet->setCellValue('B' . ($index + 2), $order['type'] ? '电话关怀' : '上门关怀');
            $sheet->setCellValue('C' . ($index + 2), $order['elder']['name']);
            $sheet->setCellValue('D' . ($index + 2),$order['project']['project_name']);
            $sheet->setCellValue('E' . ($index + 2), $label);
            $index++;
        }
        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8", "GB2312//IGNORE", '关怀列表') . '-' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
    /**
     *计次商品核销记录导出
     */
    public function verifLogsList($list,$search_type=1)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列宽
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);
        if ($search_type == 2) {
            //设置工作表标题名称
            $sheet->setTitle('旅游商品核销明细');

            $sheet->setCellValue('A1', '核销门店');
            $sheet->setCellValue('B1', '核销员');
            $sheet->setCellValue('C1', '商户名称');
            $sheet->setCellValue('D1', '订单号');
            $sheet->setCellValue('E1', '地点');
            $sheet->setCellValue('F1', '核销时间');
            $sheet->setCellValue('G1', '购卡时间');
            $sheet->setCellValue('H1', '姓名');
            $sheet->setCellValue('I1', '身份证号');
            $sheet->setCellValue('J1', '手机号');
            $sheet->setCellValue('K1', '剩余次数');
            $sheet->setCellValue('L1', '支付方式');
            $sheet->setCellValue('M1', '商品名称');
            $sheet->setCellValue('N1', '权益名称');
            $sheet->setCellValue('O1', '抵扣权益卡');
            //填充数据
            $index = 0;
            foreach ($list as $item) {
                $sheet->setCellValue('A' . ($index + 2), "\t" . $item['clerk']['store']['store_name'] . "\t");
                $sheet->setCellValue('B' . ($index + 2), "\t" . $item['clerk']['real_name'] . "\t");
                $sheet->setCellValue('C' . ($index + 2), "\t" . $item['OrderTravelers']['product']['order']['supplier']['name'] . "\t");
                $sheet->setCellValue('D' . ($index + 2), "\t" . $item['OrderTravelers']['product']['order']['order_no'] . "\t");
                $sheet->setCellValue('E' . ($index + 2),"\t" . $item['clerk']['store']['address'] . "\t");
                $sheet->setCellValue('F' . ($index + 2), $item['verify_time']);
                $sheet->setCellValue('G' . ($index + 2), date('Y-m-d H:i:s',$item['OrderTravelers']['product']['order']['pay_time']));
                $sheet->setCellValue('H' . ($index + 2), $item['OrderTravelers']['name']);
                $sheet->setCellValue('I' . ($index + 2), $item['OrderTravelers']['id_card']);
                $sheet->setCellValue('J' . ($index + 2), $item['OrderTravelers']['mobile']);
                $sheet->setCellValue('K' . ($index + 2), $item['OrderTravelers']['status'] == 0 ? 1 : 0);
                $sheet->setCellValue('L' . ($index + 2), $item['OrderTravelers']['card_order_product_id'] > 0 ? '权益卡抵扣' : $item['OrderTravelers']['product']['order']['pay_type']['text']);
                $sheet->setCellValue('M' . ($index + 2), $item['OrderTravelers']['product']['product_name']);
                $sheet->setCellValue('N' . ($index + 2), $item['OrderTravelers']['product']['benefit']['benefit_name']);
                $sheet->setCellValue('O' . ($index + 2), isset($item['OrderTravelers']['cardProduct']['product_name']) ? $item['OrderTravelers']['cardProduct']['product_name'] : '');
                $index++;
            }

            //保存文件
            $writer = new Xlsx($spreadsheet);
            $filename = iconv("UTF-8", "GB2312//IGNORE", '旅游商品核销明细') . '-' . date('YmdHis') . '.xlsx';

        } else {
            //设置工作表标题名称
            $sheet->setTitle('计次商品核销明细');

            $sheet->setCellValue('A1', '核销门店');
            $sheet->setCellValue('B1', '核销员');
            $sheet->setCellValue('C1', '商户名称');
            $sheet->setCellValue('D1', '订单号');
            $sheet->setCellValue('F1', '核销时间');
            $sheet->setCellValue('G1', '支付时间');
            $sheet->setCellValue('H1', '姓名');
            $sheet->setCellValue('I1', '身份证号');
            $sheet->setCellValue('J1', '手机号');
            $sheet->setCellValue('K1', '剩余次数');
            $sheet->setCellValue('L1', '支付方式');
            $sheet->setCellValue('M1', '商品名称');
            //填充数据
            $index = 0;
            foreach ($list as $item) {
                $sheet->setCellValue('A' . ($index + 2), "\t" . $item['clerk']['store']['store_name'] . "\t");
                $sheet->setCellValue('B' . ($index + 2), "\t" . $item['clerk']['real_name'] . "\t");
                $sheet->setCellValue('C' . ($index + 2), "\t" . $item['product']['order']['supplier']['name'] . "\t");
                $sheet->setCellValue('D' . ($index + 2), "\t" . $item['product']['order']['order_no'] . "\t");
                $sheet->setCellValue('F' . ($index + 2), $item['verify_time']);
                $sheet->setCellValue('G' . ($index + 2), date('Y-m-d H:i:s',$item['product']['order']['pay_time']));
                $sheet->setCellValue('H' . ($index + 2), $item['product']['order']['user']['nickName']);
                $sheet->setCellValue('I' . ($index + 2), $item['product']['order']['user']['id_card']);
                $sheet->setCellValue('J' . ($index + 2), $item['product']['order']['user']['mobile']);
                $sheet->setCellValue('K' . ($index + 2), $item['product']['verify_num']>0 ? $item['product']['total_num'] * $item['product']['verify_num']-$item['product']['already_verify'] : '不限次数');
                $sheet->setCellValue('L' . ($index + 2), $item['product']['order']['pay_type']['text']);
                $sheet->setCellValue('M' . ($index + 2), $item['product']['product_name']);
                $index++;
            }

            //保存文件
            $writer = new Xlsx($spreadsheet);
            $filename = iconv("UTF-8", "GB2312//IGNORE", '计次商品核销明细') . '-' . date('YmdHis') . '.xlsx';

        }


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 格式化商品信息
     */
    private function filterProductInfo($order)
    {
        $content = '';
        foreach ($order['product'] as $key => $product) {
            $content .= ($key + 1) . ".商品名称：{$product['product_name']}\n";
            !empty($product['product_attr']) && $content .= "　商品规格：{$product['product_attr']}\n";
            $content .= "　购买数量：{$product['total_num']}\n";
            $content .= "　商品总价：{$product['total_price']}元\n\n";
        }
        return $content;
    }

    /**
     * 格式化商品信息
     */
    private function agentUser($money, $user)
    {
        $content = '';
        if ($user) {
            $content .= ".昵称：{$user['nickName']}\n";
            !empty($user['mobile']) && $content .= "　电话：{$user['mobile']}\n";
            $content .= "　佣金：￥{$money}元\n\n";
        }

        return $content;
    }

    /**
     * 日期值过滤
     */
    private function filterTime($value)
    {
        if (!$value) return '';
        return date('Y-m-d H:i:s', $value);
    }

}