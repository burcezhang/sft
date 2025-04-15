<?php

namespace app\apidoc\controller\v1;

use think\facade\Request;
use fun\auth\Api;
use fun\auth\validate\ValidataBase;
use think\facade\Validate;

/**
 * @title   接口测试
 * @desc    我是模块名称
 * Class Index
 * @package app\index\controller
 */
class ApiTest extends Api
{
    /**
     * 不需要鉴权方法
     * index、save不需要鉴权
     * ['index','save']
     * 所有方法都不需要鉴权
     * [*]
     */
    protected $noAuth = ['index'];
    /**
     * @title 方法1
     * @desc  类的方法1
     * @version   v1
     * @author   yuege
     *
     * @param int $page  0 9999
     * @param int $limit 10
     *
     * @return int $id 0 索引
     * @return int $id 0 索引
     * @return int $id 0 索引
     */
    public function index()
    {
        //通用参数验证
        $this->success('ok');

    }

}
