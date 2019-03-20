<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectSchedule;
use App\Models\Project\Projects;
use App\Models\Dict;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use App\Models\ZipDownload;
class LedgerController extends Controller
{
    /**
     * 获取台账进度列表
     *
     * @return JsonResponse
     */
    public function projectLedgerList(Request $request)
    {
        $params = $request->input();
        $sql=$this->listData($params);
        return response()->json(['result' => $sql], 200);
    }
    public function listData($params=[]){
        $sql = ProjectSchedule::where('id', '>', 0);
        $start_year = date('Y', strtotime($params['start_at']));
        $end_year = date('Y', strtotime($params['end_at']));
        $start_month = date('m', strtotime($params['start_at']));
        $end_month = date('m', strtotime($params['end_at']));
        $month_num=$end_month-$start_month;
        $month=[];
        if ($start_year==$end_year) {
            if($end_month==$start_month){
                $month[0]=$start_year.'-'.$start_month;
            }else{
                for($i=0;$i<$month_num;$i++){
                    if(($start_month+$i)<10){
                        $month[$i]=$start_year.'-0'.($start_month+$i);
                    }else{
                        $month[$i]=$start_year.'-'.($start_month+$i);
                    }
                }
            }
        }else{
            if($end_month==$start_month){
                $month[0]=$start_year.'-'.$start_month;
            }else{
                for($i=0;$i<(12-$start_year);$i++){
                    if(($start_month+$i)<10){
                        $month[$i]=$start_year.'-0'.($start_month+$i);
                    }else{
                        $month[$i]=$start_year.'-'.($start_month+$i);
                    }
                }
            }
        }
        $sql = $sql->whereIn('month', $month);
        if ($params['search_project_id']) {
            $sql = $sql->where('project_id', $params['search_project_id']);
        }
        $sql = $sql->get()->toArray();

        foreach ($sql as $k => $row) {
            $projects = Projects::where('id', $row['project_id'])->first();
            $sql[$k]['project_id'] = $projects['title'];
            $sql[$k]['project_num'] = $projects['num'];
            $sql[$k]['nature'] = Dict::getOptionsArrByName('建设性质')[$projects['build_type']];
            $sql[$k]['subject'] = $projects['subject'];
            $sql[$k]['total_investors'] = $projects['amount'];
            $sql[$k]['scale_con'] = $projects['description'];
        }
        return $sql;
    }
    /**
     * 导出报表
     *
     * @param array $params
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export(Request $request)
    {
        $params = $request->input();
        $start_year = date('Y', strtotime($params['start_at']));
        $data=$this->listData($params);
        
        return $this->downLoadSchedule($data);
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
            ->setCellValue('B2', '沣西新城'.$start_year.'年重点建设项目台账')
            ->setCellValue('B3', '单位：万元')
            ->setCellValue('B4', '项目名称')
            ->setCellValue('D4', $data[0]['project_id'])
            ->setCellValue('K4', '建设性质')
            ->setCellValue('M4', $data[0]['nature'])
            ->setCellValue('B5', '投资主体')
            ->setCellValue('D5', $data[0]['subject'])
            ->setCellValue('K5', '项目总投资')
            ->setCellValue('M5', $data[0]['total_investors'])
            ->setCellValue('B6', "项目建设规模及主要内容")
            ->setCellValue('D6', $data[0]['acc_img_progress'])
            ->setCellValue('B7', $start_year.'年度项目计划投资')
            ->setCellValue('D7', $data[0]['plan_investors'])
            ->setCellValue('G7', $start_year.'年度项目主要建设内容')
            ->setCellValue('I7', $data[0]['month_img_progress']);
            $num=8;
            for($i=0;$i<count($data);$i++){
                $num=$num+$i*11;
                $spreadsheet->getActiveSheet()->setCellValue('B'.$num, $data[$i]['month'].'项目进度（需详细说明完成投资额、完成形象进度及相关手续办理情况）');
                $spreadsheet->getActiveSheet()->setCellValue('D'.$num, $data[$i]['scale_con']);
                $spreadsheet->getActiveSheet()->setCellValue('K'.$num, '存在问题（详细描述项目建设中需协调解决的手续办理、征地拆迁及影响项目施工进度的其他问题）');
                $spreadsheet->getActiveSheet()->setCellValue('M'.$num, $data[$i]['problem']);
            }
            
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('B1:O1')
            ->mergeCells('B2:O2')
            ->mergeCells('B3:O3')
            ->mergeCells('B4:C4')
            ->mergeCells('D4:J4')
            ->mergeCells('K4:L4')
            ->mergeCells('M4:O4')
            ->mergeCells('B5:C5')
            ->mergeCells('D5:J5')
            ->mergeCells('K5:L5')
            ->mergeCells('M5:O5')
            ->mergeCells('B6:C6')
            ->mergeCells('D6:O6')
            ->mergeCells('B7:C7')
            ->mergeCells('D7:F7')
            ->mergeCells('G7:H7')
            ->mergeCells('I7:O7');
            $num=8;
            for($i=0;$i<count($data);$i++){
                $num=$num+$i*11;
                $spreadsheet->getActiveSheet()->mergeCells('B'.$num.':C'.($num+10));
                $spreadsheet->getActiveSheet()->mergeCells('D'.$num.':J'.($num+10));
                $spreadsheet->getActiveSheet()->mergeCells('K'.$num.':L'.($num+10));
                $spreadsheet->getActiveSheet()->mergeCells('M'.$num.':O'.($num+10));
            }
        //  设置宽度
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(1.38);
        // 设置高度
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(22);
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(30.75);
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(15.75);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(29);
        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(28.5);
        $spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(70.5);
        $spreadsheet->getActiveSheet()->getRowDimension('7')->setRowHeight(71);
        
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
        $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('B3')->applyFromArray($numberStyleRight)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('K4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('K5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('M5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('B6')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('D6')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('B7')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('D7')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('G7')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('I7')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $num=8;
        for($i=0;$i<count($data);$i++){
            $num=$num+$i*11;
            $spreadsheet->getActiveSheet()->getStyle('B'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('D'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('K'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('M'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        }
        // 设置边框
        $borderStyleArray = [
            'borders' => [
                'buttomBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
            ],
        ];
        // $spreadsheet->getActiveSheet()->getStyle('B1')->applyFromArray($borderStyleArray);
        //字体大小
        $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="投资项目台账.xlsx"');
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
    /**
    * 下载图片
    *
    * @param Request $request
    * @return JsonResponse
    */
    public function downLoadSchedule(Request $request){
        $params = $request->input();
        $ProjectC=new ProjectController();
        $data=$ProjectC->projectProgressM($params);
        $zip = new ZipDownload();
        $path = 'storage/project/project-schedule';
        $url=$zip->downloadImages($path,$data);
        return response()->download($url);
    }
    /**
    * 导出填报
    *
    * @param Request $request
    * @return JsonResponse
    */
    public function exportSchedule(Request $request){
        $params = $request->input();
        $start_year = date('Y', strtotime($params['start_at']));
        $ProjectC=new ProjectController();
        $data=$ProjectC->projectProgressM($params);
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
            ->setCellValue('A2', '沣西新城'.$start_year.'年重点项目建设进度表')
            ->setCellValue('A3', '报送部门：')
            ->setCellValue('O3', '单位：万元')
            ->setCellValue('A4', '序号')
            ->setCellValue('B4', '项目名称')
            ->setCellValue('C4', '投资主体')
            ->setCellValue('D4', '建设起止年限')
            ->setCellValue('E4', '总投资')
            ->setCellValue('F4', '年计划投资')
            ->setCellValue('G4', '年计划形象进度')
            ->setCellValue('H4', '月形象进度')
            ->setCellValue('J4', '月实际完成投资')
            ->setCellValue('K4', '自开始累积完成投资')
            ->setCellValue('L4', '存在问题')
            ->setCellValue('M4', '开工/计划开工时间')
            ->setCellValue('N4', '土地征收情况及前期手续办理情况')
            ->setCellValue('P4', '资金来源（企业自筹、政府投资、中省资金）')
            ->setCellValue('Q4', '形象进度照片')
            ->setCellValue('R4', '备注');
        $num=5;
        $total_investors=0;
        $plan_investors=0;
        for($i=0;$i<count($data);$i++){
            $total_investors=$total_investors+$data[$i]['total_investors'];
            $plan_investors=$plan_investors+$data[$i]['plan_investors'];
            $money_from=Dict::getOptionsByName('资金来源');
            //图片
            $pics=explode(',',$data[$i]['img_progress_pic']);
            $pic=[];
            for($p=0;$p<count($pics);$p++){
                $pic[$p]=$_SERVER["HTTP_HOST"].'/'.$pics[$p];
            }
            $spreadsheet->getActiveSheet()->setCellValue('A'.$num,$i+1);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$num,$data[$i]['project_id']);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$num,$data[$i]['subject']);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$num,$data[$i]['build_start_at']."/".$data[$i]['build_end_at']);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$num,$data[$i]['total_investors']);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$num,$data[$i]['plan_investors']);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$num,$data[$i]['plan_img_progress']);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$num,$data[$i]['month_img_progress']);
            $spreadsheet->getActiveSheet()->setCellValue('J'.$num,$data[$i]['month_act_complete']);
            $spreadsheet->getActiveSheet()->setCellValue('K'.$num,$data[$i]['acc_complete']);
            $spreadsheet->getActiveSheet()->setCellValue('L'.$num,$data[$i]['problem']);
            $spreadsheet->getActiveSheet()->setCellValue('M'.$num,$data[$i]['plan_build_start_at']);
            $spreadsheet->getActiveSheet()->setCellValue('N'.$num,$data[$i]['exp_preforma']);
            $spreadsheet->getActiveSheet()->setCellValue('P'.$num,$money_from[$data[$i]['money_from']]['title']);
            $spreadsheet->getActiveSheet()->setCellValue('Q'.$num,$data[$i]['month']);
            $spreadsheet->getActiveSheet()->setCellValue('R'.$num,$data[$i]['marker']);
            $num++;
        }
        $spreadsheet->getActiveSheet()->setCellValue('A'.$num,'合计：'.count($data).' 个');
        $spreadsheet->getActiveSheet()->setCellValue('E'.$num,$total_investors);
        $spreadsheet->getActiveSheet()->setCellValue('F'.$num,$plan_investors);
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:R1')
            ->mergeCells('A2:R2')
            ->mergeCells('A3:N3')
            ->mergeCells('O3:R3')
            ->mergeCells('N4:O4')
            ->mergeCells('H4:I4');
        $num=5;
        for($i=0;$i<count($data);$i++){
            $spreadsheet->getActiveSheet()->mergeCells('N'.$num.":O".$num);
            $spreadsheet->getActiveSheet()->mergeCells('H'.$num.":I".$num);
            $num++;
        }
        $spreadsheet->getActiveSheet()->mergeCells('A'.$num.":C".$num);
        $spreadsheet->getActiveSheet()->mergeCells('H'.$num.":I".$num);
        //  设置宽度
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18.13);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(16.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(11.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(27.63);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18.88);
        // $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(1);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(16.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(12.13);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(8.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(17.63);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(21.13);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(14.25);
        // 设置高度
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(19);
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(52);
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(41);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(93.75);
        $num=5;
        for($i=0;$i<count($data);$i++){
            $spreadsheet->getActiveSheet()->getRowDimension($num)->setRowHeight(147);
            $num++;
        }
        $spreadsheet->getActiveSheet()->getRowDimension($num)->setRowHeight(55);
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
        $spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($numberStyleLeft)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('O3')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('E4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('H4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('J4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('K4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('L4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('N4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('P4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('Q4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('R4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        
        $num=5;
        for($i=0;$i<count($data);$i++){
            $spreadsheet->getActiveSheet()->getStyle('A'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('B'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('C'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('D'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('E'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('F'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('G'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('H'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('J'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('K'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('L'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('M'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('N'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('P'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('Q'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('R'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $num++;
        }
        $spreadsheet->getActiveSheet()->getStyle('A'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('E'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('F'.$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        
        // 设置边框
        $borderStyleArray = [
            'borders' => [
                'buttomBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE
                ],
            ],
        ];
        // $spreadsheet->getActiveSheet()->getStyle('B1')->applyFromArray($borderStyleArray);
        //设置字体
        $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
        $spreadsheet->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('K4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('M4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('P4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('Q4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('R4')->getFont()->setBold(true);
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="投资项目台账.xlsx"');
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
