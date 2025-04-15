## 版权所有 FunAdmin
## 使用方法
- 访问地址 域名+'apidoc'
- 配置文件 在config 目录下配置
-  只需要添加控制器（controller

```
<?php
return [
    'title'         => 'apidoc',                   # 文档title
    'version'       => '1.0',                               # 文档版本
    'copyright'     => 'Powered By FunAdmin',          # 版权信息
    'password'      => '',                                  # 访问密码，为空不需要密码
    'qq'            => '994927909',                        # 咨询QQ
    'document'      => [
        "explain" => [
            'name' => '说明',
            'list' => [
                '登录态'      => ['是否登录'],
                'formId收集' => ['你好', '很好'],
            ]
        ],
        "code"    => [
            'name' => '返回码',
            'list' => [
                '0'     => '成功',
                '1'     => '失败'
            ]
        ]
    ],
     // 全局请求header,一般存放token之类的
    'header'        => [

    ],
    // 全局请求参数
    'params'        => [
        '__uid' => 2
    ],
   //多版本方法使用
   'controller' => [
        [
            'name'=>'v2版本',
            'list'=>[
                'api\controller\v2\Demo', //控制器的命名空间+控制器名称(不需要加\\app)
                'api\controller\v2\Demo', //支持两层控制器URL自动生成
                'api\controller\v2\Demo',
                'addons\apidoc\v1\controller\ApiTest' //插件目录下需要使用文件路劲全名
            ]
        ],
        [
            'name'=>'v3版本',
            'list'=>[
                'api\controller\v3\Demo', //控制器的命名空间+控制器名称(不需要加\\app)
                'api\controller\v3\Demo', //支持两层控制器URL自动生成
                'api\controller\v3\Demo'
            ]
        ]
    ]
    // 过滤、不解析的方法名称
    'filter_method' => [
        '_empty'
    ]
];
```

### 控制器controller 参数

- app下  参数`app\\`可以不用写
- 插件(addons)应用下接口 需要写全名 例如 'addons\apidoc\v1\controller\ApiTest'

### 类注释

- title 标题
- desc描述
- Class 类名

```
/**
* @title   会员管理 
* @desc    会员管理
* Class Index
* @package app\index\controller
  */
```

### 方法注释

- title 标题
- desc描述
- url 路由
- param 参数
- return 返回数据
- ![image.png](./view/assets/image.png)

```
 /**
     * @title 会员列表
     * @desc  会员列表
     * @url   __u('api.v1/index',[],'',true) //插件列写 addons_url('index/index') 可以不填
     * @author 作者
     * @version 版本
     *
     * @param int $page  0 999
     * @param int $limit 10
     *
     * @return int $name 0 索引
     * @return int $ab 0 索引
     * @return int $id 0 索引
     */
```
