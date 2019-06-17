<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectSchedule;
use App\Models\Project\Projects;
use App\Models\Project\ProjectPlan;
use App\Models\Dict;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use App\Models\ZipDownload;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Role;
use App\Models\Departments;
class LedgerController extends Controller
{
    public $seeIds;
    public $office;

    public function __construct()
    {
        $this->getSeeIds();
    }

    public function getSeeIds()
    {
        if (Auth::check()) {
            $roleId = Auth::user()->group_id;
            $this->office = Auth::user()->office;
            $userId = Auth::id();
            $dataType = Role::where('id', $roleId)->first()->data_type;

            if ($dataType === 0) {
                $userIds = User::all()->toArray();
                $this->seeIds = array_column($userIds, 'id');
            }
            if ($dataType === 1) {
                $departmentIds = DB::table('iba_role_department')->where('role_id', $roleId)->get()->toArray();
                $departmentIds = array_column($departmentIds, 'department_id');
                $userIds = User::whereIn('department_id', $departmentIds)->get()->toArray();
                $this->seeIds = array_column($userIds, 'id');
            }
            if ($dataType === 2) {
                $this->seeIds = [$userId];
            }
        }
    }

    /**
     * 获取台账进度列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function projectLedgerList(Request $request)
    {
        $params = $request->input();
        $sql = $this->listData($params);
        $sql = $sql->where('iba_project_schedule.is_audit', 1)->whereIn('iba_project_schedule.user_id', $this->seeIds)->get()->toArray();
        foreach ($sql as $k => $row) {
            $sql[$k]['nature'] = Dict::getOptionsArrByName('建设性质')[$row['build_type']];
            $sql[$k]['description'] = Projects::where('id',$row['project_id'])->value('description');
        }
        return response()->json(['result' => $sql], 200);
    }

    public function listData($params = [])
    {
        $sql = DB::table('iba_project_schedule')->leftJoin('iba_project_projects', 'iba_project_schedule.project_id', '=', 'iba_project_projects.id');
        if (isset($params['start_at']) || isset($params['end_at'])) {
            if (isset($params['start_at']) && isset($params['end_at'])) {
                $params['start_at'] = date('Y-m', strtotime($params['start_at']));
                $params['end_at'] = date('Y-m', strtotime($params['end_at']));
                $sql = $sql->whereBetween('iba_project_schedule.month', [$params['start_at'], $params['end_at']]);
            } else {
                if (isset($params['start_at'])) {
                    $params['start_at'] = date('Y-m', strtotime($params['start_at']));
                    $sql = $sql->where('month', $params['start_at']);
                } elseif (isset($params['end_at'])) {
                    $params['end_at'] = date('Y-m', strtotime($params['end_at']));
                    $sql = $sql->where('iba_project_schedule.month', $params['end_at']);
                }
            }
        }
        
        if (isset($params['project_id'])||isset($params['money_from'])||isset($params['is_gc'])||isset($params['nep_type'])) {
            $projects = Projects::select('id');
            $i=0;
            if(isset($params['project_id'])){
                if($params['project_id']!=-1){
                    $projects = $projects->where('id', $params['project_id']);
                    $i++;
                }
            }
            if(isset($params['money_from'])){
                if($params['money_from']!=-1){
                    $projects = $projects->where('money_from', $params['money_from']);
                    $i++;
                }
            }
            if(isset($params['is_gc'])){
                if($params['is_gc']!=-1){
                    $projects = $projects->where('is_gc', $params['is_gc']);
                    $i++;
                }
            }
            if(isset($params['nep_type'])){
                if($params['nep_type']!=-1){
                    $projects = $projects->where('nep_type', $params['nep_type']);
                    $i++;
                }
            }
            $projects=$projects->get()->toArray();
            if($i>0){
                $ids = array_column($projects, 'id');
                // $ids = array_intersect($ids, $this->seeIds);
                $sql = $sql->whereIn('project_id', $ids);
            }
        }
        // if ($params['search_project_id']) {
        //     $sql = $sql->where('iba_project_schedule.project_id', $params['search_project_id']);
        // }
        return $sql;
    }
    /**
     * 导出台账报表
     *
     * @param Request $request
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(Request $request)
    {
        $params = $request->input();
        $sql = $this->listData($params);
        $data = $sql->where('iba_project_schedule.is_audit', 1)
                ->whereIn('iba_project_schedule.user_id', $this->seeIds)
                ->groupBy('project_id')
                ->get()->toArray();

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
        foreach ($data as $k => $row) {
            if($params['project_id']>0&&isset($params['project_id'])){
                $month_data = $this->listData($params)->where('iba_project_schedule.is_audit', 1)
                        ->whereIn('iba_project_schedule.user_id', $this->seeIds)
                        ->get()->toArray();
            }else{
                $month_data = $this->listData($params)->where('iba_project_schedule.is_audit', 1)
                        ->whereIn('iba_project_schedule.user_id', $this->seeIds)
                        ->where('iba_project_schedule.project_id',$row['project_id'])
                        ->get()->toArray();
            }
            $row['nature'] = Dict::getOptionsArrByName('建设性质')[$row['build_type']];
            if($k>0){
                $spreadsheet->createSheet();
            }
            // 添加表头
            $spreadsheet->setActiveSheetIndex($k)
                ->setCellValue('B2', '沣西新城年重点建设项目台账')
                ->setCellValue('B3', '单位：万元')
                ->setCellValue('B4', '项目名称')
                ->setCellValue('D4', $row['title'])
                ->setCellValue('K4', '建设性质')
                ->setCellValue('M4', $row['nature'])
                ->setCellValue('B5', '投资主体')
                ->setCellValue('D5', $row['subject'])
                ->setCellValue('K5', '项目总投资')
                ->setCellValue('M5', $row['total_investors'])
                ->setCellValue('B6', "项目建设规模及主要内容")
                ->setCellValue('D6', $row['description'])
                ->setCellValue('B7', '年度项目计划投资')
                ->setCellValue('D7', $row['plan_investors'])
                ->setCellValue('G7', '年度项目主要建设内容')
                ->setCellValue('I7', $row['plan_img_progress']);
            $num = 8;
            foreach($month_data as $m=>$m_val){
                $num = $num + $m * 11;
                $spreadsheet->getActiveSheet()->setCellValue('B' . $num, $m_val['month'] . '项目进度（需详细说明完成投资额、完成形象进度及相关手续办理情况）');
                $spreadsheet->getActiveSheet()->setCellValue('D' . $num, $m_val['month_act_complete'].','.$m_val['month_img_progress']);
                $spreadsheet->getActiveSheet()->setCellValue('K' . $num, '存在问题（详细描述项目建设中需协调解决的手续办理、征地拆迁及影响项目施工进度的其他问题）');
                $spreadsheet->getActiveSheet()->setCellValue('M' . $num, $m_val['problem']);
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
                $num = 8;
            foreach($month_data as $m=>$m_val){
                $num = $num + 11;
                $spreadsheet->getActiveSheet()->mergeCells('B' . $num . ':C' . ($num + 10));
                $spreadsheet->getActiveSheet()->mergeCells('D' . $num . ':J' . ($num + 10));
                $spreadsheet->getActiveSheet()->mergeCells('K' . $num . ':L' . ($num + 10));
                $spreadsheet->getActiveSheet()->mergeCells('M' . $num . ':O' . ($num + 10));
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
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            //右
            $numberStyleRight = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            //左
            $numberStyleLeft = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
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
            $num = 8;
            foreach($month_data as $m=>$m_val){
                $num = $num + $m * 11;
                $spreadsheet->getActiveSheet()->getStyle('B' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle('D' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle('K' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle('M' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            }
            // 设置边框
            $borderStyleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000000'],
                    ]
                ],
            ];
            $spreadsheet->getActiveSheet()->getStyle('B1:O1')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('B2:O2')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('B3:O3')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('D4:J4')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('K4:L4')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('M4:O4')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('B5:C5')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('D5:J5')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('K5:L5')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('M5:O5')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('B6:C6')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('D6:O6')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('B7:C7')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('D7:F7')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('G7:H7')->applyFromArray($borderStyleArray);
            $spreadsheet->getActiveSheet()->getStyle('I7:O7')->applyFromArray($borderStyleArray);
            
            $num = 8;
            foreach($month_data as $m=>$m_val){
                $num = $num + $m * 11;
                $spreadsheet->getActiveSheet()->getStyle('B' . $num . ':C' . ($num + 10))->applyFromArray($borderStyleArray);
                $spreadsheet->getActiveSheet()->getStyle('D' . $num . ':J' . ($num + 10))->applyFromArray($borderStyleArray);
                $spreadsheet->getActiveSheet()->getStyle('K' . $num . ':L' . ($num + 10))->applyFromArray($borderStyleArray);
                $spreadsheet->getActiveSheet()->getStyle('M' . $num . ':O' . ($num + 10))->applyFromArray($borderStyleArray);
            }
            //字体大小
            $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
            // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
            $spreadsheet->setActiveSheetIndex($k);
            // 重命名 worksheet
            $spreadsheet->getActiveSheet()->setTitle($row['title']);
        }
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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downLoadSchedule(Request $request)
    {
        $path = 'storage/project/project-schedule';
        $handler = opendir($path);
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != "..") {
                $fileInfo=pathinfo($path.'/'.$filename);
                if(isset($fileInfo['extension'])){
                    if($fileInfo['extension']==='zip'){
                        unlink($path.'/'.$filename);
                    }
                }
            }
        }
        $params = $request->input();
        $ProjectC = new ProjectController();
        $data = $ProjectC->projectProgressM($params)->get()->toArray();
        foreach ($data as $k => $row) {
            $Projects = Projects::where('id', $row['project_id'])->value('title');
            $data[$k]['project_title'] = $Projects;
        }
        $zip = new ZipDownload();
        $url = $zip->downloadImages($path, $data,$params);
        $is_file = file_exists($url);
        if ($is_file) {
            return response()->download($url);
        } else {
            return response()->download('storage/noPic.zip');
        }
    }
    /**
     * 导出进度填报
     *
     * @param Request $request
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportSchedule(Request $request)
    {
        $Letter=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO'];
        $params = $request->input();
        $ProjectC = new ProjectController();
        $data=$ProjectC->projectProgressM($params)->groupBy('project_id')->orderBy('project_id','asc')->get()->toArray();
        // $department_id = DB::table('users')->where('id', $data[0]['user_id'])->value('department_id');
        // $department_title = DB::table('iba_system_department')->where('id', $department_id)->value('title');
        foreach ($data as $k => $row) {
            $projects=Projects::where('id', $row['project_id'])->first();
            $data[$k]['money_from'] = $projects['money_from'];
            $data[$k]['project_title'] = $projects['title'];
            $data[$k]['build_start_at'] = $projects['plan_start_at'];
            $data[$k]['build_end_at'] = $projects['plan_end_at'];
        }       
        $schedule_data_month = $ProjectC->projectProgressM($params)->groupBy('month')->pluck('month')->toArray();
        $departments = Departments::where('id', Auth::user()->department_id)->value('title');
        // if(isset($params['end_at'])) {
        //     $end_at = date('Y-m', strtotime($params['end_at']));
        // }else{
        //     $end_at = date('Y-m');
        // }
        
        $start_m = 1;
        $start_at = date('Y-01');
        if (isset($params['end_at'])) {
            $end_at = date('Y-m', strtotime($params['end_at']));
            $end_m = (int)date('m', strtotime($params['end_at']));
        }else{
            $end_at = date('Y-m');
            $end_m = (int)date('m');
        }
            
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
            ->setCellValue('A2', '沣西新城重点项目建设进度表')
            ->setCellValue('A3', '报送部门：'.$departments)
            ->setCellValue('O3', '单位：万元')
            ->setCellValue('A4', '序号')
            ->setCellValue('B4', '项目名称')
            ->setCellValue('C4', '投资主体')
            ->setCellValue('D4', '建设起止年限')
            ->setCellValue('E4', '总投资')
            ->setCellValue('F4', '年计划投资')
            ->setCellValue('G4', '年计划形象进度');
        $l=7;
        $spreadsheet->getActiveSheet()->setCellValue('H4', $end_m.'月实际完成投资');
        $spreadsheet->getActiveSheet()->setCellValue('I4', $start_m.'-'.$end_m.'月形象进度');
        $spreadsheet->getActiveSheet()->setCellValue('J4', $start_m.'-'.$end_m.'月实际完成投资');
        $schedule_count = $ProjectC->projectProgressM($params)->groupBy('month')->pluck('id')->toArray();
        $s_count=count($schedule_count)*2+6;
        $spreadsheet->getActiveSheet()->setCellValue('K4', '自开始累积完成投资');
        $spreadsheet->getActiveSheet()->setCellValue('L4', '存在问题');
        $spreadsheet->getActiveSheet()->setCellValue('M4', '开工/计划开工时间');
        $spreadsheet->getActiveSheet()->setCellValue('N4', '土地征收情况及前期手续办理情况');
        $spreadsheet->getActiveSheet()->setCellValue('O4', '资金来源');
        $spreadsheet->getActiveSheet()->setCellValue('P4', '形象进度照片');
        $spreadsheet->getActiveSheet()->setCellValue('Q4', '备注');
        //居中
        $numberStyleCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        //右
        $numberStyleRight = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        //左
        $numberStyleLeft = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $num = 5;
        $total_investors=0;
        $plan_investors=0;
        for ($i = 0; $i < count($data); $i++) {
            $total_investor = $ProjectC->projectProgressM($params)->where('project_id',$data[$i]['project_id'])->where('is_audit',1)->max('total_investors');
            $plan_investor = ProjectPlan::where('project_id',$data[$i]['project_id'])->where('date',date('Y'))->value('amount');
            $schedule_data = $ProjectC->projectProgressM($params)->where('project_id',$data[$i]['project_id'])->get()->toArray();
            // $l=7;
            // foreach($schedule_data as $k=>$v){
            //     $m = (int)date('m', strtotime($v['month']));
            //     $spreadsheet->getActiveSheet()->setCellValue($Letter[$l+$k].'4', '1-'.$m.'月形象进度');
            //     $spreadsheet->getActiveSheet()->setCellValue($Letter[$l+$k+1].'4', '1-'.$m.'月实际完成投资');
            //     $l=$l+1;
            // }
            $total_investors=$total_investors+$total_investor;
            $plan_investors=$plan_investors+$plan_investor;
            $money_from = Dict::getOptionsByName('资金来源');
            $spreadsheet->getActiveSheet()->setCellValue('A' . $num, $i + 1);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $num, $data[$i]['project_title']);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $num, $data[$i]['subject']);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $num, $data[$i]['build_start_at'] . "/" . $data[$i]['build_end_at']);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $num, $total_investor);
            $spreadsheet->getActiveSheet()->setCellValue('F' . $num, $plan_investor);
            $spreadsheet->getActiveSheet()->setCellValue('G' . $num, $data[$i]['plan_img_progress']);
            // $le=7;
            // foreach($schedule_data as $k=>$v){
            //     $ac=array_keys($schedule_data_month,$v['month']);
            //     $ac=$ac[0]*2;
                $month_act_complete=ProjectSchedule::whereBetween('month',[$start_at,$end_at])->where('project_id',$data[$i]['project_id'])->sum('month_act_complete');     
                $spreadsheet->getActiveSheet()->setCellValue('H' . $num, $data[$i]['month_act_complete']);
                $spreadsheet->getActiveSheet()->setCellValue('I' . $num, $data[$i]['month_img_progress']);
                $spreadsheet->getActiveSheet()->setCellValue('J' . $num, $month_act_complete);

                $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18.88);
                $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(9.75);
                $spreadsheet->getActiveSheet()->getStyle('H'.'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle('I'.'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                
                $spreadsheet->getActiveSheet()->getStyle('H'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle('i'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle('H'. '4')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('i'.'4')->getFont()->setBold(true);
                // $le++;
            // }
            $acc_complete=$ProjectC->allActCompleteMoney($data[$i]['project_id'],$end_at);
            $spreadsheet->getActiveSheet()->setCellValue('K' . $num, $acc_complete);
            $spreadsheet->getActiveSheet()->setCellValue('L' . $num, $data[$i]['problem']);
            $spreadsheet->getActiveSheet()->setCellValue('M' . $num, $data[$i]['plan_build_start_at']);
            $spreadsheet->getActiveSheet()->setCellValue('N' . $num, $data[$i]['exp_preforma']);
            $spreadsheet->getActiveSheet()->setCellValue('O' . $num, $money_from[$data[$i]['money_from']]['title']);
            $spreadsheet->getActiveSheet()->setCellValue('P' . $num, $data[$i]['project_title']);
            $spreadsheet->getActiveSheet()->setCellValue('Q' . $num, $data[$i]['marker']);
            $num++;
        }
        $spreadsheet->getActiveSheet()->setCellValue('A' . $num, '合计：' . count($data) . ' 个');
        $spreadsheet->getActiveSheet()->setCellValue('E' . $num, $total_investors);
        $spreadsheet->getActiveSheet()->setCellValue('F' . $num, $plan_investors);
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:'.'Q1')
            ->mergeCells('A2:'.'Q2')
            ->mergeCells('A3:N3')
            ->mergeCells('O3:'.'Q3');
        $num = 5+count($data);
        $spreadsheet->getActiveSheet()->mergeCells('A' . $num . ":C" . $num);
        $spreadsheet->getActiveSheet()->mergeCells('H' . $num . ":I" . $num);
        //  设置宽度
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18.13);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(16.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(11.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(27.63);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18.88);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(16.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20.51);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(17.63);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(17.63);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(17.63);
        // 设置高度
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(19);
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(52);
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(41);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(93.75);
        $num = 5;
        for ($i = 0; $i < count($data); $i++) {
            $spreadsheet->getActiveSheet()->getRowDimension($num)->setRowHeight(147);
            $num++;
        }
        $spreadsheet->getActiveSheet()->getRowDimension($num)->setRowHeight(55);
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
        $spreadsheet->getActiveSheet()->getStyle('J4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('K4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('L4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('N4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('O4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('P4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('Q4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);


        $num = 5;
        for ($i = 0; $i < count($data); $i++) {
            $spreadsheet->getActiveSheet()->getStyle('A' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('B' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('C' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('D' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('E' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('F' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('G' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('H'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('I'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('J'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('K'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('L'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('M'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('N'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('O'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('P'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('Q'. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $num++;
        }
        $spreadsheet->getActiveSheet()->getStyle('A' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('E' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('F' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);

        // 设置边框
        $borderStyleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000000'],
                ]
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A1:Q1')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A2:Q2')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:N3')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('O3:Q3')->applyFromArray($borderStyleArray);  
        for($c=3;$c<count($data)+6;$c++){
            for($l=0;$l<17;$l++){
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l].$c)->applyFromArray($borderStyleArray);
            }
        }
        //设置字体
        $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
        $spreadsheet->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('K4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('M4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('p4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('Q4')->getFont()->setBold(true);
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('项目进度报表');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="投资项目进度报表.xlsx"');
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
     * 导出项目
     *
     * @param Request $request
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportProject(Request $request)
    {
        $Letter=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO'];
        $params = $request->input();
        $ProjectC = new ProjectController();
        $data=$ProjectC->allProjects($params);
        // $department_id = DB::table('users')->where('id', $data[0]['user_id'])->value('department_id');
        // $department_title = DB::table('iba_system_department')->where('id', $department_id)->value('title');
        $countAmount=0;
        $countPlanAmount=0;
        foreach ($data as $k => $row) {
            $countAmount=$countAmount+$row['amount'];
            $data[$k]['amount'] = number_format($row['amount'], 2);
            $data[$k]['land_amount'] = isset($row['land_amount']) ? number_format($row['land_amount'], 2) : '';
            $data[$k]['type'] = Dict::getOptionsArrByName('工程类项目分类')[$row['type']];
            $data[$k]['is_gc'] = Dict::getOptionsArrByName('是否为国民经济计划')[$row['is_gc']];
            $data[$k]['status'] = Dict::getOptionsArrByName('项目状态')[$row['status']];
            $data[$k]['money_from'] = Dict::getOptionsArrByName('资金来源')[$row['money_from']];
            $data[$k]['build_type'] = Dict::getOptionsArrByName('建设性质')[$row['build_type']];
            $data[$k]['nep_type'] = isset($row['nep_type']) ? Dict::getOptionsArrByName('国民经济计划分类')[$row['nep_type']] : '';
            $data[$k]['projectPlan'] = $ProjectC->getPlanData($row['id'], '');
            // $data[$k]['projectPlan'] = ProjectPlan::where('project_id', $row['id'])->where('date', (int)date('Y'))->first();
            $data[$k]['scheduleInfo'] = ProjectSchedule::where('project_id', $row['id'])->orderBy('id', 'desc')->first();
            $planAmount=$data[$k]['projectPlan'];
            foreach($planAmount as $k=>$v){
                if($v['date']==date('Y')){
                    $countPlanAmount=$countPlanAmount+$v['amount'];
                }
            }
        }
        
        
        $department_title = Departments::where('id', Auth::user()->department_id)->value('title');
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
            ->setCellValue('A2', date('Y').'年沣西新城重点项目表')
            ->setCellValue('A3', '责任部门：'.$department_title)
            ->setCellValue('AM3', '单位：万元')->setCellValue('A4', '序号')
            ->setCellValue('B4', '项目名称')->setCellValue('C4', '项目编号')->setCellValue('D4', '建设状态')
            ->setCellValue('E4', '投资主体')->setCellValue('F4', '项目类型')->setCellValue('G4', '承建单位')
            ->setCellValue('H4', '建设性质')->setCellValue('I4', '资金来源')->setCellValue('J4', '总投资(万元)')
            ->setCellValue('K4', '土地费用(万元)')->setCellValue('L4', '是否为国民经济计划')
            ->setCellValue('M4', '国民经济计划分类')->setCellValue('N4', '计划开始时间/结束时间')
            ->setCellValue('O4', '项目概况')->setCellValue('P4', date('Y').'年计划投资')
            ->setCellValue('Q4', date('Y').'年计划形象进度')->setCellValue('R4', '任务分解')
            ->setCellValue('R5', '1-1月')->setCellValue('T5', '1-2月')->setCellValue('V5', '1-3月')
            ->setCellValue('X5', '1-4月')->setCellValue('Z5', '1-5月')->setCellValue('AB5', '1-6月')
            ->setCellValue('AD5', '1-7月')->setCellValue('AF5', '1-8月')->setCellValue('AH5', '1-9月')
            ->setCellValue('AJ5', '1-10月')->setCellValue('AL5', '1-11月')->setCellValue('AN5', '1-12月')
            ->setCellValue('A6', '合计：'.count($data).'个')->setCellValue('J6', $countAmount)->setCellValue('P6', $countPlanAmount)
            ->setCellValue('R6', '计划投资')->setCellValue('S6', '计划形象进度')
            ->setCellValue('T6', '计划投资')->setCellValue('U6', '计划形象进度')
            ->setCellValue('V6', '计划投资')->setCellValue('W6', '计划形象进度')
            ->setCellValue('X6', '计划投资')->setCellValue('Y6', '计划形象进度')
            ->setCellValue('Z6', '计划投资')->setCellValue('AA6', '计划形象进度')
            ->setCellValue('AB6', '计划投资')->setCellValue('AC6', '计划形象进度')
            ->setCellValue('AD6', '计划投资')->setCellValue('AE6', '计划形象进度')
            ->setCellValue('AF6', '计划投资')->setCellValue('AG6', '计划形象进度')
            ->setCellValue('AH6', '计划投资')->setCellValue('AI6', '计划形象进度')
            ->setCellValue('AJ6', '计划投资')->setCellValue('AK6', '计划形象进度')
            ->setCellValue('AL6', '计划投资')->setCellValue('AM6', '计划形象进度')
            ->setCellValue('AN6', '计划投资')->setCellValue('AO6', '计划形象进度');
            
        //居中
        $numberStyleCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        //右
        $numberStyleRight = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        //左
        $numberStyleLeft = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $num = 7;
        for ($i = 0; $i < count($data); $i++) {
            $spreadsheet->getActiveSheet()->setCellValue('A'.$num, $i + 1);
            $spreadsheet->getActiveSheet()->setCellValue('B'.$num, $data[$i]['title']);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$num, $data[$i]['num']);
            $spreadsheet->getActiveSheet()->setCellValue('D'.$num, $data[$i]['status']);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$num, $data[$i]['subject']);
            $spreadsheet->getActiveSheet()->setCellValue('F'.$num, $data[$i]['type']);
            $spreadsheet->getActiveSheet()->setCellValue('G'.$num, $data[$i]['unit']);
            $spreadsheet->getActiveSheet()->setCellValue('H'.$num, $data[$i]['build_type']);
            $spreadsheet->getActiveSheet()->setCellValue('I'.$num, $data[$i]['money_from']);
            $spreadsheet->getActiveSheet()->setCellValue('J'.$num, $data[$i]['amount']);
            $spreadsheet->getActiveSheet()->setCellValue('K'.$num, $data[$i]['land_amount']);
            $spreadsheet->getActiveSheet()->setCellValue('L'.$num, $data[$i]['is_gc']);
            $spreadsheet->getActiveSheet()->setCellValue('M'.$num, $data[$i]['nep_type']);
            $spreadsheet->getActiveSheet()->setCellValue('N'.$num, $data[$i]['plan_start_at'].'/'.$data[$i]['plan_end_at']);
            $spreadsheet->getActiveSheet()->setCellValue('O'.$num, $data[$i]['description']);
            $projectPlan=$data[$i]['projectPlan'];
            foreach($projectPlan as $k=>$v){
                if($v['date']==date('Y')){
                    $spreadsheet->getActiveSheet()->setCellValue('P'.$num, $v['amount']);
                    $spreadsheet->getActiveSheet()->setCellValue('Q'.$num, $v['image_progress']);
                    $month=$v['month'];
                    $amount=0;
                    foreach($month as $m=>$vm){
                        $Le=17+($vm['date']-1)*2;
                        $amount=floatval($vm['amount'])+floatval($amount);
                        $spreadsheet->getActiveSheet()->setCellValue($Letter[$Le].$num, $amount);
                        $spreadsheet->getActiveSheet()->setCellValue($Letter[$Le+1].$num, $vm['image_progress']);
                    }
                }
            }
            $num++;
        }
        // 合并行、列
        $spreadsheet->getActiveSheet()->mergeCells('A1:AO1')->mergeCells('A2:AO2')->mergeCells('A3:AL3')->mergeCells('AM3:AO3')
            ->mergeCells('A4:A5')->mergeCells('B4:B5')->mergeCells('C4:C5')->mergeCells('D4:D5')
            ->mergeCells('E4:E5')->mergeCells('F4:F5')->mergeCells('G4:G5')->mergeCells('H4:H5')
            ->mergeCells('I4:I5')->mergeCells('J4:J5')->mergeCells('K4:K5')->mergeCells('L4:L5')
            ->mergeCells('M4:M5')->mergeCells('N4:N5')->mergeCells('O4:O5')->mergeCells('P4:P5')
            ->mergeCells('Q4:Q5')->mergeCells('R4:AO4')->mergeCells('R5:S5')->mergeCells('T5:U5')
            ->mergeCells('V5:W5')->mergeCells('X5:Y5')->mergeCells('Z5:AA5')->mergeCells('AB5:AC5')
            ->mergeCells('AD5:AE5')->mergeCells('AF5:AG5')->mergeCells('AH5:AI5')->mergeCells('AJ5:AK5')
            ->mergeCells('AL5:AM5')->mergeCells('AN5:AO5')->mergeCells('A6:I6');
            
        //  设置宽度
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(18.13);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(12.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(16.38);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(11.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(11.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(27.63);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(18.88);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(27.63);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(18.88);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(18.88);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(18.88);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(18.88);
        // 设置高度
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(19);
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(52);
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(41);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(52);
        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(41);
        $spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(52);
        $num = 7;
        for ($i = 0; $i < count($data); $i++) {
            $spreadsheet->getActiveSheet()->getRowDimension($num)->setRowHeight(81);
            $num++;
        }
        // $spreadsheet->getActiveSheet()->getRowDimension($num)->setRowHeight(55);
        $spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($numberStyleLeft)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AM3')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('A4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('B4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('D4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('E4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('F4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('H4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('I4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('J4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('K4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('L4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('N4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('O4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('P4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('Q4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('R4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('R5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('T5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('V5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('X5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('Z5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AB5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AD5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AF5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AH5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AJ5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AL5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('AN5')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);

        $num = 6;
        $count=count($data)+1;
        for ($i = 0; $i < $count; $i++) {
            for($l=0;$l<41;$l++){
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l].$num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            }
            $num++;
        }
        // 设置边框
        $borderStyleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000000'],
                ]
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle('A1:AO1')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A2:AO2')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3:AL3')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('AM3:AO3')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A4:A5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('B4:B5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('C4:C5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('D4:D5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('E4:E5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('F4:F5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('G4:G5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('H4:H5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('I4:I5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('J4:J5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('K4:K5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('L4:L5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('M4:M5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('N4:N5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('O4:O5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('P4:P5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('Q4:Q5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('R4:AO4')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('R5:S5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('T5:U5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('V5:W5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('X5:Y5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('Z5:AA5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('AB5:AC5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('AD5:AE5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('AF5:AG5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('AH5:AI5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('AJ5:AK5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('AL5:AM5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('AN5:AO5')->applyFromArray($borderStyleArray);
        $spreadsheet->getActiveSheet()->getStyle('A6:I6')->applyFromArray($borderStyleArray);
        $num = 6;
        $count=count($data)+1;
        for ($i = 0; $i < $count; $i++) {
            for($l=0;$l<41;$l++){
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l].$num)->applyFromArray($borderStyleArray);
            }
            $num++;
        }
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
        $spreadsheet->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('K4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('M4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('P4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('Q4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('R4')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('R5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('T5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('V5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('X5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('Z5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('AB5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('AD5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('AF5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('AH5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('AJ5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('AL5')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('AN5')->getFont()->setBold(true);
        for ($i = 0; $i < count($Letter); $i++) {
            $spreadsheet->getActiveSheet()->getStyle($Letter[$i].'6')->getFont()->setBold(true);
        }
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('项目报表');
        // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
        $spreadsheet->setActiveSheetIndex(0);
        // 将输出重定向到客户端的Web浏览器 (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="投资项目报表.xlsx"');
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
