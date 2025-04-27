<?php

namespace app\api\controller\v1;

use app\backend\model\HouseDeal;
use app\backend\model\HouseDealArea;
use app\backend\model\HouseDealStatistics;
use app\backend\model\ProjectBaseInfo;
use app\backend\model\PropertyInfo;
use fun\auth\Api;

/**
 * @OA\Info(title="接口文档", version="1.0.1")
 */
class Center extends Api
{
    protected $noAuth = ['*'];

    public const SALES_TYPE_DAY = 1;
    public const SALES_TYPE_WEEK = 2;
    public const SALES_TYPE_MONTH = 3;


    // 销售情况
    /**
     * @OA\Get(path="/api/v1.center/sales",
     *   tags={"销售情况"},
     *   summary="销售情况",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function sales()
    {
        $type = $this->request->param('type', self::SALES_TYPE_DAY);
        $day = date('Y-m-d', strtotime('-2 day'));
        $week = date('Y-m-d', strtotime('-9 day'));
        $month = date('Y-m-d', strtotime('-32 day'));
        $where = [
            'reportcatalog' => '住宅',
        ];
        switch ($type) {
            case self::SALES_TYPE_DAY:
                $otherWhere = "tj_date = '$day'";
                $resDate = $day;
                $cacheKey = "center_deal_" . $day;
                break;
            case self::SALES_TYPE_WEEK:
                $otherWhere = "tj_date >= '{$week}' and tj_date <= '{$day}'";
                $resDate = "{$week} 至 {$day}";
                $cacheKey = "center_deal_" . $week;
                break;
            case self::SALES_TYPE_MONTH:
                $otherWhere = "tj_date >= '{$month}' and tj_date <= '{$day}'";
                $resDate = "{$month} 至 {$day}";
                $cacheKey = "center_deal_" . $month;
                break;
            default:
                break;
        }
        $res = cache($cacheKey);
        if (!$res) {
            $res = [
                'date' => $resDate,
                'condition' => $this->condition($where, $otherWhere), // 销售情况
                'dealNum' => $this->zoneCj($where, $otherWhere), // 成交套数 按区分布
                'area' => $this->area($otherWhere), // 销售面积段分布
                'supply' => $this->supply($where, $otherWhere), // 各区供销存数据
            ];
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }

        $this->success('success', $res);
    }

    // 各区供销存数据api
    // 成交套数api：https://opendata.sz.gov.cn/data/dataSet/toDataDetails/29200_01903510
    // 单天：当天成交套数
    // 近7天：最近7天成交套数总和
    // 近30天：最近30天成交套数总和

    // 取证房源：采集数据（仅住宅）
    // 单天：当天取证房源住宅套数
    // 近7天：最近7天取证房源住宅套数总和
    // 近30天：最近30天取证房源住宅套数总和

    // 库存房源api：
    // https://opendata.sz.gov.cn/data/dataSet/toDataDetails/29200_01903510
    // 单天库存：当天可售套数
    // 近7天库存：最近7天可售套数总和
    // 近30天库存：最近30天可售套数总和

    // 去化周期api：
    // https://opendata.sz.gov.cn/data/dataSet/toDataDetails/29200_01903510
    // 单天去化周期=当前可售套数 ÷ 当日成交量
    // 近7天去化周期 = 当前可售套数 ÷ （最近7天总成交量 ÷ 7天 ）
    // 近30天去化周期 =当前可售套数 ÷ （最近30天总成交量 ÷ 30天 ）
    private function supply($where, $otherWhere)
    {
        //step 1 获取当前成交和可售
        $date = date('Y-m-d', strtotime('-2 day'));
        $currentWhere['tj_date'] = $date;
        $currentWhere['reportcatalog'] = '住宅';
        $currentData = HouseDeal::where($currentWhere)
            ->field('tj_date,zone,cj_num,ks_num')
            ->group('zone')
            ->select()
            ->toArray();

        $zone = array_column($currentData, 'zone');
        $currentData = array_combine($zone, array_map(function ($item) {
            return $item;
        }, $currentData));
        //step 2取汇总数据
        $totalData = HouseDeal::where($where)
            ->where($otherWhere)
            ->field('zone,count(id) as count,sum(cj_num) as total_cj_num,sum(ks_num) as total_ks_num')
            ->group('zone')
            ->select()
            ->toArray();
        //获取取证房源数据
        $propertyData = $this->getPropertyByZone($otherWhere);
        foreach ($totalData as &$val) {
            $val['qz_num'] = isset($propertyData[$val['zone']]) ? $propertyData[$val['zone']] : 0;
            if ($val['count'] == 0 || $val['total_cj_num'] == 0) {
                $val['cycle'] = 0;
            } else {
                $val['cycle'] = isset($currentData[$val['zone']]) ? sprintf("%.2f", $currentData[$val['zone']]['ks_num'] / ($val['total_cj_num'] / $val['count'])) : 0;
            }
        }
        return $totalData;
    }

    private function getPropertyByZone($otherWhere)
    {
        $where = [
            'proj_useage' => '住宅'
        ];
        $sypeIds = PropertyInfo::where($where)
            ->where($otherWhere)
            ->field('sype_id')
            ->select()
            ->toArray();
        $sypeIds = array_column($sypeIds, 'sype_id');
        $res = ProjectBaseInfo::whereIn('pre_sellId', $sypeIds)
            ->field('sum(ys_suites) as ys_suites, zone')
            ->group('zone')
            ->column('ys_suites', 'zone');
        return $res;
    }

    //销售面积分布 按面积
    private function area($where)
    {
        $baseWhere['zone'] = '全市';
        $res = HouseDealArea::where($where)
            ->where($baseWhere)
            ->field('area_type, sum(cj_num) as total_cj_num, sum(cj_area) as total_cj_area')
            ->group('area_type')
            ->select()
            ->toArray();
        return $res;
    }

    //区成交套数
    private function zoneCj($where, $otherWhere)
    {
        $res = HouseDeal::where($where)
            ->where($otherWhere)
            ->field('zone, sum(cj_num) as total_cj_num')
            ->group('zone')
            ->select()
            ->toArray();
        return $res;
    }

    //销售情况
    private function condition($where, $otherWhere)
    {
        $where['zone'] = '全市';
        $data = HouseDeal::where($where)
            ->where($otherWhere)
            ->field('sum(cj_num) as total_cj_num,sum(cj_area * cj_avg) as total_cj_price,sum(cj_area) as total_cj_area')
            ->find();
        $res = [
            'cj_num' => $data['total_cj_num'],
            'cj_avg' => $data['total_cj_area'] ? sprintf("%.2f", $data['total_cj_price'] / $data['total_cj_area']) : 0,
            'cj_area_avg' => $data['total_cj_num'] ? sprintf("%.2f", $data['total_cj_area'] / $data['total_cj_num']) : 0,
        ];
        return $res;
    }

    // 均价趋势
    /**
     * @OA\Get(path="/api/v1.center/avgtrend",
     *   tags={"均价趋势"},
     *   summary="均价趋势",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function avgtrend()
    {
        $where = [
            'zone' => '全市',
            'reportcatalog' => '住宅',
        ];
        //当日，当月，当年
        $currentDate = date('Y-m-d', strtotime('-2 day'));
        // $currentDate = date('Y-m-d', strtotime('-3 day'));
        $cacheKey = "center_avg_trend" . $currentDate;
        $res = cache($cacheKey);
        if (!$res) {
            //前一日，上月，去年
            $lastDate = date('Y-m-d', strtotime('-4 day'));
            $lastMonthStart = date('Y-m-01', strtotime('first day of previous month'));
            $lastYearStart = date('Y-01-01', strtotime('-1 year'));

            //step 1获取当日和上一日均价
            $dayData = HouseDeal::where($where)
                ->where("tj_date >= '{$lastDate}' and tj_date <= '{$currentDate}'")
                ->field('tj_date,cj_avg')
                ->select()
                ->toArray();
            $monthDate = HouseDeal::where($where)
                ->where("tj_date >= '{$lastMonthStart}' and tj_date <= '{$currentDate}'")
                ->field("DATE_FORMAT(tj_date, '%Y-%m') as month_year,sum(cj_area * cj_avg) as total_cj_price, sum(cj_area) as total_cj_area")
                ->group('month_year')
                ->select()
                ->toArray();
            foreach ($monthDate as &$val) {
                $val['cj_avg'] =  sprintf("%.2f", $val['total_cj_price'] / $val['total_cj_area']);
            }
            $yearDate = HouseDeal::where($where)
                ->where("tj_date >= '{$lastYearStart}' and tj_date <= '{$currentDate}'")
                ->field("DATE_FORMAT(tj_date, '%Y') as year,sum(cj_area * cj_avg) as total_cj_price, sum(cj_area) as total_cj_area")
                ->group('year')
                ->select()
                ->toArray();
            foreach ($yearDate as &$val) {
                $val['cj_avg'] =  sprintf("%.2f", $val['total_cj_price'] / $val['total_cj_area']);
            }
            $res = [
                'day' => $dayData,
                'month' => $monthDate,
                'year' => $yearDate,
            ];
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }
    // 成交均价 近12个月
    /**
     * @OA\Get(path="/api/v1.center/avgdeal",
     *   tags={"成交均价"},
     *   summary="成交均价",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function avgdeal()
    {
        $where = [
            'zone' => '全市',
            'reportcatalog' => '住宅',
        ];
        //当日，当月，当年
        $currentDate = date('Y-m-d');
        $endDate = date('Y-m-01', strtotime('+1 month', strtotime('-1 year')));
        $cacheKey = "center_avg_deal" . $currentDate;
        $res = cache($cacheKey);
        if (!$res) {
            $res = HouseDeal::where($where)
                ->where("tj_date >= '{$endDate}'")
                ->field("DATE_FORMAT(tj_date, '%Y-%m') as name,sum(cj_area * cj_avg) as total_cj_price, sum(cj_area) as total_cj_area")
                ->group('name')
                ->select()
                ->toArray();
            foreach ($res as &$val) {
                $val['value'] =  (float)number_format($val['total_cj_price'] / $val['total_cj_area'], 2);
            }
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }

    // 均价分布 近30天
    /**
     * @OA\Get(path="/api/v1.center/avgtrend",
     *   tags={"均价分布"},
     *   summary="均价分布",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function avgdistribution()
    {
        $currentDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-32 day'));
        $where['zone'] = '全市';
        $cacheKey = "center_avg_distribution" . $currentDate;
        $res = cache($cacheKey);
        if (!$res) {
            $res = HouseDealArea::where($where)
                ->where("tj_date >= '{$startDate}'")
                ->field('area_type, sum(cj_area * cj_avg) as total_cj_price, sum(cj_num) as total_cj_num, sum(cj_area) as total_cj_area')
                ->group('area_type')
                ->select()
                ->toArray();
            foreach ($res as &$val) {
                $val['cj_avg'] = sprintf("%.2f", $val['total_cj_price'] / $val['total_cj_area']);
            }
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }

    public function avgzone()
    {
        $month = $this->request->param('month', date('Y-m')) . '-01';
        $lastMonth = date('Y-m-01', strtotime('first day of -1 month', strtotime($month)));

        $where['zone'] = '全市';
        $where = "zone <> '全市' and tj_date >= '{$month}' and tj_date <= '{$month}'";
        $cacheKey = "center_avg_zone" . $month;
        $res = cache($cacheKey);
        if (!$res) {
            $res = HouseDealStatistics::where("zone <> '全市' and tj_date = '{$month}'")
                ->field('zone, cj_avg')
                ->select()
                ->toArray();
            $lastMonthData = HouseDealStatistics::where("zone <> '全市' and tj_date = '{$lastMonth}'")
                ->field('zone, cj_avg')
                ->select()
                ->toArray();

            $lastMonthData = array_column($lastMonthData, 'cj_avg', 'zone');

            foreach ($res as &$val) {
                $val['rate'] = (float)number_format(($val['cj_avg'] - $lastMonthData[$val['zone']]) / $lastMonthData[$val['zone']] * 100, 2);
            }
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }
    //新房住宅成交量
    public function dealnum()
    {
        $day = date('Y-m-d', strtotime('-2 day'));
        $lastDay = date('Y-m-d', strtotime('-3 day'));
        $month = date('Y-m-01');
        $year = date('Y-01-01');

        $lastMonthStart = date('Y-m-01', strtotime('first day of previous month'));
        $lastYearStart = date('Y-01-01', strtotime('-1 year'));

        $cacheKey = "center_avg_zone" . date('Y-m-d');
        $res = cache($cacheKey);
        if (!$res) {
            $dayData = HouseDeal::where("zone = '全市' and tj_date = '{$day}'")
                ->field('sum(cj_num) as cj_num')
                ->find();
            $lastDayData = HouseDeal::where("zone = '全市' and tj_date = '{$lastDay}'")
                ->field('sum(cj_num) as cj_num')
                ->find();
            $res['day'] = [
                'cj_num' => $dayData['cj_num'],
                'diff' => $dayData['cj_num'] - $lastDayData['cj_num'],
            ];

            $monthData = HouseDeal::where("zone = '全市' and tj_date >= '{$month}'")
                ->field('sum(cj_num) as cj_num')
                ->find();
            $lastMonthData = HouseDeal::where("zone = '全市' and tj_date >= '{$lastMonthStart}' and tj_date < '{$month}'")
                ->field('sum(cj_num) as cj_num')
                ->find();
            $res['month'] = [
                'cj_num' => $monthData['cj_num'],
                'diff' => $monthData['cj_num'] - $lastMonthData['cj_num'],
            ];

            $yearData = HouseDeal::where("zone = '全市' and tj_date >= '{$year}'")
                ->field('sum(cj_num) as cj_num')
                ->find();
            $lastYearData = HouseDeal::where("zone = '全市' and tj_date >= '{$lastYearStart}' and tj_date < '{$year}'")
                ->field('sum(cj_num) as cj_num')
                ->find();
            $res['year'] = [
                'cj_num' => $yearData['cj_num'],
                'diff' => $yearData['cj_num'] - $lastYearData['cj_num'],
            ];
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }

    // 成交量 近12个月
    /**
     * @OA\Get(path="/api/v1.center/avgtrend",
     *   tags={"成交量"},
     *   summary="成交量",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function dealmonth()
    {
        $res = $this->getDealMont();
        $this->success('ok', $res);
    }

    private function getDealMont()
    {
        $where = [
            'zone' => '全市',
            'reportcatalog' => '住宅',
        ];
        //当日，当月，当年
        $currentDate = date('Y-m-d');
        //获取最近12个月
        $endDate = date('Y-m-01', strtotime('+1 month', strtotime('-1 year')));
        $cacheKey = "center_deal_month" . $currentDate;
        $res = cache($cacheKey);
        if (!$res) {
            $res = HouseDeal::where($where)
                ->where("tj_date >= '{$endDate}'")
                ->field("DATE_FORMAT(tj_date, '%Y-%m') as year,sum(cj_num) as cj_num")
                ->group('year')
                ->select()
                ->toArray();
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        return $res;
    }

    // 各区成交占比
    /**
     * @OA\Get(path="/api/v1.center/dealzone",
     *   tags={"成交占比"},
     *   summary="成交占比",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function dealzone()
    {
        $where = [
            'reportcatalog' => '住宅',
        ];
        $startDate = date('Y-m-d', strtotime('-32 day'));
        $currentDate = date('Y-m-d');
        $cacheKey = "center_deal_zone" . $currentDate;
        $res = cache($cacheKey);
        if (!$res) {
            $res = HouseDeal::where($where)
                ->where("zone <> '全市' and tj_date >= '{$startDate}'")
                ->field('zone,sum(cj_num) as cj_num')
                ->group('zone')
                ->select()
                ->toArray();
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }

    // 新房成交面积分布
    /**
     * @OA\Get(path="/api/v1.center/dealarea",
     *   tags={"成交占比"},
     *   summary="成交占比",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function dealarea()
    {
        $where = [
            'zone' => '全市',
        ];
        $startDate = date('Y-m-d', strtotime('-32 day'));
        $currentDate = date('Y-m-d');
        $cacheKey = "center_deal_area" . $currentDate;
        $res = cache($cacheKey);
        if (!$res) {
            $res = HouseDealArea::where($where)
                ->where("tj_date >= '{$startDate}'")
                ->field('area_type, sum(cj_num) as cj_num, sum(cj_area) as cj_area')
                ->group('area_type')
                ->select()
                ->toArray();
            $areaTotal = array_sum(array_column($res, 'cj_area'));
            foreach ($res as &$val) {
                $val['rate'] = (float)number_format($val['cj_area'] / $areaTotal * 100, 2);
            }
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }

    // 可售库存和月均销量
    /**
     * @OA\Get(path="/api/v1.center/newks",
     *   tags={"可售库存和月均销量"},
     *   summary="可售库存和月均销量",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function newks()
    {
        $where = [
            'reportcatalog' => '住宅',
            'zone' => '全市',
        ];
        $day = date('Y-m-d', strtotime('-2 day'));
        $lastDay = date('Y-m-d', strtotime('-3 day'));
        $month = date('Y-m-01');
        $lastMonthStart = date('Y-m-01', strtotime('first day of previous month'));
        $currentDate = date('Y-m-d');
        $cacheKey = "center_new_ks" . $currentDate;
        $res = cache($cacheKey);
        if (!$res) {
            $dayData = HouseDeal::where($where)
                ->where("tj_date = '{$day}'")
                ->field('tj_date,ks_num')
                ->find();
            $lastDayData = HouseDeal::where($where)
                ->where("tj_date = '{$lastDay}'")
                ->field('tj_date,ks_num')
                ->find();
            $monthData = HouseDeal::where($where)
                ->where("tj_date >= '{$month}'")
                ->field('sum(ks_num) as ks_num,count(id) as count')
                ->find();
            $lastMonthData = HouseDeal::where($where)
                ->where("tj_date >= '{$lastMonthStart}' and tj_date < '{$month}'")
                ->field('sum(ks_num) as ks_num,count(id) as count')
                ->find();
            $monthAvg = (float)number_format($monthData['ks_num'] / $monthData['count'], 2);
            $lastMonthAvg = (float)number_format($lastMonthData['ks_num'] / $lastMonthData['count'], 2);
            $res = [
                'tj_date' => $dayData['tj_date'],
                'ks_num' => $dayData['ks_num'],
                'day_diff' => (float)number_format($dayData['ks_num'] - $lastDayData['ks_num'], 2),
                'month_avg' => $monthAvg,
                'month_diff' => $monthAvg - $lastMonthAvg,
            ];
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }

    // 去化周期 近180天
    /**
     * @OA\Get(path="/api/v1.center/newcycle",
     *   tags={"去化周期"},
     *   summary="去化周期",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function newcycle()
    {
        // 去化周期 = 当前可售 / 当日成交
        $where = [
            'reportcatalog' => '住宅',
            'zone' => '全市',
        ];
        $day = date('Y-m-d', strtotime('-182 day'));
        $cacheKey = "center_new_cycle" . date('Y-m-d');
        $res = cache($cacheKey);
        if (!$res) {
            $res = HouseDeal::where($where)
                ->where("tj_date >= '{$day}'")
                ->field('tj_date, ks_num, cj_num')
                ->select()
                ->toArray();
            foreach ($res as &$val) {
                $cycle = $val['cj_num'] ? (float)number_format($val['ks_num'] / $val['cj_num'], 2) : 0;
                $val['cycle'] = $cycle;
            }
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }
    //新房住宅供销存(近12月)
    public function newsupply()
    {
        $day = date('Y-m-d', strtotime('-2 day'));
        $endDate = date('Y-m-01', strtotime('+1 month', strtotime('-1 year')));
        $where = [
            'reportcatalog' => '住宅',
            'zone' => '全市',
        ];
        $cacheKey = "center_new_supply" . date('Y-m-d');
        $res = cache($cacheKey);
        if (!$res) {
            $res = HouseDeal::where($where)
                ->where("tj_date = '{$day}'")
                ->field('tj_date, ks_num')
                ->find();
            $cjData = HouseDeal::where($where)
                ->where("tj_date >= '{$endDate}'")
                ->field('sum(cj_num) as cj_num')
                ->find();
            $res['cj_num'] = $cjData['cj_num'] ? $cjData['cj_num'] : 0;
            // todo取证取数待定
            $propertySum = $this->getPropertySum("tj_date >= '{$endDate}'");
            $res['qz_num'] = $propertySum['ys_suites'] ?? 0;
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }

    private function getPropertySum($otherWhere)
    {
        $where = [
            'proj_useage' => '住宅'
        ];
        $sypeIds = PropertyInfo::where($where)
            ->where($otherWhere)
            ->field("sype_id")
            ->select()
            ->toArray();
        $sypeIds = array_column($sypeIds, 'sype_id');
        $res = ProjectBaseInfo::whereIn('pre_sellId', $sypeIds)
            ->field('sum(ys_suites) as ys_suites')
            ->find();
        return $res;
    }

    //新房住宅供销趋势
    public function newtrend()
    {
        $cacheKey = "center_new_trend" . date('Y-m-d');
        $res = cache($cacheKey);
        if (!$res) {
            $res = $this->getDealMont();
            $endDate = date('Y-m-01', strtotime('+1 month', strtotime('-1 year')));
            $propertyData = $this->getPropertyByMonth("tj_date >= '{$endDate}'");
            foreach ($res as &$val) {
                // todo 取证数据待定
                $val['qz_num'] = isset($propertyData[$val['year']]) ? $propertyData[$val['year']] : 0;
            }
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }

    private function getPropertyByMonth($otherWhere)
    {
        $where = [
            'proj_useage' => '住宅'
        ];
        $sypeIds = PropertyInfo::where($where)
            ->where($otherWhere)
            ->field("DATE_FORMAT(tj_date, '%Y-%m') as year,sype_id")
            ->select()
            ->toArray();
        foreach ($sypeIds as $val) {
            $map[$val['year']][] = $val['sype_id'];
        }
        $res = [];
        foreach ($map as $mapKey => $mapVal) {
            $data = ProjectBaseInfo::whereIn('pre_sellId', $mapVal)
                ->field('sum(ys_suites) as ys_suites')
                ->find();
            $res[$mapKey] = $data['ys_suites'] ?? 0;
        }
        return $res;
    }

    //各区库存分布
    public function newzone()
    {
        $cacheKey = "center_new_zone" . date('Y-m-d');
        $res = cache($cacheKey);
        if (!$res) {
            $date = date('Y-m-d', strtotime('-2 day'));
            $currentWhere['tj_date'] = $date;
            $currentWhere['reportcatalog'] = '住宅';
            $res = HouseDeal::where($currentWhere)
                ->field('tj_date,zone,cj_num,ks_num')
                ->group('zone')
                ->select()
                ->toArray();
            cache($cacheKey, json_encode($res), 86400);
        } else {
            $res = json_decode($res, true);
        }
        $this->success('ok', $res);
    }
}
