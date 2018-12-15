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

        return response()->json(['result' => $data, 'baseUrl' => env('APP_URL')], 200);
    }

    /**
     * 导出概览表（月报）数据
     *
     * @param string $startMonth
     * @param string $endMonth
     * @param string $reportType
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportOverviewMonthData($startMonth, $endMonth, $reportType)
    {
        $params = [
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
            'reportType' => $reportType
        ];
        $report = new FeeOverviewMonthReport();
        $data = $report->export($params);
    }
}
