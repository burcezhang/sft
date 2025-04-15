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
 * Date: 2021/10/5
 * Time: 15:48
 */

namespace app\apidoc\controller;

use app\common\controller\Frontend;
use think\App;
use think\console\Input;
use think\facade\Request;
use think\facade\View;
use app\apidoc\service\Doc;
use app\apidoc\service\Parser;

class Index extends Frontend
{

    protected $layout = false;
    protected $request; # Request 实例
    protected $view;    # 视图类实例

    # 资源类型
    protected $mimeType = [
        'xml'  => 'application/xml,text/xml,application/x-xml',
        'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js'   => 'text/javascript,application/javascript,application/x-javascript',
        'css'  => 'text/css',
        'rss'  => 'application/rss+xml',
        'yaml' => 'application/x-yaml,text/yaml',
        'atom' => 'application/atom+xml',
        'pdf'  => 'application/pdf',
        'text' => 'text/plain',
        'png'  => 'image/png',
        'jpg'  => 'image/jpg,image/jpeg,image/pjpeg',
        'gif'  => 'image/gif',
        'csv'  => 'text/csv',
        'html' => 'text/html,application/xhtml+xml,*/*',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $config = get_addons_config(app('http')->getName());
        $info = get_addons_info(app('http')->getName());
        if(!$config['status']['value']){
            exit('已禁止访问');
        }
        if(!$info['status']){
            exit('已禁止访问');
        }        //有些程序配置了默认json问题
        config('default_return_type', 'html');
        $this->doc = new Doc((array)\think\facade\Config::get('apidoc'));
        if (session('doc.is_login') !== $this->doc->__get('password')
            && $this->doc->__get('password')
            && $this->request->action() !== 'login'
        ) {
            session('doc.request_url', get_url());
            $url= __u('index/login');
            header('location:'.$url);
            exit();
        }
        View::assign('web', $this->doc->__get());
        // 序言文档
        View::assign('document', $this->doc->__get('document'));
        // 版本号
        View::assign('versions', $this->doc->__get('controller'));
        // 左侧菜单
        View::assign('menu', $this->doc->get_api_list(input('version', 0, 'intval')));
    }

    public function index()
    {
        $this->redirect(__u('index/document',['name'=>'explain']));
    }
    public function module()
    {
        $name = Input('name');
        if (class_exists($name)) {
            $reflection = new \ReflectionClass($name);
            $doc_str    = $reflection->getDocComment();
            $doc        = new Parser();
            # 解析类
            $class_doc = $doc->parse_class($doc_str);
            View::assign('data', $class_doc);
        }
        return View::fetch('module');
    }

    public function action()
    {
        $name = input('name');
        if ($this->request->isAjax()) {
            list($class, $action) = explode("::", $name);
            $data = $this->doc->get_api_detail($class, $action);
            # 全局header
            $data['_header'] = $this->doc->__get('header');
            # 全局参数
            $data['_params'] = $this->doc->__get('params');
            $this->result($data, 0, 'SUCCESS');
        } else {
            return view('action');
        }
    }

    public function document()
    {
        $name = input('name');
        View::assign('data', $this->doc->__get('document')[$name]);
        return  view('doc_' . $name);
    }

    // debug 格式化参数
    public function formatParams()
    {
        $header           = $this->format($this->request->param('header'));
        $header["Cookie"] = $this->request->param('cookie');
        $params           = $this->format($this->request->param('params'));
        $this->success('ok','',['params' => $params, 'header' => $header]);
    }

    private function format($data = [])
    {
        if (!$data || count($data) < 1) {
            return [];
        }
        $result = [];
        foreach ($data['name'] as $k => $v) {
            $result[$v] = $data['value'][$k];
        }
        return $result;
    }


    public function login()
    {
        if ($this->request->isPost()) {
            if (input('post.password') != $this->doc->__get('password')) {
                $this->error('您输入的密码不正确');
                exit();
            } else {
                session('doc.is_login', input('post.password'));
                $url = __u('index/index');
                $this->success('登录成功', session('doc.request_url') ?: $url);
            }
        } else {
            if (session('doc.is_login') == $this->doc->__get('password')) {
                $url = __u('index/index');
                header('location:'.$url);
            } else {
                return View::fetch('login');
            }
        }

    }


}
