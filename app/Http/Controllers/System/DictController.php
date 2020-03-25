<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Dict;
use App\Models\DictData;
use App\Models\OperationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DictController extends Controller
{
    /**
     * 获取所有字典
     *
     * @return JsonResponse
     */
    public function dicts()
    {
        if (!Cache::has('dictCache')) {
            Cache::put('dictCache', collect(Dict::all()->toArray()), 10080);
        }
        $dictCache = Cache::get('dictCache');
        $data = $dictCache->toArray();

        return response()->json(['result' => $data], 200);
    }

    /**
     * 添加字典
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function addDict(Request $request)
    {
        $form = $request->input();

        $dict = new Dict($form);
        $dict->created_user_id = Auth::id();
        $result = $dict->save();

        if ($result) {
            $this->addLog($request, '新建字典分类');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 增加操作日志
     *
     * @param        $request
     * @param  string  $content
     */
    public function addLog($request, $content = '')
    {
        Cache::put('dictCache', collect(Dict::all()->toArray()), 10080);
        Cache::put('dictDataCache', collect(DictData::all()->toArray()), 10080);
        Cache::put('dictAllCache', collect(Dict::with('data')->get()->toArray()), 10080);
        $log = new OperationLog();
        $log->eventLog($request, $content);
    }

    /**
     * 编辑字典
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function editDict(Request $request)
    {
        $form = $request->input();

        $dict = Dict::find($form['id']);
        $dict->updated_user_id = Auth::id();
        $result = $dict->update($form);

        if ($result) {
            $this->addLog($request, '修改字典分类');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 删除字典
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function deleteDict(Request $request)
    {
        $id = $request->get('id');

        $dictRes = Dict::destroy($id);

        $dictDataRes = DictData::where('dict_id', $id)->delete();

        $result = ($dictRes && $dictDataRes >= 0);

        if ($result) {
            $this->addLog($request, '删除字典分类和数据');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 字典数据列表
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function dictDataList(Request $request)
    {
        if (!Cache::has('dictDataCache')) {
            Cache::put('dictDataCache', collect(DictData::all()->toArray()), 10080);
        }
        $dictDataCache = Cache::get('dictDataCache');

        $dict_id = $request->get('dict_id');
        $data = $dict_id ? array_values($dictDataCache->where('dict_id',
            $dict_id)->sortBy('sort')->all()) : $dictDataCache->toArray();

        return response()->json(['result' => $data], 200);
    }

    /**
     * 添加字典
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function addDictData(Request $request)
    {
        $form = $request->input();

        $dictData = new DictData($form);
        $dictData->created_user_id = Auth::id();
        $result = $dictData->save();

        if ($result) {
            $this->addLog($request, '新建字典数据');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 编辑字典数据
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function editDictData(Request $request)
    {
        $form = $request->input();

        $dictData = DictData::find($form['id']);
        $dictData->updated_user_id = Auth::id();
        $result = $dictData->update($form);

        $result = $result ? true : false;

        if ($result) {
            $this->addLog($request, '修改字典数据');
        }

        return response()->json(['result' => $result], 200);
    }

    /**
     * 删除字典数据
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function deleteDictData(Request $request)
    {
        $id = $request->get('id');
        $ids = explode(',', $id);

        $result = DictData::destroy($ids);

        $result = $result ? true : false;

        if ($result) {
            $this->addLog($request, '删除字典数据');
        }

        return response()->json(['result' => $result], 200);
    }
}
