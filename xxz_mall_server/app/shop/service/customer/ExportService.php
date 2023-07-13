<?php

namespace app\shop\service\customer;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * 订单导出服务类
 */
class ExportService
{
    /**
     * 订单导出
     */
    public function customerList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //列宽
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);

        //设置工作表标题名称
        $sheet->setTitle('客户信息');

        $sheet->setCellValue('A1', '昵称');
        $sheet->setCellValue('B1', '手机号');
        $sheet->setCellValue('C1', '性别');
        $sheet->setCellValue('D1', '会员等级');
        $sheet->setCellValue('E1', '余额');
        $sheet->setCellValue('F1', '赠送金余额');
        $sheet->setCellValue('G1', 'MCR');
        $sheet->setCellValue('H1', '黑名单');
        $sheet->setCellValue('I1', '注册时间');

        //填充数据
        $index = 0;
        foreach ($list as $item) {
//            $address = $order['address'];
            $sheet->setCellValue('A'.($index + 2), $item['nickname']);
            $sheet->setCellValue('B'.($index + 2), $item['mobile']);
            $sheet->setCellValue('C'.($index + 2), ($item['gender'] == 1 ? '男' : ($item['gender'] == 2 ? '女' : '未知')));
            $sheet->setCellValue('D'.($index + 2), $item['level']['level_name']);
            $sheet->setCellValue('E'.($index + 2), $item['balance']);
            $sheet->setCellValue('F'.($index + 2), $item['present_balance']);
            $sheet->setCellValue('G'.($index + 2), ($item['is_black'] == 1 ? '是' : '否'));
            $sheet->setCellValue('H'.($index + 2), ($item['is_black'] == 1 ? '是' : '否'));
            $sheet->setCellValue('I'.($index + 2), $item['create_time']);
            $index ++;
        }

        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8","GB2312//IGNORE", '客户'). '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 标签导出
     */
    public function groupList($list)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列宽
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);

        //设置工作表标题名称
        $sheet->setTitle('标签信息');

        $sheet->setCellValue('A1', '标签名称');
        $sheet->setCellValue('B1', '标签类型');

        //填充数据
        $index = 0;
        foreach ($list as $item) {
            $sheet->setCellValue('A'.($index + 2), $item['group_name']);
            $sheet->setCellValue('B'.($index + 2), ($item['group_type'] == 1 ? '自动标签' : '手动标签'));
            $index ++;
        }

        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8","GB2312//IGNORE", '标签'). '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
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