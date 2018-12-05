<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\FeeOverviewMonthReport;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * 获取概览表（月报）数据
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOverviewMonthData(Request $request)
    {
        $param = $request->input();
        $report = new FeeOverviewMonthReport();
        $data = $report->getOverviewMonthData($param);

        return response()->json(['result' => $data], 200);
    }

    /**
     * 导出概览表（月报）数据
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportOverviewMonthData(Request $request)
    {
        $param = $request->input();
        $report = new FeeOverviewMonthReport();
        $data = $report->exportOverviewMonthData($param);

        return response()->json(['result' => $data], 200);
    }
}
