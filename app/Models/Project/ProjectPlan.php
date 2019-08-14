<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectPlan extends Model
{
    public $timestamps = true;

    protected $table = 'iba_project_plan';

    protected $fillable = [];

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Projects');
    }
}
