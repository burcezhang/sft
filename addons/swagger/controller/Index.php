<?php

namespace addons\swagger\controller;

use app\common\traits\Jump;
use fun\addons\Controller;
use think\App;
class Index extends Controller
{
    use Jump;
    public function __construct(App $app){
        parent::__construct($app);
    }
    public function index()
    {
        $config = get_addons_config($this->addon);
        if(!$config['status']['value']){
            $this->error('请先启用');
        }
        if(!$config['dir']['value'])  $this->error('请先配置目录');
        $dir = explode(',', $config['dir']['value']);
        foreach ($dir as $k=>$v){
            $dir[$k]= root_path().$v;
        }
        $openapi = \OpenApi\Generator::scan($dir);
        header('Content-Type: application/x-yaml');
        $jsonString = $_SERVER['DOCUMENT_ROOT'] . '/swagger.json';
        // var_dump($openapi->toJson());exit;
        unlink($jsonString);
        $res = file_put_contents($jsonString, $openapi->toJson());
        if ($res == true) {
           return redirect('/swagger/index.html');
        }
    }

}
