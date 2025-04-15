<?php

namespace app\api\controller\v1;

use app\backend\model\HouseDeal as HouseDealModel;
use app\backend\model\HouseDealArea;
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
        }else{
            $this->error($res['errorCode'], [], $res['message']);
        }
    }

    public function houseDay()
    {
        $end_date = date('Ymd');
        $start_date = date('Ymd', strtotime('-1 day'));
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
        }else{
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
