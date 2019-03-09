<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\Projects;
use App\Models\Project\ProjectPlan;
use App\Models\Project\ProjectSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ProjectInfo;
use App\Models\OperationLog;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectEarlyWarning;
use Illuminate\Support\Facades\Storage;
use App\Models\Dict;

class ProjectController extends Controller
{
    /**
     * 获取一级项目信息
     *
     * @return JsonResponse
     */
    public function getProjects()
    {
        $projects = Projects::all()->toArray();

        $projectIds = ProjectPlan::all()->pluck('project_id')->toArray();
        $projectIds = array_unique($projectIds);

        foreach ($projects as $key => $value) {
            $projects[$key]['parent_title'] = '一级项目';
            $projects[$key]['deep'] = 1;
            $projects[$key]['is_parent'] = in_array($value['id'], $projectIds, true);
        }

        return response()->json(['result' => $projects], 200);
    }

    public function loadPlan($project_id)
    {
        $yearPlans = ProjectPlan::where('project_id', $project_id)->where('parent_id', 0)->get()->toArray();

        $data = [];
        foreach ($yearPlans as $key => $value) {
            $value['children'] = $this->getChildPlan($value['id']);
            $value['key'] = $value['id'];
            $value['deep'] = 2;
            $data[] = $value;
        }

        return response()->json(['result' => $data], 200);
    }

    public function getChildPlan($parent_id)
    {
        $data = [];
        $monthPlans = ProjectPlan::where('parent_id', $parent_id)->get()->toArray();
        foreach ($monthPlans as $key => $value) {
            $value['key'] = $value['id'];
            $value['deep'] = 3;
            $data[] = $value;
        }

        return $data;
    }

    /**
     * 创建项目信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->input();
        $data['plan_start_at'] = date('Y-m', strtotime($data['plan_start_at']));
        $data['plan_end_at'] = date('Y-m', strtotime($data['plan_end_at']));
        $data['positions'] = self::buildPositions($data['positions']);
        $data['created_at'] = date('Y-m-d H:i:s');

        $planData = $data['projectPlan'];

        unset($data['projectPlan']);

        $id = DB::table('iba_project_projects')->insertGetId($data);
        $this->insertPlan($id, $planData, [$data['plan_start_at'], $data['plan_end_at']]);

        $result = $id ? true : false;

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建项目信息');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 添加项目计划
     *
     * @param $projectId
     * @param $planData
     * @param $planDate
     */
    public function insertPlan($projectId, $planData, $planDate)
    {
        $startDate = $planDate[0];
        $endDate = $planDate[1];
        $monthArr = $this->getMonthList(strtotime($startDate), strtotime($endDate));
        foreach ($planData as $k => $v) {
            $v['project_id'] = $projectId;
            $v['parent_id'] = 0;
            $v['created_at'] = date('Y-m-d H:i:s');

            $parentId = DB::table('iba_project_plan')->insertGetId($v);

            $yearAmount = $v['amount'];
            $monthAmount = round($yearAmount / count($monthArr[$v['date']]), 2);
            foreach ($monthArr[$v['date']] as $key => $value) {
                $monthData = [];
                $monthData['date'] = (int)$value['month'];
                $monthData['project_id'] = $projectId;
                $monthData['parent_id'] = $parentId;
                $monthData['amount'] = $monthAmount;
                $monthData['image_progress'] = '';
                $monthData['created_at'] = date('Y-m-d H:i:s');

                ProjectPlan::insert($monthData);
            }
        }
    }

    /**
     * 获取一段时间内的所有月份
     *
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getMonthList($startDate, $endDate)
    {
        $yearStart = date('Y', $startDate);
        $monthStart = date('m', $startDate);

        $yearEnd = date('Y', $endDate);
        $monthEnd = date('m', $endDate);

        if ($yearStart == $yearEnd) {
            $monthInterval = $monthEnd - $monthStart;
        } elseif ($yearStart < $yearEnd) {
            $yearInterval = $yearEnd - $yearStart - 1;
            $monthInterval = (12 - $monthStart + $monthEnd) + 12 * $yearInterval;
        }
        //循环输出月份
        $data = [];
        for ($i = 0; $i <= $monthInterval; $i++) {
            $tmpTime = mktime(0, 0, 0, $monthStart + $i, 1, $yearStart);
            $data[$i]['year'] = date('Y', $tmpTime);
            $data[$i]['month'] = date('m', $tmpTime);
        }
        unset($tmpTime);

        $data = collect($data)->groupBy('year')->toArray();

        return $data;
    }

    public function addProjectPlan(Request $request)
    {
        $data = $request->input();
        $result = ProjectPlan::insert($data);

        // if ($result) {
        //     $log = new OperationLog();
        //     $log->eventLog($request, '创建项目计划');
        // }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 修改项目信息
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        if ($data['deep'] === 1) {
            $data['plan_start_at'] = date('Y-m', strtotime($data['plan_start_at']));
            $data['plan_end_at'] = date('Y-m', strtotime($data['plan_end_at']));
            if ($data['is_parent']) {
                unset($data['loading'], $data['children']);
            }
            unset($data['id'], $data['updated_at'], $data['expand'], $data['parent_title'], $data['deep'], $data['is_parent'], $data['nodeKey'], $data['selected']);

            $result = Projects::where('id', $id)->update($data);
        } else {
            unset($data['id'], $data['updated_at'], $data['children'], $data['key'], $data['deep'], $data['nodeKey'], $data['selected']);
            if ($data['expand']) {
                unset($data['expand']);
            }
            $result = ProjectPlan::where('id', $id)->update($data);
        }

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改项目信息');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 获取所有项目信息
     *
     * @return JsonResponse
     */
    public function getAllProjects()
    {
        $projects = Projects::all()->toArray();

        return response()->json(['result' => $projects], 200);
    }

    /**
     * 删除菜单
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        $allIds = $request->all();
        if ($allIds['parentsIds']) {
            $parentsIds = explode(',', $allIds['parentsIds']);
            Projects::destroy($parentsIds);
            DB::table('iba_project_plan')->whereIn('project_id', $parentsIds)->delete();
        }
        if ($allIds['yearIds']) {
            $yearIds = explode(',', $allIds['yearIds']);
            ProjectPlan::destroy($yearIds);
            DB::table('iba_project_plan')->whereIn('parent_id', $yearIds)->delete();
        }
        if ($allIds['monthIds']) {
            $monthIds = explode(',', $allIds['monthIds']);
            ProjectPlan::destroy($monthIds);
        }

        return response()->json(['result' => true], 200);
    }

    public function getAllWarning()
    {
        $data = [];
        $result = ProjectEarlyWarning::all()->toArray();
        foreach ($result as $k => $row) {
            $data[$k]['key'] = $row['id'];
            $data[$k]['project_id'] = $row['project_id'];
            $data[$k]['title'] = $row['title'];
            $data[$k]['tags'] = $row['warning_type'];
        }
        return response()->json(['result' => $data], 200);
    }

    public static function buildPositions($positions)
    {
        $result = [];
        if ($positions) {
            foreach ($positions as $key => $value) {
                if ($value['status'] === 1) {
                    $result[] = $value['value'];
                }
            }
        }

        return implode(';', $result);
    }

    /**
     * 项目信息填报
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function projectProgress(Request $request)
    {
        $data = $request->input();        
        if ($data['month']) {
            $data['month'] = date('Y-m', strtotime($data['month']));
        }
        if ($data['build_start_at']) {
            $data['build_start_at'] = date('Y-m', strtotime($data['build_start_at']));
        }
        if ($data['build_end_at']) {
            $data['build_end_at'] = date('Y-m', strtotime($data['build_end_at']));
        }
        if ($data['start_at']) {
            $data['start_at'] = date('Y-m', strtotime($data['start_at']));
        }
        if ($data['plan_start_at']) {
            $data['plan_start_at'] = date('Y-m', strtotime($data['plan_start_at']));
        }
        if ($data['img_progress_pic']) {
            $data['img_progress_pic'] = substr($data['img_progress_pic'],1);
        }
        $result = ProjectSchedule::insert($data);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '投资项目进度填报');
        }

        return response()->json(['result' => $result], 200);
    }
    /**
     * 获取项目进度列表
     *
     * @return JsonResponse
     */
    public function projectProgressList(Request $request)
    {
        $data = $request->input();
        $ProjectSchedules = ProjectSchedule::all()->toArray();
        
        return response()->json(['result' => $ProjectSchedules], 200);
    }
    
    /**
     * 上传
     *
     * @return JsonResponse
     */
    public function uploadPic(Request $request)
    {
        $path = Storage::putFile(
            '/project/project-schedule', $request->file('img_pic')
        );        
        return response()->json(['result' => $path], 200);
    }
    
    /**
     * 查询项目计划
     *
     * @return JsonResponse
     */
    public function projectPlanInfo(Request $request)
    {
        $data = $request->input();
        $year=date('Y');
        if ($data['month']) {
            $year = date('Y', strtotime($data['month']));
        }
        $plans = DB::table('iba_project_plan')->where('date',$year)->first();
        return response()->json(['result' => $plans], 200);
    }
    /**
     * 查询建设性质
     *
     * @return JsonResponse
     */
    public function getData(Request $request)
    {
        $params = $request->input();
        $data=Dict::getOptionsByName($params['title']);
        return response()->json(['result' => $data], 200);
    }
    
}
