<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectLedger extends Model
{
    public $timestamps = true;

    protected $table = 'iba_project_ledger';

    protected $fillable = [];
}
