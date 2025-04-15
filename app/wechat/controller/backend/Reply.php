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
use think\App;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use app\wechat\model\WechatAccount;
use addons\wechat\backend\service\WechatService;
use fun\helper\DataHelper;

use app\wechat\model\WechatFans;
use app\wechat\model\WechatMaterialInfo;
use app\wechat\model\WechatMaterial;
use app\wechat\model\WechatMenu;
use app\wechat\model\WechatReply;
use app\wechat\model\WechatTags;

use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Article;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

/**
 * @ControllerAnnotation('回复')
 * Class Reply
 * @package addons\wechat\backend\controller
 */
class Reply extends WxBase
{

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->modelClass = new WechatReply();
    }

    /**
     * @NodeAnnotation ('回复')
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $type = $this->request->get('type', 'default');
        $typeList = [
            [
                'url' => addons_url('index', ['type' => 'default']),
                'item' => "默认回复",
                "type" => 'default'
            ],
            ['url' => addons_url('index', ['type' => 'subscribe']),
                'item' => "关注回复",
                "type" => 'subscribe'
            ],
            ['url' => addons_url('index', ['type' => 'keyword']),
                'item' => "关键字回复",
                "type" => 'keyword'
            ],
        ];
        if ($type == 'default') {
            $list =$this->modelClass->where('type', $type)->select();
        } elseif ($type == 'subscribe') {
            $list = $this->modelClass->where('type', $type)->select();
        } elseif ($type == 'keyword') {
            $list =$this->modelClass->where('type', $type)->select();
        }
        $view = [
            'title' => '粉丝',
            'type' => $type,
            'list' => $list,
            'typeList' => $typeList,
        ];
        if($this->request->isAjax()){
            $result = ['code' => 0, 'msg' => lang('operation success'), 'data' => $list, 'count' => count($list)];
            return json($result);
        }
        return view('',$view);
    }

    /**
     * @NodeAnnotation ('add')
     * @return \think\response\View
     */

    public function add()
    {
        if ($this->request->isAjax()) {
            $data = $this->request->post();
            if (!$data['type']) {
                $data['type'] = 'keyword';
            }
            if ($data['data']) {
                $data['msg_type'] = 'text';
            }
            $res = $this->modelClass->save($data);
            if ($res) {
                $this->success(lang('add success'), addons_url('index'));
            } else {
                $this->error(lang('add fail'));
            }
        }
        $view = [
            'info' => [],
            'title' => lang('add'),
            'materialGroup' => $this->getMaterialGroup(),
        ];
        return view('', $view);
    }
    public function edit()
    {
        $list = WechatReply::find($this->request->get('id'));
        if ($this->request->isAjax()) {
            $data = $this->request->post();
            $res = $list->save($data);
            if ($res) {
                $this->success(lang('operation success'), addons_url('index'));
            } else {
                $this->error(lang('edit fail'));
            }
        }
        $view = [
            'formData' => $list,
            'title' => lang('edit'),
            'materialGroup' => $this->getMaterialGroup(),
        ];
        return view('add', $view);
    }

}