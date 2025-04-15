<?php
declare (strict_types = 1);

namespace app\backend\model;
use app\common\model\BaseModel;
use think\Model;
use think\model\concern\SoftDelete;
/**
 * @mixin \think\Model
 */
class Album extends BaseModel
{
    use SoftDelete;
    protected $defaultSoftDelete = 0;


    

    protected $pk = 'id';

    protected $name = 'album';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
    public function propertyInfo()
    {
        return $this->hasOne('PropertyInfo','sype_id','id');
    }


    public function getTypeList()
    {
        return ['0'=>'Type 0','1'=>'Type 1','2'=>'Type 2','3'=>'Type 3',];
    }


    public function getPropertyInfoN()
    {
        return PropertyInfo::where('status',1)->select();
    }


    public function getStatusList()
    {
        return ['0'=>'Status 0','1'=>'Status 1',];
    }



    
}
