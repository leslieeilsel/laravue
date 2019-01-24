<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Dict;
use App\Models\DictData;
use Illuminate\Support\Facades\Auth;

class DictController extends Controller
{
    /**
     * 获取所有字典
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dicts()
    {
        $data = Dict::all()->toArray();

        return response()->json(['result' => $data], 200);
    }

    /**
     * 添加字典
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addDict(Request $request)
    {
        $form = $request->input();

        $dict = new Dict($form);
        $dict->created_user_id = Auth::id();
        $result = $dict->save();

        return response()->json(['result' => $result], 200);
    }

    /**
     * 编辑字典
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editDict(Request $request)
    {
        $form = $request->input();

        $dict = Dict::find($form['id']);
        $dict->updated_user_id = Auth::id();
        $result = $dict->update($form);

        return response()->json(['result' => $result], 200);
    }

    /**
     * 删除字典
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDict(Request $request)
    {
        $id = $request->get('id');

        $dictRes = Dict::destroy($id);

        $dictDataRes = DictData::where('dict_id', $id)->delete();

        $result = ($dictRes && $dictDataRes >= 0) ? true : false;

        return response()->json(['result' => $result], 200);
    }

    /**
     * 字典数据列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dictDataList(Request $request)
    {
        $dict_id = $request->get('dict_id');

        $data = $dict_id ? DictData::where('dict_id', $dict_id)->orderBy('sort', 'asc')->get()->toArray() : DictData::all();

        return response()->json(['result' => $data], 200);
    }

    /**
     * 添加字典
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addDictData(Request $request)
    {
        $form = $request->input();

        $dictData = new DictData($form);
        $dictData->created_user_id = Auth::id();
        $result = $dictData->save();

        return response()->json(['result' => $result], 200);
    }

    /**
     * 编辑字典数据
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editDictData(Request $request)
    {
        $form = $request->input();

        $dictData = DictData::find($form['id']);
        $dictData->updated_user_id = Auth::id();
        $result = $dictData->update($form);

        $result = $result ? true : false;

        return response()->json(['result' => $result], 200);
    }

    /**
     * 删除字典数据
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteDictData(Request $request)
    {
        $id = $request->get('id');
        $ids = explode(',', $id);

        $result = DictData::destroy($ids);

        $result = $result ? true : false;

        return response()->json(['result' => $result], 200);
    }
}
