define(['table', 'form'], function (Table, Form) {
    Table.init = {
        table_elem: 'list',
        tableId: 'list',
        requests: {
            index_url: 'wechat/backend.menu/index',
            add_url: 'wechat/backend.menu/add',
            modify_url: 'wechat/backend.menu/modify',
            edit_url: 'wechat/backend.menu/edit',
            add_full:{
                type: 'open',
                class: 'layui-btn-sm layui-btn-green',
                url: 'wechat/backend.menu/add',
                icon: 'layui-icon layui-icon-add',
                text: __('Add'),
                title: __('Add'),
                full: 1,
                extend:"data-btn=''"
                // width:'800',
                // height:'600',
            },
            edit_full:{
                type: 'open',
                class: 'layui-btn-sm layui-btn-green',
                url: 'wechat/backend.menu/edit',
                icon: 'layui-icon layui-icon-edit',
                text: __('Edit'),
                title: __('Edit'),
                full: 1,
                extend:"data-btn=''"
            },
            delete_url: 'wechat/backend.menu/delete',
        }
    }
    let Controller = {
        index: function () {
            let table = $('#' + Table.init.table_elem);
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                toolbar: ['refresh','add_full'],
                cols: [[
                    {checkbox: true,},
                    { field: 'id', title: __('ID'), sort:true,},
                    {field:'menu_name', title: __('菜单名'),align: 'center',sort:'sort'},
                    {field: 'status',filter:'status',search: 'select',selectList:{0:"disabled",1:"enabled"},title: __('接入状态'),sort:true,templet: Table.templet.switch},
                    {field:'update_time', title: __('update_time'),align: 'center',sort:'sort'},
                    {
                        minwidth: 250,
                        align: "center",
                        title: __("Operat"),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ["edit_full",'delete']
                    },
                ]],
                done: function(res){
                },
                //
                limits: [10, 15, 20, 25, 50, 100],
                limit: 15,
                page: true
            });

        },
        add:function(){
            Controller.api.bindevent();
        },
        edit:function(){
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                var element = layui.element,menu_name = '默认菜单', obj;
                //一级菜单对象
                function parents(param) {
                    this.name = param;
                    this.type = 'click';
                    this.sub_button = [];
                }
                //二级菜单对象
                function subs(param) {
                    this.name = param;
                    this.type = 'click';
                }
                $(document).on('click', '.con-body .left .wx-menu-add', function (e) {
                    $(this).removeClass('wx-menu-add');
                    $(this).addClass('active');
                    $(this).siblings().removeClass('active');
                    var id = $(this).attr("data-id");
                    var ids = id.split("_");
                    var menuLength = $(this).siblings().length;
                    //一级菜单
                    if (ids.length === 1) {
                        //首级菜单只有一个时
                        if (menuLength < 2) {
                            var data_id = menuLength + 2
                            $(this).parent().append('<li class="menu-list-view wx-menu-add" data-id="' + data_id + '"><span class="layui-icon layui-icon-close"></span></li>');
                            $(this).children().remove();
                            $(this).append("<span>一级菜单</span>")
                        }
                        //最后一个菜单时
                        if (menuLength === 2) {
                            $(this).children().remove();
                            $(this).append("<span>一级菜单</span>")
                        }
                        data_id = ids[0] + '_' + 0;
                        if($(this).find('ul').length ===0){
                            $(this).append('<ul style="display: block"><li data-id="' + data_id + '" class="menu-list-sub wx-menu-add"><span class="layui-icon layui-icon-close"></span></li></ul>');
                        }
                        menuList.push(new parents('一级菜单'))
                    } else {   //二级菜单
                        if (menuLength < 4) {
                            data_id = ids[0] + '_' + (1+parseInt(ids[1]))
                            $(this).parent().append('<li class="menu-list-sub wx-menu-add" data-id="' + data_id + '"><span class="layui-icon layui-icon-close"></span></li>');
                            $(this).children().remove();
                            $(this).append("<span>二级菜单</span>")
                        }
                        //最后一个菜单时
                        if (menuLength === 4) {
                            $(this).children().remove();
                            $(this).append("<span>二级菜单</span>")
                        }
                        if (!menuList[ids[0]].hasOwnProperty('sub_button')) {
                            var sub_button = {"sub_button": {'name':'二级菜单',"type":'click'}};
                            menuList[ids[0]].append(sub_button);
                        }
                        menuList[ids[0]].sub_button.push(new subs('二级菜单'));
                    }
                    e.stopPropagation();//阻止冒泡
                    e.preventDefault();
                });
                $(document).on('click', '.menu-list-view,.menu-list-sub', function (e) {
                    $('.menu-list').find('.active').removeClass('active');
                    $(this).addClass('active');
                    var id = $(this).attr('data-id'), type;
                    ids = id.split('_');
                    if (ids.length === 1) {
                        if($(this).find('ul>li').length > 1){
                            $('.media').hide();
                        }else{
                            $('.media').show();
                        }
                        type = menuList[ids[0]]['type']
                        $('input[value="'+type+'"]').prop("checked", 'checked');
                        $('.delmenu').html('删除菜单')
                    } else {
                        type = menuList[ids[0]]['sub_button'][ids[1]]['type'];
                        $('input[name="type"][value="'+type+'"]').prop("checked", 'checked');
                        $('.media').show();
                        $('.delmenu').html('删除子菜单');
                    }
                    var radioindex = $('.layui-tab-title').find('input[name="type"]').index($('input[value="'+type+'"]'));
                    if(type==='click' || type==='view' || type==="miniprogram"){
                        $('.layui-tab-content').find('.layui-tab-item').eq(radioindex).addClass('layui-show').siblings().removeClass('layui-show');
                        var inputVal  = ids.length>1?menuList[ids[0]]['sub_button'][ids[1]]['key']:menuList[ids[0]]['key'];
                        var input1 = $('.layui-tab-content').find('.layui-tab-item').eq(radioindex).find('input').eq(0);
                        switch(type){
                            case 'click':
                                input1.val(inputVal);
                                break;
                            case 'view':
                                inputVal = ids.length>1?menuList[ids[0]]['sub_button'][ids[1]]['url']:menuList[ids[0]]['url'];
                                input1.val(inputVal);
                                break;
                            case 'miniprogram':
                                var appid = ids.length>1?menuList[ids[0]]['sub_button'][ids[1]]['appid']:menuList[ids[0]]['appid'];
                                var pagepath = ids.length>1?menuList[ids[0]]['sub_button'][ids[1]]['pagepath']:menuList[ids[0]]['pagepath'];
                                input1.val(appid);
                                $('.layui-tab-content').find('.layui-tab-item').eq(radioindex).find('input').eq(1).val(pagepath)
                                break;
                        }
                    }else{
                        $('.layui-tab-content').find('.layui-tab-item').removeClass('layui-show');
                    }
                    layui.form.render();
                    $('input[name="title"]').val($(this).children('span').text())
                    e.stopPropagation();//阻止冒泡
                    e.preventDefault();
                })
                layui.form.on('radio(type)',function(){
                    var type = $(this).val();
                    var key = $(this).data('key');
                    upmenulist(3,type,key);
                })
                $('input').keyup(function () {
                    //更新菜单那
                    var val = $(this).val();
                    var name = $(this).attr('name');
                    if (name === 'title') {
                        $('.menu-list').find('.active').children('span').text(val);
                        upmenulist();
                    } else if (name === 'menu_name') {
                        menu_name = val;
                    } else if (name === 'url') {
                        upmenulist(2, 'view', name);
                    } else if (name === 'keyword') {
                        upmenulist(2, 'click', name);
                    } else if(name === 'miniprogram') {
                        upmenulist(2, 'miniprogram', name);
                    }else {
                        upmenulist(2, 'click', name);
                    }
                })
                //更新菜单
                function upmenulist(i=1,type='view',name='') {
                    obj = $('.menu-list').find('.active');
                    if(obj.length === 0) return false;
                    var id = obj.attr("data-id");
                    var ids = id.split("_");
                    var val = obj.children('span').text();
                    if(i ===1){ //菜单
                        if (ids.length === 1) {
                            menuList[ids[0]].name = val;
                        } else {
                            menuList[ids[0]].sub_button[ids[1]].name = val;
                        }
                    }else if(i===2){ //事件
                        val = $('input[name="'+name+'"]').val();
                        if (ids.length === 1) {
                            menuList[ids[0]].type = type;
                            menuList[ids[0]][name] = val
                        } else {
                            menuList[ids[0]].sub_button[ids[1]].type = type;
                            menuList[ids[0]].sub_button[ids[1]][name] = val
                        }
                    }else if(i===3){//radio
                        if (ids.length === 1) {
                            menuList[ids[0]].name = val;
                            menuList[ids[0]].type = type;
                            if(!menuList[ids[0]].hasOwnProperty('key')){
                                menuList[ids[0]].key = name;
                            }
                        } else {
                            menuList[ids[0]].sub_button[ids[1]].name = val;
                            menuList[ids[0]].sub_button[ids[1]].type = type;
                            if(!menuList[ids[0]].sub_button[ids[1]].hasOwnProperty('key')){
                                menuList[ids[0]].sub_button[ids[1]].key = name;
                            }
                        }
                    }
                }
                //删除菜单
                $(document).on('click', '.delmenu', function (e) {
                    layer.confirm('确定删除菜单？', {btn: ['确定', '取消'], title: "提示"}, function () {
                        $menu = $('.menu-list').find('.active');
                        if ($menu.length === 0) {
                            Fun.toastr.error('请选择要删除的菜单');
                            layer.closeAll();
                            return false;
                        }
                        if ($menu.find('ul>li').length > 1) {
                            Fun.toastr.error('有子菜单请先删除子菜单');
                            layer.closeAll();
                            return false;
                        }
                        var id = $menu.attr('data-id');
                        ids = id.split('_');
                        if (ids.length > 1) {
                            delete menuList[ids[0]]['sub_button'][ids[1]];
                            if($menu.parent('ul').children('.wx-menu-add').length ===0){
                                data_id = ids[0]+'_'+ ((parseInt(ids[1])-1));
                                $menu.parent('ul').append('<li class="menu-list-sub wx-menu-add" data-id="' + data_id + '"><span class="layui-icon layui-icon-close"></span></li>');
                            }
                        } else {
                            delete menuList[ids[0]];
                            if($menu.parent('ul').children('.wx-menu-add').length ===0) {
                                var data_id = ids[0];
                                $menu.parent('ul').append('<li class="menu-list-view wx-menu-add" data-id="' + data_id + '"><span class="layui-icon layui-icon-close"></span></li>');
                            }
                        }
                        $menu.remove();
                        layer.closeAll();
                    });
                })
                //保存自定义菜单
                $(document).on('click', '#save',function () {
                    let url = null;
                    let strRegex = '(https?|ftp|file)://[-A-Za-z0-9+&@#/%?=~_|!:,.;]+[-A-Za-z0-9+&@#/%=~_|]';
                    let re = new RegExp(strRegex);
                    let flag ;
                    if(menuList.length===0)  Fun.toastr.error("请输入菜单名字"); flag = false;
                    for (let i = 0; i < menuList.length; i++) {
                        if (menuList[i].sub_button.length) {
                            //判断是否有子元素
                            for (let j = 0; j < menuList[i].sub_button.length; j++) {
                                //二级菜单
                                if (!menuList[i].sub_button[j]['name'] || !menuList[i].sub_button[j]['type']) {
                                    Fun.toastr.error("菜单名字不能为空");
                                    return false;
                                }else if (menuList[i].sub_button[j]['type']==='view' && menuList[i].sub_button[j].hasOwnProperty('url')){
                                    flag = true;
                                    url = menuList[i].sub_button[j].url;
                                    if (!re.test(url)) {
                                        Fun.toastr.error("请输入正确的url地址！");
                                        return false;
                                    }
                                } else if (menuList[i].sub_button[j].hasOwnProperty('appid') &&  menuList[i].sub_button[j].hasOwnProperty('pagepath')) {
                                    flag = true;
                                } else  {
                                    flag = true;
                                }
                            }
                        } else {
                            //一级菜单 url
                            if (!menuList[i]['name'] || !menuList[i]['type']  ) {
                                Fun.toastr.error("菜单名或菜单类型不能为空");
                                return false;
                            }else if (menuList[i]['type']==='view' && menuList[i].hasOwnProperty('url')) {
                                flag = true;
                                url = menuList[i].url;
                                if (!re.test(url)) {
                                    Fun.toastr.error("请输入正确的url地址！");
                                    return false;
                                }
                            } else if (menuList[i].hasOwnProperty('appid') && menuList[i].hasOwnProperty('pagepath') ) {
                                flag = true;
                            } else {
                                flag = true;
                            }
                        }
                    }
                    // 添加素材
                    if (flag) {
                        Fun.ajax({
                            url: Fun.url(window.location.href),
                            data:{
                                "menu_data": JSON.stringify(menuList),//先将对象转换为字符串再传给后台
                                'menu_name':menu_name,
                                '__token__':$('input[name="__token__"]').val()
                            },
                            contentType: "application/json",
                            dataType: "json",
                        }, function (res) {
                            Fun.toastr.success(res.msg,
                                function(){
                                    Fun.api.close();
                                }
                            );
                        }, function (res) {
                            Fun.toastr.error(res.msg);
                        });
                    }
                    return false;
                });
            },
        }
    };
    return Controller;
});