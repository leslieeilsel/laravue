<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

        $data = $category ? $category->pluck('title', 'value')->toArray() : [];

        if ($data) {
            $i = 0;
            foreach ($data as $value => $title) {
                $data[$i] = [
                    'title' => $title,
                    'value' => $value
                ];
                $i++;
            }
        }

        return $data;
    }

    /**
     * 根据分类名称查找基础信息数据
     *
     * @param $name
     * @return array
     */
    public static function getOptionsArrByName($name)
    {
        if (!Cache::has('dictAllCache')) {
            Cache::put('dictAllCache', collect(Dict::with('data')->get()->toArray()), 10080);
        }
        $dictAllCache = Cache::get('dictAllCache');

        $category = $dictAllCache->where('title', $name)->first();

        return $category['data'] ? collect($category['data'])->pluck('title', 'value')->toArray() : [];
    }

}
