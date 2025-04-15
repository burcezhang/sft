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
use EasyWeChat\Kernel\Messages\Article;
use app\common\annotation\NodeAnnotation;
use app\common\annotation\ControllerAnnotation;
use fun\helper\DataHelper;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;

/**
 * Class Material
 */
class Material extends WxBase
{
    //图片（image）: 2M，支持bmp/png/jpeg/jpg/gif格式
    //
    //语音（voice）：2M，播放长度不超过60s，mp3/wma/wav/amr格式
    //
    //视频（video）：10MB，支持MP4格式
    //
    //缩略图（thumb）：64KB，支持JPG格式
    protected $imageValidate = [
        'image' => 'filesize:2048|fileExt:jpg,png,gif,jpeg'
    ];
    protected $videoValidate = [
        'file' => 'filesize:10240|fileExt:avi,rmvb,3gp,flv,mp4,rm'

    ];
    protected $voiceValidate = [
        'file' => 'filesize:2048|fileExt:mp3,wma,wav,amr'

    ];
    protected $thumbValidate = [
        'image' => 'filesize:64|fileExt:jpg'

    ];
    public function __construct(\think\App $app){
        parent::__construct($app);
        $this->modelClass = new WechatMaterial();
    }
    public function index()
    {
        $materialGroup = $this->getMaterialGroup();
        $view = [
            'title' => lang('material'),
            'formData' => '',
            'materialGroup' => $materialGroup,
        ];
        return view('',$view);
    }

    /**
     * @NodeAnnotation('add')
     * @return \think\response\View
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post('content');
            foreach ($data as $k => $v) {
                $article = new Article($data[$k]);
                $articles[$k] = $article;
            }
            $res = $this->wxapp->material->uploadArticle($articles);
            $this->showError($res);
            $materialData = [
                'type' => 'news',
                'media_id' => $res['media_id'],
            ];
            $material = $this->getMaterialByMediaId($res['media_id']);
            // 启动事务
            Db::startTrans();
            try {
                $this->modelClass->save($materialData);
                $id = $this->modelClass->getLastInsID();
//                if(isset($data[0])){
                //多图文
                foreach ($data as $k => $v) {
                    $data[$k]['cover'] = $this->modelClass->where('media_id', $data[$k]['thumb_media_id'])->value('media_url');
                    $data[$k]['local_cover'] =  $this->modelClass->where('media_id', $data[$k]['thumb_media_id'])->value('local_cover');
                    $data[$k]['url'] = $material['news_item'][$k]['url'];
                    $data[$k]['material_id'] = $id;
                    WechatMaterialInfo::create($data[$k]);
                }
                // 提交事务
                Db::commit();
                $this->success('成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error($e->getMessage());
            }
        }
        $view = [
            'formData' => [],
            'title' => lang('add'),
            'type' => input('type'),

        ];
        return view('add',$view);
    }
    public function edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post('content');
            $id = $this->request->post('mediaId');
            $mediaId = $this->modelClass->where('id', $id)->value('media_id');
            foreach ($data as $k => $v) {
                $article = new Article($data[$k]);
                $res = $this->wxapp->material->updateArticle($mediaId, $article, $k);
            }
            $this->showError($res);
            $material = $this->getMaterialByMediaId($mediaId);
            // 启动事务
            Db::startTrans();
            try {
                foreach ($data as $k => $v) {
                    $data[$k]['id'] = WechatMaterialInfo::where($this->where)->where('thumb_media_id', $v['thumb_media_id'])->value('id');
                    $data[$k]['cover'] = $this->modelClass->where('media_id', $data[$k]['thumb_media_id'])->value('media_url');
                    $data[$k]['url'] = $material['news_item'][$k]['url'];
                    $data[$k]['material_id'] = $id;
                    $data[$k]['update_time'] = time();
                    $AddonsWechatMaterialInfoModel = WechatMaterialInfo::find($data[$k]['id']);
                    $r[$k] = $AddonsWechatMaterialInfoModel->force()->save($data[$k]);
                }
                // 提交事务
                Db::commit();
                $this->success('成功');
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                $this->error($e->getMessage());
            }
        }
        $id = $this->request->get('id');
        $list = WechatMaterialInfo::where('material_id', $id)->select()->toArray();
        $view = [
            'title' => lang('edit'),
            'formData' => $list,
            'type' => input('type'),
        ];
        return view('add',$view);
    }

    /**
     * @NodeAnnotation ('aysn)
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function aysn()
    {
        if ($this->request->isPost()) {
            $res = cache('materialList');
            if (!$res) {
                $res = $this->wxapp->material->list('news', 0, 50);
                cache('materialList', $res, 3600);
            }
            $this->showError($res);
            foreach ($res['item'] as $k => $v) {
                $material = WechatMaterial::where('media_id', $v['media_id'])->find();
                if (!$material) {
                    $material = [
                        'media_id' => $v['media_id'],
                        'media_url' => $v['content']['news_item'][0]['thumb_url'],
                        'type' => "news",
                    ];
                    $wxmater = $this->modelClass->save($material);
                    foreach ($v['content']['news_item'] as $kk => $vv) {
                        $list = [
                            'material_id' => $wxmater->id,
                            'thumb_media_id' => $vv['thumb_media_id'],
                            'local_cover' => $vv['thumb_url'],
                            'cover' => $vv['thumb_url'],
                            'title' => $vv['title'],
                            'author' => $vv['author'],
                            'show_cover' => $vv['show_cover_pic'],
                            'digest' => $vv['digest'],
                            'content' => $vv['content'],
                            'url' => $vv['url'],
                            'content_source_url' => $vv['content_source_url'],
                            'need_open_comment' => $vv['need_open_comment'],
                            'only_fans_can_comment' => $vv['only_fans_can_comment'],
                        ];
                        WechatMaterialInfo::create($list);
                    }
                }
            }
            $this->success(lang('aysn success'));
        } else {
            $this->error(lang('invalid request'));
        }
    }
    /**
     * @NodeAnnotation('delete')
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        $id = $this->request->post('id');
        $material = WechatMaterial::find($id);
        //删除微信媒体库
        $this->wxapp->material->delete($material->media_id);
        if ($material['type'] == 'news') {
            $list = WechatMaterialInfo::where($this->where)->where('material_id', $id)->delete();
            if ($list && $material->delete()) {
                $this->success(lang('operation success'));
            } else {
                $this->error(lang('delete fail'));
            }
        } else {
            if ($material->delete()) {
                $this->success(lang('operation success'));
            } else {
                $this->error(lang('delete fail'));
            }
        }
    }

    /**
     * @NodeAnnotation ('发送消息')
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function send()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $list = WechatMaterial::find($id);
            $res = $this->sendAll($list);
            $this->showError($res);
            $this->success(lang('send success'));
        } else {
            $this->error(lang('send fail'));
        }
    }

    /**
     * @NodeAnnotation('预览消息)
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function preview()
    {
        if ($this->request->isAjax()) {
            $wxname = $this->request->post('wxname');
            $id = $this->request->post('id');
            $list = $this->modelClass->find($id);
            $res = $this->previewInfo($list, $wxname);

            $this->showError($res);
            $this->success(lang('send success'));
        } else {
            $this->error(lang('send fail'));
        }
    }

    protected function previewInfo($list, $wxname)
    {

        // 发送预览群发消息给指定的 openId 用户
        //$this->wxapp->broadcasting->previewText($text, $openId);
        //$this->wxapp->broadcasting->previewNews($mediaId, $openId);
        //$this->wxapp->broadcasting->previewVoice($mediaId, $openId);
        //$this->wxapp->broadcasting->previewImage($mediaId, $openId);
        //$this->wxapp->broadcasting->previewVideo($message, $openId);
        //$this->wxapp->broadcasting->previewCard($cardId, $openId);
        //发送预览群发消息给指定的微信号用户
        //$wxanme 是用户的微信号，比如：notovertrue
        //
        //$this->wxapp->broadcasting->previewTextByName($text, $wxname);
        //$this->wxapp->broadcasting->previewNewsByName($mediaId, $wxname);
        //$this->wxapp->broadcasting->previewVoiceByName($mediaId, $wxname);
        //$this->wxapp->broadcasting->previewImageByName($mediaId, $wxname);
        //$this->wxapp->broadcasting->previewVideoByName($message, $wxname);
        //$this->wxapp->broadcasting->previewCardByName($cardId, $wxname);

        $type = $list->type;
        switch ($type) {
            case 'text':
                $res = $this->wxapp->broadcasting->previewTextByName($list->media_id, $wxname);
                break;
            case 'image':
                $res = $this->wxapp->broadcasting->previewImageByName($list->media_id, $wxname);
                break;
            case 'news':
                $res = $this->wxapp->broadcasting->previewNewsByName($list->media_id, $wxname);
                break;
            case 'video':
                $res = $this->wxapp->broadcasting->previewVideoByName($list->media_id, $wxname);
                break;
            case 'voice':
                $res = $this->wxapp->broadcasting->previewVoiceByName($list->media_id, $wxname);
                break;
            case 'card':
                $res = $this->wxapp->broadcasting->previewCardByName($list->media_id, $wxname);
                break;
            default:
                $res = $this->wxapp->broadcasting->previewTextByName($list->media_id, $wxname);
                break;
        }
        return $res;
    }

    /**
     * 发布所有
     * @param $list
     * @return mixed
     */
    protected function sendAll($list)
    {
        $type = $list->type;
        switch ($type) {
            case 'image':
                $res = $this->wxapp->broadcasting->sendImage($list->media_id);
                break;
            case 'news':
                $res = $this->wxapp->broadcasting->sendNews($list->media_id);
                break;
            case 'video':
                $res = $this->wxapp->broadcasting->sendVideo($list->media_id);
                break;
            case 'voice':
                $res = $this->wxapp->broadcasting->sendVoice($list->media_id);
                break;
            case 'card':
                $res = $this->wxapp->broadcasting->sendCard($list->media_id);
                break;
            default:
                $res = $this->wxapp->broadcasting->image($list->media_id);
                break;
        }
        return $res;

    }
    /**
     ********************************上传素材接口***************************************
     */
    protected function uploads($type){
        //获取上传文件表单字段名
        $fileKey = array_keys(request()->file());
        for ($i = 0; $i < count($fileKey); $i++) {
            //获取表单上传文件
            $file = request()->file($fileKey[$i]);
            try {
                validate($type)->check(DataHelper::objToArray($file));
                $savename = \think\facade\Filesystem::disk('public')->putFile('uploads', $file);
                $path[] = '/storage/' . $savename;
            } catch (\think\exception\ValidateException $e) {
                $path = '';
                $error = $e->getMessage();
            }
        }
        if (!empty($path)) {
            $result['code'] = 1;
            //分辨是否截图上传，截图上传只能上传一个，非截图上传可以上传多个
            if (Request::param('responseType') == 'json') {
                $result["url"] = $path[0];
            } else {
                $result["url"] = $path[0];
            }
            $result['msg'] = lang('upload success');
            return $result;
        } else {
            //上传失败获取错误信息
            $result['url'] = '';
            $result['msg'] = $error;
            $result['code'] = 0;
            return $result;
        }
    }
    public function imageUpload(){
        $res = $this->uploads($this->imageValidate);
        if($res['code']<=0)  $this->error('上传失败');
        //路劲用相对路径
        $result = $this->wxapp->material->uploadImage('./'.$res['url']);
        $this->showError($result);
        $data  = [
            'media_id'=> $result['media_id'],
            'media_url'=> $result['url'],
            'local_cover'=> $res['url'],
            'type'=> 'image',
        ];
        $r = $this->modelClass->save($data);
        if($r){
            $this->success(lang('upload success'),'',$result);
        }else{
            $this->error('上传失败');
        }

    }
    public function videoUpload(){
        $data = Request::post();
        if(!$data['url'] || !$data['file_name'] || !$data['des']) $this->error('数据不能为空');
        $result = $this->wxapp->material->uploadVideo('.'.$data['url'],$data['file_name'],$data['des']);
        $this->showError($result);
        $resource = $this->wxapp->material->get($result['media_id']);
        $datas  = [
            'media_id'=> $result['media_id'],
            'media_url'=> $resource['down_url'],
            'local_cover'=> $data['url'],
            'type'=> 'video',
            'file_name'=> $data['file_name'],
            'des'=> $data['des'],
        ];
        $result['url'] = $resource['down_url'];
        $r = $this->modelClass->save($datas);
        if($r){
            $this->success(lang('upload success'),'',$result);
        }else{
            $this->error('上传失败');
        }
    }
    public function voiceUpload(){
        $res = $this->uploads($this->voiceValidate);
        if($res['code']>0){
            //路劲用相对路径
            $result = $this->wxapp->material->uploadVoice('./'.$res['url']);
            $this->showError($result);
            $resource = $this->wxapp->material->get($result['media_id']);
            $data  = [
                'media_id'=> $result['media_id'],
                'media_url'=> $resource['down_url'],
                'local_cover'=> $res['url'],
                'type'=> 'image',
            ];
            $result['url'] = $resource['down_url'];
            $r = $this->modelClass->save($data);
            if($r){
                $this->success(lang('upload success'),'',$result);
            }else{
                $this->error('上传失败');
            }
        }else{
            $this->error('上传失败');
        }
    }
    public function thumbUpload(){
        $res = $this->uploads($this->thumbValidate);
        if($res['code']>0){
            //路劲用相对路径
            $result = $this->wxapp->material->uploadThumb('./'.$res['url']);
            $this->showError($result);
            $data  = [
                'media_id'=> $result['media_id'],
                'media_url'=> $result['url'],
                'local_cover'=> $res['url'],
                'type'=> 'image',
            ];
            $r = $this->modelClass->save($data);
            if($r){
                $this->success(lang('upload success'),$result['url']);
            }else{
                $this->error(lang('upload fail'));
            }
        }else{
            $this->error(lang('upload fail'));
        }
    }
    public function UeditUploadImage(){
        $res = $this->uploads($this->imageValidate);
        if($res['code']>0){
            //路劲用相对路径
            $result = $this->wxapp->material->uploadImage('./'.$res['url']);
            $this->showError($result);
            $data  = [
                'media_id'=> $result['media_id'],
                'media_url'=> $result['url'],
                'local_cover'=> $res['url'],
                'type'=> 'image',
            ];
            $r = $this->modelClass->save($data);
            $data = ['state'=>'SUCCESS','url'=>$data['media_url']];
        }else{
            $data = ['state'=>'SUCCESS','url'=>''];
        }
        return json_encode($data);
    }
    public function UeditUploadVideo(){
        $res = $this->uploads($this->videoValidate);
        if($res['code']>0){
            //路劲用相对路径
            $result = $this->wxapp->material->uploadVideo('./'.$res['url'],$res['url'],$res['url']);
            $this->showError($result);
            $resource = $this->wxapp->material->get($result['media_id']);
            $data  = [
                'media_id'=> $result['media_id'],
                'media_url'=> $resource['down_url'],
                'local_cover'=> $res['url'],
                'type'=> 'video',
                'file_name'=> $res['url'],
                'des'=> $res['url'],
            ];
            $result['url'] = $resource['down_url'];
            $r = $this->modelClass->save($data);
            $data = ['state'=>'SUCCESS','url'=>$resource['down_url']];
        }else{
            $data = ['state'=>'SUCCESS','url'=>''];
        }
        return json_encode($data);
    }

    public function UeditUploaVoice(){
        $res = $this->uploads($this->voiceValidate);
        if($res['code']>0){
            //路劲用相对路径
            $result = $this->wxapp->material->uploadVoice('./'.$res['url']);
            $this->showError($result);
            $resource = $this->wxapp->material->get($result['media_id']);
            $data  = [
                'media_id'=> $result['media_id'],
                'media_url'=> $resource['down_url'],
                'local_cover'=> $res['url'],
                'type'=> 'image',
            ];
            $result['url'] = $resource['down_url'];
            $r = $this->modelClass->save($data);
            $data = ['state'=>'SUCCESS','url'=>$resource['down_url']];
        }else{
            $data = ['state'=>'SUCCESS','url'=>''];
        }
        return json_encode($data);
    }
    //获取媒体素材
    protected function getMaterialByMediaId($mediaId){
        $resource = $this->wxapp->material->get($mediaId);
        $this->showError($resource);
        return $resource;

    }

    protected function getListImage()
    {
        $list = WechatMaterial::where($this->where)->where('type', 'image')->select();
        $data = ['state' => 'SUCCESS', 'start' => 0, 'total' => count($list), 'list' => []];
        if ($list) {
            foreach ($list as $k => $v) {
                $data['list'][$k]['url'] = $v['media_url'];
                $data['list'][$k]['mtime'] = time();
            }
        }

        return json_encode($data);

    }
}