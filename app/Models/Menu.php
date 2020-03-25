<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public $timestamps = true;

    protected $table = 'sys_menus';

    protected $fillable = [
        'title',
        'name',
        'component',
        'parent_id',
        'description',
        'path',
        'link_type',
        'url',
        'icon',
        'target',
        'enabled',
        'sort',
        'path',
    ];
}
