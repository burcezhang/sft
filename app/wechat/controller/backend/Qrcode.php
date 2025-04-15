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

use app\wechat\model\WechatQrcode;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use think\App;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;

/**
 * @ControllerAnnotation('Qrcode')
 * Class Qrcode
 * @package addons\wechat\backend\controller
 */
class Qrcode extends WxBase {

    public function __construct(App $app){
        parent::__construct($app);
        $this->modelClass = new WechatQrcode();
    }
    /**
     * @NodeAnnotation('add')
     * @return \think\response\View
     */
    public function add(){
        if($this->request->isAjax()){
            $data = $this->request->param();
            if($data['type']==0){
                $data['expire_seconds'] = $data['expire_seconds']?:2592000;
                $res = $this->wxapp->qrcode->temporary('foo',$data['expire_seconds']);
            }else{
                $res = $this->wxapp->qrcode->forever('foo');// 或者 $app->qrcode->forever(56);
            }
            $this->showError($res);
            $data['ticket'] = $res['ticket'];
            $data['url'] = $res['url'];
            $data['qrcode'] = $this->wxapp->qrcode->url($res['ticket']);
            if($this->modelClass->save($data)){
                $this->success(lang('add success'));
            }else{
                $this->error(lang('add fail'));
            }
        }
        $view = ['formData'=>''];
        return view('',$view);
    }

    public function recycle()
    {
        if (request()->isAjax()) {
            list($this->page, $this->pageSize,$sort,$where) = $this->buildParames();
            $list = $this->modelClass->onlyTrashed()->where($where)
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