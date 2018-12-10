<?php

namespace App\Http\Controllers\System;
use App\Models\Menu;
use Illuminate\Http\Request;


class MenuController
{
    public function getMenus()
    {
        $menus = Menu::select('id', 'title', 'url', 'description', 'created_at', 'parent_id')->get()->toArray();
        $data = [];
        foreach ($menus as $k => $v) {
            if ($v['parent_id'] === 0) {
                $v['children'] = $this->getChild($v['id'], $menus);
                $v['key'] = $v['id'];
                unset($v['id']);
                unset($v['parent_id']);
                $data[] = $v;
            }
        }

        return response()->json(['result' => $data], 200);
    }

    public function getChild ($pid, $menus) {
        $tree = [];
        foreach ($menus as $k => $v) {
            if ($v['parent_id'] === $pid) {                     //匹配子记录
                $v['children'] = $this->getChild($v['id'], $menus); //递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);             //如果子元素为空则unset()
                }
                $v['key'] = $v['id'];
                unset($v['id']);
                unset($v['parent_id']);
                $tree[] = $v;
            }
        }

        return $tree;
    }

    public function getMenuSelecter() {
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
        $data[] = [
            'label' => '根菜单',
            'key' => 0,
            'value' => (string)0,
            'children' => $deptArr
        ];

        return response()->json(['result' => $data], 200);
    }

    public function getChildSelecter ($pid, $menus) {
        $tree = [];
        foreach ($menus as $k => $v) {
            if ($v['parent_id'] === $pid) {                     //匹配子记录
                $v['children'] = $this->getChildSelecter($v['id'], $menus); //递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);             //如果子元素为空则unset()
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

    public function add(Request $request)
    {
        $data = $request->input();
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = Menu::insert($data);
        if ($result) {
            return response()->json(['result' => true], 200);
        } else {
            return response()->json(['result' => false], 200);
        }
    }
}