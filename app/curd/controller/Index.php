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
 * Time: 15:48
 */

namespace app\curd\controlLer;

use app\curd\model\Curd as CurdModel;
use app\common\controller\Backend;
use fun\helper\FileHelper;
use think\App;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use think\console\Input;
use think\facade\Config;
use think\facade\Console;
use think\facade\Db;
use think\helper\Str;
use think\facade\View;

/**
 * @ControllerAnnotation('Index')
 * @package addons\curd\backend\controlLer
 */
class Index extends Backend
{
    protected $layout = '../../backend/view/layout/main';
    protected $databasePrefix = '';
    protected $systemTable = [
        "addon",
        "addons_curd",
        "admin",
        "admin_log",
        "attach",
        "auth_group",
        "auth_rule",
        "blacklist",
        "config",
        "config_group",
        "field_type",
        "field_verify",
        "languages",
        "member",
        "member_account",
        "member_address",
        "member_group",
        "member_level",
        "member_third",
        "oauth2_client",
        "provinces",
    ];

    public $methodList=  [
        ['id'=>'index','name'=>'index'],
        ['id'=>'add','name'=>'add'],
        ['id'=>'edit','name'=>'edit'],
        ['id'=>'destroy','name'=>'destroy'],
        ['id'=>'delete','name'=>'delete'],
        ['id'=>'destroy','name'=>'destroy'],
        ['id'=>'delete','name'=>'delete'],
        ['id'=>'import','name'=>'import'],
        ['id'=>'export','name'=>'export'],
        ['id'=>'recycle','name'=>'recycle'],
        ['id'=>'restore','name'=>'restore'],
    ];
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new CurdModel();
        $this->databasePrefix = config('database.connections.' . config('database.default') . '.prefix');
        foreach ($this->systemTable as &$item) {
            $item = $this->databasePrefix . $item;
        }
        unset($item);
    }

    public function index()
    {
        $this->relationSearch = true;
        if ($this->request->isAjax()) {
            list($this->page, $this->pageSize, $sort, $where) = $this->buildParames();
//            var_dump($this->page, $this->pageSize, $sort, $where);
            $count = $this->modelClass
                ->withJoin(['admin'])
                ->where($where)
                ->count();
            $list = $this->modelClass
                ->withJoin(['admin'])
                ->where($where)
                ->order($sort)
                ->page($this->page, $this->pageSize)
                ->select();
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => $count];
            return json($result);
        }
        return view();
    }

    /**
     * @NodeAnnotation('List')
     * @return \think\response\View
     */
    public function add()
    {
        $driver = array_values(array_keys(Config::get('database.connections')));
        $driver = array_combine($driver, $driver);
        $sql = "show tables";
        $list = Db::query($sql);
        $list = array_column($list, 'Tables_in_' . $this->modelClass->get_databasename());
        $table = [];
        foreach ($list as $k => $v) {
            if (!in_array($v, $this->systemTable)) {
                $table[$k] = $v;
            }
        }
        sort($table);
        $controllerList = $this->getController();
        $table = array_combine(array_values($table), $table);
        $list = array_combine(array_values($list), $list);
        if ($this->request->isPost()) {
            $config = get_addons_config(app()->http->getName());
            if (!$config['status']['value']) {
                $this->error('功能被禁用了~~');
            }
            $post = $this->request->post();
            if (input('type') == 1) {
                $action = 'curd';
                unset($post['type']);
                $arr = [];
                $curd = '';
                foreach ($post as $k => $v) {
                    if ($k == '__token__') continue;
                    if ($v === '') continue;
                    if ($k === 'joinTable') {
                        $v = str_replace($this->modelClass->get_table_prefix(), '', $v);
                    }
                    if (is_array($v)) {
                        foreach ($v as $kk => $vv) {
                            $arr[] = ['--' . $k, $vv];
                            $curd .= '--' . $k . '=' . $vv . ' ';
                        }
                    } else {
                        $arr[] = ['--' . $k, $v];
                        $curd .= '--' . $k . '=' . $v . ' ';
                    }
                }
                $result = [];
                array_walk_recursive($arr, function ($value) use (&$result) {
                    array_push($result, $value);
                });
                $output = Console::call('curd', $result);
            } else {
                $action = 'menu';
                $controller = input('controllers');
                $controllersArr = explode('@', $controller);
                $app = reset($controllersArr);
                $info = get_addons_info($app);
                if ($info) {
                    $arr = explode('/', $controllersArr[1]);
                    $result = [
                        '--controller', str_replace('.', '/', array_pop($arr)),
                        '--addon', $app,
                        '--app', '',
                        '--force', input('force'),
                        '--delete', input('delete'),
                    ];
                } else {
                    $result = [
                        '--controller', str_replace('.', '/', $controller),
                        '--addon', '',
                        '--app', $app,
                        '--force', input('force', 0),
                        '--delete', input('delete', 0),
                    ];
                }
                $curd = implode(' ', $result);
                $output = Console::call('menu', $result);
            }
            $content = $output->fetch();
            if (strpos($content, 'success')) {
                try {
                    $data['curd'] = "php think $action " . $curd;
                    $data['post_json'] = json_encode($post, JSON_UNESCAPED_UNICODE);
                    $data['admin_id'] = session('admin.id');
                    $save = $this->modelClass->save($data);
                } catch (\Exception $e) {
                    $this->error(lang($e->getMessage()));
                }
                $this->success(lang('make success'));
            }
            $this->error($content);
        }
        if ($this->request->isGet() && $this->request->isAjax()) {
            $driver = $this->request->param('driver');
            $type = $this->request->param('type');
            $table = $this->request->param('tablename');
            $database = Config::get('database.connections.' . $driver . '.database');
            $sql = "show tables";
            if ($type == 1) {//驱动
                $list = Db::connect($driver)->query($sql);
                $tableList = array_column($list, 'Tables_in_' . $database);
                $data = ['table' => $tableList];
                $this->success('', '', $data);
            }
            if ($type == 2 || $type == 3) {//选择主表
                $jointable = $this->request->param('jointablename');
                $sql = "select COLUMN_NAME from information_schema . columns  where table_name = '" . $table . "' and table_schema = '" . $database . "'";
                $tableField = Db::connect($driver)->query($sql);
                $dataFileds = [];
                $dataMFileds = [];
                foreach ($tableField as $k => $v) {
                    $dataMFileds[$k]['id'] = $v['COLUMN_NAME'];
                    $dataMFileds[$k]['title'] = $v['COLUMN_NAME'];
                    $dataFileds[] = $v['COLUMN_NAME'];
                }
                $sql = "select COLUMN_NAME from information_schema . columns  where table_name = '" . $jointable . "' and table_schema = '" . $database . "'";
                $jointableField = Db::connect($driver)->query($sql);
                $joindataFileds = [];
                $joindataMFileds = [];
                foreach ($jointableField as $k => $v) {
                    $joindataMFileds[$k]['id'] = $v['COLUMN_NAME'];
                    $joindataMFileds[$k]['title'] = $v['COLUMN_NAME'];
                    $joindataFileds[] = $v['COLUMN_NAME'];
                }
                $data = ['fields_table' => $dataFileds,'mfields_table'=>$dataMFileds, 'fields_join' => $joindataFileds,'mfields_join'=>$joindataMFileds];
                $this->success('', '', $data);
            }
            if ($type == 4) {//增加table
                $list = Db::connect($driver)->query($sql);
                $tableList = array_column($list, 'Tables_in_' . $database);
                $sql = "select COLUMN_NAME from information_schema . columns  where table_name = '" . $table . "' and table_schema = '" . $database . "'";
                $tableField = Db::connect($driver)->query($sql);
                $dataFileds = [];
                foreach ($tableField as $k => $v) {
                    $dataFileds[] = $v['COLUMN_NAME'];
                }
                $data = ['table' => $tableList, 'fields' => $dataFileds];
                $this->success('', '', $data);
            }

        }
        $formData = [];
        $id = input('id');
        if ($id) {
            $formData['table'] = $id;
        }
        $view = ['table' => $table, 'driver' => $driver, 'list' => $list, 'formData' => $formData, 'controllerList' => $controllerList];
        return view('add', $view);
    }

    /**
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function execute()
    {
        $id = input('id');
        if ($id && $this->request->isPost()) {
            $data = $this->modelClass->find($id);
            $data = $data->toArray();
            $data['admin_id'] = $data['admin_id'] ?  : 0;
            unset($data['id']);
            unset($data['create_time']);
            unset($data['update_time']);
            $post = json_decode($data['post_json'],true);
            $curd = '';
            foreach ($post as $k => $v) {
                if ($k == '__token__') continue;
                if ($v === '') continue;
                if ($k=='delete') continue;
                if ($k=='force') continue;
                if ($k=='jump') continue;
                if ($k=='delete') continue;
                if ($k === 'joinTable') {
                    $v = str_replace($this->modelClass->get_table_prefix(), '', $v);
                }
                if ($k === 'joinTable') {
                    $v = str_replace($this->modelClass->get_table_prefix(), '', $v);
                }
                if (is_array($v)) {
                    foreach ($v as $kk => $vv) {
                        $arr[] = ['--' . $k, $vv];
                        $curd .= '--' . $k . '=' . $vv . ' ';
                    }
                } else {
                    $arr[] = ['--' . $k, $v];
                    $curd .= '--' . $k . '=' . $v . ' ';
                }


            }
            $arr[] = ['--' . 'force', '1'];
            $arr[] = ['--' . 'jump', '0'];
            $arr[] = ['--' . 'delete', '0'];
            $curd .= '--' . 'force' . '=' . 1 . ' ';
            $curd .= '--' . 'jump' . '=' . false . ' ';
            $curd .= '--' . 'delete' . '=' . false . ' ';
            $result = [];
            array_walk_recursive($arr, function ($value) use (&$result) {
                array_push($result, $value);
            });
            $output = Console::call('curd', $result);
            $content = $output->fetch();
            if (strpos($content, 'success')) {
                $data['curd'] = $curd;
                $this->modelClass->save($data);
                refreshaddons();
                $this->success(lang('make success'));
            }
            $this->error('执行失败');
        }
        $this->error('请求错误');
    }

    /**
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delall()
    {
        $id = input('id');
        if ($id && $this->request->isPost()) {
            $data = $this->modelClass->find($id);
            $data = $data->toArray();
            $data['admin_id'] = $data['admin_id'] ?  : 0;
            unset($data['id']);
            unset($data['create_time']);
            unset($data['update_time']);
            $post = json_decode($data['post_json'],true);
            $curd = '';
            foreach ($post as $k => $v) {
                if ($k == '__token__') continue;
                if ($v === '') continue;
                if ($k=='jump') continue;
                if ($k=='force') continue;
                if ($k=='delete') continue;
                if ($k === 'joinTable') {
                    $v = str_replace($this->modelClass->get_table_prefix(), '', $v);
                }
                if ($k === 'joinTable') {
                    $v = str_replace($this->modelClass->get_table_prefix(), '', $v);
                }
                if (is_array($v)) {
                    foreach ($v as $kk => $vv) {
                        $arr[] = ['--' . $k, $vv];
                        $curd .= '--' . $k . '=' . $vv . ' ';
                    }
                } else {
                    $arr[] = ['--' . $k, $v];
                    $curd .= '--' . $k . '=' . $v . ' ';
                }
            }
            $arr[] = ['--' . 'force', '1'];
            $arr[] = ['--' . 'jump', '0'];
            $arr[] = ['--' . 'delete', '1'];
            $curd .= '--' . 'force' . '=' . 1 . ' ';
            $curd .= '--' . 'jump' . '=' . false . ' ';
            $curd .= '--' . 'delete' . '=' . true . ' ';
            $result = [];
            array_walk_recursive($arr, function ($value) use (&$result) {
                array_push($result, $value);
            });
            $output = Console::call('curd', $result);
            $content = $output->fetch();
            if (strpos($content, 'success')) {
                $data['curd'] = $curd;
                $this->modelClass->save($data);
                $this->success(lang('make success'));
            }
            $this->error('执行失败');
        }
        $this->error('请求错误');
    }


    /**
     * 文件列表
     * @return void
     */
    public function list()
    {
        if ($this->request->isAjax()) {
            $path = [
                root_path() . 'addons',
                root_path() . 'app',
                public_path() . 'static',
            ];
            $exts = ['php', 'js', 'html', 'tpl', 'txt', 'sql', 'ini'];
            $name = input('name', '');
            $filter = input('filter');
            $filter = json_decode($filter, true);
            if (!empty($filter)) {
                $name = $filter['name'];
                $exts = $filter['exts'];
            }
            $list = [];
            foreach ($path as $item) {
                $fileList = [];
                $this->getDirFilesLists($item, $fileList, $exts, $name, true);
                $list = array_merge($list, $fileList);
            }
            $count = count($list);
            $list = array_slice($list, $this->page - 1, $this->pageSize);
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => $count];
            return json($result);
        }
        return view();
    }

    public function addfile()
    {
        $id = input('id');
        $formData = [
            'id' => $id,
            'file' => '',
            'exts' => input('exts'),
            'mode' => $this->getMode(input('exts')),
            'content' => '',
        ];
        View::assign('formData', $formData);
        return view('addfile');

    }

    public function editfile()
    {
        $id = input('id');
        $file = root_path() . $id;
        if ($this->request->isAjax()) {
            if (!$id) {
                $this->error('文件名不能为空');
            }
            $content = input('content');
            if(input('type')==1){//新增
                $exts = input('exts') ;
                $file = Str::endsWith($file,'.'.$exts)?$file:$file.'.'.$exts;
                if(!file_exists($file)){
                    @touch('../'.$id.'.'.$exts);
                }
            }
            if (file_exists($file) && file_put_contents($file, $content)) {
                $this->success('保存成功');
            }
            $this->error('保存失败');
        }
        if (file_exists($file)) {
            $file = iconv('gb2312', 'utf-8', $file);
            $content = file_get_contents($file);
            $file_ext = strtolower(pathinfo($file)['extension']);
            $formData = [
                'id' => $id,
                'file' => $file,
                'exts' => $file_ext,
                'mode' => $this->getMode($file_ext),
                'content' => $content,
            ];
        } else {
            $this->error('文件不存在！');
        }
        View::assign('formData', $formData);
        return view('addfile');
    }

    public function delfile()
    {
        $id = input('id');
        $file = root_path() . $id;
        if (file_exists($file)) {
            unlink($file);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    protected function getMode($exts)
    {

        switch ($exts) {
            case 'txt':
            case 'ini':
                $mode = 'javascript';
                break;
            case 'sql':
                $mode = 'sql';
                break;
            case 'js':
                $mode = 'javascript';
                break;
            case 'css':
                $mode = 'css';
                break;
            default:
                $mode = 'php';
                break;
        }
        return $mode;
    }

    /*
     * 获取指定目录下指定文件后缀的函数
     * @$path   文件路径
     * @$ext    文件后缀名，默认为false为不指定，如果指定，请以数组方式传入
     * @$filename   使用时请提前赋值为空数组
     * @$name   文件名查找
     * @$baseurl    是否包含路径，默认包含
     */
    public function getDirFilesLists($path, &$filename, $ext = '', $name = '', $baseurl = true)
    {

        if (!$path) {
            die('请传入目录路径');
        }
        $resource = opendir($path);
        if (!$resource) {
            die('你传入的目录不正确');
        }
        //遍历目录
        $i = 0;
        while ($rows = readdir($resource)) {
            //如果指定为递归查询
            $relativePath = str_replace(root_path(), '', $path);
            if ($rows == 'vendor' && is_dir($path . '/' . $rows)) continue;
            if ($rows == 'install' && is_dir($path . '/' . $rows)) continue;
            if (strpos($path . '/' . $rows, 'static/plugins') !== false) continue;
            if (is_dir($path . '/' . $rows) && $rows != "." && $rows != "..") {
                $this->getDirFilesLists($path . '/' . $rows, $filename, $ext, $name, $baseurl);
            } elseif ($rows != "." && $rows != "..") {
                //如果指定后缀名
                if ($ext) {
                    //必须为数组
                    if (is_string($ext)) {
                        $ext = explode(',', $ext);
                    }
                    //转换小写
                    foreach ($ext as &$v) {
                        $v = strtolower($v);
                    }
                    //匹配后缀
                    $file_ext = strtolower(pathinfo($rows)['extension']);
                    if (in_array($file_ext, $ext)) {
                        if ($name && strpos($rows, $name) === false) {
                            continue;
                        }
                        //是否包含路径
                        if ($baseurl) {
                            $filePath = $relativePath . '/' . $rows;
                        } else {
                            $filePath = $rows;
                        }
                        $filename[$i]['id'] = $filePath;
                        $filename[$i]['name'] = $rows;
                        $filename[$i]['path'] = $filePath;
                        $filename[$i]['size'] = format_bytes(filesize($path . '/' . $rows));
                        $filename[$i]['mtime'] = date('Y-m-d H:i:s', filemtime($path . '/' . $rows));
                        $filename[$i]['exts'] = strtolower(substr($rows, strrpos($rows, '.') - strlen($rows)));

                    }
                } else {
                    if ($name && strpos($rows, $name) === false) {
                        continue;
                    }
                    if ($baseurl) {
                        $filePath = $relativePath . '/' . $rows;
                    } else {
                        $filePath = $rows;
                    }
                    $filename[$i]['id'] = $filePath;
                    $filename[$i]['name'] = $rows;
                    $filename[$i]['path'] = $filePath;
                    $filename[$i]['size'] = format_bytes(filesize($path . '/' . $rows));
                    $filename[$i]['mtime'] = date('Y-m-d H:i:s', filemtime($path . '/' . $rows));
                    $filename[$i]['exts'] = strtolower(substr($rows, strrpos($rows, '.') - strlen($rows)));
                }
            }
            $i++;
        }
        return $filename;

    }

    /**
     * 获取所有的控制器
     */
    protected function getController()
    {
        //查询**模块所有控制器
        $backendPath = root_path() . 'app' . DS . 'backend' . DS . 'controller' . DS;
        $addonsPath = root_path() . 'addons' . DS;
        //配置
        $scanBackendPath = scandir($backendPath);
        $scanAddonsPath = scandir($addonsPath);
        $addonsControllers = [];
        foreach ($scanAddonsPath as $name) {
            if (in_array($name, ['.', '..'])) continue;
            $controllerPath = $addonsPath . $name . DS . 'app' . DS . $name . DS . 'controller' . DS;
            if (is_dir($controllerPath)) {
                foreach (scandir($controllerPath) as $mdir) {
                    if (in_array($mdir, ['.', '..'])) continue;
                    //路由配置文件
                    if (is_file($controllerPath . $mdir)) {
                        $mdir = ucfirst(Str::studly($mdir));
                        $keys = $name . '@' . str_replace('.php', '', $mdir);
                        $addonsControllers[$keys] = $keys;
                    } else {
                        foreach (scandir($controllerPath) as $mdir) {
                            if (in_array($mdir, ['.', '..'])) continue;
                            //路由配置文件
                            if (is_file($controllerPath . $mdir)) {
                                $mdir = ucfirst(Str::studly($mdir));
                                $keys = $name . '@' . $mdir . str_replace('.php', '', $mdir);
                                $addonsControllers[$keys] = $keys;
                            }
                        }
                    }
                }
            }
        }
        $controllers = [];
        foreach ($scanBackendPath as $key => $name) {
            if (in_array($name, ['.', '..'])) continue;
            if (is_file($backendPath . $name)) {
                $name = Str::snake($name);
                $keys = 'backend@' . str_replace('.php', '', $name);
                $addonsControllers[$keys] = $keys;
            } else {
                $module_dir = $backendPath . $name . DS;
                foreach (scandir($module_dir) as $mdir) {
                    if (in_array($mdir, ['.', '..'])) continue;
                    if (is_file($backendPath . $name . DS . $mdir)) {
                        $mdir = ucfirst(Str::studly($mdir));
                        $keys = 'backend@' . str_replace('.php', '', $name . '/' . $mdir);
                        $addonsControllers[$keys] = $keys;
                    }
                }
            }
        }
        return array_merge($addonsControllers, $controllers);
    }

    /**
     * 根据文件夹获取文件中内容
     * @param $path 扫描路径目录
     * @return array
     */
//    protected  function getFileListByDir($path)
//    {
//        $_path = $this->dealPath($path, 'u2g');
//        $dirs = scandir($_path);
//        $result = [];
//        foreach($dirs as $dir_name){
//            if($dir_name !== '.' && $dir_name !== '..' && $dir_name!=='vendor') {
//                $rows = [];
//                $rows['name'] = $this->dealPath($dir_name, 'g2u');
//                $rows['path'] = $path;
//                $rows['time'] = date('Y-m-d H:i:s', filemtime($_path . '/' . $rows['name']));;
//                if( is_dir( $_path.'/'.$dir_name ) ){
//                    $rows['type'] = 'dir';
//                    $rows['children'] = [];
//                    $rows['size'] = FileHelper::getDirSize($_path.'/'.$rows['name']);
//                    $rows['exts'] = '';
//                }else{
//                    $rows['type'] = 'file';
//                    $rows['size'] = format_bytes(filesize($_path . '/' . $rows['name']));
//                    $rows['exts'] = strtolower(substr($rows['name'], strrpos($rows['name'], '.') - strlen($rows['name'])));
//
//                }
//                $result[] = $rows;
//            }
//        }
//        $arr = array_column($result, 'type');
//        array_multisort($arr , SORT_DESC, $result);
//        return $result;
//    }
//
//    /**
//     * 如果是win系统, 处理路径
//     * @param $path
//     * @param bool $G2U 国标码转为UTF-8
//     * @return bool|mixed|string
//     */
//    protected  function dealPath($path, $type = '')
//    {
//        static $is_win;
//        if($is_win === null){
//            $is_win = (strtoupper(substr(PHP_OS,0,3))==='WIN')?true:false;
//        }
//        if(!$is_win){
//            return $path;
//        }
//
//        $path = str_replace("\\",'/',$path);
//
//        if($type == 'u2g'){
//            $path = $this->iconv_to($path,'UTF-8','GBK');
//        }else if($type == 'g2u'){
//            // 判断编码是否为utf-8
//            if(!mb_detect_encoding($path, 'UTF-8', true)){
//                $path = $this->iconv_to($path,'GBK','UTF-8');
//            }
//        }
//        return $path;
//    }
//    protected  function iconv_to($str,$from,$to){
//        if (!function_exists('iconv')){
//            return $str;
//        }
//
//        if(function_exists('mb_convert_encoding')){
//            $result = @mb_convert_encoding($str,$to,$from);
//        }else{
//            $result = @iconv($from, $to, $str);
//        }
//        if(strlen($result)==0){
//            return $str;
//        }
//        return $result;
//    }
}
