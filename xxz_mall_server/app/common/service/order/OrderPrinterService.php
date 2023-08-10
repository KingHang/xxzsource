<?php

namespace app\common\service\order;

use app\common\model\setting\Setting as SettingModel;
use app\common\model\setting\Printer as PrinterModel;
use app\common\enum\settings\DeliveryTypeEnum;
use app\common\library\printer\Driver as PrinterDriver;
use app\common\enum\order\OrderSourceEnum;
use app\common\enum\order\OrderPayTypeEnum;

/**
 * 订单打印服务类
 */
class OrderPrinterService
{
    /**
     * 执行订单打印
     */
    public function printTicket($order, $scene = 20)
    {
        // 打印机设置
        $printerConfig = SettingModel::getSupplierItem('printer', $order['purveyor_id'], $order['app_id']);
        // 判断是否开启打印设置
        if (!$printerConfig['is_open']
            || !$printerConfig['printer_id']
            || !in_array($scene, $printerConfig['order_status'])) {
            return false;
        }
        // 获取当前的打印机
        $printer = PrinterModel::detail($printerConfig['printer_id']);
        if (empty($printer) || $printer['is_delete']) {
            return false;
        }
        // 实例化打印机驱动
        $PrinterDriver = new PrinterDriver($printer);
        // 获取订单打印内容
        $content = $this->getPrintContent($order);
        // 执行打印请求
        return $PrinterDriver->printTicket($content);
    }
    /**
     * 构建订单打印的内容
     */
    private function getPrintContent($order)
    {
        // 商城名称
        $storeName = SettingModel::getItem('store', $order['app_id'])['name'];
        // 收货地址
        $address = $order['address'];
        // 拼接模板内容
        $content = "<CB>{$storeName}</CB><BR>";

        $content .= "订单号：{$order['order_no']}<BR>";
        $content .= '付款时间：' . date('Y-m-d H:i:s', $order['pay_time']) . '<BR>';
        $content .= '<BR>';
        $content .= '配送方式：' .DeliveryTypeEnum::data()[$order['delivery_type']['value']]['name'] . '<BR>';
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXPRESS) {
            $content .= "收货人信息：{$address['name']},{$address['phone']}," .  $address->getFullAddress() . "<BR>";
        }
        // 自提信息
        if ($order['delivery_type']['value'] == DeliveryTypeEnum::EXTRACT && !empty($order['extract'])) {
            $content .= "自提门店：{$order['extract_store']['shop_name']}<BR>";
            $content .= "联系人：{$order['extract']['linkman']},{$order['extract']['phone']}<BR>";
        }
        $content .= '--------------------------------<BR>';
        $total_num = 0;
        if ($order['order_source'] == OrderSourceEnum::CARD) {
            // 卡项信息
            $content .= '=========== 卡项信息 ===========<BR>';
            $content .= "单价         数量       小计<BR>";
            foreach ($order['orderCarditem'] as $key => $product) {
                $total_num ++;
                $total_num ++;

                $content .= "{$product['title']}<BR>";
                $content .= "{$product['retail_price']}          {$product['total_num']}        {$product['total_price']}<BR>";
                $content .= " <BR>";
            }
        } else {
            // 商品信息
            $content .= '=========== 商品信息 ===========<BR>';
            $content .= "单价         数量        小计<BR>";
            foreach ($order['product'] as $key => $product) {
                $total_num ++;
                $product_attr = !empty($product['product_attr']) ? "(" . $product['product_attr'] . ")" : '';
                $content .= "<W><L><BOLD>{$product['product_name']}{$product_attr}</BOLD></L></W><BR>";
                $content .= "{$product['product_price']}        {$product['total_num']}        {$product['total_price']}<BR>";
                $content .= " <BR>";
            }
        }
        $content .= '--------------------------------<BR>';
        $content .= "<W><L><BOLD>共：{$total_num}件</W></L></BOLD><BR>";
        $content .= "配送费：               {$order['express_price']}元<BR>";

        // 订单金额
        if ($order['coupon_money'] > 0) {
            $content .= "优惠券：               -{$order['coupon_money']}元<BR>";
        }
        if ($order['points_num'] > 0) {
            $content .= "优惠券：               -{$order['points_money']}元<BR>";
        }
        if ($order['update_price']['value'] != '0.00') {
            $content .= "优惠券：               {$order['update_price']['symbol']}{$order['update_price']['value']}元<BR>";
        }

        $content .= "支付方式：           " . OrderPayTypeEnum::data()[$order['pay_type']['value']]['name'] . "<BR>";
        $content .= "<RIGHT><W><L><BOLD>实付款：</W></L></BOLD><W><L><BOLD>{$order['pay_price']}元</W></L></BOLD></RIGHT><BR>";
        $content .= '--------------------------------<BR>';

        if (!empty($order['buyer_remark'])) {
            $content .= '============ 买家备注 ============<BR>';
            $content .= "<B>{$order['buyer_remark']}</B><BR>";
            $content .= '--------------------------------<BR>';
        }
        $content .= "会员昵称：{$order['user']['nickName']} <BR>";
        $content .= "积分：{$order['user']['exchangepurch']} <BR>";
        $content .= "余额：{$order['user']['balance']} <BR>";
        $content .= " <BR>";
        $content .= " <BR>";
        $content .= "<C>感谢您的购物欢迎下次光临</C>";

        return $content;
    }


}