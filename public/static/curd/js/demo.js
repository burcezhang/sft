define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'curd/demo/index',
                    add_url:'curd/demo/add',
                    edit_url:'curd/demo/edit',
                    destroy_url:'curd/demo/destroy',
                    delete_url:'curd/demo/delete',
                    recycle_url:'curd/demo/recycle',
                    import_url:'curd/demo/import',
                    export_url:'curd/demo/export',
                    modify_url:'curd/demo/modify',

                }
            }
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                toolbar: ['refresh','add','destroy','export','recycle'],
                cols: [[
                    {checkbox: true,},
                    {field: 'id', title: __('ID'), sort:true,},
                    {field:'cate_id',search: 'select',selectList:cateIdList, title: __('CateId'),align: 'center',templet: Table.templet.select},
                    {field:'cate_ids',search: 'select',selectList:cateIdList, title: __('CateIds'),align: 'center',templet: Table.templet.select},
                    {field:'week',search: 'select',title: __('Week'),filter: 'week',selectList:weekList,templet: Table.templet.select},
                    {field:'sexdata',search: 'xmselect',title: __('Sexdata'),selectList:sexdataList,filter: 'xmselect',extend:' data-url="curd/demo/index?type=1" data-tree="false" data-autorow="false" data-prop="value,name"'},
                    {field:'textarea', title: __('Textarea'),align: 'center'},
                    {field:'image',title: __('Image'),templet: Table.templet.image},
                    {field:'images', title: __('Images'),align: 'center',templet: Table.templet.image},
                    {field:'attach_file',title: __('AttachFile'),templet: Table.templet.url},
                    {field:'attach_files', title: __('AttachFiles'),align: 'center',templet: Table.templet.url},
                    {field:'keywords', title: __('Keywords'),align: 'center'},
                    {field:'price',title: __('Price'),align: 'center'},
                    {field:'startdate',title: __('Startdate'),align: 'center',dateformat:'yyyy-MM-dd',searchdateformat:'yyyy-MM-dd',search:'time',templet: Table.templet.time,sort:true},
                    {field:'activitytime',title: __('Activitytime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'time',templet: Table.templet.time,sort:true},
                    {field:'timestaptime',title: __('Timestaptime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'time',templet: Table.templet.time,sort:true},
                    {field:'year',title: __('Year'),align: 'center',dateformat:'yyyy',searchdateformat:'yyyy',timeType:'year',search:'time',templet: Table.templet.time,sort:true},
                    {field:'times',title: __('Times'),align: 'center',dateformat:'HH:mm:ss',searchdateformat:'HH:mm:ss',timeType:'time',search:'time',templet: Table.templet.time,sort:true},
                    {field:'switch',search: 'select',title: __('Switch'),filter: 'switch',selectList:switchList,templet: Table.templet.select},
                    {field:'open_switch',search: 'select',title: __('OpenSwitch'),filter: 'open_switch',selectList:openSwitchList,templet: Table.templet.select},
                    {field:'mystate',search: 'select',title: __('mystate'),filter: 'mystate',selectList:mystateList,templet: Table.templet.tags},
                    {field:'my2state',search: 'select',title: __('my2state'),filter: 'my2state',selectList:my2stateList,templet: Table.templet.tags},
                    {field:'editor_content', title: __('EditorContent'),align: 'center'},
                    {field:'description', title: __('Description'),align: 'center'},
                    {field:'my_color', title: __('myColor'),align: 'center'},
                    {field:'status',search: 'select',title: __('Status'),filter: 'status',selectList:statusList,templet: Table.templet.select},
                    {field:'create_time',title: __('CreateTime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'range',templet: Table.templet.time,sort:true},
                    {
                        minWidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["edit", "destroy","delete"]
                    },
                ]],
                limits: [10, 15, 20, 25, 50, 100,500],
                limit: 15,
                page: false,
                done: function (res, curr, count) {
                }
            });
            Table.api.bindEvent(Table.init.tableId);
        },
        add: function () {
            Controller.api.bindevent()
        },
        edit: function () {
            Controller.api.bindevent()
        },
        recycle: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests: {
                    delete_url:'curd/demo/delete',
                    recycle_url:'curd/demo/recycle',
                    restore_url:'curd/demo/restore',

                },
            };
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.recycle_url),
                init: Table.init,
                toolbar: ['refresh','delete','restore'],
                cols: [[
                    {checkbox: true,},
                    {field: 'id', title: __('ID'), sort:true,},
                    {field:'cate_id', title: __('CateId'),align: 'center',sort:true},
                    {field:'cate_ids', title: __('CateIds'),align: 'center',sort:true},
                    {field:'week',search: 'select',title: __('Week'),filter: 'week',selectList:weekList,templet: Table.templet.select},
                    {field:'sexdata',search: 'select',title: __('Sexdata'),filter: 'sexdata',selectList:sexdataList,templet: Table.templet.select},
                    {field:'textarea', title: __('Textarea'),align: 'center'},
                    {field:'image',title: __('Image'),templet: Table.templet.image},
                    {field:'images', title: __('Images'),align: 'center'},
                    {field:'attach_file',title: __('AttachFile'),templet: Table.templet.image},
                    {field:'attach_files', title: __('AttachFiles'),align: 'center'},
                    {field:'keywords', title: __('Keywords'),align: 'center'},
                    {field:'price',title: __('Price'),align: 'center'},
                    {field:'startdate',title: __('Startdate'),align: 'center',dateformat:'yyyy-MM-dd',searchdateformat:'yyyy-MM-dd',search:'time',templet: Table.templet.time,sort:true},
                    {field:'activitytime',title: __('Activitytime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'time',templet: Table.templet.time,sort:true},
                    {field:'timestaptime',title: __('Timestaptime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'time',templet: Table.templet.time,sort:true},
                    {field:'year',title: __('Year'),align: 'center',dateformat:'yyyy',searchdateformat:'yyyy',timeType:'year',search:'time',templet: Table.templet.time,sort:true},
                    {field:'times',title: __('Times'),align: 'center',dateformat:'HH:mm:ss',searchdateformat:'HH:mm:ss',timeType:'time',search:'time',templet: Table.templet.time,sort:true},
                    {field:'switch',search: 'select',title: __('Switch'),filter: 'switch',selectList:switchList,templet: Table.templet.select},
                    {field:'open_switch',search: 'select',title: __('OpenSwitch'),filter: 'open_switch',selectList:openSwitchList,templet: Table.templet.select},
                    {field:'mystate',search: 'select',title: __('mystate'),filter: 'mystate',selectList:mystateList,templet: Table.templet.tags},
                    {field:'my2state',search: 'select',title: __('my2state'),filter: 'my2state',selectList:my2stateList,templet: Table.templet.tags},
                    {field:'editor_content', title: __('EditorContent'),align: 'center'},
                    {field:'description', title: __('Description'),align: 'center'},
                    {field:'my_color', title: __('myColor'),align: 'center'},
                    {field:'status',search: 'select',title: __('Status'),filter: 'status',selectList:statusList,templet: Table.templet.select},
                    {field:'create_time',title: __('CreateTime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'time',templet: Table.templet.time,sort:true},
                    {
                        minWidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["restore","delete"]
                    },
                ]],
                limits: [10, 15, 20, 25, 50, 100,500],
                limit: 15,
                page: false,
                done: function (res, curr, count) {
                }
            });
            Table.api.bindEvent(Table.init.tableId);
        },
        api: {
            bindevent: function () {
                Form.api.bindEvent($('form'))
            }
        }
    };
    return Controller;
});