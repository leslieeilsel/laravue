<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectLedger;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
class LedgerController extends Controller
{
    /**
     * 导出报表
     *
     * @param array $params
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export(Request $request)
    {
        $data = $request->input();
        $data['search_project_id']?$search_project_id=$data['search_project_id']:$search_project_id=1;
        $data['search_year']?$search_year=$data['search_year']:$search_year=date('Y');
        
        // $data = $this->getOverviewMonthData($params);
        // $startMonth = str_replace('-', '.', $params['startMonth']);
        // $endMonth = str_replace('-', '.', $params['endMonth']);
        // 创建一个Spreadsheet对象
        $spreadsheet = new Spreadsheet();
        // 设置文档属性
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        // 添加表头
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B2', '沣西新城'.$search_year.'年重点建设项目季度台账')
            ->setCellValue('B3', '单位：万元')
            ->setCellValue('B4', '项目名称')
            ->setCellValue('K4', '建设性质');
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('B1:O1')
            ->mergeCells('B2:O2')
            ->mergeCells('B3:O3')
            ->mergeCells('B4:C4')
            ->mergeCells('D4:J4')
            ->mergeCells('K4:L4')
            ->mergeCells('M4:O4');
        // // 添加动态数据
        // $spreadsheet->getActiveSheet()
        //     ->fromArray(
        //         $data,      // The data to set
        //         null,       // Array values with this value will not be set
        //         'A5'        // Top left coordinate of the worksheet range where
        //                     // we want to set these values (default is A1)
        //     );
        // //  设置宽度
        // $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(16);
        // $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(16);
        // 设置高度
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(30.75);
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(15.75);
        $spreadsheet->getActiveSheet()->getRowDimensions('4')->setRowHeight(29);
        $spreadsheet->getActiveSheet()->getRowDimensions('5')->setRowHeight(28.5);
        $spreadsheet->getActiveSheet()->getRowDimensions('6')->setRowHeight(70.5);
        $spreadsheet->getActiveSheet()->getRowDimension('7')->setRowHeight(71);
        
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(1.38);
        // 设置对齐方式
        //居中
        $numberStyleCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        //右
        $numberStyleRight = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        //左
        $numberStyleLeft = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ];
        $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray($numberStyleCenter);
        $spreadsheet->getActiveSheet()->getStyle('B3')->applyFromArray($numberStyleRight);
        // 设置边框
        $borderStyleArray = [
            'borders' => [
                'buttomBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('B1')->applyFromArray($borderStyleArray);
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="发行费分配概览-月报.xlsx"');
        header('Cache-Control: max-age=0');
        // 如果正在使用IE 9
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
