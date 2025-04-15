<?php
/**
 * FunAdmin
 * ============================================================================
 * 版权所有 2017-2028 FunAdmin，并保留所有权利。
 * 网站地址: http://www.FunAdmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2021/5/11
 * Time: 15:55
 */
namespace app\curd\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Demo extends BaseModel{
    use SoftDelete;
    protected $name = 'addons_demo';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
    public function admin(){
        return $this->belongsTo('app\backend\model\Admin','admin_id','id');
    }
    public function citys(){
        return $this->belongsTo('app\backend\model\Admin','admin_id','id');
    }
    public function province(){
        return $this->belongsTo('app\backend\model\Provinces','province_id','id');
    }
    public function city(){
        return $this->belongsTo('app\backend\model\Provinces','city_id','id');
    }
    public function area(){
        return $this->belongsTo('app\backend\model\Provinces','area_id','id');
    }
    public function democate(){
        return $this->belongsTo('\app\curd\model\DemoCate','cate_id','id');
    }

    public function getCateIdList()
    {
        return \app\curd\model\DemoCate::column('title','id');

    }
    public function getWeekList()
    {
        return ['monday'=>'Week monday','tuesday'=>'Week tuesday','wednesday'=>'Week wednesday',];
    }


    public function getSexdataList()
    {
        return ['male'=>'Sexdata male','female'=>'Sexdata female','secret'=>'Sexdata secret',];
    }


    public function getSwitchList()
    {
        return ['0'=>'Switch 0','1'=>'Switch 1',];
    }


    public function getOpenSwitchList()
    {
        return ['0'=>'OpenSwitch 0','1'=>'OpenSwitch 1',];
    }


    public function getMystateList()
    {
        return [1=>'Mystate 1',2=>'Mystate 2',3=>'Mystate 3',];
    }


    public function getMy2stateList()
    {
        return [0=>'My2state 0',1=>'My2state 1',2=>'My2state 2',];
    }


    public function getStatusList()
    {
        return ['0'=>'Status 0','1'=>'Status 1',];
    }

}