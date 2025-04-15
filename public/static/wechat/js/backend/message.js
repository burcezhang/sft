define(['table','form'], function (Table,Form) {
    Table.init = {
        table_elem: 'list',
        tableId: 'list',
        requests:{
            index_url:'wechat/backend.message/index',
            edit_url:'wechat/backend.message/edit',
            delete_url:'wechat/backend.message/delete',
            reply: {
                'type':'open',
                url:'wechat/backend.message/reply',
                icon:'layui-icon layui-icon-reply-fill',
                class:'layui-btn layui-btn-sm',
                title:'回复消息',
                text:'回复消息',
                full:1,
                extend:'data-btn=""'
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
                toolbar: ['refresh'],
                cols: [[
                    {checkbox: true, fixed: true},
                    {field: 'id', title: 'ID', width: 80, fixed: true, sort: true},
                    {field: 'nickname', title: '昵称', width: 120,},
                    {field: 'openid', title: 'openid', width: 200,},
                    {field: 'content', title: '内容', width: 120,templet:function(d){
                            return qqWechatEmotionParser(d.content)
                        }},
                    {field: 'type', title: '类型', width: 120,},
                    {field: 'event', title: '事件', width: 120,},
                    {field: 'create_time', title: '添加时间', width: 180,},
                    {field: 'update_time', title: '更新时间', width: 180,},
                    {
                        minwidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["reply",'delete']
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
        reply: function () {
            //一些事件触发
            layui.element.on('tab(reply)', function(data){
                $("input[name='type']").val($(this).data('type'))
            });
            layui.form.on('submit(submit)', function (data) {
                var type = '';
                if ($("input[name='type']").val() === 'text' && !$("textarea[name='data']").val()) {
                    Fun.toastr.tips('请填写文本内容');
                    return false;
                }
                data.field.openid = openid;
                Fun.ajax({url:Table.init.requests.reply.url, data: data.field}, function (res) {
                    Fun.toastr.success(res.msg, {time: 1000, icon: 1});
                    return false;
                },function (res){
                    Fun.toastr.error(res.msg);
                });
                return false;
            });
            $('.img-box').click(function (e) {
                index = layer.open({
                    type: 1,
                    content: $(this).next('.layui-row'),
                    area: ['800px', '600px'],
                    anim: 2,
                    maxmin: true,
                    cancel: function(){
                        $(this).parents('.layui-tab-item').find('.layui-row').hide();;
                    },end:function (){
                        $(this).parents('.layui-tab-item').find('.layui-row').hide();;
                    }
                });
            })
            $('.select').click(function (){
                var id = $(this).data('id')
                var type = $(this).data('type')
                var src = $(this).parents('.layui-tab-item').find('.media-img').attr('src');
                $("input[name='material_id']").val(id)
                $("input[name='type']").val(type)
                $(this).parents('.layui-tab-item').find('.img-box').find('img').attr('src', src);
                $(this).parents('.layui-tab-item').find('.layui-row').hide();
                layer.close(index);
            })

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