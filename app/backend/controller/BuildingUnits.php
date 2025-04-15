<?php
declare (strict_types = 1);

namespace app\backend\controller;

use think\Request;
use think\App;
use think\facade\View;
use app\backend\model\BuildingUnits as BuildingUnitsModel;
use app\common\annotation\NodeAnnotation;
use app\common\annotation\ControllerAnnotation;

/**
 * @ControllerAnnotation (title="楼栋单元信息表")
 */
class BuildingUnits extends \app\common\controller\Backend
{
    protected $pageSize = 15;
    protected $layout = 'layout/main';

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new BuildingUnitsModel();
        View::assign('statusList',$this->modelClass->getStatusList());


    }

    

    

}

