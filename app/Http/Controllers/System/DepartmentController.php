<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departments;
use Illuminate\Support\Facades\Auth;
use App\Models\OperationLog;

class DepartmentController extends Controller
{
    /**
     * 获取部门
     *
     * @param $parentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByParentId($parentId)
    {
        $departments = Departments::where('parent_id', $parentId)->orderBy('sort', 'asc')->get()->toArray();
        $parentIds = Departments::get()->pluck('parent_id')->toArray();
        $parentIds = array_unique($parentIds);
        
        $parenTitle = ($parentId == 0) ? '一级部门' : Departments::where('id', $parentId)->first()->title ;
        foreach ($departments as $key => $value) {
            $departments[$key]['parent_title'] = $parenTitle;
            $departments[$key]['is_parent'] = in_array($value['id'], $parentIds);
        }
        
        return response()->json(['result' => $departments], 200);
    }

    /**
     * 创建部门
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->input();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['create_by'] = Auth::user()->name;
        $result = Departments::insert($data);
        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '创建部门');
        }

        return $result ? response()->json(['result' => true], 200) : response()->json(['result' => false], 200);
    }

    /**
     * 修改部门
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $data['update_by'] = Auth::user()->name;
        if ($data['is_parent']) {
            unset($data['loading'], $data['children']);
        }
        unset($data['id'], $data['updated_at'], $data['parent_title'], $data['is_parent'], $data['nodeKey'], $data['selected']);

        $result = Departments::where('id', $id)->update($data);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改部门');
        }

        return response()->json(['result' => $result], 200);
    }
}
