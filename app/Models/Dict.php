<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Dict extends Model
{
    public $timestamps = true;

    protected $table = 'sys_dict_categories';

    protected $fillable = ['title', 'type', 'description'];

    /**
     * 类型转化
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 根据分类名称查找基础信息数据
     *
     * @param $name
     *
     * @return array
     */
    public static function getOptionsByName($name)
    {
        if (!Cache::has('dictAllCache')) {
            Cache::put('dictAllCache', collect(Dict::with('data')->get()->toArray()), 10080);
        }
        $dictAllCache = Cache::get('dictAllCache');

        $category = $dictAllCache->where('title', $name)->first();

        $data = $category['data'] ? collect($category['data'])->pluck('title', 'value')->toArray() : [];

        if ($data) {
            $i = 0;
            foreach ($data as $value => $title) {
                $data[$i] = [
                    'title' => $title,
                    'value' => $value,
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
     *
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

    /**
     * 根据分类名称查找基础信息数据
     *
     * @param $nameArr
     *
     * @return array
     */
    public static function getOptionsByNameArr($nameArr)
    {
        if (!Cache::has('dictAllCache')) {
            Cache::put('dictAllCache', collect(Dict::with('data')->get()->toArray()), 10080);
        }
        $dictAllCache = Cache::get('dictAllCache');

        $dataArr = [];
        if ($nameArr) {
            foreach ($nameArr as $field => $name) {
                $category = $dictAllCache->where('title', $name)->first();
                $data = $category['data'] ? collect($category['data'])->pluck('title', 'value')->toArray() : [];
                if ($data) {
                    $i = 0;
                    foreach ($data as $value => $title) {
                        $data[$i] = [
                            'title' => $title,
                            'value' => $value,
                        ];
                        $i++;
                    }
                }
                $dataArr[$field] = $data;
            }
        }

        return $dataArr;
    }

    public function data()
    {
        return $this->hasMany('App\Models\DictData', 'dict_id');
    }

}
