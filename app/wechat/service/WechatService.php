<?php

namespace app\wechat\service;

use app\common\service\AbstractService;
use app\wechat\model\WechatAccount;
use app\common\traits\Jump;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\Exception;

class WechatService extends AbstractService
{
    use Jump;
    public $wxapp =null;
    public $config = [];
    public function __construct()
    {
        $this->config = WechatAccount::where('status',1)->find();
        if($this->config){
            $this->config = $this->config->toArray();
            $this->config['response_type'] = 'array';
            $this->init();
        }
    }
    public function init(){
        try {
            $this->wxapp = Factory::officialAccount($this->config);
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }

}