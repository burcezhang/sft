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

use app\wechat\model\WechatFans;
use app\wechat\model\WechatTags;
use app\common\annotation\NodeAnnotation;
use think\App;

/**
 * @ControllerAnnotation("Fans")
 * Class Fans
 * @package addons\wechat\backend\controller
 */
class Fans extends WxBase {

    public function __construct(App $app) {
        parent::__construct($app);
        $this->modelClass = new WechatFans();
    }

    /**
     * @NodeAnnotation("list")
     * @return array|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
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
            $tag = WechatTags::select()->toArray();
            foreach ($list as $k=>$v){
                $list[$k]['tags_list'] = $tag;
            }
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => $count];
            return json($result);
        }
        return view();
    }

    /**
     * @NodeAnnotation("同步粉丝")
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function aysn(){
        $usersOpenidList =$this->wxapp->user->list();
        $users = [];
        if($usersOpenidList['total']>0){
            foreach ($usersOpenidList['data']['openid'] as $k=>$v){
                $users[$k] =  $this->wxapp->user->get($v);
            }
        }
        foreach ($users as $k=>$v){
            $v['tagid_list'] = json_encode($v['tagid_list']);
            $list = $this->modelClass
                ->where('openid',$v['openid'])
                ->find();
            try {
                if($list){
                    $list->save($v);
                }else{
                    $model = clone $this->modelClass;
                    $model->save($v);
                }
            }catch (\Exception  $e){
                $this->error($e->getMessage());
            }
        }
        $this->success('同步成功');
    }
    /**
     * @NodeAnnotation("edit")
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit(){
        $id = $this->request->param('id');
        $list = $this->modelClass->find($id);
        if($this->request->isAjax()){
            $tags_id = $this->request->post('tags');
            $tags = WechatTags::cache(3600)->find($tags_id);
            $list->tags = $tags->name;
            $res = $this->wxapp->user_tag->tagUsers([$list->openid],$tags['tags_id']);
            if($res){
                $list->save();
                $this->success(lang('update success'));
            }else{
                $this->error(lang('update fail'));
            }
        }
        $tagsList =  WechatTags::select();
        $view = ['formData'=>$list,'tagsList'=>$tagsList];
        return view('add',$view);
    }

}
