<?php
/**
 * funadmin
 * ============================================================================
 * 版权所有 2018-2027 funadmin，并保留所有权利。
 * 网站地址: https://www.funadmin.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/9/4
 */

namespace app\wechat\controller\backend;


use app\wechat\model\WechatAccount;
use app\wechat\model\WechatMenu;
use think\App;
use think\facade\View;

class Menu extends WxBase
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new WechatMenu();
        $account = WechatAccount::cache(3600)->where('status', 1)->find();
        View::assign('account',$account);
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            if($this->request->param('selectFields')){
                $this->selectList();
            }
            list($this->page, $this->pageSize,$sort,$where) = $this->buildParames();
            $count = $this->modelClass
                ->where($where)
                ->count();
            $list = $this->modelClass
                ->where($where)
                ->order($sort)
                ->page($this->page,$this->pageSize)
                ->select();
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => $count];
            return json($result);
        }
        return view();
    }

    /**
     * @NodeAnnotation ('Add')
     * @return \think\response\View|void
     */
    public function add()
    {
        if($this->request->isAjax()){
            $post = $this->request->post();
            $rule = ['menu_data'=>'require'];
            try {
                $this->validate($post, $rule);
            }catch (\ValidateException $e){
                $this->error(lang($e->getMessage()));
            }
            try {
                $post['status'] = 0;
                $save = $this->modelClass->save($post);
            } catch (\Exception $e) {
                $this->error(lang($e->getMessage()));
            }
            $save ? $this->success(lang('operation success')) : $this->error(lang('operation failed'));
        }
        $view = ['formData'=>[]];
        return view('add',$view);
    }
    /**
     * 跟新菜单
     */
    public function edit()
    {
        $id = $this->request->param('id');
        $list = $this->modelClass->find($id);
        if(empty($list)) $this->error(lang('Data is not exist'));
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = ['menu_data'=>'require'];
            try {
                $this->validate($post, $rule);
            }catch (\ValidateException $e){
                $this->error(lang($e->getMessage()));
            }
            try {
                $save = $list->save($post);
            } catch (\Exception $e) {
                $this->error(lang($e->getMessage()));
            }
            $save ? $this->success(lang('operation success')) : $this->error(lang('operation failed'));
        }
        $list->menu_data = json_decode($list->menu_data,true);
        $view = ['formData'=>$list];
        return view('add',$view);
    }
    /**
     * @NodeAnnotation(title="modify")
     */
    public function modify(){
        $id = input('id');
        $field = input('field');
        $value = input('value');
        if($id){
            if($this->allowModifyFields != ['*'] and !in_array($field,$this->allowModifyFields)){
                $this->error(lang('Field Is Not Allow Modify：' . $field));
            }
            $model = $this->findModel($id);
            if (!$model) {
                $this->error(lang('Data Is Not Exist'));
            }
            $model->$field = $value;
            try{
                $save = $model->save();
                if($value==1){
                    $this->modelClass->where('id','<>',$id)->update(['status'=>0]);
                    $this->wxapp->menu->delete();
                    $menu_data =$this->menuData(json_decode($model->menu_data,true));
                    $this->wxapp->menu->create($menu_data);
                }
            }catch(\Exception $e){
                $this->error(lang($e->getMessage()));
            }
            $save ? $this->success(lang('Modify success')) :  $this->error(lang("Modify Failed"));
        }else{
            $this->error(lang('Invalid data'));
        }
    }

    protected function menuData($menu_data){
        $data = [];
        foreach($menu_data as $k=>&$v){
            $data[$k]['type'] = $v['type'];
            $data[$k]['name'] = $v['name'];
            switch($v['type']){
                case 'click':
                    $data[$k]['key'] = $v['key'];
                    if(!$v['key']) {throw new \Exception('第'.$k.'个菜单key不能为空'); };
                    break;
                case 'view':
                    $data[$k]['url'] = $v['url'];
                    if(!$v['url']) {throw new \Exception('第'.$k.'个菜单url不能为空') ;};
                    break;
                case 'miniprogram':
                    $data[$k]['appid'] = $v['appid'];
                    $data[$k]['pagepath'] = $v['pagepath'];
                    if(!$v['appid'] || !$v['pagepath']) {throw new \Exception('第'.$k.'个菜单appid或pagepath不能为空') ;};
                    break;
                case 'scancode_waitmsg':
                    $data[$k]['key'] = 'rselfmenu_0_0';
                    break;
                case 'scancode_push':
                    $data[$k]['key'] = 'rselfmenu_0_1';
                    break;
                case 'location_select':
                    $data[$k]['key'] = 'rselfmenu_2_0';
                    break;
                case 'pic_sysphoto':
                    $data[$k]['key'] = 'rselfmenu_1_0';
                    break;
                case 'pic_photo_or_album':
                    $data[$k]['key'] = 'rselfmenu_1_1';
                    break;
                case 'pic_weixin':
                    $data[$k]['key'] = 'rselfmenu_1_2';
                    break;
            }
            if(isset($v['sub_button']) && $v['sub_button']){
                foreach($v['sub_button'] as $key=>$val){
                    $data[$k]['sub_button'][$key]['type'] =  $val['type'];
                    $data[$k]['sub_button'][$key]['name'] =  $val['name'];
                    switch($val['type']){
                        case 'click':
                            $data[$k]['sub_button'][$key]['key'] = $val['key'];
                            if(!$val['key']) {throw new \Exception('第'.$k.'个菜单第'.$key.'个子菜单key不能为空'); };
                            break;
                        case 'view':
                            $data[$k]['sub_button'][$key]['url'] = $val['url'];
                            if(!$val['url']) {throw new \Exception('第'.$k.'个菜单第'.$key.'个子菜单url不能为空'); };
                            break;
                        case 'miniprogram':
                            $data[$k]['sub_button'][$key]['appid'] = $val['appid'];
                            $data[$k]['sub_button'][$key]['pagepath'] = $val['pagepath'];
                            if(!$val['appid'] || !$val['pagepath']) {throw new \Exception('第'.$k.'个菜单第'.$key.'个子菜单appid或pagepath不能为空'); };
                            break;
                        case 'scancode_waitmsg':
                            $data[$k]['sub_button'][$key]['key'] = 'rselfmenu_0_0';
                            break;
                        case 'scancode_push':
                            $data[$k]['sub_button'][$key]['key'] = 'rselfmenu_0_1';
                            break;
                        case 'location_select':
                            $data[$k]['sub_button'][$key]['key'] = 'rselfmenu_2_0';
                            break;
                        case 'pic_sysphoto':
                            $data[$k]['sub_button'][$key]['key'] = 'rselfmenu_1_0';
                            break;
                        case 'pic_photo_or_album':
                            $data[$k]['sub_button'][$key]['key'] = 'rselfmenu_1_1';
                            break;
                        case 'pic_weixin':
                            $data[$k]['sub_button'][$key]['key'] = 'rselfmenu_1_2';
                            break;
                    }
                }
            }
        }
        return $data;
    }



}