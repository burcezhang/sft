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
declare (strict_types=1);
namespace app\wechat\model;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class WechatAccount extends BaseModel
{
    /**
     * @var bool
     */
    use SoftDelete;


    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    protected $name = 'addons_wechat_account';
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public function getAppIdList()
    {
        return;
    }
    public function getStatusList()
    {
        return [0 => "enabled", 1 => "disabled"];
    }


    public function getTypeList()
    {
        return ['1' => '普通订阅号', '2' => '认证订阅号', '3' => '普通服务号', '4' => '认证服务号/认证媒体/政府订阅号',];
    }


}
