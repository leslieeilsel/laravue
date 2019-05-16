<?php

namespace App\Console\Commands;

use App\Models\Dict;
use App\Models\Project\Projects;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class buildProjectGeoJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildProjectGeoJson';

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

        $this->info('- Building project data');
        $projectData = $this->getAllProjects();

        $count = count($projectData);
        $this->info('- Total ' . $count . ' datas');
        $projectArr = [];
        $dataArr = [];
        $i = 1;
        foreach ($projectData as $k => $v) {

            $positions = $v['positions'];
            if ($v['positions'] && $v['center_point']) {
                $positions = json_decode($positions, true);
                foreach ($positions as $kk => $vv) {
                    $aaa = [];
                    $data = [];

                    $aaa['type'] = 'Feature';
                    $this->info('- Building [' . ($k + 1) . '/' . $count .'][' . ($kk + 1) . '], ID=' . $v['id']);
                    if ($vv['drawingMode'] === 'polyline') {
                        $vv['coordinates'] = array_merge($vv['coordinates'], array_reverse($vv['coordinates']));
                    }
                    $pointsItem = $this->bd_to_gd($vv['coordinates']);
                    $aaa['geometry'] = [
                        'type' => 'Polygon',
                        'coordinates' => [$pointsItem],
                    ];

                    $properties = [];
                    $properties['name'] = $v['title'];
                    $properties['adcode'] = $i;
                    $properties['level'] = 'district';
                    $center = json_decode($v['center_point'], true);
                    $centerPoint = $this->bd_to_gd([$center['coordinates']]);
                    $properties['center'] = [
                        'lng' => (float) $centerPoint[0][0],
                        'lat' => (float) $centerPoint[0][1],
                    ];

                    $aaa['properties'] = $properties;
                    // 数据
                    $data['area_id'] = $i;
                    $data['value'] = 64;
                    $data['info'] =
                        '项目名称：' . $v['title'] . '<br/>' .
                        '项目类型：' . $v['type'] . '<br/>' .
                        '投资状态：' . $v['status'] . '<br/>' .
                        '投资概况：' . $v['description'];

                    $projectArr[] = $aaa;
                    $dataArr[] = $data;

                    $i++;
                }
            } else {
                $this->info('- Miss [' . ($k + 1) . '/' . $count .'], ID = ' . $v['id'] . ', no coordinates data');
            }

        }

        $projectJson = json_encode(['type' => 'FeatureCollection', 'features' => $projectArr], JSON_UNESCAPED_UNICODE);
        $dataJson = json_encode($dataArr, JSON_UNESCAPED_UNICODE);

        $this->info('- Save as json file');
        Storage::put('public/jsonData/project.json', $projectJson);
        Storage::put('public/jsonData/data.json', $dataJson);

        $this->info('- Done!');
    }

    public function getAllProjects()
    {
        $projects = Projects::whereIn('is_audit', [1, 3])->get()->toArray();

        $type = Dict::getOptionsArrByName('工程类项目分类');
        $is_gc = Dict::getOptionsArrByName('是否为国民经济计划');
        $status = Dict::getOptionsArrByName('项目状态');
        $money_from = Dict::getOptionsArrByName('资金来源');
        $build_type = Dict::getOptionsArrByName('建设性质');
        $nep_type = Dict::getOptionsArrByName('国民经济计划分类');

        foreach ($projects as $k => $row) {
            $projects[$k]['amount'] = number_format($row['amount'], 2);
            $projects[$k]['land_amount'] = isset($row['land_amount']) ? number_format($row['land_amount'], 2) : '';
            $projects[$k]['type'] = $type[$row['type']];
            $projects[$k]['is_gc'] = $is_gc[$row['is_gc']];
            $projects[$k]['status'] = $status[$row['status']];
            $projects[$k]['money_from'] = $money_from[$row['money_from']];
            $projects[$k]['build_type'] = $build_type[$row['build_type']];
            $projects[$k]['nep_type'] = isset($row['nep_type']) ? $nep_type[$row['nep_type']] : '';
        }

        return $projects;
    }

    public function bd_to_gd($pointsArr){
        $gdPointsArr = [];
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        foreach ($pointsArr as $k => $point) {
            $data = [];
            $x = $point['lng'] - 0.0065;
            $y = $point['lat'] - 0.006;
            $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
            $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
            $gg_lon = $z * cos($theta);
            $gg_lat = $z * sin($theta);
            $data[] = round($gg_lon, 6);
            $data[] = round($gg_lat, 6);
            $gdPointsArr[] = $data;
        }

        return $gdPointsArr;
    }
}
