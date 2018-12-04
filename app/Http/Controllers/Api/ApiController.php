<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Repositories\FeeOverviewMonthReport;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @return false|string
     */
    public function getFxfOverviewMonthData(Request $request)
    {
        $param = $request->input();
        $report = new FeeOverviewMonthReport();
        $data = $report->getFxfOverviewMonthData($param);

        return response()->json(['result' => $data], 200);
//        return json_encode($data);
    }
}
