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
 * Date: 2022/4/11
 * Time: 15:48
 */
namespace app\curd\controller;

use app\backend\model\MemberGroup;
use app\common\annotation\ControllerAnnotation;
use app\common\controller\Backend;
use app\common\model\Member;
use app\common\model\Provinces;
use app\curd\model\Demo as DemoModel;
use think\App;
use think\facade\View;

/**
 * @ControllerAnnotation('Index')
 * @package addons\curd\backend\controlLer
 */
class Demo extends Backend
{

    protected $layout = '../../backend/view/layout/main';
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new DemoModel();
        View::assign('weekList',$this->modelClass->getWeekList());
        View::assign('sexdataList',$this->modelClass->getSexdataList());
        View::assign('switchList',$this->modelClass->getSwitchList());
        View::assign('openSwitchList',$this->modelClass->getOpenSwitchList());
        View::assign('mystateList',$this->modelClass->getMystateList());
        View::assign('my2stateList',$this->modelClass->getMy2stateList());
        View::assign('statusList',$this->modelClass->getStatusList());
        View::assign('cateIdList',$this->modelClass->getCateIdList());
    }
    public function index()
    {
        if ($this->request->isAjax()) {
            if ($this->request->param('selectFields') && input('type')==1) {
                $data = [
                    ['name' => 'female', 'value' => __('Sexdata female')],
                    ['name' => 'male', 'value' => __('Sexdata male')],
                    ['name' => 'secret', 'value' => __('Sexdata secret')],
                ];
                return $this->success('ok','',$data);
            }
            if ($this->request->param('selectFields')) {
                $this->selectList();
            }
            list($this->page, $this->pageSize,$sort,$where) = $this->buildParames();
            $list = $this->modelClass->with(['democate'])
                ->where($where)
                ->order($sort)
                ->paginate([
                    'list_rows'=> $this->pageSize,
                    'page' => $this->page,
                ]);
            $result = ['code' => 0, 'msg' => lang('Get Data Success'), 'data' => $list->items(), 'count' =>$list->total()];
            return json($result);
        }
        return view();
    }
    public function getcitys(){
        if($this->request->isAjax()) {}{
            $params = $this->request->get("row/a");
            if (!empty($params)) {
                $province = $params['province_id'] ?? null;
                $city     = $params['city_id'] ?? null;
            } else {
                $province = $this->request->get('province_id');
                $city     = $this->request->get('city_id');
            }
            $where        = ['pid' => 0];
            $provincelist = null;
            if ($province !== null) {
                $where['pid']   = $province;
                if ($city !== null) {
                    $where['pid']   = $city;
                }
            }
            $cache = array_merge($where,empty($params)?[]:$params);
            $provincelist = Provinces::where($where)->order('id asc')->field('id,name,pid')
                ->cache(json_encode($cache),3600*24)->select()->toArray();
            $this->success('','',$provincelist);
        }
    }
    public function getmystateList(){
        return $this->success('','',$this->modelClass->getMystateList());
    }
    public function member(){
        if ($this->request->isAjax()) {
            $this->modelClass = new Member();
            if ($this->request->param('selectFields')) {
                $this->selectList();
            }
            list($this->page, $this->pageSize, $sort, $where) = $this->buildParames();
            $list = $this->modelClass
                ->withJoin(['memberGroup','memberLevel'])
                ->where($where)
                ->order($sort)
                ->paginate([
                    'list_rows'=> $this->pageSize,
                    'page' => $this->page,
                ]);
            $result = ['code' => 0, 'msg' => lang('Get Data Success'), 'data' => $list->items(), 'count' =>$list->total()];
            return json($result);
        }
    }
    public function getmember(){
        if($this->request->isAjax()) {}{
            $member = Member::order('id desc')->field('id,nickname')
                ->select()->toArray();
            $pid = $this->request->param('pid',0);
//            $citys = Provinces::where('pid',$pid)->order('id desc')->field('id,name,pid')
//                ->cache('provinces.'.$pid,3600*24)->select()->toArray();
//            $this->success('','',$citys);
            $this->success('','',$member);
        }
    }
    public function getgroup(){
        if($this->request->isAjax()) {}{
            $memberGroup = MemberGroup::where('status', 1)->select()->toArray();
            $this->success('','',$memberGroup);
        }
    }
}
