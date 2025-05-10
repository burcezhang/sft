<?php

namespace app\api\controller\v1;

use app\backend\model\HouseDeal as HouseDealModel;
use app\backend\model\HouseDealArea;
use app\backend\model\HouseDealStatistics;
use app\backend\model\PropertyInfo;
use app\backend\model\ProjectBaseInfo;
use app\backend\model\PropertyStatistics;
use fun\auth\Api;

/**
 * @title   数据同步
 * @desc    异步获取数据同步
 * Class HouseDeal
 * @package app\index\controller
 */
class House extends Api
{
    protected $noAuth = ['*'];
    private $houseDealUrl = "https://opendata.sz.gov.cn/api/29200_01903510/1/service.xhtml";
    private $houseDealAreaUrl = "https://opendata.sz.gov.cn/api/29200_01903511/1/service.xhtml";
    private $rows = 2000;
    //同步一手商品房成交信息
    private function houseDealSync($start_date, $end_date)
    {
        // 不限制内存
        ini_set('memory_limit', '-1');
        // 限制最大执行时间
        set_time_limit(0);
        // https://opendata.sz.gov.cn/api/29200_01903510/1/service.xhtml?page=1&rows=500&appKey=2cb7d001dcca4903b1829c049a0d907b&startDate=20241204&endDate=20241205
        $appkey = env('OPENDATA_SZ_APPKEY', '');
        if (!$appkey) {
            $this->error('请配置OPENDATA_SZ_APPKEY');
        }
        $param = [
            'page' => 1,
            'rows' => $this->rows,
            'appKey' => $appkey,
            'startDate' => $start_date,
            'endDate' => $end_date,
        ];
        // $url = $this->houseDealUrl . '?' . http_build_query($param);
        $res = $this->getData($this->houseDealUrl, http_build_query($param));
        if (!isset($res['errorCode'])) {
            echo "一手商品房成交信息 全量 -- 开始执行";
            $this->saveHouseDeal($res['data']);
            echo "一手商品房成交信息 全量 -- 数据正常写入";
            sleep(2);
            //如果返回的总数据 total 大于当前请求的每页数据  则进行循环获取数据
            if ($res['total'] > $this->rows) {
                $total_page = ceil($res['total'] / $this->rows);
                // 数据入库操作
                for ($i = 2; $i <= $total_page; $i++) {
                    $param['page'] = $i;
                    $res = $this->getData($this->houseDealUrl, http_build_query($param));
                    $this->saveHouseDeal($res['data']);
                    sleep(2);
                }
            }
            echo "一手商品房成交信息 全量 -- 共写入" . $res['total'] . "条数据";
            $this->success('ok');
        } else {
            $this->error($res['errorCode'], [], $res['message']);
        }
    }

    public function houseDay()
    {
        $end_date = date('Ymd');
        $start_date = date('Ymd', strtotime('-4 day'));
        $this->houseDealSync($start_date, $end_date);
    }

    public function houseMonth()
    {
        $end_date = date('Ymd');
        $start_date = date('Ymd', strtotime('-91 day'));
        $this->houseDealSync($start_date, $end_date);
    }

    private function saveHouseDeal($data)
    {
        if ($data) {
            $saveData = [];
            foreach ($data as $v) {
                $saveData[] = [
                    'id' => $v['ID'],
                    'tj_date' => $v['TJ_DATE'],
                    'reportcatalog' => $v['REPORTCATALOG'],
                    'cj_num' => $v['CJ_NUM'],
                    'cj_area' => $v['CJ_AREA'],
                    'cj_avg' => $v['CJ_AVG'],
                    'ks_num' => $v['KS_NUM'],
                    'ks_area' => $v['KS_AREA'],
                    'zone' => $v['ZONE'],
                ];
            }
            $houseDealModel = new HouseDealModel();
            $houseDealModel->saveAll($saveData);
            echo "一手商品房成交信息 全量 -- 数据正常写入";
        }
    }

    //一手商品房按面积统计成交信息（按日统计）
    private function houseDealAreaSync($start_date, $end_date)
    {
        // 不限制内存
        ini_set('memory_limit', '-1');
        // 限制最大执行时间
        set_time_limit(0);
        // https://opendata.sz.gov.cn/api/29200_01903511/1/service.xhtml?page=1&rows=500&appKey=2cb7d001dcca4903b1829c049a0d907b&startDate=20241204&endDate=20241205
        $appkey = env('OPENDATA_SZ_APPKEY', '');
        if (!$appkey) {
            $this->error('请配置OPENDATA_SZ_APPKEY');
        }
        $param = [
            'page' => 1,
            'rows' => $this->rows,
            'appKey' => $appkey,
            'startDate' => $start_date,
            'endDate' => $end_date,
        ];
        $res = $this->getData($this->houseDealAreaUrl, http_build_query($param));
        if (!isset($res['errorCode'])) {
            echo "一手商品房按面积统计成交信息（按日统计） -- 开始执行";
            $this->saveHouseAreaDeal($res['data']);
            echo "一手商品房按面积统计成交信息（按日统计） -- 数据正常写入";
            sleep(2);
            //如果返回的总数据 total 大于当前请求的每页数据  则进行循环获取数据
            if ($res['total'] > $this->rows) {
                $total_page = ceil($res['total'] / $this->rows);
                // 数据入库操作
                for ($i = 2; $i <= $total_page; $i++) {
                    $param['page'] = $i;
                    $res = $this->getData($this->houseDealUrl, http_build_query($param));
                    $this->saveHouseAreaDeal($res['data']);
                    sleep(2);
                }
            }
            echo "一手商品房按面积统计成交信息（按日统计） -- 共写入" . $res['total'] . "条数据";
            $this->success('ok');
        } else {
            $this->error($res['errorCode'], [], $res['message']);
        }
    }

    private function saveHouseAreaDeal($data)
    {
        if ($data) {
            $saveData = [];
            foreach ($data as $v) {
                $saveData[] = [
                    'id' => $v['ID'],
                    'tj_date' => $v['TJ_DATE'],
                    'area_type' => $v['AREA_TYPE'],
                    'cj_num' => $v['CJ_NUM'],
                    'cj_area' => $v['CJ_AREA'],
                    'cj_avg' => $v['CJ_AVG'],
                    'zone' => $v['ZONE'],
                ];
            }
            $houseDealAreaModel = new HouseDealArea();
            $houseDealAreaModel->saveAll($saveData);
            echo "一手商品房成交信息 全量 -- 数据正常写入";
        }
    }

    public function houseAreaDay()
    {
        $end_date = date('Ymd');
        $start_date = date('Ymd', strtotime('-1 day'));
        $this->houseDealAreaSync($start_date, $end_date);
    }

    public function houseAreaMonth()
    {
        $end_date = date('Ymd');
        $start_date = date('Ymd', strtotime('-91 day'));
        $this->houseDealAreaSync($start_date, $end_date);
    }

    //一手商品房成交信息 按月统计 按区分组
    public function statistics()
    {
        echo "一手商品房成交信息 按月统计 按区分组 开始执行";
        $i = 0;
        $staData = HouseDealModel::where("reportcatalog = '住宅'")
            ->field("sum(cj_num) as cj_num,reportcatalog,DATE_FORMAT(tj_date, '%Y-%m-01') as month_year,zone,sum(cj_area * cj_avg) as cj_price, sum(cj_area) as cj_area")
            ->group("month_year,zone")
            ->select()
            ->toArray();
        foreach ($staData as $val) {
            $val['cj_avg'] = $val['cj_area'] ? $val['cj_price'] / $val['cj_area'] : 0;
            $val['tj_date'] = $val['month_year'];
            $exit = HouseDealStatistics::where(['tj_date' => $val['month_year'], 'zone' => $val['zone'], 'reportcatalog' => '住宅'])
                ->field('id')
                ->find();
            if (!$exit) {
                unset($val['month_year']);
                (new HouseDealStatistics())->save($val);
                $i++;
            }
        }
        echo "一手商品房成交信息 按月统计 按区分组 执行完成 共写入{$i}条数据";
    }

    //取证数据统计
    public function propertyStatistics()
    {
        try {
            $tj_date = input('date', date('Y-m-d', strtotime('-10 day')));
            $deal = HouseDealModel::where("tj_date >= '{$tj_date}' and zone = '全市'")
                ->where("reportcatalog = '住宅'")
                ->field('tj_date, cj_num')
                ->select()
                ->toArray();

            $propertyInfo = PropertyInfo::where("tj_date >= '{$tj_date}' and proj_useage = '住宅'")
                ->field('sype_id, tj_date')
                ->select()
                ->toArray();

            $qzNum = PropertyInfo::where("tj_date >= '{$tj_date}' and proj_useage = '住宅'")
                ->field('count(id) as count, tj_date')
                ->group('tj_date')
                ->select()
                ->toArray();
            $qzNum = array_column($qzNum, 'count', 'tj_date');

            $sypeIds = array_column($propertyInfo, 'sype_id');
            $projectBaseInfo = ProjectBaseInfo::where('pre_sellId', 'in', $sypeIds)
                ->field('sum(ys_suites) as ys_suites,pre_sellId')
                ->group('pre_sellId')
                ->select()
                ->toArray();
            $proNum = array_column($projectBaseInfo, 'ys_suites', 'pre_sellId');
            $ysSuites = [];
            foreach ($propertyInfo as $val) {
                if (isset($ysSuites[$val['tj_date']])) {
                    $ysSuites[$val['tj_date']] += isset($proNum[$val['sype_id']]) ? $proNum[$val['sype_id']] : 0;
                } else {
                    $ysSuites[$val['tj_date']] = isset($proNum[$val['sype_id']]) ? $proNum[$val['sype_id']] : 0;
                }

            }
            foreach ($deal as $val) {
                $saveData = [
                    'tj_date' => $val['tj_date'],
                    'cj_num' => $val['cj_num'],
                    'qz_num' => isset($qzNum[$val['tj_date']]) ? $qzNum[$val['tj_date']] : 0,
                    'ys_suites' => isset($ysSuites[$val['tj_date']]) ? $ysSuites[$val['tj_date']] : 0,
                    'house_suites' => isset($ysSuites[$val['tj_date']]) ? $ysSuites[$val['tj_date']] : 0,
                ];
                $exit = (new PropertyStatistics())->where(['tj_date' => $val['tj_date']])
                    ->field('id')
                    ->find();
                if ($exit) {
                    (new PropertyStatistics())->save($saveData, ['tj_date' => $val['tj_date']]);
                } else {
                    (new PropertyStatistics())->save($saveData);
                }
            }
            echo "房源信息统计 按日 '{$tj_date}' 后的数据统计完成";
        } catch (\Throwable $th) {
            echo "{$th->getMessage()}";
        }
    }


    public function propertyInfo()
    {
        // todo
        $startDate = '2025-01-01';
        $houses = PropertyInfo::where('status', 1)
            ->where("tj_date >= '{$startDate}'")
            ->field('sype_id')
            ->select()
            ->toArray();
        foreach ($houses as $house) {
            sleep(1);
            try {
                $app = \think\App::getInstance();
                $datasync = new \app\api\controller\v1\Datasync($app);
                $res = $datasync->getSuiteInformation($house['sype_id']);
            } catch (\Throwable $th) {
                var_dump($th->getMessage());
            }
        }
    }

    public function getData($url, $data, $header = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($curl, CURLOPT_TIMEOUT, 300); // 设置超时时间

        $output = curl_exec($curl);
        if (curl_errno($curl)) {
            $errorMsg = curl_error($curl);
            curl_close($curl);
            $this->error('网络错误: ' . $errorMsg);
        }

        curl_close($curl);
        $output = json_decode($output, true);

        return $output;
    }
}
