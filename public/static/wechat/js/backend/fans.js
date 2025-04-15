define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'wechat/backend.fans/index',
                    edit_url:'wechat/backend.fans/edit',
                    delete_url:'wechat/backend.fans/delete',
                    aysn: {
                        'type':'request',
                        url:'wechat/backend.fans/aysn',
                        icon:'layui-icon layui-icon-refresh-3',
                        class:'layui-btn layui-btn-sm',
                        title:'同步粉丝',
                        text:'确定同步吗',
                    },
                    reply: {
                        'type':'open',
                        url:'wechat/backend.message/index',
                        // icon:'layui-icon layui-icon-refresh-3',
                        class:'layui-btn layui-btn-sm',
                        title:'同步粉丝',
                        text:'确定同步吗',
                    },
                }
            }
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                toolbar: ['refresh','aysn'],
                cols: [[
                    {checkbox: true, fixed: true},
                    {field: 'id', title: 'ID', width: 80, sort: true},
                    // {field: 'member_id', title: '会员id', width: 80, fixed: true, sort: true},
                    {field: 'nickname', title: __('nickname'), width: 180,sort: true},
                    {field: 'headimgurl', title: __('avatar'), width: 150,templet:Table.templet.image,sort: true},
                    {field: 'sex', title: __('sex'),selectList:{0:'保密',1:'男',2:'女'}, width: 120, templet:Table.templet.select,sort: true},
                    // {field: 'openid', title: 'openid', width: 200, sort: true},
                    // {field: 'unionid', title: 'unionid', width: 120, sort: true},
                    // {field: 'groupid', title: __('粉丝组id'),width: 120,sort: true},
                    {field: 'subscribe', title: __('subscribe'),filter:'subscribe' ,width: 120,templet:Table.templet.switch,sort: true},
                    // {field: 'remark', title: __('remark'), width: 120, sort: true},
                    {field: 'tags', title: __('tags'), width: 120,sort: true},
                    {field: 'subscribe_time', title: __('subscribe_time'), width: 180,templet:Table.templet.time,sort: true},
                    {field: 'unsubscribe_time', title: __('unsubscribe_time'), width: 180,templet:Table.templet.time,sort: true},
                    {
                        minwidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["edit",'reply']
                    },
                ]],
                done: function(res){
                },
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