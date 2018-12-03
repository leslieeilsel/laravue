<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\SaleMonthSummary;
use App\Models\Region;
use PDO;

class DfaoReport
{
    public function getDfaoReportData ($startMonth, $endMonth)
    {
        $body = [];
        $this_year = [];//今年合计数据
        $last_year = [];//同比去年数据
        $this_year_ct = ['合计', 0, 0, 0, 0, 0, 0, 0, 0];
        $last_year_ct = ['上年同期', 0, 0, 0, 0, 0, 0, 0, 0];

        $fee = ['0.04', '0.04', '0.04', '0.03', '0.005', '0.03', '0.015'];
        $jin = [];
        $jinPer = [];

        $startArr = explode('-', $startMonth);
        $endArr = explode('-', $endMonth);
        $query = DB::table('ibiart_slms_sale_m_summary')->select('year', 'region_id', 'game_type', 'game_num', DB::raw("sum(sale_amt) as sale"));
        $query->whereIn('year', [$startArr[0] - 1, $startArr[0]]);
        $query->whereBetween('month', [$startArr[1], $endArr[1]]);
        $query->whereNotIn('region_id', [42, 43]);
        $query->groupBy('year', 'region_id', 'game_type', 'game_num');
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        $data = DB::select("SELECT region_id,year,
                                SUM(case when game_num IN('A0009','A0011','A0034') then sale else 0 end) as tc1,
                                SUM(case when game_num='A0014' then sale else 0 end) as tc2, 
                                SUM(case when game_num='A0010' then sale else 0 end) as tc3,
                                SUM(case when game_num='A0052' then sale else 0 end) as tc4, 
                                SUM(case when game_type=2 then sale else 0 end) as tc5,
                                SUM(case when game_num IN('B009','B010','B012','B002') then sale else 0 end) as tc6,
                                SUM(case when game_type=0 then sale else 0 end) as tc7
                            FROM(" . $sql . ") as base 
                            GROUP BY year,region_id", $bindings);
        $data = collect($data)->groupBy('year');

        //组织今年-同比年数据
        $this_year = $this->dataByYear($data[$startArr[0]]);
        if (!empty($data[$startArr[0] - 1])) {
            $last_year = $this->dataByYear($data[$startArr[0] - 1]);
        } else {
            $last_year = [];
        }

        //添加合计+增幅行
        $this_year_ct = $this->array_sum_column($this_year);
        $last_year_ct = $this->array_sum_column($last_year);
        $great = $this->greating($this_year_ct, $last_year_ct);
        $order = $this->orderGreaating($great);

        array_splice($this_year_ct, 0, 1, '合计');
        array_splice($last_year_ct, 0, 1, '上年同期');
        array_splice($great, 0, 1, '同比销售增幅');
        array_splice($order, 0, 1, '同比增幅排名');
        //组织body
        $body = $this_year;
        $body[] = $this_year_ct;
        $body[] = $last_year_ct;
        $body[] = $great;
        $body[] = $order;

        $sale = $body;
        $body = [];
        //对应费率
        if (!empty($sale)) {
            foreach ($sale as $sk => $sv) {
                $temp = $sale[$sk];
                array_splice($sv, 0, 1);//删除地区
                array_splice($sv, -1, 1);//删除总量行
                $k = 0;//费率下标
                if ($sk !== 14 && $sk !== 15) {
                    foreach ($sv as $svk => $svv) {
                        $jinPer["fee" . $k] = $svv * $fee[$k];//其余行 销量*费率
                        $k++;
                    }
                    $jinPerCount = array_sum($jinPer);
                    $jinPer["fee" . ($k + 1)] = $jinPerCount;

                    $jin[$sk] = $jinPer;
                    $sale_jin[] = array_merge($sale[$sk], $jin[$sk]);
                } else {
                    $jin[$sk] = $temp;
                    array_splice($jin[$sk], 0, 1);//增幅和排名行保持不变
                    foreach ($jin[$sk] as $jk => $jv) {
                        $sale[$sk]["fee" . $k] = $jv;
                        $k++;
                    }
                    $sale_jin[] = $sale[$sk];
                }
                //合并销量和公益金

                $jinPer = [];
            }
            $data = $sale_jin;
        }

        return $data;
    }

    public function getFeeData ($data, $fee)
    {
        $body = [];
        $feebody = [];
        $region = [];
        $sale = [];
        foreach ($data as $key => $rows) {
            foreach ($rows as $k => $row) {
                $region[$k]['city'] = $row->region_name;
                $body[$k]['gl'] = $row->sale_gailv;
                $body[$k]['dlt'] = $row->sale_daletou;
                $body[$k]['ps'] = $row->sale_paisan;
                $body[$k]['elefive'] = $row->sale_xuan;
                $body[$k]['jc'] = $row->sale_jincai;
                $body[$k]['zc'] = $row->sale_zucai;
                $body[$k]['jk'] = $row->sale_jikai;
            }
            foreach ($body as $k => $value) {
                $body[$k]['totalSale'] = array_sum($value);
                $feebody[$k]['glf'] = $value['gl'] * $fee[0];
                $feebody[$k]['dltf'] = $value['dlt'] * $fee[1];
                $feebody[$k]['psf'] = $value['ps'] * $fee[2];
                $feebody[$k]['elefivef'] = $value['elefive'] * $fee[3];
                $feebody[$k]['jcf'] = $value['jc'] * $fee[4];
                $feebody[$k]['zcf'] = $value['zc'] * $fee[5];
                $feebody[$k]['jkf'] = $value['jk'] * $fee[6];
            }
            foreach ($feebody as $k => $value) {
                $feebody[$k]['totalSalef'] = array_sum($value);
                $sale[$k] = array_merge($region[$k], $body[$k], $feebody[$k]);
            }
        }

        return $sale;
    }

    public function formatNumber ($data)
    {
        foreach ($data as $k => $rows) {
            $data[$k]['gl'] = number_format($rows['gl'], 2);
            $data[$k]['dlt'] = number_format($rows['dlt'], 2);
            $data[$k]['ps'] = number_format($rows['ps'], 2);
            $data[$k]['elefive'] = number_format($rows['elefive'], 2);
            $data[$k]['jc'] = number_format($rows['jc'], 2);
            $data[$k]['zc'] = number_format($rows['zc'], 2);
            $data[$k]['jk'] = number_format($rows['jk'], 2);
            $data[$k]['totalSale'] = number_format($rows['totalSale'], 2);
            $data[$k]['glf'] = number_format($rows['glf'], 2);
            $data[$k]['dltf'] = number_format($rows['dltf'], 2);
            $data[$k]['psf'] = number_format($rows['psf'], 2);
            $data[$k]['elefivef'] = number_format($rows['elefivef'], 2);
            $data[$k]['jcf'] = number_format($rows['jcf'], 2);
            $data[$k]['zcf'] = number_format($rows['zcf'], 2);
            $data[$k]['jkf'] = number_format($rows['jkf'], 2);
            $data[$k]['totalSalef'] = number_format($rows['totalSalef'], 2);
        }

        return $data;
    }

    //数组各列求和
    public function array_sum_column ($arr)
    {
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

    protected function dataByYear ($data)
    {
        $region = $this->getRegionBut();//地市text
        foreach ($data as $dk => $dv) {
            array_splice($dv, 0, 1, $region[$dv['region_id']]);
            array_splice($dv, 1, 1);
            $dv['tc8'] = $dv['tc1'] + $dv['tc2'] + $dv['tc3'] + $dv['tc4'] + $dv['tc5'] + $dv['tc6'] + $dv['tc7'];
            $year[] = $dv;
//            array_splice($year,12,2);//去除省直，互联网
        }
        return $year;
    }

    //地市筛选 num->text
    protected function getRegionBut ()
    {
        $regionArr = [];
        $region_id = Region::distinct()->whereRaw('length(num)>2')->get();
        $region_id ? $regionArr = $region_id->pluck('name', 'id')->toArray() : [];//地市id->text
        return $regionArr;
    }

    //求增幅array
    public function greating ($this_year, $last_year)
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

    //增幅排序,返回原数组的对应的排名array
    protected function orderGreaating ($arr)
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
        array_splice($order, 0, 0, '0');;
        return $order;
    }
}
