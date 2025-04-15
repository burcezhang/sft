define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'wechat/backend.index/index',
                    add_url:'wechat/backend.index/add',
                    edit_url:'wechat/backend.index/edit',
                    delete_url:'wechat/backend.index/delete',
                    modify_url:'wechat/backend.index/modify',
                }
            }
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                toolbar: ['refresh'],
                cols: [[
                    {checkbox: true,},
                    { field: 'id', title: __('ID'), sort:true,},
                    {field:'wxname', title: __('公众号名称'),align: 'center',sort:'sort'},
                    {field:'wx_code', title: __('微信号'),align: 'center',sort:'sort'},
                    {field:'app_id', title: __('appid'),align: 'center',sort:'sort'},
                    {field:'secret', title: __('appsecret'),align: 'center',sort:'sort'},
                    {field:'origin_id', title: __('公众号原始ID'),align: 'center',sort:'sort'},
                    // {field:'aes_key', title: __('微信对接encodingaeskey'),align: 'center',sort:'sort'},
                    {field:'token', title: __('对接token'),align: 'center',sort:'sort'},
                    {field:'logo', title: __('头像地址'),align: 'center',sort:'sort'},
                    {field:'related', title: __('微信对接地址'),align: 'center',sort:'sort'},
                    {field: 'status',filter:'status',search: 'select',selectList:{0:"disabled",1:"enabled"},title: __('微信接入状态'),sort:true,templet: Table.templet.switch},
                    {field:'type',title: __('类型'),search: 'select',selectList:{1:"普通订阅号",2:"认证订阅号",3:'普通服务号',4:'认证服务号/认证媒体/政府订阅号'},align: 'center',sort:'sort'},
                    {field:'update_time', title: __('update_time'),align: 'center',sort:'sort'},
                    {
                        minwidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["edit"]
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
        api: {
            bindevent: function () {
                Form.api.bindEvent($('form'))
            }
        }
    };
    return Controller;
});