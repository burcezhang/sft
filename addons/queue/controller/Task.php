<?php

/**
 * FunAdmin
 * ============================================================================
 * 版权所有 2018-2027 FunAdmin，并保留所有权利。
 * 网站地址: https://www.funadmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2022/11/7
 */

namespace addons\queue\controller;

use think\facade\Db;
use think\facade\Log;
use think\queue\Job;
use addons\queue\library\Queue;
use app\backend\model\PropertyInfo;
use Swoole\Coroutine\Http\Client;

function asyncRequest($url, $data)
{
    return go(function () use ($url, $data) {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];
        $port = $parsedUrl['scheme'] === 'https' ? 443 : 80;
        $path = $parsedUrl['path'] . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');

        // 创建 HTTP 客户端
        $client = new \Swoole\Coroutine\Http\Client($host, $port, $parsedUrl['scheme'] === 'https');
        $client->set(['timeout' => 5]);

        // 发送 POST 请求
        $client->post($path, http_build_query($data));

        // 处理响应
        if ($client->statusCode === 200) {
            echo "Success: " . $client->body . "\n";
        } else {
            echo "Failed: Status Code " . $client->statusCode . "\n";
        }

        // 关闭客户端
        $client->close();
    });
}

/**
 * 消费者类
 * 用于处理 dismiss_job_queue 队列中的任务
 * 用于牌局解散
 */
class Task extends Queue
{

    protected function execute($data): bool
    {
        $params = [
            'queue' => $data['params']['name'],
            'payload' => $data['params']['name'],
        ];

        $res = 1;
        $app = \think\App::getInstance();
        $datasync = new \app\api\controller\v1\Datasync($app);
        go(function () use ($datasync) {

            go(function () use ($datasync) {
                $houses = PropertyInfo::where('status', 1)->field('sype_id')->select()->toArray();

                foreach ($houses as $house) {
                    // $url = 'http://39.108.239.118/api/v1.datasync/synchouse?sype_id='.$house['sype_id'];
                    // $data = [
                    //     'sype_id' => $house['sype_id'],
                    // ];
                    // var_dump($url,$data);
                    sleep(1);
                    try {
                        $res = $datasync->getSuiteInformation($house['sype_id']);
                    } catch (\Throwable $th) {
                        var_dump($th->getMessage());
                    }
                }
            });




            // $url = 'http://39.108.239.118/api/v1.datasync/househistory';
            // $data = [

            // ];
            // var_dump($url,$data);
            // $res = file_get_contents($url);
            go(function () use ($datasync) {
                $datasync->househistory();
            });
            go(function () use ($datasync) {
                $houses = PropertyInfo::where('status', 1)->field('sype_id')->select()->toArray();

                foreach ($houses as $house) {

                    try {
                        $res = $datasync->getSuiteInformationHistory($house['sype_id']);
                    } catch (\Throwable $th) {
                        var_dump($th->getMessage());
                    }
                }
            });
        });



        //这里写你的逻辑
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
}
