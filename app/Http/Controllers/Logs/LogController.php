<?php

namespace App\Http\Controllers\Logs;

use App\Models\OperationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController
{
    public function getOperationLogs(Request $request)
    {
        $params = $countParams = $request->all();
        unset($countParams['pageNumber'], $countParams['pageSize']);

        $result = $this->getLos($params)->orderBy('created_at', 'desc')->get()->toArray();
        $count = $this->getLos($countParams)->count();

        foreach ($result as $key => $value) {
            $user = DB::table('users')->where('id', $value['user_id'])->first();
            $result[$key]['username'] = $user['name'];
        }
        $result = array_merge($result);

        return response()->json(['result' => $result, 'total' => $count], 200);
    }

    public function getLos($params)
    {
        $query = new OperationLog();

        if (isset($params['pageNumber']) && isset($params['pageSize'])) {
            $query = $query
                ->limit($params['pageSize'])
                ->offset(($params['pageNumber'] - 1) * $params['pageSize']);
        }

        return $query;
    }
}
