<?php

namespace app\api\controller\v1;

use app\backend\model\Building;
use app\backend\model\BuildingUnits;
use app\backend\model\BuildingUnitsHistory;
use app\backend\model\ProjectBaseInfo;
use app\backend\model\ProjectBuilding;
use app\backend\model\PropertyInfo;
use app\backend\model\MoneyWatcher;
use app\backend\model\Region;
use app\backend\model\Floor;
use app\backend\model\PropertyInfoHistory;
use app\backend\model\Seat;
use fun\auth\Api;

/**
 * @title   数据同步
 * @desc    异步获取数据同步
 * Class Datasync
 * @package app\index\controller
 */
class Datasync extends Api
{
    protected $noAuth = ['*'];

    public $pageIndex = 1;

    public $header = [
        'Content-Type: application/json',
        'accept: application/json, text/plain, */*',
        'cookie: WSESSIONID-SZFDC-SCJY=IhBjl71aJATu-uZ6obMgknIPp45vD9dBQCxOoiIUYKuBn4T-bEC_!373897280; AD_insert_cookie_89188=73136699;',
        'host: zjj.sz.gov.cn',
        'origin: https://zjj.sz.gov.cn',
        'referer: https://zjj.sz.gov.cn/szfdcscjy/',
        'sec-fetch-dest: empty',
        'sec-fetch-mode: cors',
        'sec-fetch-site: same-origin',
        'x-requested-with: XMLHttpRequest',
        'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1',
    ];

    // 同步地区数据
    public function region()
    {
        $data = [];
        $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getProjectListSearchItem', $data);

        if ($res['status'] == 200) {
            $region = $res['data'][0];
            $list = $region['list'];
            if (!empty($list)) {
                // 处理数据入库
                // var_dump($list);exit;
                foreach ($list as $key => $value) {
                    // 先查询是否存在
                    $where = ['old_key' => $value['key']];
                    $info = Region::where($where)->find();
                    if (!$info) {
                        if ($value['label'] != '全部') {
                            $data = [
                                'label' => $value['label'],
                                'value' => $value['value'],
                                'old_key' => $value['key'],
                                'key' => $value['key'],
                                'acive' => $value['acive'] == 'true' ? 1 : 0,
                                'status' => 1,
                                'create_time' => time(),
                                'update_time' => time(),
                            ];
                            Region::create($data);
                        }
                    } else {
                        // 过滤掉全部
                        if ($value['label'] != '全部') {
                            $data = [
                                'label' => $value['label'],
                                'value' => $value['value'],
                                'old_key' => $value['key'],
                                'acive' => $value['acive'] == 'true' ? 1 : 0,
                            ];
                            Region::where($where)->update($data);
                        }
                    }
                }
            }
            // 处理数据
            $this->success('获取数据成功', $res['data']);
        } else {
            $this->error('region获取数据失败');
        }
    }

    // 同步房源信息
    public function house()
    {
        // 不限制内存
        ini_set('memory_limit', '-1');
        // 限制最大执行时间
        set_time_limit(0);
        $json = '{"pageIndex":' . $this->pageIndex . ',"pageSize":10,"zone":"","bldgNameNo":"","ownerName":"","parcelNo":"","unitNo":"","searchType":0,"searchKeyWords":""}';
        $params = json_decode($json, true);
        // var_dump($params);exit;
        $header = $this->header;
        $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getProjectList', json_encode($params), $header);

        if ($res['status'] == 200) {
            // echo '<pre>';
            // var_dump($res['data']['list']);exit;
            $list = $res['data']['list'];
            // 处理数据入库
            if (!empty($list)) {

                foreach ($list as $key => $value) {
                    // 查看是否存在
                    $where = ['syp_id' => $value['sypId'], 'sype_id' => $value['sypeId']];
                    $info = PropertyInfo::where($where)->find()->toArray();
                    // 驼峰转下划线
                    $data = $this->camelToSnakeAdvanced($value);
                    $data['site_address'] = $value['siteaddress'];
                    unset($data['siteaddress']);
                    $data['image_path'] = 'https://zjj.sz.gov.cn/szfdcscjy/' . $value['imagePath'];
                    // 图片本地化
                    $data['image_path'] = $this->getLocalImage($data['image_path']);
                    if (!$info) {
                        PropertyInfo::create($data);
                    } else {
                        unset($data['averagePrice']);
                        $data['update_time'] = time();
                        PropertyInfo::where($where)->update($data);
                    }
                    // echo '<pre>';
                    // var_dump($info);exit;
                    // 同步套房信息
                    // $this->getSuiteInformation($value['sypeId']);
                }

                // usleep(50000);

                if (count($list) == 10) {
                    unset($list);
                    $this->pageIndex++;
                    $this->house();
                    return;
                }
                unset($list);

                $this->success('获取数据成功');
            } else {
                $this->error('获取数据失败1');
            }
        } else {
            $this->error('house获取数据失败');
        }
    }
    public function househistory()
    {
        var_dump('开始执行househistory'.$this->pageIndex);
        // 不限制内存
        ini_set('memory_limit', '-1');
        // 限制最大执行时间
        set_time_limit(0);
        $json = '{"pageIndex":' . $this->pageIndex . ',"pageSize":10,"zone":"","bldgNameNo":"","ownerName":"","parcelNo":"","unitNo":"","searchType":0,"searchKeyWords":""}';
        $params = json_decode($json, true);
        // var_dump($params);exit;
        $header = $this->header;
        $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getProjectList', json_encode($params), $header);

        if ($res['status'] == 200) {
            $list = $res['data']['list'];
            // 处理数据入库
            if (!empty($list)) {
                foreach ($list as $key => $value) {
                    // 驼峰转下划线
                    $data = $this->camelToSnakeAdvanced($value);
                    $data['site_address'] = $value['siteaddress'];
                    unset($data['siteaddress']);
                    $data['image_path'] = 'https://zjj.sz.gov.cn/szfdcscjy/' . $value['imagePath'];
                    // 图片本地化
                    $data['image_path'] = $this->getLocalImage($data['image_path']);
                    PropertyInfoHistory::create($data);
                }

                if (count($list) == 10) {
                    unset($list);
                    $this->pageIndex++;
                    $this->houseHistory();
                    return;
                }
                unset($list);
                var_dump('获取数据成功');
                // $this->success('获取数据成功');
            } else {
                var_dump('获取数据失败1');
                // $this->error('获取数据失败1');
            }
        } else {
            $this->error('house获取数据失败');
        }
    }

    // getLocalImage
    public function getLocalImage($url)
    {
        // url 中 \ 替换成 /
        $url = str_replace('\\', '/', $url);
        $path = app()->getRootPath() . 'public'. DS . 'uploads' . DS . 'house' . DS;
        $filename = basename($url);
        $file = $path . $filename;
        if (file_exists($file)) {
            return config('app.domain') . '/uploads/house/' . $filename;
        }
        file_put_contents($file, file_get_contents($url));
        return config('app.domain') . '/uploads/house/' . $filename;
    }

    public function synchouse()
    {
        // sype_id
        $sype_id = input('param.sype_id');
        if (empty($sype_id)) {
            $this->error('参数错误');
        }
        $this->getSuiteInformation($sype_id);
        $this->success('执行成功');
    }
    public function synchousehistory()
    {
        // sype_id
        $sype_id = input('param.sype_id');
        if (empty($sype_id)) {
            $this->error('参数错误');
        }
        $this->getSuiteInformationHistory($sype_id);
        $this->success('执行成功');
    }

    // 获取套房信息
    public function getSuiteInformation($preSellId)
    {
        var_dump("开始执行getSuiteInformation");
        // 不限制内存
        ini_set('memory_limit', '-1');
        // 限制最大执行时间
        set_time_limit(0);

        // https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getProjectByPreSellId
        // preSellId=137270
        $header = $this->header;
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        // array 转 form-urlencoded

        $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getProjectByPreSellId', http_build_query(['preSellId' => $preSellId]), $header);
        // echo '<pre>';
        // var_dump($res['data']);exit;
        if ($res['status'] == 200) {


            $jsonData = $res['data'];
            // 项目基础信息
            $baseInfoData = $jsonData['iszYsProjectBaseInfoVo'];
            $baseInfoData = $this->camelToSnakeAdvanced($baseInfoData);
            $info = ProjectBaseInfo::where('pre_sellId',$preSellId)->find();
            if(!$info){
                $baseInfoData['create_time'] = time();
                $baseInfoData['update_time'] = time();
                $baseInfoData['pre_sellId'] = $preSellId;
                (new ProjectBaseInfo())->save($baseInfoData);
            }

            // 项目建筑信息
            $projectBuilding = $jsonData['iszYsProjectBuildingVoList'];
            $buildings = [];
            foreach ($projectBuilding as $value) {
                $value = $this->camelToSnakeAdvanced($value);
                $buildingData = $value; // 每条数据
                $info = ProjectBuilding::where('id',$buildingData['id'])->find();
                if(!$info){
                    // unset($buildingData['id']);
                    $buildingData['create_time'] = time();
                    $buildingData['update_time'] = time();
                    $buildingData['pre_sellId'] = $preSellId; // 关联的预售 ID
                    // $buildings[] = $buildingData; // 将数据添加到待插入的数组中
                    ProjectBuilding::create($buildingData);
                }else{
                    $buildingData['update_time'] = time();
                    ProjectBuilding::where('id',$buildingData['id'])->update($buildingData);
                }
                
            }

            // 收款监管信息
            $moneyWatcherVoLists = $jsonData['moneyWatcherVoList'];
            $moneyWatcherVoList = [];

            foreach ($moneyWatcherVoLists as $key => $value) {
                $value = $this->camelToSnakeAdvanced($value);
                $buildingData = $value; // 每条数据
                $info = MoneyWatcher::where('fyp_id',$buildingData['fyp_id'])->where('pre_sellId',$preSellId)->find();
                if(!$info){
                    $buildingData['create_time'] = time();
                    $buildingData['update_time'] = time();
                    $buildingData['pre_sellId'] = $preSellId; // 关联的预售 ID
                    $moneyWatcherVoList[] = $buildingData; // 将数据添加到待插入的数组中
                }
            }

            if (!empty($moneyWatcherVoList)) {
                $moneyWatcher = new MoneyWatcher();
                $moneyWatcher->saveAll($moneyWatcherVoList);
            }
            $this->getBuildingNameListToPublicity($preSellId, $moneyWatcherVoLists[0]['fypId']);
            

            var_dump('getSuiteInformation获取数据成功');
            // $this->success('getSuiteInformation获取数据成功');
        } else {
            var_dump('getSuiteInformation获取数据失败2');
            // $this->error('getSuiteInformation获取数据失败2');
        }
    }
    // 获取套房信息
    public function getSuiteInformationHistory($preSellId)
    {
        var_dump("开始执行getSuiteInformationHistory");
        // 不限制内存
        ini_set('memory_limit', '-1');
        // 限制最大执行时间
        set_time_limit(0);

        $buildings = Building::where('preSellId', $preSellId)->select();

        foreach ($buildings as $key => $value) {
            
            $this->getHouseInfoListToPublicityHistory([
                'status' => -1,
                'floor' => '',
                'buildingbranch' => '',
                'fybId' => $value['key'],
                'preSellId' => $preSellId,
                'ysProjectId' => $value['ysProjectId'],
                'type' => '',
                'useage' => '',
            ]);
            sleep(1);
        }
        var_dump('getSuiteInformation获取数据成功');
        // https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getProjectByPreSellId
        // preSellId=137270
        // $header = $this->header;
        // $header[] = 'Content-Type: application/x-www-form-urlencoded';
        // // array 转 form-urlencoded

        // $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getProjectByPreSellId', http_build_query(['preSellId' => $preSellId]), $header);
        // // echo '<pre>';
        // // var_dump($res['data']);exit;
        // if ($res['status'] == 200) {
       
        //     $jsonData = $res['data'];
            
        //     // {"status":-1,"floor":"","buildingbranch":"","fybId":"53754","preSellId":"137270","ysProjectId":"34426","type":"","useage":""}
        //     $this->getHouseInfoListToPublicityHistory([
        //         'status' => -1,
        //         'floor' => '',
        //         'buildingbranch' => '',
        //         'fybId' => $jsonData['iszYsProjectBuildingVoList'][0]['id'],
        //         'preSellId' => $preSellId,
        //         'ysProjectId' => $jsonData['moneyWatcherVoList'][0]['fypId'],
        //         'type' => '',
        //         'useage' => '',
        //     ]);
        //     var_dump('getSuiteInformation获取数据成功');
        //     // $this->success('getSuiteInformation获取数据成功');
        // } else {
        //     var_dump('getSuiteInformation获取数据失败2');
        //     // $this->error('getSuiteInformation获取数据失败2');
        // }
    }



    // 获取套房信息
    public function getHouseInformation($preSellId, $fybId, $ysProjectId)
    {
        // https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getBuildingDictToPublicity
        $header = $this->header;
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getBuildingDictToPublicity', http_build_query([
            'preSellId' => $preSellId,
            'fybId' => $fybId,
            'ysProjectId' => $ysProjectId,
        ]), $header);
        // var_dump($res,'https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getBuildingDictToPublicity', http_build_query([
        //     'preSellId' => $preSellId,
        //     'fybId' => $fybId,
        //     'ysProjectId' => $ysProjectId,
        // ]), $header);exit;
        if ($res['status'] == 200) {
            // 层号跟座号
            $chList = $res['data']['chList'];
            $gnqmcList = $res['data']['gnqmcList'];
            // 入库楼层
            $floor = new Floor();
            $floors = [];
            foreach ($chList as $value) {
                // 是否库中有
                $floorInfo = Floor::where('title', $value)->where('preSellId', $preSellId)->where('fybId', $fybId)->where('ysProjectId', $ysProjectId)->find();
                if (!$floorInfo) {
                    $floors[] = [
                        'title' => $value,
                        'preSellId' => $preSellId,
                        'fybId' => $fybId,
                        'ysProjectId' => $ysProjectId,
                    ];
                }
            }
            if (!empty($floors)) {
                $floor->saveAll($floors);
            }


            $seat = new Seat();
            $seats = [];
            foreach ($gnqmcList as $value) {
                // 是否库中有
                $seatInfo = Seat::where('title', $value)->where('preSellId', $preSellId)->where('fybId', $fybId)->where('ysProjectId', $ysProjectId)->find();
                if (!$seatInfo) {
                    $seats[] = [
                        'title' => $value,
                        'preSellId' => $preSellId,
                        'fybId' => $fybId,
                        'ysProjectId' => $ysProjectId,
                    ];
                }
            }
            if (!empty($seats)) {
                $seat->saveAll($seats);
            }
            var_dump('getHouseInformation获取数据成功');
            // var_dump('getHouseInformation获取数据成功', $floors, $seats);
        } else {
            $this->error('getHouseInformation获取数据失败');
        }
    }

    // 获取楼盘楼栋信息
    public function getBuildingNameListToPublicity($preSellId, $ysProjectId)
    {
        // https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getBuildingNameListToPublicity
        $header = $this->header;
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getBuildingNameListToPublicity', http_build_query([
            'preSellId' => $preSellId,
            'ysProjectId' => $ysProjectId,
        ]), $header);
        if ($res['status'] == 200) {
            // 楼栋信息
            $list = $res['data'];
            foreach ($list as $value) {
                $info = Building::where('old_key', $value['key'])->where('preSellId', $preSellId)->where('ysProjectId', $ysProjectId)->find();
                if(!$info){
                    Building::create([
                        'label' => $value['label'],
                        'value' => $value['value'],
                        'old_key' => $value['key'],
                        'key' => $value['key'],
                        'acive' => $value['acive'] == 'true' ? 1 : 0,
                        'status' => 1,
                        'create_time' => time(),
                        'update_time' => time(),
                        'preSellId' => $preSellId,
                        'ysProjectId' => $ysProjectId,
                    ]);
                }

                $this->getHouseInformation($preSellId, $value['key'], $ysProjectId);
                
                $this->getHouseInfoListToPublicity([
                    'status' => -1,
                    'floor' => '',
                    'buildingbranch' => '',
                    'fybId' => $value['key'],
                    'preSellId' => $preSellId,
                    'ysProjectId' => $ysProjectId,
                    'type' => '',
                    'useage' => '',
                ],$value['label']);
            }
            // if (!empty($buildings)) {
            //     $building->saveAll($buildings);
            // }
            var_dump('getBuildingNameListToPublicity获取数据成功');
            // var_dump('getBuildingNameListToPublicity获取数据成功', $buildings);
        } else {
            $this->error('getBuildingNameListToPublicity获取数据失败');
        }
    }

    // 获取楼盘房屋信息列表
    public function getHouseInfoListToPublicity($data,$buildingbranch = '')
    {
        // https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getHouseInfoListToPublicity
        // {"status":-1,"floor":"","buildingbranch":"","fybId":"53754","preSellId":"137270","ysProjectId":"34426","type":"","useage":""}

        $header = $this->header;

        $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getHouseInfoListToPublicity', json_encode($data), $header);
        if ($res['status'] == 200) {
            // 层号房屋信息
            $units  = [];
            $list = $res['data'];
            foreach ($list as $value) {
                foreach ($value['list'] as $unit) {
                    $old = $unit;
                    $unit = $this->camelToSnakeAdvanced($unit);
                    // if (isset($unit['id'])) {
                    //     unset( $unit['id']);
                    // }
                    $unit['pre_sellId'] = $data['preSellId'];
                    $unit['fybId'] = $data['fybId'];
                    $unit['ysProjectId'] = $data['ysProjectId'];
                    $unit['house_nb'] = $unit['housenb'];
                    $unit['building_branch'] = $old['buildingbranch'];
                    $unit['usage'] = $old['useage'];
                    unset($unit['buildingbranch']);
                    unset($unit['housenb']);
                    unset($unit['useage']);
                    
                    $info = BuildingUnits::where('id', $unit['id'])->find();
                    $unit['ys_inside_area'] = $unit['ysinsidearea'];
                    $unit['ys_expand_area'] = $unit['ysexpandarea'];
                    $unit['ys_building_area'] = $unit['ysbuildingarea'];
                    $unit['jg_inside_area'] = $unit['jginsidearea'];
                    $unit['jg_expand_area'] = $unit['jgexpandarea'];
                    $unit['jg_building_area'] = $unit['jgbuildingarea'];
                    $unit['ask_price_each_b'] = $unit['askpriceeach_b'];
                    $unit['ask_price_total_b'] = $unit['askpricetotal_b'];
                    $unit['str_contract_id'] = $unit['strcontractid'];
                    unset($unit['ysinsidearea']);
                    unset($unit['ysexpandarea']);
                    unset($unit['ysbuildingarea']);
                    unset($unit['jginsidearea']);
                    unset($unit['jgexpandarea']);
                    unset($unit['jgbuildingarea']);
                    unset($unit['askpriceeach_b']);
                    unset($unit['askpricetotal_b']);
                    unset($unit['strcontractid']);

                    if(empty($unit['building_branch'])){
                        $unit['building_branch'] = '未知';
                    }
                    if ($info) {
                        if(!in_array($info['last_status_name'],['已签认购书', '已签合同', '已备案']) && 
                            in_array($unit['last_status_name'],['已签认购书', '已签合同', '已备案']))
                        {
                            $unit['check_time'] = time();
                        }
                        // 更新时间
                        $unit['update_time'] = time();
                        BuildingUnits::where('id',$unit['id'])->update($unit);
                    }else{
                        if(in_array($unit['last_status_name'],['已签认购书', '已签合同', '已备案']))
                        {
                            $unit['check_time'] = time();
                        }
                        // 创建时间
                        $unit['create_time'] = time();
                        $unit['sf3_ask_price_each_b'] = $unit['ask_price_total_b'] * 0.7;
                        // 首付4成
                        $unit['sf4_ask_price_each_b'] = $unit['ask_price_total_b'] * 0.6;

                        BuildingUnits::create($unit);
                    }
                    $units[] = $unit;
                }
            }
            // if (!empty($units)) {
            //     $unit = new BuildingUnits();
            //     $unit->saveAll($units);
            // }
            var_dump('getHouseInfoListToPublicity获取数据成功');
            // var_dump('getHouseInfoListToPublicity获取数据成功', $units);
        } else {
            $this->error('getHouseInfoListToPublicity获取数据失败');
        }
    }
    public function getHouseInfoListToPublicityHistory($data)
    {
        // https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getHouseInfoListToPublicity
        // {"status":-1,"floor":"","buildingbranch":"","fybId":"53754","preSellId":"137270","ysProjectId":"34426","type":"","useage":""}

        $header = $this->header;

        $res =  $this->getData('https://zjj.sz.gov.cn/szfdcscjy/projectPublish/getHouseInfoListToPublicity', json_encode($data), $header);
        if ($res['status'] == 200) {
            // 层号房屋信息
            $units  = [];
            $list = $res['data'];
            foreach ($list as $value) {
                foreach ($value['list'] as $unit) {
                    $old = $unit;
                    $unit = $this->camelToSnakeAdvanced($unit);
                    if (isset($unit['id'])) {
                        unset( $unit['id']);
                    }
                    $unit['pre_sellId'] = $data['preSellId'];
                    $unit['fybId'] = $data['fybId'];
                    $unit['ysProjectId'] = $data['ysProjectId'];
                    $unit['house_nb'] = $unit['housenb'];
                    $unit['building_branch'] = $old['buildingbranch'];
                    $unit['usage'] = $old['useage'];
                    unset($unit['buildingbranch']);
                    unset($unit['housenb']);
                    unset($unit['useage']);
                    
                    $unit['ys_inside_area'] = $unit['ysinsidearea'];
                    $unit['ys_expand_area'] = $unit['ysexpandarea'];
                    $unit['ys_building_area'] = $unit['ysbuildingarea'];
                    $unit['jg_inside_area'] = $unit['jginsidearea'];
                    $unit['jg_expand_area'] = $unit['jgexpandarea'];
                    $unit['jg_building_area'] = $unit['jgbuildingarea'];
                    $unit['ask_price_each_b'] = $unit['askpriceeach_b'];
                    $unit['ask_price_total_b'] = $unit['askpricetotal_b'];
                    $unit['str_contract_id'] = $unit['strcontractid'];
                    unset($unit['ysinsidearea']);
                    unset($unit['ysexpandarea']);
                    unset($unit['ysbuildingarea']);
                    unset($unit['jginsidearea']);
                    unset($unit['jgexpandarea']);
                    unset($unit['jgbuildingarea']);
                    unset($unit['askpriceeach_b']);
                    unset($unit['askpricetotal_b']);
                    unset($unit['strcontractid']);

                    $units[] = $unit;
                    BuildingUnitsHistory::create($unit);
                }
            }
            // if (!empty($units)) {
            //     $unit = new BuildingUnits();
            //     $unit->saveAll($units);
            // }
            var_dump('getHouseInfoListToPublicity获取数据成功');
            // var_dump('getHouseInfoListToPublicity获取数据成功', $units);
        } else {
            $this->error('getHouseInfoListToPublicity获取数据失败');
        }
    }

    public function camelToSnakeAdvanced($input)
    {
        if (is_array($input)) {
            $output = [];
            foreach ($input as $key => $value) {
                // 判断是否为驼峰格式的键（是否包含大写字母）
                if (preg_match('/[A-Z]/', $key)) {
                    // 转换为 snake_case
                    $newKey = preg_replace('/(?<!^)[A-Z]/', '_$0', $key);
                    $newKey = strtolower($newKey);
                } else {
                    // 如果没有大写字母，保持原键名
                    $newKey = $key;
                }

                // 递归处理值
                $output[$newKey] = $this->camelToSnakeAdvanced($value);
            }
            return $output;
        }
        return $input; // 非数组直接返回原值
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

        // if ($output['status'] != 200) {
        //     $this->error('获取数据失败12'.$output['msg']);
        // }

        return $output;
    }
}
