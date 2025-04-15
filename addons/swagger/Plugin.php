<?php

namespace addons\swagger;

use fun\Addons;
use fun\helper\FileHelper;
/**
 * 插件
 */
class Plugin extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        FileHelper::copyDir(__DIR__ . DS.$this->name,root_path().'public/'.$this->name,true);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        FileHelper::copyDir(root_path().'public/'.$this->name,__DIR__ . DS.$this->name,true);
        return true;
    }

    /**
     * 插件启用方法
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

}
