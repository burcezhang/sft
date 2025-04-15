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

use app\common\controller\Backend;
use app\wechat\model\WechatMaterial;
use app\wechat\model\WechatMaterialInfo;
use app\wechat\service\WechatService;
use fun\helper\ZipHelper;
use think\App;
class WxBase extends Backend{

    protected $layout = '../../backend/view/layout/main';
    protected $wxapp;
    protected $config;
    protected $materialType = ['news','video','voice','image','music'];

    public function __construct(App $app) {
        parent::__construct($app);
        $this->getWxApp();

    }
    protected function getWxApp(){
        $service = WechatService::instance();
        $this->config = $service->config;
        if(count($this->config)>1){
            $this->wxapp = $service->wxapp;
        }
    }
    protected function showError($res)
    {
        if (isset($res['errcode']) && $res['errcode'] > 0) {
            $this->error($res['errmsg']);
        }
    }
    /**
     * @param $type
     * @param int $id
     * @return array|\think\Collection|\think\Model|null
     * 获取素材
     */
    protected function getMaterialGroup(){
        $materialGroup = [];
        foreach ($this->materialType as $k=>$v){
            if($v=='news'){
                $weixin_material_info = WechatMaterial::where('type','news')->select()->toArray();
                if (!empty($weixin_material_info)) {
                    foreach($weixin_material_info as $key=>$value){
                        $item_info = WechatMaterialInfo::where('material_id' , $value['id'])->select()->toArray();
                        $weixin_material_info[$key]['item_info'] = $item_info;
                    }
                }
                $materialGroup[$v] = $weixin_material_info;
            }else{
                $weixin_material_info = WechatMaterial::where('type',$v)->select()->toArray();
                $materialGroup[$v] = $weixin_material_info;
            }
        }
        return $materialGroup;
    }



}
