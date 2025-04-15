<?php
declare (strict_types = 1);

namespace app\backend\model;
use app\common\model\BaseModel;
use think\Model;
use think\model\concern\SoftDelete;
/**
 * @mixin \think\Model
 */
class Building extends BaseModel
{
    use SoftDelete;
    protected $defaultSoftDelete = 0;


    

    protected $pk = 'id';

    protected $name = 'building';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
    
    public function getStatusList()
    {
        return ['0'=>'Status 0','1'=>'Status 1',];
    }



    
}
