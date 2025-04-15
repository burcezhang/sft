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
 * Date: 2019/11/7
 */

namespace addons\backbg;
// 注意命名空间规范
use app\common\model\Config as ConfigModel;
use fun\Addons;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class Plugin extends Addons    // 需继承fun\Addon类
{
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {

        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
    /**
     * 插件使用方法
     * @return bool
     */
    public function enabled()
    {
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disabled()
    {
        return true;
    }

    public function bgHook($param)
    {
        $bg = '/static/backend/images/admin-bg.jpg';
        $ini = get_addons_info($this->name);
        if(!$ini['status']){
            return $bg;
        }
        if(!$ini['install']){
            return $bg;
        }
        $config = get_addons_config($this->name);

        $value =$config['images']['value'];
        $arr = [];
        if($value){
            $arr = explode(',',$value);
        }
        $num = cache('bgnum');
        if(!$num){
            $num = rand(0,$config['images']['num']);
            cache('bgnum',$num,3600*24);
        }
        return $arr && isset($arr[$num])?str_replace('\\','/',$arr[$num]):$bg;
    }
}