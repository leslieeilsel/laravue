<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class buildDataVLuWangJsonData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildDataVLuWangJsonData';

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
        $luwangData = file_get_contents('http://139.217.6.78:9000/assets/json/luwang.geo.json');
        $luwangData = json_decode($luwangData, true);

        foreach ($luwangData['features'] as $k => $v) {
            $luwangData['features'][$k]['geometry']['type'] = 'Polygon';
            $luwangData['features'][$k]['geometry']['coordinates'] = [array_merge($v['geometry']['coordinates'], array_reverse($v['geometry']['coordinates']))];
            $points = '';
            foreach ($luwangData['features'][$k]['geometry']['coordinates'][0] as $kkk => $vvv) {
                $points = $points . implode(',', $vvv) . ';';
            }
            $points = $this->getGaoDeGeo($points);
            $pointsArr = explode(';', $points);
            $pointsItem = [];
            foreach ($pointsArr as $kkkk => $vvvv) {
                $pointArr = explode(',', $vvvv);
                $point = [
                    (float)$pointArr[0],
                    (float)$pointArr[1],
                ];
                $pointsItem[] = $point;
            }
            $luwangData['features'][$k]['geometry']['coordinates'] = [array_merge($pointsItem, array_reverse($pointsItem))];
        }
        $projectJson = json_encode($luwangData, JSON_UNESCAPED_UNICODE);
        Storage::put('public/jsonData/luwang.json', $projectJson);
    }

    public function getGaoDeGeo($point)
    {
        $res = file_get_contents('https://restapi.amap.com/v3/assistant/coordinate/convert?locations=' . $point . '&coordsys=baidu&output=json&key=86a30535207aa2d5fc6d2aec25c26b12');
        $res = json_decode($res);

        return $res->locations;
    }
}
