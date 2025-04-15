<?php
declare (strict_types = 1);

namespace app\backend\model;
use app\common\model\BaseModel;
use think\Model;
use think\model\concern\SoftDelete;
/**
 * @mixin \think\Model
 */
class PropertyInfo extends BaseModel
{
    use SoftDelete;
    protected $defaultSoftDelete = 0;


    

    protected $pk = 'id';

    protected $name = 'property_info';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
    
    public function getStatusList()
    {
        return ['0'=>'Status 0','1'=>'Status 1',];
    }



    

    public function getSypIdList()
    {
        return property_info;
    }


    public function getSypeIdList()
    {
        return property_info;
    }


}