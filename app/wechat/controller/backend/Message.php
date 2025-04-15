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


use app\wechat\controller\backend\WxBase;
use app\wechat\model\WechatMaterial;
use app\wechat\model\WechatMaterialInfo;
use app\wechat\model\WechatReply;
use app\wechat\model\WechatMessage;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use app\common\annotation\ControllerAnnotation;
use app\common\annotation\NodeAnnotation;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/**
 * @ControllerAnnotation("消息")
 * Class Message
 */
class Message extends WxBase
{
    public function __construct(\think\App $app)
    {
        parent::__construct($app);
        $this->modelClass = new WechatMessage();
    }

    /**
     * @NodeAnnotation('reply')
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reply()
    {
        if ($this->request->isPost()) {
            $material_id = $this->request->post('material_id');
            $openid = $this->request->post('openid');
            $type = $this->request->post('type');
            $material = WechatMaterial::find($material_id);
            if ($material) {
                $media_id = $material->media_id;
            } else {
                $media_id = '';
            }
            $message = WechatReply::where('type', 'default')->value('data');
            switch ($type) {
                case 'text':
                    $data = $this->request->post('data');
                    $message = new Text($data);
                    break;
                case 'image':
                    if (WechatMaterial::where('media_id', $media_id)->find()) {
                        $message = new Image($media_id);
                    }
                    break;
                case 'news':
                    $new = WechatMaterialInfo::where('material_id', $material_id)->find();
                    if ($new) {
                        $newsList[] = new NewsItem([
                            'title' => $new->title,
                            'description' => $new->digest,
                            'url' => $new->url,
                            'image' => $new->cover,
                        ]);
                        $message = new News($newsList);
                    } else {
                        $message = WechatReply::where('type', 'default')->value('data');
                    }
                    break;
                case 'video':
                    if (WechatMaterial::where('media_id', $media_id)->find()) {
                        $message = new Video($media_id, $material->file_name, $material->des);
                    }
                    break;
                case 'voice':
                    if (WechatMaterial::where('media_id', $media_id)->find()) {
                        $message = new Voice($media_id);
                    }
                    break;
                default:
                    break;
            }
            $result = $this->wxapp->customer_service->message($message)->to($openid)->send();
            if ($result['errcode'] == 0) {
                $this->success(lang('send success'));
            } else {
                $this->error(lang('send fail'));
            }
        } else {
            $id = $this->request->get('id');
            $list = $this->modelClass->find($id);
            $member = $this->wxapp->user->get($list->openid);
            $materialGroup = $this->getMaterialGroup();
            $view = ['title' => lang('reply'), 'member' => $member,
                'formData' => $list, 'materialGroup' => $materialGroup];
            return view('',$view);
        }
    }
}