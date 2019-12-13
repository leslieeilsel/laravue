<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dict extends Model
{
    public $timestamps = true;

    protected $table = 'iba_system_dict';

    protected $fillable = ['title', 'type', 'description'];

    public function dictData()
    {
        return $this->hasMany('App\Models\DictData');
    }
    
    /**
     * 根据分类名称查找基础信息数据
     *
     * @param $name
     * @return array
     */
    public static function getOptionsArrByName($name)
    {
        $category = DB::table('iba_system_dict')->where('title', $name)->first();
        $data = $category? DB::table('iba_system_dict_data')->where('dict_id',$category['id'])->pluck('title', 'value')->toArray() : [];
        return $data;
    }

}
