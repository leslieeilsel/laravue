<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class buildLuwangGeoJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildLuwangGeoJson';

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
        $luwangData = file_get_contents('http://139.217.6.78:9000/assets/json/luwang.json');
        $luwangData = json_decode($luwangData, true);


        $projectArr = [];
        foreach ($luwangData as $k => $v) {
            $aaa = [];
            $aaa['type'] = 'Feature';

            $properties = [];
            $properties['name'] = $v['name'];
            $aaa['properties'] = $properties;
            $arr = [];
            $positions = $v['data'];
            $positionsArr = explode(';', $positions);
            foreach ($positionsArr as $kk => $vv) {
//                $vv = $this->getGaoDeGeo($vv);
                $ccc = explode(',', $vv);
                $arr[] = [
                    (float)$ccc[0],
                    (float)$ccc[1],
                ];
            }

            $aaa['geometry'] = [
                'type' => 'LineString',
                'coordinates' => $arr,
            ];

//            $properties['adcode'] = $v['id'];
//            $properties['level'] = 'district';
//            $this->getGaoDeGeo($v['center_point']);
//            $centerPoint = explode(',', $v['center_point']);
//            $properties['center'] = [
//                'lng' => (float)$centerPoint[0],
//                'lat' => (float)$centerPoint[1],
//            ];

            $projectArr[] = $aaa;
        }

        $projectJson = json_encode(['type' => 'FeatureCollection', 'features' => $projectArr], JSON_UNESCAPED_UNICODE);
        Storage::put('public/jsonData/luwang.json', $projectJson);
        $b = 1;
    }
}
