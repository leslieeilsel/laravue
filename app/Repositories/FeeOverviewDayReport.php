<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use PDO;

class FeeOverviewDayReport
{
    /**
     * 发行分配表
     *
     * @param string $date
     * @return array
     */
    public function getmb01ReportData ($date)
    {
        $fee = ['0.04', '0.04', '0.04', '0.03', '0.005', '0.03', '0.015'];
        $body = $this->getFeeData($date, $fee);

        return $body;
    }

    /**
     * 公益金分配表
     *
     * @param string $date
     * @return array
     */
    public function getmb02ReportData ($date)
    {
        $fee = ['0.0925', '0.09', '0.085', '0.07', '0.045', '0.055', '0.05'];
        $body = $this->getFeeData($date, $fee);

        return $body;
    }

    /**
     * 佣金分配表
     *
     * @param string $date
     * @return array
     */
    public function getmb03ReportData ($date)
    {
        $fee = ['0.08', '0.08', '0.08', '0.08', '0.08', '0.08', '0.1'];
        $body = $this->getFeeData($date, $fee);

        return $body;
    }

    /**
     * 返奖分配表
     *
     * @param string $date
     * @return array
     */
    public function getmb04ReportData ($date)
    {
        $fee = ['0.5', '0.51', '0.53', '0.59', '0.73', '0.65', '0.65'];
        $body = $this->getFeeData($date, $fee);

        return $body;
    }

    /**
     * 组织报表体数据
     *
     * @param string $date
     * @param array $fee
     * @return array
     */
    protected function getFeeData ($date, $fee)
    {
        $body = [];
        $sale_jin = [];
        $jin = [];
        $jinPer = [];
        $sale = $this->getFeeReportData($date);
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
    public function getFeeReportData ($date)
    {
        $date = explode(",", $date);
        $start_year = date_format(date_create($date[0]), 'Y');
        $start_month = date_format(date_create($date[0]), 'm');
        $start_day = date_format(date_create($date[0]), 'd');

        $end_year = date_format(date_create($date[1]), 'Y');
        $end_month = date_format(date_create($date[1]), 'm');
        $end_day = date_format(date_create($date[1]), 'd');

        if ($start_year != $end_year) {
            throw new ApplicationException("请确保年份相同！");
        } else {
            if ($start_month <= $end_month) {
                if ($start_year == 2017 || $start_year == 2016) {
                    throw new ApplicationException($start_year . "年无明细数据！");
                }
                if ($start_month == $end_month && $start_day > $end_day) {
                    throw new ApplicationException("起始天应小于或等于结束天！");
                }
                $publicFun = new PublicReportRepository();
                $startDates = $publicFun->getDateFromRange($start_year . '-' . $start_month . '-' . $start_day, $end_year . '-' . $end_month . '-' . $end_day);
                $lastDate = [$start_year - 1 . '-' . $start_month . '-' . $start_day, $end_year - 1 . '-' . $end_month . '-' . $end_day];
                $lastDates = (($start_year - 1) != 2017) ? $publicFun->getDateFromRange($lastDate[0], $lastDate[1]) : [];
                $dates = array_merge($startDates, $lastDates);

                DB::setFetchMode(PDO::FETCH_ASSOC);
                $query = DB::table('ibiart_slms_sale_m')->select('year', 'region_id', 'game_type', 'game_num', DB::raw("sum(sale_amt) as sale"));
                $query->whereIn('sale_at', $dates);
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
                //组织今年-同比年数据
                $this_year = !empty($data[$end_year]) ? $publicFun->dataByYear($data[$end_year]) : [];
                $last_year = !empty($data[$end_year - 1]) ? $publicFun->dataByYear($data[$end_year - 1]) : [];
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
            } else {
                throw new ApplicationException("起始月份应小于或等于结束月份！");
            }
        }

        return $body;
    }
}
