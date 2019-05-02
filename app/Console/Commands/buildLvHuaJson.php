<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class buildLvHuaJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildLvHuaJson {folderPath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->info('- Start!');

        $this->info('- Read file list');
        $dir = $this->argument('folderPath');
        $handler = opendir($dir);
        $files = [];
        while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
            if ($filename != "." && $filename != "..") {
                $files[] = $filename;
            }
        }
        closedir($handler);

        $this->info('- Read file content');
        $lvhua = [];
        foreach ($files as $k => $value) {
            // echo $k + 1 . ' - ' . $value . PHP_EOL;
            $json_string = file_get_contents($dir . '\\' . $value);
            $data = json_decode($json_string, true);
            foreach ($data['features'] as $feature) {
                $lvhua[] = $feature;
            }
        }

        $this->info('- Build json Data');
        $lvhuaData = ['type' => 'FeatureCollection', 'features' => $lvhua];
        $projectJson = json_encode($lvhuaData, JSON_UNESCAPED_UNICODE);

        $this->info('- Save as json file');
        Storage::put('public/jsonData/lvhua.json', $projectJson);

        $this->info('- Done!');
    }
}
