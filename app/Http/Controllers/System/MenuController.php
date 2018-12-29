<?php

namespace App\Http\Controllers\System;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MenuController
{
    public $chooseMenus = [];
    public function getMenus()
    {
        return response()->json(['result' => $this->getMenuList()], 200);
    }

    public function getMenuList($role_id = null)
    {
        $menus = Menu::select('id', 'title', 'path', 'description', 'created_at', 'parent_id')->get()->toArray();
        $data = [];
        foreach ($menus as $k => $v) {
            if ($v['parent_id'] === 0) {
                if ($role_id) {
                    $res = DB::table('ibiart_slms_role_menus')->where('role_id', $role_id)->where('menu_id', $v['id'])->first();
                    if ($res) {
                        $v['checked'] = (boolean) $res['checked'];
                    }
                }
                $v['children'] = $this->getChild($v['id'], $menus, $role_id);
                $v['key'] = $v['id'];
                $v['expand'] = true;
                $data[] = $v;
            }
        }

        return $data;
    }

    public function getChild($pid, $menus, $role_id)
    {
        $tree = [];
        foreach ($menus as $k => $v) {
            if ($v['parent_id'] === $pid) {
                if ($role_id) {
                    $res = DB::table('ibiart_slms_role_menus')->where('role_id', $role_id)->where('menu_id', $v['id'])->first();
                    if ($res) {
                        $v['checked'] = (boolean) $res['checked'];
                    }
                }
                // 匹配子记录
                $v['children'] = $this->getChild($v['id'], $menus, $role_id); // 递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);                          // 如果子元素为空则unset()
                }
                $v['key'] = $v['id'];
                $v['expand'] = true;
                $tree[] = $v;
            }
        }

        return $tree;
    }

    public function getMenuSelecter()
    {
        $menus = Menu::select('id', 'title', 'parent_id')->get()->toArray();
        $deptArr = [];
        foreach ($menus as $k => $v) {
            if ($v['parent_id'] === 0) {
                $v['children'] = $this->getChildSelecter($v['id'], $menus);
                $v['key'] = $v['id'];
                $v['value'] = (string)$v['id'];
                $v['label'] = $v['title'];
                unset($v['id']);
                unset($v['title']);
                unset($v['parent_id']);
                $deptArr[] = $v;
            }
        }

        return response()->json(['result' => $deptArr], 200);
    }

    public function getChildSelecter($pid, $menus)
    {
        $tree = [];
        foreach ($menus as $k => $v) {
            if ($v['parent_id'] === $pid) {                                 // 匹配子记录
                $v['children'] = $this->getChildSelecter($v['id'], $menus); // 递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);                                  // 如果子元素为空则unset()
                }
                $v['label'] = $v['title'];
                $v['key'] = $v['id'];
                $v['value'] = (string)$v['id'];
                unset($v['id']);
                unset($v['title']);
                unset($v['parent_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    /**
     * 添加菜单
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        $data = $request->input();
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = Menu::insert($data);

        return $result ? response()->json(['result' => true], 200) : response()->json(['result' => false], 200);
    }

    public function getMenuTree(Request $request)
    {
        $id = $request->input('id');
        $allMenus = $this->getMenuList($id);

        return response()->json(['result' => $allMenus], 200);
    }

    public function getRouter()
    {
        $roleId = Auth::user()->group_id;

        $result = DB::table('ibiart_slms_role_menus')->where('role_id', $roleId)->get()->toArray();
        $menuIds = array_column($result, 'menu_id');
        $routers = $this->buildRouter($menuIds);

        return response()->json(['result' => $routers], 200);
    }

    public function buildRouter($menuIds)
    {
        $menus = Menu::select('id', 'path', 'name', 'title', 'component', 'icon', 'parent_id')->whereIn('id', $menuIds)->where('enabled', 1)->get()->toArray();
        $data = [];
        foreach ($menus as $k => $v) {
            if ($v['parent_id'] === 0) {
                $v['path'] = ($v['path']) ? $v['path'] : '/' ;
                $v['component'] = ($v['component']) ? $v['component'] : 'layout' ;
                $v['meta']['title'] = $v['title'];
                $v['meta']['icon'] = $v['icon'];
                if (!$v['name']) {
                    unset($v['name']);
                }
                $v['children'] = $this->getRouterChild($v['id'], $menus);
                if ($v['children'] && count($v['children']) == 1) {
                    $v['showParent'] = false;
                }
                unset($v['id']);
                unset($v['title']);
                unset($v['icon']);
                unset($v['parent_id']);
                $data[] = $v;
            }
        }

        return $data;
    }

    public function getRouterChild($pid, $menus)
    {
        $tree = [];
        foreach ($menus as $k => $v) {
            $v['path'] = ($v['path']) ? $v['path'] : '/' ;
            $v['component'] = ($v['component']) ? $v['component'] : 'layout' ;
            $v['meta']['title'] = $v['title'];
            $v['meta']['icon'] = $v['icon'];
            if ($v['parent_id'] === $pid) {
                // 匹配子记录
                $v['children'] = $this->getRouterChild($v['id'], $menus); // 递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);                          // 如果子元素为空则unset()
                }
                unset($v['id']);
                unset($v['title']);
                unset($v['icon']);
                unset($v['parent_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }
}
