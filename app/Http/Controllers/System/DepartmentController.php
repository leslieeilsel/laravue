<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\OperationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    public $departmentCache;

    public function __construct()
    {
        if (!Cache::has('departmentsCache')) {
            Cache::put('departmentsCache', collect(Departments::all()->toArray()), 10080);
        }
        $this->departmentCache = Cache::get('departmentsCache');
    }

    /**
     * 获取部门
     *
     * @param $parentId
     *
     * @return JsonResponse
     */
    public function getByParentId($parentId)
    {
        $departments = Departments::where('parent_id', $parentId)->orderBy('sort', 'asc')->get()->toArray();
        $parentIds = Departments::all()->pluck('parent_id')->toArray();
        $parentIds = array_unique($parentIds);

        $parenTitle = ((int) $parentId === 0) ? '一级部门' : Departments::where('id', $parentId)->first()->title;
        foreach ($departments as $key => $value) {
            $departments[$key]['parent_title'] = $parenTitle;
            $departments[$key]['is_parent'] = in_array($value['id'], $parentIds, true);
        }

        return response()->json(['result' => $departments], 200);
    }

    /**
     * 创建部门
     *
     * @param  Request  $request
     *
     * @return JsonResponse
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
            Cache::put('departmentsCache', collect(Departments::all()->toArray()), 10080);
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 修改部门
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->input();
        $id = $data['id'];
        $data['update_by'] = Auth::user()->name;
        if ($data['is_parent']) {
            unset($data['loading'], $data['children'], $data['expand']);
        }
        unset($data['id'], $data['updated_at'], $data['parent_title'], $data['is_parent'], $data['nodeKey'], $data['selected']);

        $result = Departments::where('id', $id)->update($data);

        if ($result) {
            $log = new OperationLog();
            $log->eventLog($request, '修改部门');
            Cache::put('departmentsCache', collect(Departments::all()->toArray()), 10080);
        }

        return response()->json(['result' => $result], 200);
    }

    public function getAllDepartment()
    {
        $departments = $this->departmentCache->pluck('title', 'id')->toArray();

        return response()->json(['result' => $departments], 200);
    }

    public function getClassDepartment()
    {
        $departments = [];
        foreach ($this->departmentCache as $key => $value) {
            $departments[] = [
                'id'        => $value['id'],
                'title'     => $value['title'],
                'parent_id' => $value['parent_id'],
            ];
        }
        $data = $this->getClassDepartmentTree(0, $departments);

        return $data;
    }

    public function getClassDepartmentTree($pid, $departments)
    {
        $data = [];
        foreach ($departments as $k => $v) {
            if ($v['parent_id'] === $pid) {
                $parent_count = $this->departmentCache->where('parent_id', $v['id'])->count();
                if ($parent_count > 0) {
                    $v['children'] = $this->getClassDepartmentTree($v['id'], $departments);
                }
                $v['label'] = $v['title'];
                $v['value'] = (string) $v['id'];
                unset($v['id'], $v['title'], $v['parent_id']);
                $data[] = $v;
            }
        }

        return $data;
    }
}
