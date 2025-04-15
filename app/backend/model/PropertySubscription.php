<?php
declare (strict_types = 1);

namespace app\backend\model;
use app\common\model\BaseModel;
use think\Model;
use think\model\concern\SoftDelete;
/**
 * @mixin \think\Model
 */
class PropertySubscription extends BaseModel
{
    use SoftDelete;
    protected $defaultSoftDelete = 0;


    

    protected $pk = 'id';

    protected $name = 'property_subscription';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
    
    public function getStatusList()
    {
        return ['0'=>'Status 0',' 1'=>'Status  1',];
    }



    
    public function getSubscriptionTimeAttr($value)
    {
        $value = $value ? $value  : '';
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function setSubscriptionTimeAttr($value)
    {
        $value = $value ? $value  : '';
        return $value == '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
}
