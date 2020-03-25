<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DictData extends Model
{
    public $timestamps = true;

    protected $table = 'sys_dict_data';

    protected $fillable = ['title', 'value', 'description', 'dict_id', 'sort', 'status'];

    public function dict()
    {
        return $this->belongsTo('App\Models\Dict');
    }

    /**
     * 类型转化
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
}
