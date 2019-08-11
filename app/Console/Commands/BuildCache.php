<?php

namespace App\Console\Commands;

use App\Models\Departments;
use App\Models\Dict;
use App\Models\DictData;
use App\Models\Project\ProjectPlan;
use App\Models\Project\Projects;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class BuildCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildCache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建数据缓存';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('- Building Project Cache');
        Cache::put('projectsCache', collect(Projects::all()->toArray()), 10080);
        $this->info('- Building ProjectPlan Cache');
        Cache::put('projectPlanCache', collect(ProjectPlan::all()->toArray()), 10080);
        $this->info('- Building Department Cache');
        Cache::put('departmentsCache', collect(Departments::all()->toArray()), 10080);
        $this->info('- Building Dict Cache');
        Cache::put('dictCache', collect(Dict::all()->toArray()), 10080);
        Cache::put('dictDataCache', collect(DictData::all()->toArray()), 10080);
        Cache::put('dictAllCache', collect(Dict::with('data')->get()->toArray()), 10080);
    }
}
