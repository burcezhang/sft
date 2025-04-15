<?php
/**
 * funadmin
 * ============================================================================
 * 版权所有 2018-2027 funadmin，并保留所有权利。
 * 网站地址: https://www.funadmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2021/3/2
 */
namespace addons\queue\controller;

use app\common\traits\Jump;
use fun\addons\Controller;
use think\App;
use think\facade\Queue;
use think\facade\Log;
class Index extends Controller
{
    use Jump;
    public function index(){
        //获取参数
        $params = [
            'name'=>'这是一个测试名字',
            'title'=>'这是一个测试title',
            'description'=>'这是一个测试描述',
        ];
        // 当前任务由哪个类负责处理
        $jobHandlerName = "addons\queue\controller\Task";
        // 当前队列归属的队列名称
        $jobQueueName = "taskQueue";
        // 当前任务所需的业务数据
        $jobData = ["ts"=>time(), "bizid"=>uniqid(), "params"=>$params];
        // 将任务推送到消息队列等待对应的消费者去执行
        $isPushed = Queue::push($jobHandlerName, $jobData, $jobQueueName);
        // $isPushed = Queue::later(0,$jobHandlerName,$jobData,$jobQueueName);
        if($isPushed == false){
           return ("task job queue went wrong");
        }
        return ("task job queue went success");
    }
}