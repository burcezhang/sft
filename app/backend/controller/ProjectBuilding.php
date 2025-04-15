<?php
declare (strict_types = 1);

namespace app\backend\controller;

use think\Request;
use think\App;
use think\facade\View;
use app\backend\model\ProjectBuilding as ProjectBuildingModel;
use app\common\annotation\NodeAnnotation;
use app\common\annotation\ControllerAnnotation;

/**
 * @ControllerAnnotation (title="建筑信息表")
 */
class ProjectBuilding extends \app\common\controller\Backend
{
    protected $pageSize = 15;
    protected $layout = 'layout/main';

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new ProjectBuildingModel();
        View::assign('statusList',$this->modelClass->getStatusList());


    }

    

    

}

