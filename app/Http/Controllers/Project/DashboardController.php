<?php

namespace App\Http\Controllers\Project;

use App\Models\Departments;
use App\Models\Dict;
use App\Models\Project\ProjectPlan;
use App\Models\Project\Projects;
use App\Models\Project\ProjectSchedule;
use App\Models\ProjectEarlyWarning;
use App\User;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public $seeIds;

    public function __construct()
    {
        $this->getSeeIds();
    }

    public function getSeeIds()
    {
        $userIds = User::all()->toArray();
        $this->seeIds = array_column($userIds, 'id');
    }

    public function dashboard()
    {
        $currentYear = date('Y');
        $plan = ProjectPlan::select('project_id')->where('date', $currentYear)->get()->toArray();

        $projectIds = array_column($plan, 'project_id');
        $project = Projects::whereIn('is_audit', [1, 3])->whereIn('id', $projectIds)->whereIn('user_id', $this->seeIds)->get();
        $projects = $project->toArray();

        foreach ($projects as $k => $row) {
            $projects[$k]['amount'] = number_format($row['amount'], 2);
            $projects[$k]['land_amount'] = isset($row['land_amount']) ? number_format($row['land_amount'], 2) : '';
            $projects[$k]['type'] = Dict::getOptionsArrByName('工程类项目分类')[$row['type']];
            $projects[$k]['is_gc'] = Dict::getOptionsArrByName('是否为国民经济计划')[$row['is_gc']];
            $projects[$k]['status'] = Dict::getOptionsArrByName('项目状态')[$row['status']];
            $projects[$k]['money_from'] = Dict::getOptionsArrByName('资金来源')[$row['money_from']];
            $projects[$k]['build_type'] = Dict::getOptionsArrByName('建设性质')[$row['build_type']];
            $projects[$k]['nep_type'] = isset($row['nep_type']) ? Dict::getOptionsArrByName('国民经济计划分类')[$row['nep_type']] : '';
            $projects[$k]['projectPlan'] = $this->getPlanData($row['id'], 'preview');
            $projects[$k]['scheduleInfo'] = ProjectSchedule::where('project_id', $row['id'])->orderBy('id', 'desc')->first();
        }
        $count = count($projects);

        $mapData = $projects;
        $totalPlan = 0;
        foreach ($projects as $k => $v) {
            $planMoney = ProjectPlan::where('project_id', $v['id'])->where('date', $currentYear)->first()->amount;
            $totalPlan += (float) $planMoney;
        }
        $accPlan = 0;
        foreach ($projects as $k => $v) {
            $accComplete = ProjectSchedule::select('acc_complete')->where('project_id', $v['id'])->where('month', 'like', '%' . $currentYear . '%')->orderBy('month', 'desc')->first();
            if ($accComplete) {
                $accPlan += $accComplete->acc_complete;
            }
        }
        // 重点项目列表
        $pointList = $project->sortByDesc('amount')->toArray();

        // 饼图
        $typeAmount = $project->groupBy('type')->toArray();
        $typeArr = [];
        $typeOption = [];
        $typeCount = 0;
        foreach ($typeAmount as $k => $v) {
            $name = Dict::getOptionsArrByName('工程类项目分类')[$k];
            $typeArr[] = $name;
            $typeSum = array_sum(array_column($v, 'amount'));
            $typeCount += $typeSum;
            $typeOption[] = [
                'name' => $name,
                'value' => $typeSum,
            ];
        }

        // 项目概况
        $statusData = $project->groupBy('status')->toArray();
        $projectStatus = [
            [
                'status' => '在建',
                'fj' => '-',
                'lh' => '-',
                'sz' => '-',
                'sl' => '-',
            ],
            [
                'status' => '已建',
                'fj' => '-',
                'lh' => '-',
                'sz' => '-',
                'sl' => '-',
            ],
            [
                'status' => '停工',
                'fj' => '-',
                'lh' => '-',
                'sz' => '-',
                'sl' => '-',
            ],
        ];
        foreach ($statusData as $k => $v) {
            $v = collect($v)->groupBy('type')->toArray();
            foreach ($v as $kk => $vv) {
                if ($kk === 0) {
                    $projectStatus[$k]['fj'] = count($vv);
                }
                if ($kk === 1) {
                    $projectStatus[$k]['lh'] = count($vv);
                }
                if ($kk === 2) {
                    $projectStatus[$k]['sz'] = count($vv);
                }
                if ($kk === 3) {
                    $projectStatus[$k]['sl'] = count($vv);
                }
            }
        }

        // 项目预警列表
        $projectIds = array_column($projects, 'id');
        $projectSchedules = ProjectSchedule::whereIn('project_id', $projectIds)->get()->toArray();
        $scheduleIds = array_column($projectSchedules, 'id');

        $earlyWarning = new ProjectEarlyWarning;
        $result = $earlyWarning->whereIn('schedule_id', $scheduleIds)->get()->take(10)->toArray();
        $warningType = [
            '已经超额', '警告超额', '严重超额',
        ];
        $data = [];
        foreach ($result as $k => $row) {
            $data[$k]['key'] = $row['id'];
            $res = ProjectSchedule::where('id', $row['schedule_id'])->first();
            foreach ($projects as $kk => $v) {
                if ($v['id'] === (int)$res->project_id) {
                    $data[$k]['title'] = $v['title'];
                }
            }
            $data[$k]['project_id'] = $res->project_id;
            $data[$k]['problem'] = $res->problem;
            $data[$k]['tags'] = $warningType[$row['warning_type']];
            $data[$k]['schedule_at'] = $row['schedule_at'];
        }

        $allWarning = $data;

        $departmentNameArr = [];
        $departmentPlan = [];
        $departmentAcc = [];

        $departmentList = Departments::where('parent_id', '!=', 0)->where('id', '!=', 6)->get()->toArray();
        foreach ($departmentList as $k => $v) {
            $departmentNameArr[] = $v['title'];
            $userIds = User::where('department_id', $v)->get()->toArray();
            $projects = Projects::whereIn('is_audit', [1, 3])->whereIn('id', $projectIds)->whereIn('user_id', $userIds)->get();

            $totalPlanDept = 0;
            $totalAcc = 0;
            foreach ($projects as $k => $v) {
                $planMoney = ProjectPlan::where('project_id', $v['id'])->where('date', $currentYear)->first()->amount;
                $totalPlanDept += (float) $planMoney;
                $AccMoney = ProjectSchedule::select('acc_complete')->where('project_id', $v['id'])->where('month', 'like', '%' . $currentYear . '%')->orderBy('month', 'desc')->first();
                if ($AccMoney) {
                    $totalAcc += $AccMoney->acc_complete;
                }
            }
            $departmentPlan[] = $totalPlanDept;
            $departmentAcc[] = $totalAcc;
        }
        $data = [
            'total' => $count,
            'totalPlan' => $totalPlan,
            'accPlan' => $accPlan,
            'allWarning' => $allWarning,
            'typeOption' => [
                'legend' => $typeArr,
                'data' => $typeOption,
                'count' => $typeCount,
            ],
            'pointList' => $pointList,
            'projectStatus' => $projectStatus,
            'mapData' => $mapData,
            'departmentChartData' => [
                'departmentNameArr' => $departmentNameArr,
                'departmentPlan' =>$departmentPlan,
                'departmentAcc' => $departmentAcc
            ]
        ];
        return view('dashboard', ['data' => $data]);
    }

    /**
     * 获取计划数据
     *
     * @param integer $project_id
     * @param string  $status
     * @return array
     */
    public function getPlanData($project_id, $status)
    {
        $projectPlans = ProjectPlan::where('project_id', $project_id)->where('parent_id', 0)->get()->toArray();
        $data = [];
        foreach ($projectPlans as $k => $row) {
            $data[$k]['date'] = $row['date'];
            $data[$k]['amount'] = $status === 'preview' ? number_format($row['amount'], 2) : (float)$row['amount'];
            $data[$k]['image_progress'] = $row['image_progress'];
            $monthPlan = ProjectPlan::where('parent_id', $row['id'])->get()->toArray();
            foreach ($monthPlan as $key => $v) {
                $data[$k]['month'][$key]['date'] = $v['date'];
                $data[$k]['month'][$key]['amount'] = $status === 'preview' ? number_format($v['amount'], 2) : (float)$v['amount'];
                $data[$k]['month'][$key]['image_progress'] = $v['image_progress'];
            }
        }

        return $data;
    }

    public function slogan()
    {
        return view('slogan');
    }
}
