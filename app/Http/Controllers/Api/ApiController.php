<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Repositories\DfaoReport;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    /**
     * @return false|string
     */
    public function getSaleDayData(Request $request)
    {
        $startMonth = $request->input('startMonth');
        $endMonth = $request->input('endMonth');
        $report = new DfaoReport();
        $data = $report->getDfaoReportData($startMonth, $endMonth);

        return response()->json(['result' => $data], 200);
//        return json_encode($data);
    }
}
