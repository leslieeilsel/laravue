<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use PDO;
use Ibiart\Slms\Controllers\Home;

class FeeOverviewMonthReport
{
    public $month_this;
    public $year_this;

    public function __construct()
    {
        parent::__construct();
        $this->getLatestYearM();
    }

    //获取最近年月
    public function getLatestYearM()
    {
        $home = new Home;
        $this->month_this = $home->m_this;
        $this->year_this = $home->y_this;
    }

    /**
     * 发行分配表
     *
     * @param string $date
     * @return array
     */
    public function getmb01MonthReportData($date)
    {
        $fee = ['0.04', '0.04', '0.04', '0.03', '0.005', '0.03', '0.015'];
        $body = $this->getMonthFeeData($date, $fee);

        return $body;
    }

    /**
     * 公益金分配表
     *
     * @param string $date
     * @return array
     */
    public function getmb02MonthReportData($date)
    {
        $fee = ['0.0925', '0.09', '0.085', '0.07', '0.045', '0.055', '0.05'];
        $body = $this->getMonthFeeData($date, $fee);

        return $body;
    }

    /**
     * 佣金分配表
     *
     * @param string $date
     * @return array
     */
    public function getmb03MonthReportData($date)
    {
        $fee = ['0.08', '0.08', '0.08', '0.08', '0.08', '0.08', '0.1'];
        $body = $this->getMonthFeeData($date, $fee);

        return $body;
    }

    /**
     * 返奖分配表
     *
     * @param string $date
     * @return array
     */
    public function getmb04MonthReportData($date)
    {
        $fee = ['0.5', '0.51', '0.53', '0.59', '0.73', '0.65', '0.65'];
        $body = $this->getMonthFeeData($date, $fee);

        return $body;
    }

    /**
     * 组织报表体数据
     *
     * @param string $date
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
                $k = 0;//费率下标
                foreach ($sv as $svk => $svv) {
                    $jinPer["fee" . $k] = $svv * $fee[$k];//其余行 销量*费率
                    $k++;
                }
                $jinPerCount = array_sum($jinPer);
                $jinPer["fee" . ($k + 1)] = $jinPerCount;
                if ($sk == 14 || $sk == 15) {
                    $jin[$sk] = $temp;
                    array_splice($jin[$sk], 0, 1);//增幅和排名行保持不变
                    foreach ($jin[$sk] as $jk => $jv) {
                        array_push($sale[$sk], $jv);
                    }
                    $sale_jin[] = $sale[$sk];
                } else {
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
     * @param string $date
     * @return array
     */
    public function getFeeMonthReportData($date)
    {
        if (empty($date)) {
            $month = $this->month_this;
            $year = $this->year_this;
            $where = [$year . '-' . $month . '01'];
        } else {
            $where = [];
            $dates = explode(',', $date);
            foreach ($dates as $key => $value) {
                $yearMonthZh = str_replace(' ', '', $value);
                $yearMonth = preg_replace('/[\x80-\xff]/', '', $yearMonthZh);
                $yearMonths = explode('-', $yearMonth);
                $year = $yearMonths[0];
                $where[] = $yearMonths[0] . '-' . $yearMonths[1] . '-01';
                $where[] = $yearMonths[0] - 1 . '-' . $yearMonths[1] . '-01';
            }
        }

        DB::setFetchMode(PDO::FETCH_ASSOC);
        $query = DB::table('ibiart_slms_sale_m_summary')->select('year', 'region_id', 'game_type', 'game_num', DB::raw("sum(sale_amt) as sale"));
        $query->whereIn('sale_at', $where);
        $query->groupBy('year', 'region_id', 'game_type', 'game_num');
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        $data = DB::select("SELECT region_id,year,
                                SUM(case when game_num IN('A0009','A0011','A0034') then sale else 0 end) as sale_gailv,
                                SUM(case when game_num='A0014' then sale else 0 end) as sale_daletou, 
                                SUM(case when game_num='A0010' then sale else 0 end) as sale_paisan,
                                SUM(case when game_num='A0052' then sale else 0 end) as sale_xuan, 
                                SUM(case when game_type=2 then sale else 0 end) as sale_jincai,
                                SUM(case when game_num IN('B009','B010','B012','B002') then sale else 0 end) as sale_zucai,
                                SUM(case when game_type=0 then sale else 0 end) as sale_jikai
                            FROM(" . $sql . ") as base 
                            GROUP BY year,region_id", $bindings);
        $data = collect($data)->groupBy('year');

        $publicFun = new PublicReportRepository();

        //组织今年-同比年数据
        $this_year = $publicFun->dataByYear($data[$year]);
        if (!empty($data[$year - 1])) {
            $last_year = $publicFun->dataByYear($data[$year - 1]);
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
        array_splice($order, 0, 1, '同比增幅排名名');
        //组织body
        $body = $this_year;
        $body[] = $this_year_ct;
        $body[] = $last_year_ct;
        $body[] = $great;
        $body[] = $order;

        return $body;
    }
}
