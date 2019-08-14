<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectSchedule extends Model
{
    public $timestamps = true;

    protected $table = 'iba_project_schedule';

    protected $fillable = [];

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Projects');
    }
}
