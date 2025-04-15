<?php
/**
 * FunAdmin
 * ============================================================================
 * 版权所有 2017-2028 FunAdmin，并保留所有权利。
 * 网站地址: http://www.FunAdmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2021/3/11
 * Time: 15:45
 */
namespace addons\curd\controller;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use fun\addons\Controller;
use think\App;

/**
 * @ControllerAnnotation ('Index')
 * @package addons\curd\frontend\controller
 */
class Index extends Controller
{
    public function __construct(App $app){
        parent::__construct($app);
    }

    /**
     * @NodeAnnotation('首页')
     * @return string
     */
    public function index(){
        return '插件没有前台';
    }
}