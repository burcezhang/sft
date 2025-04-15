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
 * Date: 2019/9/5
 */

namespace addons\wechat\backend\validate;

use think\Validate;

class WechatAccount extends Validate
{
    protected $rule = [
        'wxname|wxname' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'wx_account',
        ],
        'app_id|APPID' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'wx_account',
        ],
        'secret|APPSECRET' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'wx_account',
        ],
        'origin_id|原始id' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'wx_account',
        ],
        'token|对接token' => [
            'require' => 'require',
            'max'     => '255',
        ],
        'type|类型' => [
            'require' => 'require',
            'max'     => '2',
        ],
        'status|状态' => [
            'require' => 'require',
            'max'     => '1',
        ],


    ];
}