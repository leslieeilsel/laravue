<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dict extends Model
{
    public $timestamps = true;

    protected $table = 'iba_system_dict';

    protected $fillable = ['title', 'type', 'description'];

    public function data()
    {
        return $this->hasMany('App\Models\DictData');
    }

    /**
     * 根据分类名称查找基础信息数据
     *
     * @param $name
     * @return array
     */
    public static function getOptionsByName($name)
    {
        $category = self::where('title', $name)->first()->data;

        return $category ? $category->pluck('title', 'value')->toArray() : [];
    }
}
