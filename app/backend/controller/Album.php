<?php
declare (strict_types = 1);

namespace app\backend\controller;

use think\Request;
use think\App;
use think\facade\View;
use app\backend\model\Album as AlbumModel;
use app\common\annotation\NodeAnnotation;
use app\common\annotation\ControllerAnnotation;

/**
 * @ControllerAnnotation (title="楼盘相册")
 */
class Album extends \app\common\controller\Backend
{
    protected $pageSize = 15;
    protected $layout = 'layout/main';

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new AlbumModel();
        View::assign('typeList',$this->modelClass->getTypeList());
        View::assign('statusList',$this->modelClass->getStatusList());


    }

    /**
     * @NodeAnnotation (title="add")
     * @return \think\response\View
     */
    public function add()
    {
        if (request()->isPost()) {
            $post = request()->post();
            foreach ($post as $k=>$v){
                if(is_array($v)){
                    $post[$k] = implode(',',$v);
                }
            }
            $rule = [];
            try {
                $this->validate($post, $rule);
            }catch (\ValidateException $e){
                $this->error(lang($e->getMessage()));
            }
            try {
                $save = $this->modelClass->save($post);
            } catch (\Exception $e) {
                $this->error(lang($e->getMessage()));
            }
            $save ? $this->success(lang('operation success')) : $this->error(lang('operation failed'));
        }
        $view = [
            'formData' => '',
            'title' => lang('Add'),
        ];

        View::assign('propertyInfo',$this->modelClass->getPropertyInfoN());

        return view('',$view);
    }

    /**
     * @NodeAnnotation(title="edit")
     * @return \think\response\View
     */
    public function edit()
    {
        $id = request()->param($this->modelClass->getPk());
        $list = $this->findModel($id);
        if(empty($list)) $this->error(lang('Data is not exist'));
        if (request()->isPost()) {
            $post = request()->post();
            $rule = [];
            try {
                $this->validate($post, $rule);
            }catch (\ValidateException $e){
                $this->error(lang($e->getMessage()));
            }
            foreach ($post as $k=>$v){
                if(is_array($v)){
                    $post[$k] = implode(',',$v);
                }
            }
            try {
                $save = $list->save($post);
            } catch (\Exception $e) {
                $this->error(lang($e->getMessage()));
            }
            $save ? $this->success(lang('operation success')) : $this->error(lang('operation failed'));
        }
        $view = ['formData'=>$list,'title' => lang('Add'),];

        View::assign('propertyInfo',$this->modelClass->getPropertyInfoN());

        return view('add',$view);
    }


        /**
    * @NodeAnnotation(title="List")
    */
    public function index()
    {
        $this->relationSearch = true;
        if ($this->request->isAjax()) {
            if ($this->request->param('selectFields')) {
                $this->selectList();
            }
            list($this->page, $this->pageSize,$sort,$where) = $this->buildParames();
            $list = $this->modelClass
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



    /**
    * @NodeAnnotation(title="Recycle")
    */
    public function recycle()
    {
        $this->relationSearch = true;
        if ($this->request->isAjax()) {
            if ($this->request->param('selectFields')) {
                $this->selectList();
            }
            list($this->page, $this->pageSize,$sort,$where) = $this->buildParames();
            $list = $this->modelClass->onlyTrashed()
                ->where($where)
                ->withJoin(['propertyInfo'])
                ->order($sort)
                ->paginate([
                    'list_rows'=> $this->pageSize,
                    'page' => $this->page,
                ]);
            $result = ['code' => 0, 'msg' => lang('Get Data Success'), 'data' => $list->items(), 'count' =>$list->total()];
            return json($result);
        }
        return view('index');
    }





}

