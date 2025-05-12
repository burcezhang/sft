<?php
declare (strict_types = 1);

namespace app\backend\model;
use app\common\model\BaseModel;
use think\Model;
use think\model\concern\SoftDelete;
/**
 * @mixin \think\Model
 */
class BuildingDeal extends BaseModel
{
    use SoftDelete;
    protected $defaultSoftDelete = 0;
    protected $pk = 'id';
    protected $name = 'building_deal';
}
