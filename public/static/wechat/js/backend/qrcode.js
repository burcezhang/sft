define(['table','form'], function (Table,Form) {
    Table.init = {
        table_elem: 'list',
        tableId: 'list',
        requests:{
            index_url:'wechat/backend.qrcode/index',
            add_url:'wechat/backend.qrcode/add',
            edit_url:'wechat/backend.qrcode/edit',
            modify_url:'wechat/backend.qrcode/modify',
            delete_url:'wechat/backend.qrcode/delete',
            destroy_url:'wechat/backend.qrcode/destroy',
            recycle_url:'wechat/backend.qrcode/recycle',
            restore_url:'wechat/backend.qrcode/restore',
        }
    }
    let Controller = {
        index: function () {
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                toolbar: ['refresh','add','recycle'],
                cols: [[
                    {checkbox: true,},
                    {checkbox: true, },
                    {field: 'id', title: 'ID', width: 80,  sort: true},
                    {field: 'name', title: __('name'), width: 120,sort:true},
                    {field: 'type', title: __('type'), width: 80,selectList:{0:__("temporary"),1:__("forever")},templet:Table.templet.select,sort:true},
                    {field: 'qrcode', title: __('QR'), width: 120,templet:Table.templet.image,sort:true},
                    {field: 'url', title: 'url', width: 220,sort:true,templet:Table.templet.url},
                    {field: 'ticket', title: 'ticket', width: 250, sort:true},
                    {field: 'expire_seconds', title: __('expire_seconds'), width: 120, sort:true},
                    // {field: 'store_id', title: '店铺id', width: 120,sort: true},
                    {field: 'status', title:  __('status'),filter:'status', width: 180, templet:Table.templet.switch,sort:true},
                    {field: 'create_time', title: '添加时间', width: 180,sort:true},
                    {field: 'update_time', title: '更新时间', width: 180,sort:true},
                    {
                        minwidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["edit",'destroy']
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
                    {checkbox: true,},
                    {checkbox: true, },
                    {field: 'id', title: 'ID', width: 80,  sort: true},
                    {field: 'name', title: __('name'), width: 120,sort:true},
                    {field: 'type', title: __('type'), width: 80,selectList:{0:__("temporary"),1:__("forever")},templet:Table.templet.select,sort:true},
                    {field: 'qrcode', title: __('QR'), width: 120,templet:Table.templet.image,sort:true},
                    {field: 'url', title: 'url', width: 220,sort:true,templet:Table.templet.url},
                    {field: 'ticket', title: 'ticket', width: 250, sort:true},
                    {field: 'expire_seconds', title: __('expire_seconds'), width: 120, sort:true},
                    // {field: 'store_id', title: '店铺id', width: 120,sort: true},
                    {field: 'status', title:  __('status'),filter:'status', width: 180, templet:Table.templet.switch,sort:true},
                    {field: 'create_time', title: '添加时间', width: 180,sort:true},
                    {field: 'update_time', title: '更新时间', width: 180,sort:true},
                    {
                        minwidth: 250,
                        align: 'center',
                        title: __('Operat'),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ['restore', 'delete']
                    }
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