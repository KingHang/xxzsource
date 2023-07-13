<?php

namespace app\shop\controller\customer;

use app\shop\controller\Controller;
use app\shop\model\customer\User as UserModel;
use app\shop\model\customer\Group as GroupModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * 客户操作
 * @package app\shop\controller\customer
 */
class Operate extends Controller
{
    /**
     * 客户导出
     */
    public function export()
    {
        $model = new UserModel();
        return $model->exportList($this->postData());
    }

    /**
     * 客户导出
     */
    public function exportGroup()
    {
        $model = new GroupModel();
        return $model->exportGroup($this->postData());
    }

    /**
     * 下载导入客户模板
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //列宽
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);

        //设置工作表标题名称
        $sheet->setTitle('客户信息');

        $sheet->setCellValue('A1', '昵称');
        $sheet->setCellValue('B1', '姓名');
        $sheet->setCellValue('C1', '手机号');
        $sheet->setCellValue('D1', 'open_id');
        $sheet->setCellValue('E1', '积分');
        $sheet->setCellValue('F1', '余额');
        $sheet->setCellValue('G1', '注册时间(请输入文本的时间格式：2021-01-01)');

        //保存文件
        $writer = new Xlsx($spreadsheet);
        $filename = iconv("UTF-8","GB2312//IGNORE", '客户导入模板'). '-' . date('YmdHis') . '.xlsx';


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    /**
     * 客户导入
     */
    public function importCustomer()
    {
        // 文件信息
        $fileInfo = request()->file('iFile');
        $model = new UserModel();
        if($model->uploadCustomer($fileInfo)){
            return $this->renderSuccess('导入成功');
        }
        return $this->renderError($model->getError() ?: '导入失败');
    }
}