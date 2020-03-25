<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OperationLog extends Model
{
    public $timestamps = true;

    protected $table = 'sys_operation_logs';

    /**
     * 类型转化
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function eventLog($request, $active)
    {
        $user_id = 0;
        if (Auth::check()) {
            $user_id = (int) Auth::id();
        }
        if ('GET' != $request->method()) {
            $data = [
                'title'      => $active,
                'user_id'    => $user_id,
                'method'     => $request->method(),
                'path'       => $request->path(),
                'ip'         => $request->ip(),
                'ip_place'   => '',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            self::insert($data);
        }
    }
}
