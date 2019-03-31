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
        $projectData = $this->getAllProjects();

        $projectArr = [];
        $dataArr = [];
        foreach ($projectData as $k => $v) {
            $aaa = [];
            $aaa['type'] = 'Feature';

            $positions = $v['positions'];
            $positionsArr = explode(';', $positions);
            $arr = [];
            foreach ($positionsArr as $kk => $vv) {
                $ccc = explode(',', $vv);
                $arr[] = [
                    (float)$ccc[0],
                    (float)$ccc[1],
                ];
            }

            $aaa['geometry'] = [
                'type' => 'Polygon',
                'coordinates' => [$arr],
            ];

            $properties = [];
            $properties['name'] = $v['title'];
            $properties['adcode'] = $v['id'];
            $properties['level'] = 'district';
            $centerPoint = explode(',', $v['center_point']);
            $properties['center'] = [
                'lng' => (float)$centerPoint[0],
                'lat' => (float)$centerPoint[1],
            ];

            $aaa['properties'] = $properties;

            // 数据
            $data['area_id'] = $v['id'];
            $data['value'] = 64;
            $data['info'] =
                '项目名称：' . $v['title'] . '<br/>' .
                '项目类型：' . $v['type'] . '<br/>' .
                '投资状态：' . $v['status'] . '<br/>' .
                '投资概况：' . $v['description'];

            $projectArr[] = $aaa;
            $dataArr[] = $data;
        }

        $projectJson = json_encode(['type' => 'FeatureCollection', 'features' => $projectArr]);
        $dataJson = json_encode($dataArr);

        Storage::put('public/jsonData/project.json', $projectJson);
        Storage::put('public/jsonData/data.json', $dataJson);
    }

    public function getAllProjects()
    {
        $projects = Projects::whereIn('is_audit', [1, 3])->get()->toArray();

        foreach ($projects as $k => $row) {
            $projects[$k]['amount'] = number_format($row['amount'], 2);
            $projects[$k]['land_amount'] = isset($row['land_amount']) ? number_format($row['land_amount'], 2) : '';
            $projects[$k]['type'] = Dict::getOptionsArrByName('工程类项目分类')[$row['type']];
            $projects[$k]['is_gc'] = Dict::getOptionsArrByName('是否为国民经济计划')[$row['is_gc']];
            $projects[$k]['status'] = Dict::getOptionsArrByName('项目状态')[$row['status']];
            $projects[$k]['money_from'] = Dict::getOptionsArrByName('资金来源')[$row['money_from']];
            $projects[$k]['build_type'] = Dict::getOptionsArrByName('建设性质')[$row['build_type']];
            $projects[$k]['nep_type'] = isset($row['nep_type']) ? Dict::getOptionsArrByName('国民经济计划分类')[$row['nep_type']] : '';
//            $projects[$k]['projectPlan'] = $this->getPlanData($row['id'], 'preview');
//            $projects[$k]['scheduleInfo'] = ProjectSchedule::where('project_id', $row['id'])->orderBy('id', 'desc')->first();
        }

        return $projects;
    }

}
