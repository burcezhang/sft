<?php
declare (strict_types = 1);

namespace app\backend\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
/**
 * @mixin \think\Model
 */
class HouseDealArea extends BaseModel
{
    use SoftDelete;
    protected $defaultSoftDelete = 0;
    

    protected $pk = 'id';

    protected $name = 'house_deal_area';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}
