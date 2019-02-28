<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project\Projects;
use App\Models\Project\ProjectPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ProjectInfo;
use App\Models\OperationLog;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectEarlyWarning;

class ProjectController extends Controller
{
    /**
     * 获取一级项目信息
     *
     * @param $parentId
     * @return JsonResponse
     */
    public function getProjects()
    {
        $peojects = Projects::get()->toArray();

        $projectIds = ProjectPlan::get()->pluck('project_id')->toArray();
        $projectIds = array_unique($projectIds);

        foreach ($peojects as $key => $value) {
            $peojects[$key]['parent_title'] = '一级项目';
            $peojects[$key]['deep'] = 1;
            $peojects[$key]['is_parent'] = in_array($value['id'], $projectIds);
        }
        
        return response()->json(['result' => $peojects], 200);
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
        $data['plan_start_at'] = date("Y-m-d", strtotime($data['plan_start_at']));
        $data['plan_end_at'] = date("Y-m-d", strtotime($data['plan_end_at']));
        $data['actual_start_at'] = date("Y-m-d", strtotime($data['actual_start_at']));
        $data['actual_end_at'] = date("Y-m-d", strtotime($data['actual_end_at']));
        $data['positions'] = $this->buildPositions($data['positions']);

        $result = Projects::insert($data);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建项目信息');
        }

        return response()->json(['result' => $result], 200);
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
        $data['plan_start_at'] = date("Y-m-d", strtotime($data['plan_start_at']));
        $data['plan_end_at'] = date("Y-m-d", strtotime($data['plan_end_at']));
        $data['actual_start_at'] = date("Y-m-d", strtotime($data['actual_start_at']));
        $data['actual_end_at'] = date("Y-m-d", strtotime($data['actual_end_at']));
        if ($data['is_parent']) {
            unset($data['loading'], $data['children']);
        }
        unset($data['id'], $data['updated_at'], $data['expand'], $data['parent_title'], $data['is_parent'], $data['nodeKey'], $data['selected']);

        $result = ProjectInfo::where('id', $id)->update($data);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改项目信息');
        }

        return response()->json(['result' => $result], 200);
    }

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
            $project_info_id = $row['project_info_id'];
            $parent_id = ProjectInfo::where('id', $project_info_id)->pluck('parent_id')->first();
            if ($parent_id === 0) {
                $data[$k]['key'] = $row['id'];
                $data[$k]['project_info_id'] = $row['project_info_id'];
                $data[$k]['title'] = $row['title'];
                $data[$k]['tags'] = $row['warning_title'];
            }
        }

        return response()->json(['result' => $data], 200);
    }

    public function buildPositions($positions)
    {
        $result = [];
        if ($positions) {
            foreach ($positions as $key => $value) {
                if ($value['status'] == 1) {
                    $result[] = $value['value'];
                }
            }
        }

        return implode(';', $result);
    }
}
