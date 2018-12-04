<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\FeeOverviewMonthReport;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFxfOverviewMonthData(Request $request)
    {
        $param = $request->input();
        $report = new FeeOverviewMonthReport();
        $data = $report->getFxfOverviewMonthData($param);

        return response()->json(['result' => $data], 200);
    }
}
