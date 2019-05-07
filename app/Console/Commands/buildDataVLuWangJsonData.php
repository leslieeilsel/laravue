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
        $this->info('- Start!');

        $this->info('- Building ShiZheng road network');
        // 市政路网
        $shizhengData = file_get_contents('http://139.217.6.78:9000/assets/json/luwang.geo.json');
        $shizhengData = json_decode($shizhengData, true);

        foreach ($shizhengData['features'] as $k => $v) {
            $shizhengData['features'][$k]['geometry']['type'] = 'Polygon';
            $shizhengData['features'][$k]['geometry']['coordinates'] = [array_merge($v['geometry']['coordinates'], array_reverse($v['geometry']['coordinates']))];
            $points = '';
            foreach ($shizhengData['features'][$k]['geometry']['coordinates'][0] as $kkk => $vvv) {
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
            $shizhengData['features'][$k]['geometry']['coordinates'] = [array_merge($pointsItem, array_reverse($pointsItem))];
        }

        // 绿化路网
        $this->info('- Building LvHua road network');
        $lvhuaData = file_get_contents('http://139.217.6.78:9000/assets/json/lvhua.geo.json');
        $lvhuaData = json_decode($lvhuaData, true);

        foreach ($lvhuaData['features'] as $k => $v) {
            $points = '';
            foreach ($lvhuaData['features'][$k]['geometry']['coordinates'][0] as $kkk => $vvv) {
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
            $lvhuaData['features'][$k]['geometry']['coordinates'] = [$pointsItem];
        }

        // 合并数据，存为单文件
        $this->info('- Merge ShiZheng and LvHua road network');
        $luwangData = array_merge($shizhengData['features'], $lvhuaData['features']);
        $luwangJson = json_encode(['type' => 'FeatureCollection', 'features' => $luwangData], JSON_UNESCAPED_UNICODE);
        Storage::put('public/luwang/luwang.datav.json', $luwangJson);

        $this->info('- End!');
    }

    public function getGaoDeGeo($point)
    {
        $res = file_get_contents('https://restapi.amap.com/v3/assistant/coordinate/convert?locations=' . $point . '&coordsys=baidu&output=json&key=86a30535207aa2d5fc6d2aec25c26b12');
        $res = json_decode($res);

        return $res->locations;
    }
}
