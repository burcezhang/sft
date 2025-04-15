<?php
declare (strict_types = 1);

namespace app\curd\model;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * @mixin \think\Model
 */
class DemoCate extends BaseModel
{
    use SoftDelete;
    protected $name = 'addons_demo_cate';
    protected $defaultSoftDelete = 0;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }



}
