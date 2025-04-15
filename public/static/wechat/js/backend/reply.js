define(['table','form'], function (Table,Form) {
    Table.init = {
        table_elem: 'list',
        tableId: 'list',
        requests:{
            index_url:'wechat/backend.reply/index',
            add_url:'wechat/backend.reply/add',
            edit_url:'wechat/backend.reply/edit',
            delete_url:'wechat/backend.reply/delete',
            reply: {
                'type':'open',
                url:'wechat/backend.reply/index',
                icon:'layui-icon layui-icon-reply-fill',
                class:'layui-btn layui-btn-sm',
                title:'回复消息',
                text:'回复消息',
            },
        }
    }
    let Controller = {
        index: function () {
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url)+'?type='+type,
                init: Table.init,
                toolbar: [],
                cols: [[
                    {checkbox: true, fixed: true},
                    {field: 'id', title: 'ID', width: 80, fixed: true, sort: true},
                    {field: 'keyword', title: '关键字', width: 120,},
                    {field: 'type', title: '事件类型', width: 120,},
                    {field: 'msg_type', title: '回复消息类型 ', width: 120, },
                    {field: 'data', title: '文本回复内容', width: 120, },
                    {field: 'material_id', title: '媒体id', width: 120,},
                    {field: 'create_time', title: '添加时间', width: 180,},
                    {field: 'update_time', title: '更新时间', width: 180,},
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
                limits: [10, 15, 20, 25, 50, 100],
                limit: 15,
                page: true
            });
            Table.api.bindEvent(Table.init.tableId);

        },
        // reply: function () {
        //     Table.render({
        //         elem: '#' + Table.init.table_elem,
        //         id: Table.init.tableId,
        //         url: Fun.url(Table.init.requests.reply),
        //         init: Table.init,
        //         toolbar: ['refresh'],
        //         cols: [[
        //             {checkbox: true, fixed: true},
        //             {field: 'id', title: 'ID', width: 80, fixed: true, sort: true},
        //             {field: 'nickname', title: '昵称', width: 120,},
        //             {field: 'openid', title: 'openid', width: 200,},
        //             {field: 'content', title: '内容', width: 120,templet:function(d){
        //                     return qqWechatEmotionParser(d.content)
        //                 }},
        //             {field: 'type', title: '类型', width: 120,},
        //             {field: 'event', title: '事件', width: 120,},
        //             {field: 'create_time', title: '添加时间', width: 180,},
        //             {field: 'update_time', title: '更新时间', width: 180,},
        //             {
        //                 minwidth: 250,
        //                 align: "center",
        //                 title: __("Operat"),
        //                 init: Table.init,
        //                 templet: Table.templet.operat,
        //                 operat: ["reply"]
        //             },
        //         ]],
        //         done: function(res){
        //         },
        //         limits: [10, 15, 20, 25, 50, 100],
        //         limit: 15,
        //         page: true
        //     });
        //
        //     Table.api.bindEvent(Table.init.tableId);
        //
        // },

        add: function () {

            Controller.api.bindevent()
        },
        edit: function () {
            Controller.api.bindevent()
        },

        api: {
            bindevent: function () {

                $('.img-box').click(function (e) {
                    var index = Fun.api.open({
                        type: 1,
                        url:$(this).next('.layui-row').html(),
                        area: ['800px', '600px'],
                        anim: 2,
                        maxmin: true,
                        success: function(layero, index){
                            // 父页面获取子页面的iframe
                            var str =  "<script>function materialSelect(obj, id, type) {\n" +
                                "$(obj).parent('.media-btn').siblings('.media-btn').find('a').css('background-color','#fff');" +
                                "$(obj).css('background-color','#c2c2c2');"+
                                "                                var src = $(obj).parents('.media-body').find('.media-img').attr('src');\n" +
                                "                                $(body).find(\"input[name='material_id']\").val(id);\n" +
                                "                                $(body).find(\"input[name='msg_type']\").val(type);\n " +
                                "console.log($(body).find('#'+type).find('img').length);\n"+
                                "                                $(body).find('#'+type).find('img').attr('src', src);\n" +
                                "                            }"+
                                "</script>";
                            layero.append(str)
                        },yes:function(index){
                            Fun.api.close(index);
                        }
                    })
                })

                Form.api.bindEvent($('form'))
            }
        }
    };
    return Controller;
});