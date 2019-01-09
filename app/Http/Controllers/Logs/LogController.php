<?php

namespace App\Http\Controllers\Logs;

use App\Models\OperationLog;
use Illuminate\Support\Facades\DB;

class LogController
{
    public function getOperationlogs()
    {
        $result = OperationLog::get()->toArray();

        foreach ($result as $key => $value) {
            $user = DB::table('users')->where('id', $value['user_id'])->first();
            $result[$key]['username'] = $user['name'];
        }
        
        return response()->json(['result' => $result], 200);
    }
}
