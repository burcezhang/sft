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

use app\common\controller\AddonsBackend;
use app\wechat\model\WechatTags;
use addons\wechat\backend\validate\WxTags;
use think\App;
use think\facade\View;

class Tags extends WxBase
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new WechatTags();
    }

    /**
     * @NodeAnnotation ("add")
     * @return \think\response\View
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $data = $this->request->post();
            if ($data) {
//                $this->validate($data,WxTags::class);
                $res = $this->wxapp->user_tag->create($data['name']);
                $data['tags_id'] = $res['tag']['id'];
                if ($res) {
                    $this->modelClass->save($data);
                    $this->success(lang('operation success'));
                } else {
                    $this->error(lang('operation fail'));
                }
            } else {
                $this->error(lang('operation fail'));
            }
        }
        $view = [
            'title' => 'Add',
            'formData' => '',
        ];
        return view('', $view);
    }

    /**
     * @NodeAnnotation ("edit")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit()
    {
        $id = $this->request->param('id');
        $list = $this->modelClass->find($id);
        if ($this->request->isAjax()) {
            $data = $this->request->post();
            $res = $this->wxapp->user_tag->update($list['tags_id'], $data['name']);
            if ($res['errcode'] == 0) {
                $list->save($data);
                $this->success(lang('operation success'));
            } else {
                $this->error(lang('operation fail'));
            }
        }
        $view = [
            'title' => 'edit',
            'formData' => $list,
        ];
        return view('add', $view);

//        $this->wxapp->user_tag->update($tagId, $name);
    }
    /**
     * @NodeAnnotation("同步标签")
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function aysn()
    {
        if($this->request->isAjax()){
            $tags = $this->wxapp->user_tag->list();

            foreach($tags['tags'] as $k=>$v){
                $list =  $this->modelClass->where('tags_id',$v['id'])->find();
                $v['tags_id'] = $v['id'];
                unset($v['id']);
                if($list){
                    $list->save($v);
                }else{
                    $model = clone $this->modelClass;
                    $model->save($v);
                }
            }
            $this->success(lang('operation success'));
        }
        $this->error(lang('invalid options'));
    }
    /**
     * @NodeAnnotation ('delete')
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        $id = $this->request->param('id');
        $list = $this->modelClass->find($id);
        $res = $this->wxapp->user_tag->delete($list['tags_id']);
        if ($res['errcode'] == 0) {
            $this->modelClass->destroy($id, true);
        } else {
            $this->error(lang('delete fai'));
        }
        $this->success(lang('operation success'));

    }

}