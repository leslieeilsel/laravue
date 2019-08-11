<?php

namespace App\Console\Commands;

use App\Models\Departments;
use App\Models\Project\Projects;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RefreshProjectUnit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RefreshProjectUnit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '刷新项目归属部门';

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
        $projects = Projects::all()->toArray();

        $this->info('- Total ' . count($projects));
        // 创建进度条
        $bar = $this->output->createProgressBar(count($projects));

        foreach ($projects as $k => $v) {
            // 查询标题
            $unitTitle = DB::table('users as user')->join('iba_system_department as dept', 'dept.id', '=', 'user.department_id')
                ->select('dept.title')
                ->where('user.id', $v['user_id'])
                ->value('title');
            // 更新标题
            Projects::where('id', $v['id'])->update([
                'unit' => $unitTitle
            ]);
            // Advances the progress output X steps
            $bar->advance();
        }

        // 结束
        $bar->finish();

        $this->info(PHP_EOL);
        $this->info('- Completed');
    }
}
