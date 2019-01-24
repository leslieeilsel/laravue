<?php

namespace App\Http\Controllers\Logs;

use App\Models\OperationLog;
use Illuminate\Support\Facades\DB;

class LogController
{
    public function getOperationLogs()
    {
        $result = OperationLog::all()->sortByDesc('created_at')->toArray();

        foreach ($result as $key => $value) {
            $user = DB::table('users')->where('id', $value['user_id'])->first();
            $result[$key]['username'] = $user['name'];
        }
        $result = array_merge($result);

        return response()->json(['result' => $result], 200);
    }
}
