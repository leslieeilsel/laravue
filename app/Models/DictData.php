<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DictData extends Model
{
    public $timestamps = true;

    protected $table = 'iba_system_dict_data';

    protected $fillable = ['title', 'value', 'description', 'dict_id', 'sort', 'status'];

    public function dict()
    {
        return $this->belongsTo('App\Models\Dict');
    }
}
