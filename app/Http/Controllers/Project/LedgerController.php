<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\ProjectSchedule;
use App\Models\Project\Projects;
use App\Models\Dict;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\Request;
use App\Models\ZipDownload;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Role;

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
            if(isset($params['project_id'])){
                $projects = $projects->where('project_id', $params['project_id']);
            }
            if(isset($params['money_from'])){
                $projects = $projects->where('money_from', $params['money_from']);
            }
            if(isset($params['is_gc'])){
                $projects = $projects->where('is_gc', $params['is_gc']);
            }
            if(isset($params['nep_type'])){
                $projects = $projects->where('nep_type', $params['nep_type']);
            }
            $projects=$projects->get()->toArray();
            $ids = array_column($projects, 'id');
            $ids = array_intersect($ids, $this->seeIds);
            $sql = $sql->whereIn('project_id', $ids);
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
            if($params['search_project_id']>0&&isset($params['search_project_id'])){
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
                ->setCellValue('D6', $row['acc_img_progress'])
                ->setCellValue('B7', '年度项目计划投资')
                ->setCellValue('D7', $row['plan_investors'])
                ->setCellValue('G7', '年度项目主要建设内容')
                ->setCellValue('I7', $row['plan_img_progress']);
            $num = 8;
            foreach($month_data as $m=>$m_val){
                $num = $num + $m * 11;
                $spreadsheet->getActiveSheet()->setCellValue('B' . $num, $m_val['month'] . '项目进度（需详细说明完成投资额、完成形象进度及相关手续办理情况）');
                $spreadsheet->getActiveSheet()->setCellValue('D' . $num, $m_val['month_img_progress']);
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
                $num = $num + $m * 11;
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
                    'buttomBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                    ],
                ],
            ];
            // $spreadsheet->getActiveSheet()->getStyle('B1')->applyFromArray($borderStyleArray);
            //字体大小
            $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
            // 将活动工作表索引设置为第一个工作表，以便Excel将其作为第一个工作表打开
            $spreadsheet->setActiveSheetIndex($k);
            // 重命名 worksheet
            $spreadsheet->getActiveSheet()->setTitle('sheet'.$k);
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
        $params = $request->input();
        $ProjectC = new ProjectController();
        $data = $ProjectC->projectProgressM($params);
        $zip = new ZipDownload();
        $path = 'storage/project/project-schedule';
        $url = $zip->downloadImages($path, $data);
        $is_file = file_exists($url);
        if ($is_file) {
            return response()->download($url);
        } else {
            return response()->download('storage/noPic.zip');
        }
    }

    /**
     * 导出填报
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
        $data=$ProjectC->projectProgressM($params)->groupBy('project_id')->get()->toArray();
        $department_id = DB::table('users')->where('id', $data[0]['user_id'])->value('department_id');
        $department_title = DB::table('iba_system_department')->where('id', $department_id)->value('title');
        foreach ($data as $k => $row) {
            $data[$k]['money_from'] = Projects::where('id', $row['project_id'])->value('money_from');
            $Projects = Projects::where('id', $row['project_id'])->value('title');
            $data[$k]['project_title'] = $Projects;
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
            ->setCellValue('A3', '报送部门：'.$department_title)
            ->setCellValue('O3', '单位：万元')
            ->setCellValue('A4', '序号')
            ->setCellValue('B4', '项目名称')
            ->setCellValue('C4', '投资主体')
            ->setCellValue('D4', '建设起止年限')
            ->setCellValue('E4', '总投资')
            ->setCellValue('F4', '年计划投资')
            ->setCellValue('G4', '年计划形象进度');

        $schedule_count = $ProjectC->projectProgressM($params)->groupBy('month')->count();
        $s_count=$schedule_count*2+6;
        $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+1].'4', '自开始累积完成投资');
        $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+2].'4', '存在问题');
        $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+3].'4', '开工/计划开工时间');
        $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+4].'4', '土地征收情况及前期手续办理情况');
        $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+5].'4', '资金来源');
        $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+6].'4', '形象进度照片');
        $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+7].'4', '备注');
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
        $total_investors = 0;
        $plan_investors = 0;
        for ($i = 0; $i < count($data); $i++) {
            $schedule_data = $ProjectC->projectProgressM($params)->where('project_id',$data[$i]['project_id'])->get()->toArray();
            $l=7;
            foreach($schedule_data as $k=>$v){
                $spreadsheet->getActiveSheet()->setCellValue($Letter[$l+$k].'4', $v['month'].'月形象进度');
                $spreadsheet->getActiveSheet()->setCellValue($Letter[$l+$k+1].'4', $v['month'].'月实际完成投资');
                $l=$l+1;
            }

            $total_investors = $total_investors + $data[$i]['total_investors'];
            $plan_investors = $plan_investors + $data[$i]['plan_investors'];
            $money_from = Dict::getOptionsByName('资金来源');
            $spreadsheet->getActiveSheet()->setCellValue('A' . $num, $i + 1);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $num, $data[$i]['project_title']);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $num, $data[$i]['subject']);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $num, $data[$i]['build_start_at'] . "/" . $data[$i]['build_end_at']);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $num, $data[$i]['total_investors']);
            $spreadsheet->getActiveSheet()->setCellValue('F' . $num, $data[$i]['plan_investors']);
            $spreadsheet->getActiveSheet()->setCellValue('G' . $num, $data[$i]['plan_img_progress']);
            $l=7;
            foreach($schedule_data as $k=>$v){
                $spreadsheet->getActiveSheet()->setCellValue($Letter[$l+$k] . $num, $v['month_img_progress']);
                $spreadsheet->getActiveSheet()->setCellValue($Letter[$l+$k+1] . $num, $v['month_act_complete']);
                
                $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$l+$k])->setWidth(18.88);
                $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$l+$k+1])->setWidth(9.75);
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l+$k].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l+$k+1].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l+$k]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l+$k+1]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l+$k]. '4')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle($Letter[$l+$k+1].'4')->getFont()->setBold(true);
                $l=$l+1;
            }
            $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+1] . $num, $data[$i]['acc_complete']);
            $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+2] . $num, $data[$i]['problem']);
            $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+3] . $num, $data[$i]['plan_build_start_at']);
            $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+4] . $num, $data[$i]['exp_preforma']);
            $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+5] . $num, $money_from[$data[$i]['money_from']]['title']);
            $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+6] . $num, $data[$i]['month']);
            $spreadsheet->getActiveSheet()->setCellValue($Letter[$s_count+7] . $num, $data[$i]['marker']);
            $num++;
        }
        $spreadsheet->getActiveSheet()->setCellValue('A' . $num, '合计：' . count($data) . ' 个');
        $spreadsheet->getActiveSheet()->setCellValue('E' . $num, $total_investors);
        $spreadsheet->getActiveSheet()->setCellValue('F' . $num, $plan_investors);
        // 合并行、列
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:R1')
            ->mergeCells('A2:R2')
            ->mergeCells('A3:N3')
            ->mergeCells('O3:R3');
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
        $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$s_count+1])->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$s_count+2])->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$s_count+3])->setWidth(16.75);
        $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$s_count+4])->setWidth(9.75);
        $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$s_count+5])->setWidth(20.51);
        $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$s_count+6])->setWidth(17.63);
        $spreadsheet->getActiveSheet()->getColumnDimension($Letter[$s_count+7])->setWidth(17.63);
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
        $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+1].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+2].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+3].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+4].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+5].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+6].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+7].'4')->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);


        $num = 5;
        for ($i = 0; $i < count($data); $i++) {
            $spreadsheet->getActiveSheet()->getStyle('A' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('B' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('C' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('D' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('E' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('F' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle('G' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+1]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+2]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+3]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+4]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+5]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+6]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+7]. $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
            $num++;
        }
        $spreadsheet->getActiveSheet()->getStyle('A' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('E' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('F' . $num)->applyFromArray($numberStyleCenter)->getAlignment()->setWrapText(true);

        // 设置边框
        $borderStyleArray = [
            'borders' => [
                'buttomBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
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
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+1].'4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+2].'4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+3].'4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+4].'4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+5].'4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+6].'4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle($Letter[$s_count+7].'4')->getFont()->setBold(true);
        // 重命名 worksheet
        $spreadsheet->getActiveSheet()->setTitle('sheet');
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
}
