<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function getDepts()
    {
        $depts = Department::all();
        $data = [];
        foreach ($depts as $k => $v) {
            if ($v['parent_id'] === 0) {
                $v['children'] = $this->getChild($v['id'], $depts);
                $v['key'] = $v['id'];
                unset($v['id']);
                unset($v['updated_at']);
                unset($v['parent_id']);
                $data[] = $v;
            }
        }

        return response()->json(['result' => $data], 200);
    }

    public function getChild($pid, $depts)
    {
        $tree = [];
        foreach ($depts as $k => $v) {
            if ($v['parent_id'] === $pid) {                         // 匹配子记录
                $v['children'] = $this->getChild($v['id'], $depts); // 递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);                          // 如果子元素为空则unset()
                }
                $v['key'] = $v['id'];
                unset($v['id']);
                unset($v['updated_at']);
                unset($v['parent_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    public function getDeptSelecter()
    {
        $depts = Department::select('id', 'name', 'parent_id')->get()->toArray();
        $deptArr = [];
        foreach ($depts as $k => $v) {
            if ($v['parent_id'] === 0) {
                $v['children'] = $this->getChildSelecter($v['id'], $depts);
                $v['key'] = $v['id'];
                $v['value'] = (string)$v['id'];
                $v['label'] = $v['name'];
                unset($v['id']);
                unset($v['name']);
                unset($v['parent_id']);
                $deptArr[] = $v;
            }
        }

        return response()->json(['result' => $deptArr], 200);
    }

    public function getChildSelecter($pid, $depts)
    {
        $tree = [];
        foreach ($depts as $k => $v) {
            if ($v['parent_id'] === $pid) {                                 // 匹配子记录
                $v['children'] = $this->getChildSelecter($v['id'], $depts); // 递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);                                  // 如果子元素为空则unset()
                }
                $v['label'] = $v['name'];
                $v['key'] = $v['id'];
                $v['value'] = (string)$v['id'];
                unset($v['id']);
                unset($v['name']);
                unset($v['parent_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    public function add(Request $request)
    {
        $data = $request->input();
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = DB::table('department')->insert($data);

        return $result ? response()->json(['result' => true], 200) : response()->json(['result' => false], 200);
    }
}
