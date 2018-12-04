<?php

namespace App\Repositories;

use App\Models\Region;
use Illuminate\Support\Facades\Cache;

class PublicReportRepository
{
    public function __construct()
    {
        // parent::__construct();
    }

    /**
     * 获取指定日期段内每一天的日期
     *
     * @param  string $startDate 开始日期
     * @param  string $endDate 结束日期
     * @return array
     */
    public function getDateFromRange($startDate, $endDate)
    {
        $sTimeStamp = strtotime($startDate);
        $eTimeStamp = strtotime($endDate);

        // 计算日期段内有多少天
        $days = ($eTimeStamp - $sTimeStamp) / 86400 + 1;

        // 保存每天日期
        $date = array();

        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $sTimeStamp + (86400 * $i));
        }

        return $date;
    }

    /**
     * 组织xx年数据
     *
     * @param array $data
     * @return array
     */
    public function dataByYear($data)
    {
        $year = [];
        $region = $this->getRegionBut();//地市text
        foreach ($data as $dk => $dv) {
            array_splice($dv, 0, 1, $region[$dv['region_id']]);
            array_splice($dv, 1, 1);
            $dv['tc8'] = $dv['tc1'] + $dv['tc2'] + $dv['tc3'] + $dv['tc4'] + $dv['tc5'] + $dv['tc6'] + $dv['tc7'];
            $year[] = $dv;
            array_splice($year, 12, 2);//去除省直，互联网
        }

        return $year;
    }

    /**
     * 地市筛选 num->text
     *
     * @return array
     */
    public function getRegionBut()
    {
        if (Cache::has('regionArr')) {
            $regionArr = Cache::get('regionArr');
        } else {
            $regionArr = [];
            $region_id = Region::distinct()->whereRaw('length(num)>2')->get();
            $region_id ? $regionArr = $region_id->pluck('name', 'id')->toArray() : [];//地市id->text
            Cache::put('regionArr', $regionArr, 120);
        }

        return $regionArr;
    }

    /**
     * 数组各列求和
     *
     * @param array $arr
     * @return array
     */
    public function array_sum_column($arr)
    {
        $count = [];
        $colums = [];
        if (!empty($arr)) {
            $keys = array_keys($arr[0]);
            foreach ($keys as $kk => $kv) {
                $colums[] = array_column($arr, $kv);
            }
            foreach ($colums as $ck => $cv) {
                $count['tc' . $ck] = array_sum($cv);
            }
        } else {
            $count = [
                'tc0' => 0,
                'tc1' => 0,
                'tc2' => 0,
                'tc3' => 0,
                'tc4' => 0,
                'tc5' => 0,
                'tc6' => 0,
                'tc7' => 0,
                'tc8' => 0
            ];
        }

        return $count;
    }


    /**
     * 求增幅
     *
     * @param array $this_year
     * @param array $last_year
     * @return array
     */
    public function greating($this_year, $last_year)
    {
        $great = [];
        $temp[] = $this_year;
        $temp[] = $last_year;
        foreach ($temp[0] as $tk => $tv) {
            if ($temp[1][$tk] != 0) {
                $per = (($tv / $temp[1][$tk]) - 1) * 100;
                $great[$tk] = number_format($per, 1) . '%';
            } else {
                $great[$tk] = '-- %';
            }
        }

        return $great;
    }

    /**
     * 增幅排序,返回原数组的对应的排名array
     *
     * @param array $arr 需要排列的数组
     * @return array
     */
    public function orderGreaating($arr)
    {
        //剔除第一个元素
        array_splice($arr, 0, 1);
        //百分号字符转换成可运算的整形
        $result = array_map(function ($value) {
            return substr($value, 0, -1);
        }, $arr);
        $temp = $result;
        rsort($result);
        $flip = array_flip($result);
        foreach ($temp as $tk => $tv) {
            $order[$tk] = strval($flip[$tv] + 1);
        }
        //首位汉子排名为0
        array_splice($order, 0, 0, '0');

        return $order;
    }
}
