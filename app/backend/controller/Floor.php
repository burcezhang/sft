<?php
declare (strict_types = 1);

namespace app\backend\controller;

use think\Request;
use think\App;
use think\facade\View;
use app\backend\model\Floor as FloorModel;
use app\common\annotation\NodeAnnotation;
use app\common\annotation\ControllerAnnotation;

/**
 * @ControllerAnnotation (title="地区表")
 */
class Floor extends \app\common\controller\Backend
{
    protected $pageSize = 15;
    protected $layout = 'layout/main';

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new FloorModel();
        View::assign('statusList',$this->modelClass->getStatusList());


    }

    

    

}

