<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneLog extends Model
{
    public $timestamps = false;
    protected $table = 'phone_log';

    protected $fillable = [
        'phone_number',
        'verified_at',
    ];
}
