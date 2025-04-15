<?php
declare (strict_types = 1);

namespace app\backend\model;
use app\common\model\BaseModel;
use think\Model;
use think\model\concern\SoftDelete;
/**
 * @mixin \think\Model
 */
class SplashAds extends BaseModel
{
    

    

    protected $pk = 'id';

    protected $name = 'splash_ads';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
    
    public function getStatusList()
    {
        return ['0'=>'Status 0','1'=>'Status 1',];
    }



    
}
