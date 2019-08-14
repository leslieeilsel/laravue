<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use App\Models\Dict;

class Projects extends Model
{
    public $timestamps = true;

    protected $table = 'iba_project_projects';

    protected $fillable = [];

    public function plan()
    {
        return $this->hasMany('App\Models\Project\ProjectPlan', 'project_id');
    }

    public function schedule()
    {
        return $this->hasMany('App\Models\Project\ProjectSchedule', 'project_id');
    }

    /**
     * 获取数据字典数据
     *
     * @param $nameArr
     * @return array
     */
    public static function getDictDataByName($nameArr)
    {
        $data = [];

        if ($nameArr) {
            foreach ($nameArr as $key => $name) {
                $data[$key] = Dict::getOptionsByName($name);
            }
        }

        return $data;
    }
}
