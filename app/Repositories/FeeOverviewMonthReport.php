<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeeOverviewMonthReport
{
    public $choose_year;
    public $last_year;

    public function __construct()
    {
    }

    /**
     * 导出报表
     *
     * @param array $params
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportOverviewMonthData($params)
    {
    }

    /**
     * 入口函数
     *
     * @param array $params
     * @return array
     */
    public function getOverviewMonthData($params)
    {
        $body = [];
        $reportType = $params['reportType'];
        unset($params['reportType']);
        $date = $params;

        switch ($reportType) {
            case 'fxf':
                $body = $this->getFxfOverviewMonthData($date);
                break;
            case 'gyj':
                $body = $this->getGyjOverviewMonthData($date);
                break;
            case 'yj':
                $body = $this->getYjOverviewMonthData($date);
                break;
            case 'fj':
                $body = $this->getFjOverviewMonthData($date);
                break;
        }

        return $body;
    }

    /**
     * 发行分配概览表
     *
     * @param array $date
     * @return array
     */
    public function getFxfOverviewMonthData($date)
    {
        $fee = ['0.04', '0.04', '0.04', '0.03', '0.005', '0.03', '0.015'];
        $body = $this->getMonthFeeData($date, $fee);
        $body = $this->bodyFormat($body);

        return $body;
    }

    /**
     * 公益金分配概览表
     *
     * @param array $date
     * @return array
     */
    public function getGyjOverviewMonthData($date)
    {
        $fee = ['0.0925', '0.09', '0.085', '0.07', '0.045', '0.055', '0.05'];
        $body = $this->getMonthFeeData($date, $fee);
        $body = $this->bodyFormat($body);

        return $body;
    }

    /**
     * 佣金分配概览表
     *
     * @param array $date
     * @return array
     */
    public function getYjOverviewMonthData($date)
    {
        $fee = ['0.08', '0.08', '0.08', '0.08', '0.08', '0.08', '0.1'];
        $body = $this->getMonthFeeData($date, $fee);
        $body = $this->bodyFormat($body);

        return $body;
    }

    /**
     * 返奖分配概览表
     *
     * @param array $date
     * @return array
     */
    public function getFjOverviewMonthData($date)
    {
        $fee = ['0.5', '0.51', '0.53', '0.59', '0.73', '0.65', '0.65'];
        $body = $this->getMonthFeeData($date, $fee);
        $body = $this->bodyFormat($body);

        return $body;
    }

    /**
     * 组织报表体数据
     *
     * @param array $date
     * @param array $fee
     * @return array
     */
    protected function getMonthFeeData($date, $fee)
    {
        $body = [];
        $jin = [];
        $jinPer = [];
        $sale_jin = [];
        $sale = $this->getFeeMonthReportData($date);
        //对应费率
        if (!empty($sale)) {
            foreach ($sale as $sk => $sv) {
                $temp = $sale[$sk];
                array_splice($sv, 0, 1);//删除地区
                array_splice($sv, -1, 1);//删除总量行
                if ($sk == 14 || $sk == 15) {
                    $jin[$sk] = $temp;
                    array_splice($jin[$sk], 0, 1);//增幅和排名行保持不变
                    $k = 0;//费率下标
                    foreach ($jin[$sk] as $jk => $jv) {
                        $sale[$sk]["fee" . $k] = $jv;
                        $k++;
                    }
                    $sale_jin[] = $sale[$sk];
                } else {
                    $k = 0;//费率下标
                    foreach ($sv as $svk => $svv) {
                        $jinPer["fee" . $k] = $svv * $fee[$k];//其余行 销量*费率
                        $k++;
                    }
                    $jinPerCount = array_sum($jinPer);
                    $jinPer["fee" . $k] = $jinPerCount;
                    $jin[$sk] = $jinPer;
                    $sale_jin[] = array_merge($sale[$sk], $jin[$sk]);
                }
                //合并销量和公益金
                $jinPer = [];
            }
            $body = $sale_jin;
        }

        return $body;
    }

    /**
     * @param array $date
     * @return array
     */
    public function getFeeMonthReportData($date)
    {
        $query = DB::table('ibiart_slms_sale_m_summary')->select('year', 'region_id', 'game_type', 'game_num', DB::raw("sum(sale_amt) as sale"));
        $query->whereIn('sale_at', $this->buildMonthList($date));
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

        $publicFun = new PublicReportRepository();

        //组织今年-同比年数据
        $this_year = $publicFun->dataByYear($data[$this->choose_year]);
        if (!empty($data[$this->last_year])) {
            $last_year = $publicFun->dataByYear($data[$this->last_year]);
        } else {
            $last_year = [];
        }
        //添加合计+增幅行
        $this_year_ct = $publicFun->array_sum_column($this_year);
        $last_year_ct = $publicFun->array_sum_column($last_year);
        $great = $publicFun->greating($this_year_ct, $last_year_ct);
        $order = $publicFun->orderGreaating($great);

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

        return $body;
    }

    /**
     * 组装所有的月
     *
     * @param array $date
     * @return array
     */
    public function buildMonthList($date)
    {
        $dates = [];
        $startDate = explode('-', $date['startMonth']);
        $endDate = explode('-', $date['endMonth']);
        $this->choose_year = $startDate[0];
        $this->last_year = $this->choose_year - 1;
        for ($i = $startDate[1]; $i <= $endDate[1]; $i++) {
            $dates[] = $this->choose_year . '-' . $i . '-01';
            $dates[] = $this->choose_year - 1 . '-' . $i . '-01';
        }

        return $dates;
    }

    /**
     * 数字格式化
     *
     * @param $body
     * @return array
     */
    public function bodyFormat($body)
    {
        $newBody = [];
        foreach ($body as $key => $rows) {
            if ($key >= 14) {
                $newBody[$key] = $rows;
            } else {
                foreach ($rows as $k => $row) {
                    $newBody[$key][$k] = ($k === 0) ? $row : number_format($row, 2);
                }
            }
        }

        return $newBody;
    }
}
