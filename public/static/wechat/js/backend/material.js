define(['table','form','upload'], function (Table,Form,Upload) {
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
            imgUploadUrl: 'wechat/backend.material/imageUpload',
            videoUploadUrl : 'wechat/backend.material/videoUpload',
            voiceUploadUrl : 'wechat/backend.material/voiceUpload',
            addUrl : 'wechat/backend.material/add',
            editUrl : 'wechat/backend.material/edit',
            send : 'wechat/backend.material/send',
            preview : 'wechat/backend.material/preview',
        }
    }

    let Controller = {
        index: function () {
            Table.api.bindEvent(Table.init.tableId);

            $('.media-item-type2 button').click(function (){
                var type = $(this).data('type')
                var id = $(this).data('id')
                switch (type){
                    case 'sendAll':
                        var index= layer.confirm('确定群发吗', function(index){
                            Fun.ajax({url:Table.init.requests.send,data:{id:id}},function(res){
                                layui.layer.closeAll();
                                Fun.toastr.success(res.msg);
                            },function (res){
                                layui.layer.closeAll();
                                Fun.toastr.error(res.msg);
                            });
                        });
                        break;
                    case 'preview':
                        //例子2
                        layer.prompt({
                            formType: 0,
                            value: 'yue909',
                            title: '请输入微信号',
                        }, function(value, index, elem){
                            Fun.ajax({url:Table.init.requests.preview,data:{id:id,wxname:value}},function(res){
                                layui.layer.closeAll();
                                Fun.toastr.success(res.msg);
                            },function (res){
                                layui.layer.closeAll();
                                Fun.toastr.error(res.msg);
                            });
                        });

                        break;
                    case 'edit':
                        Fun.api.open({
                            url:Table.init.requests.editUrl+'?type=news&id='+id,
                            full:1,
                            btn:''
                        })
                        break;
                    case 'del':
                        alert(1)
                        Fun.ajax({url:Table.init.requests.delete_url,data:{id:id}},function (res){
                            $(this).parents('.layui-col-md3').remove();
                        });
                        break;
                }
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
                var element = layui.element,upload = layui.upload;
                //设定文件大小限制
                upload.render({
                    elem: '#coverUpload'
                    ,url: Fun.url(Table.init.requests.imgUploadUrl)
                    ,size: 2048 //限制文件大小，单位 KB
                    ,done: function(res){
                        if(res.code>0){
                            var url = res.data.url +'&wxfrom=5&wx_lazy=1';
                            var dir = $("#dir").attr('dir');
                            $('#coverUpload').find('img').attr('src',url);
                            $(".type-img-"+ dir).attr('src',url).show();
                            $(".type-img-"+ dir).next().hide();
                            $("#cover"+ dir).val(res.data.media_id);
                            Fun.toastr.success(res.msg)
                        }else{
                            Fun.toastr.error(res.msg)
                        }
                    }
                });
                //设定图片大小限制
                upload.render({
                    elem: '#imageUpload'
                    ,url: Fun.url(Table.init.requests.imgUploadUrl)
                    ,size: 2048 //限制文件大小，单位 KB
                    ,done: function(res){
                        if(res.code>0){
                            Fun.toastr.success(res.msg)
                        }else{
                            Fun.toastr.error(res.msg)
                        }
                    }
                });
                //设定视频大小限制
                upload.render({
                    elem: '#videoUpload'
                    ,url: Fun.url(Table.init.requests.videoUploadUrl)
                    ,accept: 'video' //视频
                    ,size: 10240 //限制文件大小，单位 KB
                    ,done: function(res){
                        if(res.code>0){
                            $('#videoUpload').find('input').val(res.url);
                            $('#videoUpload').find('video').attr('src',res.url);
                            Fun.toastr.success(res.msg);
                        }else{
                            Fun.toastr.error(res.msg);
                        }
                    }
                });
                //设定音频大小限制
                upload.render({
                    elem: '#voiceUpload'
                    ,url: Fun.url(Table.init.requests.voiceUploadUrl)
                    ,accept: 'audio' //音频
                    ,size: 2048 //限制文件大小，单位 KB
                    ,done: function(res){
                        if(res.code>0){
                            Fun.toastr.success(res.msg);
                        }else{
                            Fun.toastr.error(res.msg);
                        }
                    }
                });
                $(document).on('click','.media-plus',function(){
                    var num = $(this).parents(".media-left").find(".media-body").length;
                    if (num > 7) {
                        Fun.toastr.error('最多只可以加入8条图文消息。');
                    }
                    if(Config.formData.length>0){
                        htmlEdit = '';
                    }else{
                        htmlEdit = '<span class="del">删除</span>';
                    }
                    var html = '';
                    html += '<div class="media-body js-action" data-key="'+num+'">';
                    html += '<p class="type-title-' + num + '">标题</p><div class="media-body-div"><img class="type-img-' + num + '" src="" style="max-width:62px;max-height:62px;display:none;"><span class="img-text">缩略图</span></div>';
                    html += '<div class="actions"><span class="edit">编辑</span>'+htmlEdit+'</div>';
                    html += '<span class="editting">编辑中</span>';
                    html += '<input type="hidden" name="hidden' + num + '" id="title' + num + '" value="">';
                    html += '<input type="hidden" name="hidden' + num + '" id="author' + num + '" value="">';
                    html += '<input type="hidden" name="hidden' + num + '" id="show_cover' + num + '" value="1">';
                    html += '<input type="hidden" name="hidden' + num + '" id="content_source_url' + num + '" value="">';
                    html += '<input type="hidden" name="hidden' + num + '" id="cover' + num + '" value="">';
                    html += '<input type="hidden" name="hidden' + num + '" id="need_open_comment' + num + '" value="1">';
                    html += '<input type="hidden" name="hidden' + num + '" id="only_fans_can_comment' + num + '" value="0">';
                    html += '<input type="hidden" name="hidden' + num + '" id="digest' + num + '" value="">';
                    html += '<input type="hidden" name="hidden' + num + '" id="content' + num + '" value="">';
                    html += '</div>';
                    $(this).parents(".media-left").find(".media-body").eq(num -1).before(html);
                });
                $(document).on('change','input,textarea',function(){
                    var name = $(this).attr('name');
                    var dir = $("#dir").attr('dir');
                    if(name === 'digest'){
                        $("#" + name + dir).val($("textarea[name='" + name+"'").val());
                    }else if (name === 'title') {
                        if ($("input[name='" + name+"'").val() === '') {
                            $(".type-title-" + dir).html('标题');
                        } else {
                            $(".type-title-" + dir).html($("input[name='" + name+"'").val());
                        }
                        $("#" + name + dir).val($("input[name='" + name+"'").val());
                    }else{
                        $("#" + name + dir).val($("input[name='" + name+"'").val());
                    }
                })
                //单选按钮
                layui.form.on('radio(radio)',function (data) {
                    var dir = $("#dir").attr('dir');
                    var name = $(this).attr('name');
                    $("#" + name + dir).val($(this).val());
                })
                $(document).on('click','#submit', function(data){
                    var contents = {};
                    var num = $(".js-action").length;
                    var flag = true;
                    for (var i = 0; i < num; i++) {
                        $("input[name='hidden" + i + "']").each(function (index) {
                            if ($("input[name='hidden" + i + "']").eq(0).val() == "") {
                                flag = false;
                                Fun.toastr.error('第' + (i + 1) + '篇文章的标题不能为空');
                                return false;
                            } else if ($("input[name='hidden" + i + "']").eq(4).val() == "") {
                                flag = false;
                                Fun.toastr.error( '第' + (i + 1) + '篇文章的封面图片不能为空');
                                return false;
                            }else if ($("input[name='hidden" + i + "']").eq(7).val() == "") {
                                flag = false;
                                Fun.toastr.error( '第' + (i + 1) + '篇文章的摘要不能为空');
                                return false;
                            } else if ($("input[name='hidden" + i + "']").eq(8).val() == "") {
                                flag = false;
                                Fun.toastr.error('第' + (i + 1) + '篇文章的正文不能为空');
                                return false;
                            }
                        });
                        var title =  $("input[name='hidden" + i + "']").eq(0).val();
                        var author =  $("input[name='hidden" + i + "']").eq(1).val();
                        var show_cover =  $("input[name='hidden" + i + "']").eq(2).val();
                        var content_source_url =  $("input[name='hidden" + i + "']").eq(3).val();
                        var thumb_media_id =  $("input[name='hidden" + i + "']").eq(4).val();
                        var need_open_comment =  $("input[name='hidden" + i + "']").eq(5).val();
                        var only_fans_can_comment =  $("input[name='hidden" + i + "']").eq(6).val();
                        var digest =  $("input[name='hidden" + i + "']").eq(7).val();
                        var content =  $("input[name='hidden" + i + "']").eq(8).val();
                        contents[i] = {title:title,author:author,show_cover:show_cover,content_source_url:content_source_url
                            ,thumb_media_id:thumb_media_id,need_open_comment:need_open_comment,only_fans_can_comment:only_fans_can_comment,
                            digest:digest,content:content};
                    }
                    if (flag === false) {
                        return false;
                    }
                    var load;
                    if(Config.formData.length>0){
                        url = Table.init.requests.editUrl;
                        mediaId = Config.formData[0]['material_id']
                    }else{
                        url = Table.init.requests.addUrl;
                        mediaId = '';
                    }
                    Fun.ajax({
                        type: "post",
                        url: url,
                        async: true,
                        data: {
                            'mediaId':mediaId,"content": contents
                        },
                    },function (res) {
                        layer.close(load);
                        if (res.code > 0) {
                            layer.msg(res.msg, {time: 2000}, function () {
                                layer.closeAll();
                                parent.location.reload();
                            });
                        } else{
                            layer.alert(res.msg)
                            return false;
                        }
                    }, function (res) {
                        layer.close(load);
                    }, function (res) {
                        load = layer.load(1);
                        return load;
                    });
                    return  false;
                })
                layui.form.on('submit(video)',function (data) {
                    Fun.ajax({url:Fun.url(videoUploadUrl),data:data.field},function (res) {
                        Fun.toastr.success(res.msg);
                    },function(){
                        Fun.toastr.error(res.msg);
                    });
                    return false;
                });
                $(document).on('click','.del',function(){
                    $(this).parents(".media-body").remove();
                })
                $(document).on('click','.js-action',function(){
                    $(".js-action").removeClass('action');
                    var num = $(this).data('key')
                    $(this).addClass('action');
                    $("#dir").attr('dir', num);
                    var title = $("#title" + num).val();
                    var author = $("#author" + num).val();
                    var cover = $(".type-img-" + num).attr('src');
                    var show_cover = $(".show_cover-" + num).val();
                    var need_open_comment = $("#need_open_comment" + num).val();
                    var only_fans_can_comment = $("#only_fans_can_comment" + num).val();
                    var digest = $("#digest" + num).val();
                    var content = $("#content" + num).val();
                    var content_source_url = $("#content_source_url" + num).val();
                    $("input[name='title']").val(title);
                    $("input[name='author']").val(author);
                    $("input[name='cover']").val(cover);
                    $("input[name='digest']").val(digest);
                    $("input[name='content_source_url']").val(content_source_url);
                    layui.form.render();
                    if (cover != "") {
                        $("#coverImage").attr('src', cover);
                    }else{
                        $("#coverImage").attr('src', '');
                    }
                    obj = {show_cover:show_cover,need_open_comment:need_open_comment,only_fans_can_comment:only_fans_can_comment,digest:digest,content:content};
                    layui.form.val("form", obj);
                    // layui.layedit.setValue(content)
                })
                $(document).on('mouseout','.js-action',function(){
                    $(this).children('.actions').hide();
                })
                $(document).on('mouseover','.js-action',function(){
                    $(this).children('.actions').show();
                })
                //删除素材
                $(document).on('click','.materialDel',function(){
                    var id = $(this).data('id');
                    layer.confirm('{:lang("Are you sure you want to delete it")}', function(index){
                        loading =layer.load(1, {shade: [0.1,'#fff']});
                        Fun.ajax({url:"wechat/backend.material/delete",data:{id:id}},function(res){
                            layer.close(loading);
                            Fun.toastr.success(res.msg);
                            obj.del();
                        },function(){
                            Fun.toastr.error(res.msg);
                        });
                    });
                })
                Form.api.bindEvent($('form'))
                $('.layui-layedit-iframe #LAY_layedit_1').contents().find("body").on('blur', function () {
                    var content = $(this).html(),dir = $("#dir").attr('dir');
                    console.log(content)
                    console.log(dir)
                    $("#content" + dir).val(content);
                });
            }
        }
    };
    return Controller;
});