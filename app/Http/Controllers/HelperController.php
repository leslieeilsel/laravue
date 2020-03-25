<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HelperController extends Controller
{
    /**
     * 转化UTC时间
     *
     * @param  string  $value
     * @param  string  $format
     *
     * @return Carbon|string
     * @throws \Exception
     */
    public static function transformDateTime(string $value, string $format = 'Y-m-d')
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
        } catch (\ErrorException $e) {
            return Carbon::createFromFormat($format, $value);
        }
    }
}
