<?php

return
    [
        'is_nav' => -1,//1导航栏；0 自建导航栏, -1  子菜单作为一级级菜单
        'menu' => [
                'href' => 'curd',
                'title' => 'Curd管理',
                'status' => '1',
                'auth_verify' => '1',
                'type' => '1',
                'menu_status' => '1',
                'icon' => 'layui-icon layui-icon-list',
                'menulist' => [
                    [
                        'href' => 'index',
                        'title' => 'Curd',
                        'status' => 1,
                        'auth_verify' => 1,
                        'menu_status' => 1,
                        'type' => 1,
                        'icon' => 'layui-icon layui-icon-component',
                        'menulist' => [
                            ['href' => 'index/index', 'title' => 'list', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/add', 'title' => 'add', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/edit', 'title' => 'edit', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/delete', 'title' => 'delete', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/modify', 'title' => 'modify', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/delfile', 'title' => '删除文件', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/addfile', 'title' => '添加文件', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/editfile', 'title' => '修改文件', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/list', 'title' => '文件列表', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/execute', 'title' => '强制执行', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'index/delall', 'title' => '删除所有', 'status' => 1, 'menu_status' => 0,],
                        ]
                    ],
                    [
                        'href' => 'table',
                        'title' => '数据库',
                        'status' => '1',
                        'menu_status' => '1',
                        'type' => '1',
                        'icon' => 'layui-icon layui-icon-app',
                        'menulist' => [
                            [
                                'href' => 'table/index',
                                'title' => 'index',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/add',
                                'title' => 'add',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/edit',
                                'title' => 'edit',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/list',
                                'title' => 'list',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/destroy',
                                'title' => 'destroy',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/delete',
                                'title' => 'delete',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/fieldlist',
                                'title' => '字段列表',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/addfield',
                                'title' => '添加字段',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/delfield',
                                'title' => '删除字段',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/delfield',
                                'title' => '删除字段',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/savefield',
                                'title' => '保存字段',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ], [
                                'href' => 'table/backup',
                                'title' => '备份',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                            [
                                'href' => 'table/delfile',
                                'title' => '备份',
                                'status' => '1',
                                'menu_status' => '0',
                                'icon' => 'layui-icon layui-icon-app',
                            ],
                        ],

                    ],
                    [
                        'href' => 'demo',
                        'title' => '组件演示',
                        'status' => 1,
                        'auth_verify' => 1,
                        'menu_status' => 1,
                        'type' => 1,
                        'icon' => 'layui-icon layui-icon-component',
                        'menulist' => [
                            ['href' => 'demo/index', 'title' => 'list', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'demo/add', 'title' => 'add', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'demo/edit', 'title' => 'edit', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'demo/delete', 'title' => 'delete', 'status' => 1, 'menu_status' => 0,],
                            ['href' => 'demo/modify', 'title' => 'modify', 'status' => 1, 'menu_status' => 0,],
                        ]
                    ]
                ],
            ],

    ];
