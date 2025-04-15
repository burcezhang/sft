<?php

namespace app\api\controller\v1;

use app\backend\model\Album;
use app\backend\model\Building;
use app\backend\model\BuildingUnits;
use app\backend\model\BuildingUnitsHistory;
use app\backend\model\Floor;
use app\backend\model\MoneyWatcher;
use app\backend\model\ProjectBaseInfo;
use app\backend\model\ProjectBuilding;
use app\backend\model\PropertyInfo;
use app\backend\model\PropertySubscription;
use app\backend\model\Region;
use app\backend\model\Seat;
use app\backend\model\SplashAds;
use app\common\model\Config;
use app\common\model\Member;
use app\common\service\UploadService;
use EasyWeChat\Factory;
use Firebase\JWT\JWT;
use fun\auth\Api;
use think\facade\Db;

/**
 * @OA\Info(title="接口文档", version="1.0.1")
 */
class Index extends Api
{
    protected $noAuth = ['splash', 'homedata', 'datacenter', 'stock', 'sales', 'daily', 'dailydetail', 'category', 'getindex', 'search',  'subscribe', 'unsubscribe', 'deeproom', 'deeproomrankings', 'prices', 'sale', 'history', 'album', 'login', 'upload', 'protocol', 'help', 'tutorial', 'explain', 'building'];

    public $appid = '';
    public $appsecret = '';
    public $key = '';
    // 开屏广告
    /**
     * @OA\Get(path="/api/v1.index/splash",
     *   tags={"开屏广告"},
     *   summary="开屏广告",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function splash()
    {
        $data = SplashAds::where('status', 1)->select();

        foreach ($data as $key => $value) {
            $data[$key]['image_url'] = config('api.domain') . $value['image_url'];
        }

        $this->success('success', $data);
    }

    /**
     * @OA\Get(path="/api/v1.index/homedata",
     *   tags={"首页数据"},
     *   summary="首页数据",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function homedata()
    {
        // 缓存首页数据
        $data = cache('homedata');

        if ($data) {
            $this->success('success', $data);
        }

        // 获取时间戳
        $now = time();
        $todayStart = strtotime(date('Y-m-d'));  // 今日零点
        $monthStart = strtotime(date('Y-m-01')); // 本月第一天零点
        $yearStart = strtotime(date('Y-01-01')); // 今年第一天零点

        // 进行一次性查询，减少数据库请求
        $result = Db::name('building_units')
            ->where('status', 1)
            ->whereIn('last_status_name', ['已签认购书', '已签合同', '已备案'])
            ->field([
                'COUNT(CASE WHEN check_time BETWEEN ' . $todayStart . ' AND ' . $now . ' THEN 1 END) as today_count',
                'COUNT(CASE WHEN check_time BETWEEN ' . $monthStart . ' AND ' . $now . ' THEN 1 END) as month_count',
                'COUNT(CASE WHEN check_time BETWEEN ' . $yearStart . ' AND ' . $now . ' THEN 1 END) as year_count',
                'IFNULL(AVG(CASE WHEN check_time BETWEEN ' . $todayStart . ' AND ' . $now . ' THEN ask_price_each_b END), 0) as today_avg_price',
                'IFNULL(AVG(CASE WHEN check_time BETWEEN ' . $monthStart . ' AND ' . $now . ' THEN ask_price_each_b END), 0) as month_avg_price',
                'IFNULL(AVG(CASE WHEN check_time BETWEEN ' . $yearStart . ' AND ' . $now . ' THEN ask_price_each_b END), 0) as year_avg_price',
                'IFNULL(AVG(ask_price_total_b), 0) as avg_total_price'
            ])
            ->find();

        // 处理数据
        $data = [
            [
                'name' => '成交套数',
                'value' => intval($result['today_count']),
                'unit' => '套',
                'date' => date('d'),
            ],
            [
                'name' => '成交均价',
                'value' => round($result['today_avg_price'] ?? 0, 2),
                'unit' => '元/m²',
                'date' => date('d'),
            ],
            [
                'name' => '套均总价',
                'value' => intval($result['today_count']) > 0 ? round($result['avg_total_price'] / 10000, 2) : 0,
                'unit' => '万',
                'date' => date('d'),
            ],
            [
                'name' => '本月成交套数',
                'value' => intval($result['month_count']),
                'unit' => '套',
                'date' => 0,
            ],
            [
                'name' => '本月成交均价',
                'value' => intval($result['month_count']) > 0 ? round($result['month_avg_price'] ?? 0, 2) : 0,
                'unit' => '元/m²',
                'date' => 0,
            ],
            [
                'name' => '今年成交套数',
                'value' => intval($result['year_count']),
                'unit' => '套',
                'date' => 0,
            ],
            [
                'name' => '今年成交均价',
                'value' => round($result['year_avg_price'] ?? 0, 2),
                'unit' => '元/m²',
                'date' => 0,
            ],
        ];

        // 计算明天 00:00 的时间戳
        $tomorrowStart = strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')));
        // 计算缓存存活时间（秒）
        $cacheTTL = $tomorrowStart - time();

        // 设置缓存，过期时间为到明天 00:00
        cache('homedata', $data, $cacheTTL);
        $this->success('success', $data);
    }


    // 数据中心
    /**
     * @OA\Get(path="/api/v1.index/datacenter",
     *   tags={"数据中心"},
     *   summary="数据中心",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function datacenter()
    {
        $data = [
            [
                'name' => '最新成交',
                'data' => [
                    [
                        'title' => '南山一期',
                        'price' => 100000,
                        'img' => 'http://www.example.com/img1.jpg',
                    ],
                    [
                        'title' => '南山二期',
                        'price' => 120000,
                        'img' => 'http://www.example.com/img2.jpg',
                    ],
                ],
            ],
            [
                'name' => '最新成交',
                'data' => [
                    [
                        'title' => '南山一期',
                        'price' => 100000,
                        'img' => 'http://www.example.com/img1.jpg',
                    ],
                    [
                        'title' => '南山二期',
                        'price' => 120000,
                        'img' => 'http://www.example.com/img2.jpg',
                    ],
                ],
            ],
        ];
        $this->success('success',  $data);
    }

    // 板块库存
    /**
     * @OA\Get(path="/api/v1.index/stock",
     *   tags={"板块库存"},
     *   summary="板块库存",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function stock()
    {
        $data = [
            [
                'name' => '南山',
                'data' => [
                    [
                        'title' => '南山一期',
                        'price' => 100000,
                        'img' => 'http://www.example.com/img1.jpg',
                    ],
                    [
                        'title' => '南山二期',
                        'price' => 120000,
                        'img' => 'http://www.example.com/img2.jpg',
                    ],
                ],
            ],
            [
                'data' => [
                    [
                        'price' => 100000,
                        'img' => 'http://www.example.com/img1.jpg',
                    ],
                    [
                        'price' => 120000,
                        'img' => 'http://www.example.com/img2.jpg',
                    ],
                ],
            ],
        ];
        $this->success('success', '', $data);
    }

    // 销售榜单
    /**
     * @OA\Get(path="/api/v1.index/sales",
     *   tags={"销售榜单"},
     *   summary="销售榜单",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function sales()
    {
        $data = [
            [
                'name' => '南山',
                'data' => [
                    [
                        'title' => '南山一期',
                        'price' => 100000,
                        'img' => 'http://www.example.com/img1.jpg',
                    ],
                    [
                        'title' => '南山二期',
                        'price' => 120000,
                        'img' => 'http://www.example.com/img2.jpg',
                    ],
                ],
            ],
            [
                'data' => [
                    [
                        'price' => 100000,
                        'img' => 'http://www.example.com/img1.jpg',
                    ],
                    [
                        'price' => 120000,
                        'img' => 'http://www.example.com/img2.jpg',
                    ],
                ],
            ],
        ];
        $this->success('success', '', $data);
    }

    // 深房日报
    /**
     * @OA\Get(path="/api/v1.index/daily",
     *   tags={"深房日报"},
     *   summary="深房日报",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function daily()
    {
        $data = [
            [
                'title' => '南山一期',
                'img' => 'http://www.example.com/img1.jpg',
                'content' => '南山一期深房日报',
            ],
            [
                'title' => '南山二期',
                'img' => 'http://www.example.com/img2.jpg',
                'content' => '南山二期深房日报',
            ],
        ];
        $this->success('success', '', $data);
    }

    // 日报详情
    /**
     * @OA\Get(path="/api/v1.index/dailydetail",
     *   tags={"日报详情"},
     *   summary="日报详情",
     *   @OA\Parameter(name="id", in="query", description="日报id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function dailydetail()
    {
        $id = $this->request->param('id');
        $data = [
            'title' => '南山一期',
            'img' => 'http://www.example.com/img1.jpg',
            'content' => '南山一期深房日报',
        ];
        $this->success('success', '', $data);
    }


    /**
     * @OA\Get(path="/api/v1.index/category",
     *   tags={"最新取证楼盘分类"},
     *   summary="最新取证楼盘分类",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function category()
    {
        $data = Region::where('status', 1)->field('id,label,value,key,acive')->select();

        if ($data) {

            foreach ($data as $k => &$v) {
                $v['acive'] = $v['acive'] == 1 ? true : false;
            }
            unset($v);
        }

        $this->success('success', $data);
    }

    /**
     * @OA\Get(path="/api/v1.index/getindex",
     *   tags={"最新取证楼盘"},
     *   summary="最新取证楼盘",
     *   @OA\Parameter(name="label", in="query", description="分类label", @OA\Schema(type="varchar", default="0")),
     *   @OA\Parameter(name="page", in="query", description="页码", @OA\Schema(type="int", default="1")),
     *   @OA\Parameter(name="limit", in="query", description="每页数量", @OA\Schema(type="int", default="10")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function getindex()
    {
        $label = $this->request->param('label');
        $page = $this->request->param('page', 1);
        $limit = $this->request->param('limit', 10);
        $keywords = $this->request->param('keywords');

        $model = PropertyInfo::where('p.status', 1);

        if (!empty($label) && $label !== '全部') {
            $model->where('p.zone', $label);
        }

        if (!empty($keywords)) {
            $model->where('p.project', 'like', "%$keywords%");
        }

        // **查询每个 project 的最新 sype_id**
        $subQuery = PropertyInfo::where('status', 1)
            ->field('MAX(sype_id) as max_sype_id, project')
            ->group('project')
            ->buildSql();  // 生成子查询 SQL

        // **使用 JOIN 关联查询最新数据**
        $data = $model->alias('p')
            ->join("({$subQuery}) latest", 'p.project = latest.project AND p.sype_id = latest.max_sype_id')
            ->order('p.sype_id', 'desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        // 处理数据
        $this->success('success',  $this->getIndexData($data));
    }

    // 处理首页楼盘数据
    // public function getIndexData($data)
    // {
    //     if (empty($data)) {
    //         return [];
    //     }

    //     // 批量查询 pre_sellId 相关数据
    //     $sypeIds = array_column($data, 'sype_id');

    //     // 获取 ProjectBaseInfo 数据
    //     $projectBaseInfos = ProjectBaseInfo::whereIn('pre_sellId', $sypeIds)
    //         ->column('ys_suites', 'pre_sellId'); // 以 pre_sellId 作为 key

    //     // 获取 BuildingUnits 面积和价格
    //     $buildingUnits = BuildingUnits::whereIn('pre_sellId', $sypeIds)
    //         ->group('pre_sellId')
    //         ->field('pre_sellId, MIN(ys_building_area) AS min_area, MAX(ys_building_area) AS max_area, MIN(ask_price_each_b) AS min_price, MAX(ask_price_each_b) AS max_price')
    //         ->select()
    //         ->toArray();
    //     $buildingUnitsMap = array_column($buildingUnits, null, 'pre_sellId'); // 以 pre_sellId 作为 key

    //     // 获取历史价格
    //     $lastPrices = BuildingUnitsHistory::whereIn('pre_sellId', $sypeIds)
    //         ->where('status', 1)
    //         ->order('create_time', 'desc')
    //         ->column('ask_price_each_b', 'pre_sellId');

    //     $debug = $this->request->param('debug', 0);

    //     // 获取历史记录条数
    //     $historyCounts = PropertyInfo::whereIn('project', array_column($data, 'project'))
    //         ->where('status', 1)
    //         ->group('project')
    //         ->column('count(*) as count', 'project');

    //     // 获取 Building 数据
    //     $buildingSeats = Building::whereIn('preSellId', $sypeIds)
    //         ->where('status', 1)
    //         ->select()
    //         ->toArray();
    //     $buildingSeatsMap = [];
    //     foreach ($buildingSeats as $seat) {
    //         $buildingSeatsMap[$seat['preSellId']][] = $seat['label'];
    //     }

    //     // 遍历数据进行处理
    //     foreach ($data as &$value) {
    //         $sypeId = $value['sype_id'];

    //         // 取证套数
    //         $value['count'] = !empty($projectBaseInfos[$sypeId]) ? $projectBaseInfos[$sypeId] : 0;

    //         // 获取面积、价格
    //         $unitData = $buildingUnitsMap[$sypeId] ?? null;
    //         $value['area'] = ($unitData) ? round($unitData['min_area'], 0) . ' - ' . round($unitData['max_area'], 0) : '未知';
    //         $value['lowest_price'] = $unitData['min_price'] ?? 0;
    //         $value['highest_price'] = $unitData['max_price'] ?? 0;

    //         // 计算总价
    //         $value['total_price'] = round(($unitData['min_area'] * $value['lowest_price']) / 10000, 0) . ' - ' .
    //             round(($unitData['max_area'] * $value['highest_price']) / 10000, 0);

    //         // 最后一次取证价格
    //         $last_build_units_history = $lastPrices[$sypeId] ?? null;
    //         $current_price = $unitData['max_price'] ?? null;
    //         if ($historyCounts > 1) {
    //             // if ($debug == 1) {
    //             $subQuery = PropertyInfo::where('status', 1)
    //                 ->where('project', $value['project'])
    //                 ->field('project, sype_id')
    //                 ->order('sype_id', 'desc')  // 获取最新的批次
    //                 ->limit(2)
    //                 ->column('sype_id');
    //             // echo '<pre>';
    //             // 倒序
    //             $avg_price = [];
    //             for ($i = count($subQuery) - 1; $i >= 0; $i--) {
    //                 $avg_price[] =  BuildingUnits::where('pre_sellId', $subQuery[$i])->avg('ask_price_each_b');
    //             }
    //             $increase = round($avg_price[0] - $avg_price[1], 2);

    //             //         var_dump($increase,$avg_price, $subQuery);exit;
    //             // }
    //             $value['increase'] = $increase;
    //         } else {
    //             $value['increase'] = '-';
    //         }

    //         // 预售时间
    //         $value['pre_time'] = str_replace('获批预售', '', $value['ys_date_str']);

    //         // 历史记录数量
    //         $value['history_count'] = $historyCounts[$value['project']] ?? 0;

    //         // 销售范围
    //         $value['sale_range'] = isset($buildingSeatsMap[$sypeId]) ? implode('、', $buildingSeatsMap[$sypeId]) : '';
    //     }
    //     unset($value);

    //     return $data;
    // }
    public function getIndexData($data)
    {
        if (empty($data)) {
            return [];
        }

        // 提取所有 `sype_id`，避免重复查询
        $sypeIds = array_column($data, 'sype_id');
        $projects = array_column($data, 'project');

        // **批量查询**
        // 1. 获取 ProjectBaseInfo 数据
        $projectBaseInfos = ProjectBaseInfo::whereIn('pre_sellId', $sypeIds)
            ->column('ys_suites', 'pre_sellId');

        // 2. 获取 BuildingUnits 面积和价格（减少 `foreach` 内重复查询）
        $buildingUnitsMap = BuildingUnits::whereIn('pre_sellId', $sypeIds)
            ->group('pre_sellId')
            ->column('MIN(ys_building_area) AS min_area, MAX(ys_building_area) AS max_area, MIN(ask_price_each_b) AS min_price, MAX(ask_price_each_b) AS max_price', 'pre_sellId');

        // 4. 获取历史记录数量（减少 `foreach` 内重复计算）
        $historyCounts = PropertyInfo::whereIn('project', $projects)
            ->where('status', 1)
            ->group('project')
            ->column('count(*) as count', 'project');

        // 5. 获取 Building 数据
        $buildingSeats = Building::whereIn('preSellId', $sypeIds)
            ->where('status', 1)
            ->column('preSellId, label');

        // 处理 Building 数据
        $buildingSeatsMap = [];
        foreach ($buildingSeats as $seat) {
            $buildingSeatsMap[$seat['preSellId']][] = $seat['label'];
        }

        // 6. 获取所有项目的最新两个批次（减少 `foreach` 内查询）
        $projectLatestBatches = [];
        $projectBatches = PropertyInfo::whereIn('project', $projects)
            ->where('status', 1)
            ->order('sype_id', 'desc')
            ->column('project, sype_id');

        foreach ($projectBatches as $row) {
            $projectLatestBatches[$row['project']][] = $row['sype_id'];
        }

        // **遍历数据进行处理**
        foreach ($data as &$value) {
            $sypeId = $value['sype_id'];

            // 取证套数
            $value['count'] = $projectBaseInfos[$sypeId] ?? 0;

            // 获取面积、价格
            $unitData = $buildingUnitsMap[$sypeId] ?? null;
            $value['area'] = ($unitData) ? round($unitData['min_area'], 0) . ' - ' . round($unitData['max_area'], 0) : '未知';
            $value['lowest_price'] = $unitData['min_price'] ?? 0;
            $value['highest_price'] = $unitData['max_price'] ?? 0;

            // 计算总价
            $value['total_price'] = ($unitData)
                ? round(($unitData['min_area'] * $value['lowest_price']) / 10000, 0) . ' - ' .
                round(($unitData['max_area'] * $value['highest_price']) / 10000, 0)
                : '未知';

            // **优化 increase 计算**
            $historyCount = $historyCounts[$value['project']] ?? 0;
            if ($historyCount > 1) {
                $latestBatches = $projectLatestBatches[$value['project']] ?? [];
                if (count($latestBatches) >= 2) {
                    $latestPrices = BuildingUnits::whereIn('pre_sellId', [$latestBatches[0], $latestBatches[1]])
                        ->group('pre_sellId')
                        ->column('AVG(ask_price_each_b) as avg_price', 'pre_sellId');

                    if (isset($latestPrices[$latestBatches[0]]) && isset($latestPrices[$latestBatches[1]])) {
                        $value['increase'] = round($latestPrices[$latestBatches[1]] - $latestPrices[$latestBatches[0]], 2);
                    } else {
                        $value['increase'] = '-';
                    }
                } else {
                    $value['increase'] = '-';
                }
            } else {
                $value['increase'] = '-';
            }

            // 预售时间
            $value['pre_time'] = str_replace('获批预售', '', $value['ys_date_str']);

            // 历史记录数量
            $value['history_count'] = $historyCount;

            // 销售范围
            $value['sale_range'] = isset($buildingSeatsMap[$sypeId]) ? implode('、', $buildingSeatsMap[$sypeId]) : '';
        }
        unset($value);

        return $data;
    }





    // 楼盘搜索
    /**
     * @OA\Get(path="/api/v1.index/search",
     *   tags={"楼盘搜索"},
     *   summary="楼盘搜索",
     *   @OA\Parameter(name="keywords", in="query", description="搜索关键词", @OA\Schema(type="string", default="")),
     *   @OA\Parameter(name="page", in="query", description="页码", @OA\Schema(type="int", default="1")),
     *   @OA\Parameter(name="limit", in="query", description="每页数量", @OA\Schema(type="int", default="10")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function search()
    {
        $keywords = $this->request->param('keywords');
        $page = $this->request->param('page', 1);
        $limit = $this->request->param('limit', 10);
        if (!empty($keywords)) {
            $data = PropertyInfo::where('status', 1)->where('project', 'like', "%$keywords%")->page($page, $limit)->select();
            $this->success('success',  $this->getIndexData($data));
        } else {
            $data = [];
            $this->success('success', $data);
        }
    }

    /**
     * @OA\Post(path="/api/v1.index/details",
     *   tags={"楼盘详情"},
     *   summary="楼盘详情",
     *   @OA\Parameter(name="sype_id", in="query", description="楼盘sype_id", @OA\Schema(type="int", default="0")),
     *   @OA\Parameter(name="usage", in="query", description="usage（0全部1住宅2商铺）", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function details()
    {

        if (!$this->member_id) {
            $this->error('请先登录');
        }

        $id = $this->request->param('sype_id');
        $usage = $this->request->param('usage');


        $propertyInfo = PropertyInfo::where('status', 1)->where('sype_id', $id)->find();
        // var_dump($propertyInfo);exit;
        $propertyInfos = $this->getIndexData([$propertyInfo]);
        $propertyInfo = $propertyInfos[0];

        $projectBaseInfo = ProjectBaseInfo::where('pre_sellId', $propertyInfo['sype_id'])->find();
        $projectBuilding = ProjectBuilding::where('pre_sellId', $propertyInfo['sype_id'])->find();
        $moneyWatcher = MoneyWatcher::where('pre_sellId', $propertyInfo['sype_id'])->find();

        $ysSuites = $projectBaseInfo['ys_suites'];

        // $model = BuildingUnits::where('pre_sellId', $propertyInfo['sype_id']);

        // if($usage == 1){
        //     $model->where('usage', '住宅');
        // }else if($usage == 2){
        //     $model->where('usage', '商铺');
        // }
        $where = [];
        if ($usage == 1) {
            $where[] = ['usage', '=', '住宅'];
        } else if ($usage == 2) {
            $where[] = ['usage', '=', '商铺'];
        }

        // 期房待售
        $house_ds = BuildingUnits::where('pre_sellId', $propertyInfo['sype_id'])->where('last_status_name', '期房待售')->where('status', 1)->where($where)->count();
        $house_yqrgs = BuildingUnits::where('pre_sellId', $propertyInfo['sype_id'])->where('last_status_name', '已签认购书')->where('status', 1)->where($where)->count();
        $house_yqht = BuildingUnits::where('pre_sellId', $propertyInfo['sype_id'])->where('last_status_name', '已签合同')->where('status', 1)->where($where)->count();
        $house_yqht += BuildingUnits::where('pre_sellId', $propertyInfo['sype_id'])->where('last_status_name', '已备案')->where('status', 1)->where($where)->count();
        // var_dump($house_ds,$house_yqrgs,$house_yqht);exit;

        $deal_data = [];
        $total = 0;
        for ($i = 0; $i < 15; $i++) {
            $day = BuildingUnits::where('pre_sellId', $propertyInfo['sype_id'])
                ->whereIn('last_status_name', ['已签认购书', '已签合同', '已备案'])
                ->where($where)
                ->where('status', 1)
                ->where('create_time', '=', strtotime("-$i day"))->count();
            $total += $day;
            $deal_data[] = [
                'name' => date('d', strtotime("-" . $i . " day")),
                'day' => $day,
                'total' => $total,
            ];
        }
        $is_store_show = BuildingUnits::where('pre_sellId', $propertyInfo['sype_id'])->where('usage', '商铺')->where('status', 1)->count() > 0 ? 1 : 0;
        $data = [
            'is_store_show' => $is_store_show,
            'propertyInfo' => $propertyInfo,
            'projectBaseInfo' => $projectBaseInfo,
            'projectBuilding' => $projectBuilding,
            'moneyWatcher' => $moneyWatcher,
            // 销售进度
            'progress' => [
                [
                    'name' => '取证总套数',
                    'value' => $ysSuites,
                ],
                [
                    'name' => '总已售',
                    'value' => $house_yqrgs + $house_yqht,
                ],
                [
                    'name' => '剩余库存',
                    'value' => $house_ds,
                ],
                [
                    'name' => '楼盘去化',
                    'value' => round(($house_yqrgs + $house_yqht) / $ysSuites * 100, 2),
                ],
            ],
            // 房源状态
            'house_status' => [
                [
                    'name' => '期房待售',
                    'value' => $house_ds,
                    // 占比
                    'percent' => round($house_ds / $ysSuites * 100, 2),
                ],
                [
                    'name' => '已签认购书',
                    'value' => $house_yqrgs,
                    'percent' => round($house_yqrgs / $ysSuites * 100, 2),
                ],
                [
                    'name' => '已网签备案',
                    'value' => $house_yqht,
                    'percent' => round($house_yqht / $ysSuites * 100, 2),
                ],
                [
                    'name' => '异常',
                    'value' => ($ysSuites - $house_ds - $house_yqrgs - $house_yqht),
                    'percent' => round(($ysSuites - $house_ds - $house_yqrgs - $house_yqht) / $ysSuites * 100, 2),
                ]
            ],
            // 近15日成交
            'deal_data' => $deal_data
        ];

        // 是否订阅
        $data['is_subscribe'] = PropertySubscription::where('property_id', $id)->where('user_id', $this->member_id)->count() > 0 ? true : false;

        $this->success('success',  $data);
    }

    // 楼盘订阅
    /**
     * @OA\Post(path="/api/v1.index/subscribe",
     *   tags={"楼盘订阅"},
     *   summary="楼盘订阅",
     *   @OA\Parameter(name="id", in="query", description="楼盘id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function subscribe()
    {
        $id = $this->request->param('id');

        if (!$this->member_id) {
            $this->error('请先登录');
        }
        $propertyInfo = PropertyInfo::where('status', 1)->where('sype_id', $id)->find();
        if (!$propertyInfo) {
            $this->error('楼盘不存在');
        }
        $propertySubscription = PropertySubscription::where('property_id', $id)->where('user_id', $this->member_id)->find();
        if ($propertySubscription) {
            $this->error('您已订阅该楼盘');
        }
        $propertySubscription = new PropertySubscription();
        $propertySubscription->property_id = $id;
        $propertySubscription->user_id = $this->member_id;
        // subscription_time
        $propertySubscription->subscription_time = time();
        // status
        $propertySubscription->status = 1;
        $res = $propertySubscription->save();


        $this->success('success', $res);
    }

    // 取消楼盘订阅
    /**
     * @OA\Post(path="/api/v1.index/unsubscribe",
     *   tags={"取消楼盘订阅"},
     *   summary="取消楼盘订阅",
     *   @OA\Parameter(name="id", in="query", description="楼盘id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function unsubscribe()
    {
        $id = $this->request->param('id');

        if (!$this->member_id) {
            $this->error('请先登录');
        }
        $propertyInfo = PropertyInfo::where('status', 1)->where('sype_id', $id)->find();
        if (!$propertyInfo) {
            $this->error('楼盘不存在');
        }
        $propertySubscription = PropertySubscription::where('property_id', $id)->where('user_id', $this->member_id)->find();
        if (!$propertySubscription) {
            $this->error('您未订阅该楼盘');
        }
        // 删除记录
        $res = PropertySubscription::where('property_id', $id)->where('user_id', $this->member_id)->delete();

        $this->success('success', $res);
    }

    // 深房指数
    /**
     * @OA\Get(path="/api/v1.index/deeproom",
     *   tags={"深房指数"},
     *   summary="深房指数",
     *   @OA\Parameter(name="id", in="query", description="楼盘id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function deeproom()
    {
        // site_shenfang_index
        $data = [
            'title' => '深房指数',
            'content' => Config::where('code', 'site_shenfang_index')->value('value'),
        ];
        $this->success('success', $data);
    }

    // 深房指数
    /**
     * @OA\Get(path="/api/v1.index/deeproomrankings",
     *   tags={"深房指数排行榜"},
     *   summary="深房指数排行榜",
     *   @OA\Parameter(name="area", in="query", description="位置", @OA\Schema(type="string", default="")),
     *   @OA\Parameter(name="ranking", in="query", description="1：指数榜，2：销量，3：销售额，4：库存榜", @OA\Schema(type="int", default="2")),
     *   @OA\Parameter(name="type", in="query", description="（'week', 'month', 'quarter', 'year'）", @OA\Schema(type="string", default="month")),
     *   @OA\Parameter(name="day", in="query", description="时间(如：'2024-06' 或 '2024-03' 等）", @OA\Schema(type="string", default="")),
     *   @OA\Parameter(name="page", in="query", description="页码", @OA\Schema(type="int", default="1")),
     *   @OA\Parameter(name="limit", in="query", description="每页数量", @OA\Schema(type="int", default="10")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function deeproomrankings()
    {
        $area = $this->request->param('area');
        $date = $this->request->param('day'); // 从前端传入的年月（如：'2024-06' 或 '2024-03' 等）
        $time_type = $this->request->param('type'); // 从前端传入的时间类型（'week', 'month', 'quarter', 'year'）

        // 动态构建查询条件
        $salesQuery = Db::table('hi_building_units_history')
            ->alias('buh')
            ->join('hi_property_info pi', 'buh.pre_sellId = pi.sype_id')  // 连接小区表
            ->whereIn('buh.last_status_name', ['已签认购书', '已签合同', '已备案'])  // 销售状态
        ;  // 按地区筛选

        if ($area) {
            $salesQuery->where('pi.zone', '=', $area);
        }

        // 处理时间筛选
        if ($time_type == 'year') {
            // 按年筛选
            if ($date) {
                $salesQuery = $salesQuery->whereYear('buh.create_time', $date);
            }
        } elseif ($time_type == 'month') {
            // 按月筛选
            if ($date) {
                $salesQuery = $salesQuery->whereMonth('buh.create_time', $date);  // 提取月份
            }
        } elseif ($time_type == 'quarter') {
            // 按季度筛选
            if ($date) {
                $quarterStartMonth = (substr($date, 5, 2) == '01' || substr($date, 5, 2) == '02' || substr($date, 5, 2) == '03')
                    ? 1
                    : ((substr($date, 5, 2) == '04' || substr($date, 5, 2) == '05' || substr($date, 5, 2) == '06')
                        ? 4
                        : ((substr($date, 5, 2) == '07' || substr($date, 5, 2) == '08' || substr($date, 5, 2) == '09')
                            ? 7
                            : 10));

                $quarterEndMonth = $quarterStartMonth + 2;
                // var_dump($quarterStartMonth, $quarterEndMonth);exit;
                // $salesQuery = $salesQuery->whereBetweenMonth('buh.create_time', $quarterStartMonth, $quarterEndMonth);

                $quarterStartDate = $date . '-' . str_pad($quarterStartMonth, 2, '0', STR_PAD_LEFT) . '-01';  // Start of the quarter
                $quarterEndDate = date('Y-m-t', strtotime($quarterStartDate . ' +2 months'));  // End of the quarter

                // Filter the results by the quarter's start and end dates
                $salesQuery = $salesQuery->whereBetween('buh.create_time', [$quarterStartDate, $quarterEndDate]);
            }
        } elseif ($time_type == 'week') {
            // 按周筛选
            if ($date) {
                // 检查日期格式是否正确
                if (preg_match('/^(\d{4})-W(\d{2})$/', $date, $matches)) {
                    // 提取年和周
                    $year = $matches[1];
                    $week = $matches[2];
                    // 根据年和周生成对应的日期范围（例如：生成开始和结束日期）
                    $startDate = date('Y-m-d', strtotime($year . 'W' . $week));
                    $endDate = date('Y-m-d', strtotime($startDate . ' +6 days'));

                    // 按周筛选
                    $salesQuery = $salesQuery->whereBetween('buh.create_time', [$startDate, $endDate]);
                } else {
                    // 如果日期格式不符合预期，可以返回错误或者日志
                    return $this->error('Invalid week format');
                }
            }
        }

        // 获取销量排名前四的结果
        $salesRanking = $salesQuery
            ->field('pi.*, COUNT(buh.id) AS sales_count')  // 选择地区和销售量
            ->group('pi.sype_id')  // 按地区分组
            ->order('sales_count', 'desc')  // 按销售量降序排列
            ->limit(4)  // 只取前四名
            ->select();

        $this->success('success', $salesRanking);
    }


    /**
     * @OA\Get(path="/api/v1.index/explain",
     *   tags={"取证均价/取证总价/建面区间"},
     *   summary="取证均价/取证总价/建面区间",
     *   @OA\Parameter(name="type", in="query", description="average_price/total_price/area_section", @OA\Schema(type="string", default="")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function explain()
    {
        $type = $this->request->param('type', 'average_price');
        $data = [
            'title' => $type == 'average_price' ? '取证均价' : ($type == 'total_price' ? '取证总价' : '建面区间'),
            'content' => Config::where('code', $type)->value('value'),
        ];
        $this->success('success', $data);
    }

    // building
    /**
     * @OA\Get(path="/api/v1.index/building",
     *   tags={"楼盘信息"},
     *   summary="楼盘信息",
     *   @OA\Parameter(name="sype_id", in="query", description="楼盘sype_id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function building()
    {
        $sype_id = $this->request->param('sype_id');
        $building = Building::where('preSellId', $sype_id)->where('status', 1)->select();
        $this->success('success', $building);
    }

    // 备案价表
    /**
     * @OA\Get(path="/api/v1.index/prices",
     *   tags={"备案价表"},
     *   summary="备案价表",
     *   @OA\Parameter(name="sype_id", in="query", description="楼盘sype_id", @OA\Schema(type="int", default="0")),
     *   @OA\Parameter(name="fyb_id", in="query", description="楼栋fyb_id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function prices()
    {
        $sype_id = $this->request->param('sype_id');
        $fybId = $this->request->param('fyb_id');

        $list = BuildingUnits::where('pre_sellId', $sype_id)->where('fybId', $fybId)->where('status', 1)->order('house_nb asc')->select();


        $seat = Seat::where('preSellId', $sype_id)->where('fybId', $fybId)->where('status', 1)->select();
        // $building = Building::where('preSellId', $sype_id)->where('status', 1)->select();

        $floor = Floor::where([
            ['preSellId', '=', $sype_id],
            ['fybId', '=', $fybId],
            ['status', '=', 1],
        ])->select();
        // var_dump('preSellId', $list,'fybId', $fybId,$floor,Floor::getLastSql());exit;
        $loucheng = [];
        foreach ($floor as $key => $value) {
            $floor_list = [];
            foreach ($list as $k => $v) {
                if ($v['floor'] == $value['title']) {
                    // ask_price_total_b / 10000
                    $v['ask_price_total_b'] = round($v['ask_price_total_b'] / 10000, 2) . '万';
                    $floor_list[] = $v;
                }
            }
            $loucheng[] = [
                'floor' => $value['title'],
                'list' => $floor_list
            ];
        }

        // if(!empty($loucheng)){
        //     foreach ($loucheng as $key => &$value) {
        //         // list 
        //     }
        // }

        $data = [];
        $this->success('success', [
            // 'building' => $building,
            'seat' => $seat,
            'loucheng' => $loucheng,
            'floor' => $floor,
        ]);
    }

    // 房源分析
    /**
     * @OA\Get(path="/api/v1.index/analysis",
     *   tags={"房源分析"},
     *   summary="房源分析",
     *   @OA\Parameter(name="id", in="query", description="楼盘id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function analysis()
    {
        $id = $this->request->param('id');
        // building_units

        $sql = "
            SELECT
                ys_building_area,
                `usage`,
                COUNT(*) AS total_count,
                SUM(CASE WHEN last_status_name = '期房待售' THEN 1 ELSE 0 END) AS pending_count,
                MIN(ask_price_each_b) AS min_price,
                MAX(ask_price_each_b) AS max_price,
                ROUND(SUM(ys_building_area) / (SELECT SUM(ys_building_area) FROM hi_building_units WHERE pre_sellId = :pre_sellId) * 100, 2) AS area_percentage
            FROM
            hi_building_units
            WHERE
            pre_sellId = :pre_sellId2
            and `usage`='住宅'
            GROUP BY
                ys_building_area
            ORDER BY
                ys_building_area DESC;
        ";

        $result = Db::query($sql, ['pre_sellId' => $id, 'pre_sellId2' => $id]);

        $usagetotal = [
            'total_count' => 0,
            'pending_count' => 0,
            'min_price' => 0,
            'min_total_price' => 0,
            'max_price' => 0,
            'max_total_price' => 0,
        ];
        foreach ($result as $key => &$value) {
            $usagetotal['total_count'] += $value['total_count'];
            $usagetotal['pending_count'] += $value['pending_count'];
            // min_total
            $value['min_total_price'] = round((($value['min_price'] * $value['ys_building_area']) / 10000), 2);
            // max_total_price
            $value['max_total_price'] = round((($value['max_price'] * $value['ys_building_area']) / 10000), 2);

            $usagetotal['min_price'] = $usagetotal['min_price'] == 0 || $value['min_price'] < $usagetotal['min_price'] ? $value['min_price'] : $usagetotal['min_price'];
            $usagetotal['max_price'] = $value['max_price'] > $usagetotal['max_price'] ? $value['max_price'] : $usagetotal['max_price'];

            $usagetotal['min_total_price'] = $usagetotal['min_price'] == 0 || $value['min_total_price'] < $usagetotal['min_total_price'] ? $value['min_total_price'] : $usagetotal['min_total_price'];
            $usagetotal['max_total_price'] = $value['max_total_price'] > $usagetotal['max_total_price'] ? $value['max_total_price'] : $usagetotal['max_total_price'];
        }

        unset($value);


        $sql = "
            SELECT
                ys_building_area,
                `usage`,
                COUNT(*) AS total_count,
                SUM(CASE WHEN last_status_name = '期房待售' THEN 1 ELSE 0 END) AS pending_count,
                MIN(ask_price_each_b) AS min_price,
                MAX(ask_price_each_b) AS max_price,
                ROUND(SUM(ys_building_area) / (SELECT SUM(ys_building_area) FROM hi_building_units WHERE pre_sellId = :pre_sellId) * 100, 2) AS area_percentage
            FROM
            hi_building_units
            WHERE
            pre_sellId = :pre_sellId2
            and `usage`='商铺'
            GROUP BY
                ys_building_area
            ORDER BY
                ys_building_area DESC;
        ";

        $result2 = Db::query($sql, ['pre_sellId' => $id, 'pre_sellId2' => $id]);
        $usagetotal2 = [
            'total_count' => 0,
            'pending_count' => 0,
            'min_price' => 0,
            'min_total_price' => 0,
            'max_price' => 0,
            'max_total_price' => 0,
        ];
        foreach ($result2 as $key => &$value) {
            $usagetotal2['total_count'] += $value['total_count'];
            $usagetotal2['pending_count'] += $value['pending_count'];
            // min_total
            $value['min_total_price'] = round((($value['min_price'] * $value['ys_building_area']) / 10000), 2);
            // max_total_price
            $value['max_total_price'] = round((($value['max_price'] * $value['ys_building_area']) / 10000), 2);

            $usagetotal2['min_price'] = $usagetotal2['min_price'] == 0 || $value['min_price'] < $usagetotal2['min_price'] ? $value['min_price'] : $usagetotal2['min_price'];
            $usagetotal2['max_price'] = $value['max_price'] > $usagetotal2['max_price'] ? $value['max_price'] : $usagetotal2['max_price'];

            $usagetotal2['min_total_price'] = $usagetotal2['min_total_price'] == 0 || $value['min_total_price'] < $usagetotal2['min_total_price'] ? $value['min_total_price'] : $usagetotal2['min_total_price'];
            $usagetotal2['max_total_price'] = $value['max_total_price'] > $usagetotal2['max_total_price'] ? $value['max_total_price'] : $usagetotal2['max_total_price'];
        }

        unset($value);
        //usage
        // $result['is_subscribe'] = ;

        $this->success('success', [
            'is_subscribe' => PropertySubscription::where('property_id', $id)->where('user_id', $this->member_id)->count() > 0 ? true : false,
            'usage1' => $result,
            'usagetotal' => $usagetotal,

            'usage2' => $result2,
            'usagetotal2' => $usagetotal2,
        ]);
    }
    // 房源分析
    /**
     * @OA\Get(path="/api/v1.index/analysis_details",
     *   tags={"房源详情分析"},
     *   summary="房源详情分析",
     *   @OA\Parameter(name="id", in="query", description="楼盘id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function analysis_details()
    {
        $id = $this->request->param('id');
        // building_units

        $building_units = BuildingUnits::where('id', $id)->where('status', 1)->find();
        // var_dump($building_units);exit;
        $building_units['ask_price_total_b'] = $building_units['ask_price_total_b'] / 10000;
        // sf3_ask_price_each_b / 10000
        $building_units['sf3_ask_price_each_b'] = $building_units['sf3_ask_price_each_b'] / 10000;
        // sf4_ask_price_each_b
        $building_units['sf4_ask_price_each_b'] = $building_units['sf4_ask_price_each_b'] / 10000;

        $propertyInfo = PropertyInfo::where('status', 1)->where('sype_id', $building_units['pre_sellId'])->find();
        // var_dump($propertyInfo);exit;
        $propertyInfos = $this->getIndexData([$propertyInfo]);
        $propertyInfo = $propertyInfos[0];

        $building_units['propertyInfo'] = $propertyInfo;
        $building_units['is_subscribe'] = PropertySubscription::where('property_id', $id)->where('user_id', $this->member_id)->count() > 0 ? true : false;
        $this->success('success', $building_units);
    }

    // 销控图
    /**
     * @OA\Get(path="/api/v1.index/sale",
     *   tags={"销控图"},
     *   summary="销控图",
     *   @OA\Parameter(name="sype_id", in="query", description="楼盘sype_id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function sale()
    {
        $sype_id = $this->request->param('sype_id');

        $list = BuildingUnits::where('pre_sellId', $sype_id)->where('status', 1)->order('house_nb asc')->select();


        $seat = Seat::where('preSellId', $sype_id)->where('status', 1)->select();
        $building = Building::where('preSellId', $sype_id)->where('status', 1)->select();

        $floor = Floor::where('preSellId', $sype_id)->where('status', 1)->select();
        $loucheng = [];
        foreach ($floor as $key => $value) {
            $floor_list = [];
            foreach ($list as $k => $v) {
                if ($v['floor'] == $value['title']) {
                    $floor_list[] = $v;
                }
            }
            $loucheng[] = [
                'floor' => $value['title'],
                'list' => $floor_list
            ];
        }

        $data = [];
        $this->success('success', [
            'building' => $building,
            'seat' => $seat,
            'loucheng' => $loucheng,
            'floor' => $floor,
        ]);
    }

    // 历史取证
    /**
     * @OA\Get(path="/api/v1.index/history",
     *   tags={"历史取证"},
     *   summary="历史取证",
     *   @OA\Parameter(name="sype_id", in="query", description="楼盘sype_id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function history()
    {
        $sype_id = $this->request->param('sype_id');

        // 缓存
        $cacheKey = 'history_' . $sype_id;
        $data = cache($cacheKey);
        if ($data) {
            // $this->success('success', $data);
        }
        $data = [];


        $probject = [];


        $propertyInfo = PropertyInfo::where('status', 1)->where('sype_id', $sype_id)->find();
        // var_dump($propertyInfo);exit;
        $propertyInfos = $this->getIndexData([$propertyInfo]);
        $propertyInfo = $propertyInfos[0];





        $data['price_trend'] = [];
        $data['price_trend_step'] = [];
        $data['price_total_step'] = [];
        $data['price_total'] = [];


        $historyCounts = PropertyInfo::where('project', $propertyInfo['project'])
            ->where('status', 1)
            // ->field('sype_id,project')
            ->select();

        foreach ($historyCounts as $value) {

            $projectBaseInfo = ProjectBaseInfo::where('pre_sellId', $value['sype_id'])->find();
            $projectBuilding = ProjectBuilding::where('pre_sellId', $value['sype_id'])->select();
            $moneyWatcher = MoneyWatcher::where('pre_sellId', $value['sype_id'])->find();
            $seat = Seat::where('preSellId', $value['sype_id'])->select();
            $ysSuites = $projectBaseInfo['ys_suites'];

            $sale_range = '';
            foreach ($projectBuilding as $bb) {
                $sale_range .= $bb['name'] . '、';
            }
            // 去除最后一个 、
            $sale_range = rtrim($sale_range, '、');


            // 期房待售
            $house_ds = BuildingUnits::where('pre_sellId', $value['sype_id'])->where('last_status_name', '期房待售')->where('status', 1)->count();
            $house_yqrgs = BuildingUnits::where('pre_sellId', $value['sype_id'])->where('last_status_name', '已签认购书')->where('status', 1)->count();
            $house_yqht = BuildingUnits::where('pre_sellId', $value['sype_id'])->where('last_status_name', '已签合同')->where('status', 1)->count();
            $house_yqht += BuildingUnits::where('pre_sellId', $value['sype_id'])->where('last_status_name', '已备案')->where('status', 1)->count();

            $value['strpreprojectid'] = str_replace('深房许字', '', $value['strpreprojectid']);
            $value['ys_date_str'] = str_replace('获批预售', '', $value['ys_date_str']);

            $probject[] = [
                'progress' => [
                    [
                        'name' => '备案套数',
                        'value' => $ysSuites,
                    ],
                    [
                        'name' => '已售',
                        'value' => $house_yqrgs + $house_yqht,
                    ],
                    [
                        'name' => '待售',
                        'value' => $house_ds,
                    ],
                    [
                        'name' => '去化',
                        'value' => round(($house_yqrgs + $house_yqht) / $ysSuites * 100, 2),
                    ],
                ],
                'projectBaseInfo' => $projectBaseInfo,
                'projectBuilding' => $projectBuilding,
                'moneyWatcher' => $moneyWatcher,
                'propertyInfo' => $this->getIndexData([$value])[0],
                'seat' => $seat,
                // 销售范围
                'sale_range' => $sale_range
            ];

            $max_price = BuildingUnitsHistory::where('pre_sellId', $value['sype_id'])->max('ask_price_each_b');
            $min_price = BuildingUnitsHistory::where('pre_sellId', $value['sype_id'])->min('ask_price_each_b');
            $avg_price = BuildingUnitsHistory::where('pre_sellId', $value['sype_id'])->avg('ask_price_each_b');


            $data['price_trend'][] = [
                'max_price' => $max_price,
                'min_price' => $min_price,
                'avg_price' => round($avg_price, 2),
                'price_change' => 0,
            ];

            $max_price2 = BuildingUnitsHistory::where('pre_sellId', $value['sype_id'])->max('ask_price_total_b');
            $min_price2 = BuildingUnitsHistory::where('pre_sellId', $value['sype_id'])->min('ask_price_total_b');
            $avg_price2 = BuildingUnitsHistory::where('pre_sellId', $value['sype_id'])->avg('ask_price_total_b');



            $data['price_total'][] = [
                'max_price' => round($max_price2 / 10000, 2),
                'min_price' => round($min_price2 / 10000, 2),
                'avg_price' => round($avg_price2 / 10000, 2),
                'price_change' => 0,
            ];
        }

        $price_trend = $data['price_trend'];
        $price_trend_max = 0;
        $price_trend_min = PHP_INT_MAX; // 确保找到真正的最小值
        
        for ($i = count($price_trend) - 1; $i > 0; $i--) {
            $price_trend_max = max($price_trend[$i - 1]['max_price'], $price_trend_max);
            $price_trend_min = min($price_trend[$i - 1]['min_price'], $price_trend_min);
            $price_trend[$i - 1]['price_change'] = round($price_trend[$i - 1]['avg_price'] - $price_trend[$i]['avg_price'], 2);
        }
        
        // 确保最旧数据的 price_change = 0
        $price_trend[count($price_trend) - 1]['price_change'] = 0;
        
        // 避免最小值仍是 PHP_INT_MAX
        if ($price_trend_min === PHP_INT_MAX) {
            $price_trend_min = 0;
        }
        
        // 调整最大最小值
        $price_trend_min = max(0, $price_trend_min - abs($price_trend_min) * 0.5);
        $price_trend_max += abs($price_trend_max) * 0.5;
        
        // 计算步长，确保不会太小
        $range = $price_trend_max - $price_trend_min;
        if ($range < 1) {
            $range = 1; // 防止刻度过小
        }
        $step = round($range / 4, 2); // 计算 5 个刻度，步长保留两位小数
        
        // 生成刻度数组
        $price_trend_ticks = [];
        for ($i = 0; $i < 5; $i++) {
            $price_trend_ticks[] = round($price_trend_min + $i * $step, 2);
        }
        
        $data['price_trend_step'] = $price_trend_ticks;
        $data['price_trend'] = $price_trend;

        

        $price_total = $data['price_total'];
        $price_total_max = 0;
        $price_total_min = PHP_INT_MAX;

        for ($i = count($price_total) - 1; $i > 0; $i--) {
            // 计算最大值
            $price_total_max = max($price_total[$i - 1]['max_price'], $price_total_max);

            // 计算最小值
            $price_total_min = min($price_total[$i - 1]['min_price'], $price_total_min);

            // 计算 avg_price 变化
            $price_total[$i - 1]['price_change'] = round($price_total[$i - 1]['avg_price'] - $price_total[$i]['avg_price'], 2);
        }

        // 处理最旧数据的 price_change
        $price_total[count($price_total) - 1]['price_change'] = 0;

        // 修正最小值防止 PHP_INT_MAX 影响
        if ($price_total_min === PHP_INT_MAX) {
            $price_total_min = 0; // 设定合理的默认值
        }

        // 计算范围，避免负数问题
        $price_total_min = max(0, $price_total_min - abs($price_total_min) * 0.5);
        $price_total_max += abs($price_total_max) * 0.5;

        // 刻度步长计算
        $range = max(1, $price_total_max - $price_total_min);
        $step = $range / 4;

        // 生成刻度数组
        $price_total_step = [];
        for ($i = 0; $i < 5; $i++) {
            $price_total_step[] = round($price_total_min + $i * $step, 2);
        }
        $data['price_total_step'] = $price_total_step;

        // 恢复原顺序
        $data['price_total'] = $price_total;


        // $price_trend = $data['price_trend'];
        // $price_trend_max = 0;
        // $price_trend_min = 0;
        // for ($i = count($price_trend) - 1; $i > 0; $i--) {
        //     $price_trend_max = $price_trend[$i - 1]['max_price'] > $price_trend_max  ? $price_trend[$i - 1]['max_price'] : $price_trend_max; 
        //     $price_trend_min = $price_trend[$i - 1]['min_price'] > $price_trend_min  ? $price_trend[$i - 1]['min_price'] : $price_trend_min; 

        //     // 当前项与上一项的 avg_price 差值
        //     $price_trend[$i - 1]['price_change'] = round($price_trend[$i - 1]['avg_price'] - $price_trend[$i]['avg_price'],2);
        // }

        // $price_trend_min -= $price_trend_min*0.5;
        // $price_trend_max += $price_trend_max*0.5;

        // // 按照最大值最小值。切分5个刻度
        // $step = ($price_trend_max - max($price_trend_min,0)) / 4; // 5个刻度，需要4个步长

        // // 生成刻度数组
        // $price_trend_ticks = [];
        // for ($i = 0; $i < 5; $i++) {
        //     $price_trend_ticks[] = round($price_trend_min + $i * $step, 2);
        // }
        // $data['price_trend_step'] = $price_trend_ticks;

        // // 最旧数据的 price_change 固定为 0
        // $price_trend[count($price_trend) - 1]['price_change'] = 0;
        // // 将数据恢复为原顺序
        // $data['price_trend'] = $price_trend;

        // $price_total = $data['price_total'];
        // $price_total_max = 0;
        // $price_total_min = PHP_INT_MAX; // 设为最大整数，确保找到最小值

        // for ($i = count($price_total) - 1; $i > 0; $i--) {
        //     // 计算最大值
        //     $price_total_max = max($price_total[$i - 1]['max_price'], $price_total_max);

        //     // 计算最小值
        //     $price_total_min = min($price_total[$i - 1]['min_price'], $price_total_min);

        //     // 计算 avg_price 变化
        //     $price_total[$i - 1]['price_change'] = round($price_total[$i - 1]['avg_price'] - $price_total[$i]['avg_price'], 2);
        // }

        // // 处理最旧数据的 price_change
        // $price_total[count($price_total) - 1]['price_change'] = 0;

        // // 修正最小值防止 PHP_INT_MAX 影响
        // if ($price_total_min === PHP_INT_MAX) {
        //     $price_total_min = 0; // 设定合理的默认值
        // }

        // // 赋值回原数组
        // $data['price_total'] = $price_total;


        // // 最旧数据的 price_change 固定为 0
        // $price_total[count($price_total) - 1]['price_change'] = 0;
        // // 将数据恢复为原顺序
        // $data['price_total'] = $price_total;

        // $price_total_min -= $price_total_min*0.5;
        // $price_total_max += $price_total_max*0.5;

        // // 按照最大值最小值。切分5个刻度
        // $step = ($price_total_max - max($price_total_min,0)) / 4; // 5个刻度，需要4个步长

        // // 生成刻度数组
        // $price_total_step = [];
        // for ($i = 0; $i < 5; $i++) {
        //     $price_total_step[] = round($price_total_min + $i * $step, 2);
        // }
        // $data['price_total_step'] = $price_total_step;





        $data['probject'] = $probject;
        // $data['seat'] = $seat;
        // $data['projectBuilding'] = $projectBuilding;
        $propertyInfo['strpreprojectid'] = str_replace('深房许字', '', $propertyInfo['strpreprojectid']);
        $propertyInfo['ys_date_str'] = str_replace('获批预售', '', $propertyInfo['ys_date_str']);

        $data['propertyInfo'] = $propertyInfo;
        cache($cacheKey, $data, 83000);
        $this->success('success', $data);
    }

    // 楼盘相册
    /**
     * @OA\Get(path="/api/v1.index/album",
     *   tags={"楼盘相册"},
     *   summary="楼盘相册",
     *   @OA\Parameter(name="id", in="query", description="楼盘id", @OA\Schema(type="int", default="0")),
     *   @OA\Response(response="200", description="类型=[0:总平图,1:规划图,2:效果图,3:户型图]")
     * )
     */
    public function album()
    {
        $id = $this->request->param('id');
        $albums = Album::where('sype_id', $id)->where('status', 1)->select();

        $typeData = [];
        foreach ($albums as $key => &$album) {
            $album['thumbs'] = explode(',', $album['thumbs']);
            $typeData[$album['type']][] = $album;
        }

        $this->success('success',  $typeData);
    }

    // 登录
    /**
     * @OA\Post(path="/api/v1.index/login",
     *   tags={"登录"},
     *   summary="登录",
     *   @OA\Parameter(name="code", in="query", description="code", @OA\Schema(type="string", default="")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function login()
    {
        $code = $this->request->param('code');

        // use EasyWeChat\Factory;

        $config = [
            'app_id' => 'wxa7e1e73afd86e576',
            'secret' => 'c08d380e1ac2487e411b826cd5466025',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__ . '/wechat.log',
            ],
        ];

        $app = Factory::miniProgram($config);

        // try {
        $userinfo = $app->auth->session($code);
        $session_key = '';
        if (array_key_exists('openid', $userinfo)) {
            $openid = $userinfo['openid'];
            $session_key = $userinfo['session_key'];
        } else {
            $this->error('登录失败,获取不到openid');
        }



        if (!$openid) {
            // 登录失败,获取不到openid
            $this->error('登录失败,获取不到openid');
        }

        // 查询用户是否存在
        $member = Member::where('openid', $openid)->find();
        if (!$member) {
            // 用户不存在,注册用户
            $member = Member::create([
                'openid' => $openid,
                'session_key' => $session_key,
                'unionid' => isset($unionid) ? $unionid : '',
                'nickname' => '微信用户',
                'avatar' => '',
                'province' => '',
                'city' => '',
                'district' => '',
                'address' => '',
                'phone' => '',
                'email' => '',
            ]);
        }
        // token 生成
        $accessToken = $this->buildAccessToken($member, 3600);


        $this->success(lang('success'), ['access_token' => $accessToken, 'member' => $member]);
    }

    // getUnlimited
    /**
     * @OA\Get(path="/api/v1.index/getUnlimited",
     *   tags={"获取小程序码"},
     *   summary="获取小程序码",
     *   @OA\Parameter(name="page", in="query", description="page", @OA\Schema(type="string", default="")),
     *   @OA\Parameter(name="scene", in="query", description="scene", @OA\Schema(type="string", default="")),
     *   @OA\Response(response="200", description="The User")
     * )
     **/
    public function getUnlimited()
    {
        $page = $this->request->param('page');
        $scene = $this->request->param('scene');
        $config = [
            'app_id' => 'wxa7e1e73afd86e576',
            'secret' => 'c08d380e1ac2487e411b826cd5466025',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__ . '/wechat.log',
            ],
        ];

        $app = Factory::miniProgram($config);

        $response = $app->app_code->getUnlimit($scene, [
            'page' => $page,
            'width' => 600,
        ]);

        $this->success('success', $response);
    }

    /**
     * token
     * @param $memberInfo
     * @param $expires
     * @return string
     */
    protected function buildAccessToken($memberInfo, $expires)
    {
        $time = time(); //签发时间
        $expire = $time + $expires; //过期时间
        $scopes = 'role_access';
        // if ($expires == $this->refreshExpires)  $scopes = 'role_refresh';
        $token = array(
            "member_id" => $memberInfo['id'],
            'appid' => $this->appid,
            'appsecret' => $this->appsecret,
            "iss" => "admim.com", //签发组织
            "aud" => "admim", //签发作者
            "scopes" => $scopes, //刷新
            "iat" => $time,
            "nbf" => $time,
            "exp" => $expire,      //过期时间时间戳
        );
        return   JWT::encode($token,  config('api.jwt_key'), 'HS256');
    }

    // 获取个人信息
    /**
     * @OA\Get(path="/api/v1.index/member",
     *   tags={"个人信息"},
     *   summary="个人信息",
     * @OA\Parameter(name="token", in="header", description="token", @OA\Schema(type="string")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function member()
    {
        if (!$this->member_id) {
            $this->error('请先登录');
        }
        $member = Member::where('id', $this->member_id)->find();
        $data = [
            'id' => $member['id'],
            'username' => $member['username'],
            'nickname' => $member['nickname'],
            'avatar' => $member['avatar'],
            'province' => $member['province'],
            'city' => $member['city'],
            'district' => $member['district'],
            'address' => $member['address'],
            'phone' => $member['phone'],
            'email' => $member['email'],
            'create_time' => $member['create_time'],
        ];
        $this->success('success', $data);
    }

    // 上传头像 file
    /**
     * @OA\Post(path="/api/v1.index/upload",
     *   tags={"上传头像"},
     *   summary="上传头像",
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *         @OA\Schema(
     *           @OA\Property(description="头像", property="file", type="file"),
     *           required={"file"})
     *       )
     *     ),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function upload()
    {
        $upload = UploadService::instance();
        $result = $upload->uploads(0, 0);
        $this->success('上传成功', $result);
    }

    // 修改资料 昵称，头像
    /**
     * @OA\Post(path="/api/v1.index/edit",
     *   tags={"修改资料"},
     *   summary="修改资料",
     * @OA\Parameter(name="token", in="header", description="token", @OA\Schema(type="string")),
     * @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="content-type/json",
     *         @OA\Schema(
     *           @OA\Property(description="昵称", property="nickname", type="string"),
     *           @OA\Property(description="头像", property="avatar", type="string"),
     *           required={"nickname", "avatar"})
     *       )
     *     ),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function edit()
    {
        if (!$this->member_id) {
            $this->error('请先登录');
        }
        $data = $this->request->param();
        $member = Member::where('id', $this->member_id)->find();
        // $member->save($data);
        if (!$member) {
            $this->error('用户不存在');
        }

        // 只允许修改avatar跟nickname



        Member::where('id', $this->member_id)->update($data);
        $this->success('修改成功');
    }

    // 绑定手机 code
    /**
     * @OA\Post(path="/api/v1.index/bindphone",
     *   tags={"绑定手机"},
     *   summary="绑定手机",
     * @OA\Parameter(name="token", in="header", description="token", @OA\Schema(type="string")),
     *   @OA\Parameter(name="code", in="query", description="code", @OA\Schema(type="string", default="")),
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function bindphone()
    {
        // 
        if (!$this->member_id) {
            $this->error('请先登录');
        }

        $code = $this->request->param('code');

        // use EasyWeChat\Factory;
        $config = [
            'app_id' => 'wxa7e1e73afd86e576',
            'secret' => 'c08d380e1ac2487e411b826cd5466025',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__ . '/wechat.log',
            ],
        ];

        try {

            $app = Factory::miniProgram($config);
            $res = $app->phone_number->getUserPhoneNumber($code);

            $phone_info = $res['phone_info'];
            $phone = $phone_info['pure_phone'];
            $member = Member::get($this->member_id);
            $member->phone = $phone;
            $member->save();
            $this->success('绑定成功');
        } catch (\Exception $e) {
            $this->error('绑定失败' . $e->getMessage());
        }
    }

    // 协议
    /**
     * @OA\Get(path="/api/v1.index/protocol",
     *   tags={"协议"},
     *   summary="协议",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function protocol()
    {
        $data = [
            'title' => '用户协议',
            'content' => Config::where('code', 'site_service')->value('value'),
        ];
        $this->success('success',  $data);
    }

    // 帮助教程
    /**
     * @OA\Get(path="/api/v1.index/help",
     *   tags={"帮助教程"},
     *   summary="帮助教程",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function help()
    {
        $data = [
            'title' => '帮助中心',
            'content' => Config::where('code', 'site_help')->value('value'),
        ];
        $this->success('success', $data);
    }
    // 教程详情
    /**
     * @OA\Get(path="/api/v1.index/privacy",
     *   tags={"隐私协议"},
     *   summary="隐私协议",
     *   @OA\Response(response="200", description="The User")
     * )
     */
    public function privacy()
    {
        $data = [
            'title' => '教程标题',
            'content' => Config::where('code', 'site_privacy')->value('value'),
        ];
        $this->success('success', $data);
    }
}
