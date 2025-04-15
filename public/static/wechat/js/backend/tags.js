define(['table','form'], function (Table,Form) {
    Table.init = {
        table_elem: 'list',
        tableId: 'list',
        requests:{
            index_url:'wechat/backend.tags/index',
            add_url:'wechat/backend.tags/add',
            edit_url:'wechat/backend.tags/edit',
            delete_url:'wechat/backend.tags/delete',
            destroy_url:'wechat/backend.tags/destroy',
            recycle_url:'wechat/backend.tags/recycle',
            restore_url:'wechat/backend.tags/restore',
            aysn: {
                'type':'request',
                url:'wechat/backend.tags/aysn',
                icon:'layui-icon layui-icon-refresh-3',
                class:'layui-btn layui-btn-sm',
                title:'同步标签',
                text:'确定同步吗',
            },
        }
    }
    let Controller = {
        index: function () {
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                toolbar: ['refresh','add','recycle','aysn'],
                cols: [[
                    {checkbox: true, fixed: true},
                    {field: 'id', title: 'ID', width: 80, fixed: true, sort: true},
                    {field: 'name', title: __('name'), width: 120,},
                    {field: 'tag_id', title: __('tagid'), width: 120,},
                    {field: 'status',search: 'select',filter:'status',selectList:{0:"enabled",1:"disabled"},title: __('status'),sort:true,templet: Table.templet.switch},
                    {field:'create_time', title: __('createtime'),align: 'center',sort:'sort'},
                    {field:'update_time', title: __('updatetime'),align: 'center',sort:'sort'},
                    {
                        minwidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["edit",'delete']
                    },
                ]],
                done: function(res){
                },
                //
                limits: [10, 15, 20, 25, 50, 100],
                limit: 15,
                page: true
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
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.recycle_url),
                init: Table.init,
                toolbar: ['refresh','delete','restore'],
                cols: [[
                    {checkbox: true, fixed: true},
                    {field: 'id', title: 'ID', width: 80, fixed: true, sort: true},
                    {field: 'name', title: __('name'), width: 120,},
                    {field: 'tag_id', title: __('tagid'), width: 120,},
                    {field: 'status',search: 'select',filter:'status',selectList:{0:"enabled",1:"disabled"},title: __('status'),sort:true,templet: Table.templet.switch},
                    {field:'create_time', title: __('createtime'),align: 'center',sort:'sort'},
                    {field:'update_time', title: __('updatetime'),align: 'center',sort:'sort'},
                ]],
                done: function(res){
                },
                //
                limits: [10, 15, 20, 25, 50, 100],
                limit: 15,
                page: true
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