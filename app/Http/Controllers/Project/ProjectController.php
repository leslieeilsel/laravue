<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ProjectInfo;
use Illuminate\Support\Facades\Auth;
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
    public function getByParentId($parentId)
    {
        $departments = ProjectInfo::where('parent_id', $parentId)->get()->toArray();
        $parentIds = ProjectInfo::get()->pluck('parent_id')->toArray();
        $parentIds = array_unique($parentIds);
        
        $parenTitle = ($parentId == 0) ? '一级项目' : ProjectInfo::where('id', $parentId)->first()->title ;
        foreach ($departments as $key => $value) {
            $departments[$key]['parent_title'] = $parenTitle;
            $departments[$key]['is_parent'] = in_array($value['id'], $parentIds);
        }
        
        return response()->json(['result' => $departments], 200);
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

        $result = DB::table('iba_project_info')->insertGetId($data);

        $this->earlyWarning($data, $result, 'insert');
        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建项目信息');
        }

        return $result ? response()->json(['result' => true], 200) : response()->json(['result' => false], 200);
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
        unset($data['id'], $data['updated_at'], $data['parent_title'], $data['is_parent'], $data['nodeKey'], $data['selected']);

        $result = ProjectInfo::where('id', $id)->update($data);

        $this->earlyWarning($data, $id, 'update');
        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改项目信息');
        }

        return response()->json(['result' => $result], 200);
    }

    public function getAllDepartment()
    {
        $departments = ProjectInfo::all()->toArray();

        return response()->json(['result' => $departments], 200);
    }

    /**
     * 删除菜单
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        $id = $request->get('id');
        $ids = explode(',', $id);

        $menuRes = ProjectInfo::destroy($ids);
        $roleRes = DB::table('iba_project_early_warning')->whereIn('project_info_id', $ids)->delete();

        $result = ($menuRes && $roleRes > 0) ? true : false;

        return response()->json(['result' => $result], 200);
    }

    public function earlyWarning($data, $id, $type)
    {
        $insert = [];
        $insert['project_info_id'] = $id;
        $insert['title'] = $data['title'];
        if ($data['actual_start_at'] > $data['plan_start_at']) {
            $insert['warning_title'] = '项目延期开始';
        } elseif ($data['actual_start_at'] > $data['plan_start_at']) {
            $insert['warning_title'] = '项目延期结束';
        } else {
            $insert['warning_title'] = '项目进展正常';
        }

        if ($type === 'insert') {
            ProjectEarlyWarning::insert($insert);
        } else {
            ProjectEarlyWarning::where('project_info_id', $id)->update($insert);
        }
    }

    public function getAllWarning()
    {
        $data = [];
        $result = ProjectEarlyWarning::all()->toArray();
        foreach ($result as $k => $row) {
            $data[$k]['key'] = $row['id'];
            $data[$k]['project_info_id'] = $row['project_info_id'];
            $data[$k]['title'] = $row['title'];
            $data[$k]['tags'] = $row['warning_title'];
        }

        return response()->json(['result' => $data], 200);
    }
}
