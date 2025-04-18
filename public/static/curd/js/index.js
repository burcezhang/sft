define(['jquery','table','form'], function ($,Table,Form) {
    Table.init = {
        table_elem: 'list',
        tableId: 'list',
        requests: {
            index_url: 'curd/index/index',
            delete_url: 'curd/index/delete',
            add_url: 'curd/index/add',

            execute: {
                type: 'request',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/index/execute',
                icon: 'layui-icon layui-icon-add-1',
                text: __('强制重新执行,将覆盖所有文件'),
                title: __('强制重新执行'),
                full: 1,
                node:'create',
            },
            delall: {
                type: 'request',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/index/delall',
                icon: 'layui-icon layui-icon-delete',
                text: __('删除所有内容,将删除文件和菜单'),
                title: __('删除所有内容'),
                full: 1,
                node:'create',
            },
            create: {
                type: 'open',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/table/add',
                icon: 'layui-icon layui-icon-add-1',
                text: __('建表'),
                title: __('建表'),
                full: 1,
                node:'create',
            },
            tablelist: {
                type: 'iframe',
                event: 'iframe',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/table/index',
                icon: 'layui-icon layui-icon-list',
                text: __('数据表列表'),
                title: __('数据表列表'),
                full: 1,
                node: 'tablelist',
            },
            add_full: {
                type: 'open',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/index/add',
                icon: 'layui-icon layui-icon-add-1',
                text: __('Add'),
                title: __('Add'),
                full: 1,
                extend:"data-btn=''",
            }, delfile: {
                type: 'request',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/index/delfile',
                icon: 'layui-icon layui-icon-add-1',
                text: __('删除文件'),
                title: __('删除文件'),
                full: 1,
            },editfile: {
                type: 'open',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/index/editfile',
                icon: 'layui-icon layui-icon-edit',
                text: __('编辑文件'),
                title: __('编辑文件'),
                full: 1,
                btn:false,
            },addphp: {
                type: 'open',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/index/addfile?exts=php',
                icon: 'layui-icon layui-icon-add-1',
                text: __('添加PHP'),
                title: __('添加PHP'),
                full: 1,
                btn:false,
            },addhtml: {
                type: 'open',
                class: 'layui-btn-sm',
                url: 'curd/index/addfile?exts=html',
                icon: 'layui-icon layui-icon-add-1',
                text: __('添加html'),
                title: __('添加html'),
                full: 1,
                btn:false,
            },addcss: {
                type: 'open',
                class: 'layui-btn-sm',
                url: 'curd/index/addfile?exts=css',
                icon: 'layui-icon layui-icon-add-1',
                text: __('添加CSS'),
                title: __('添加CSS'),
                full: 1,
                btn:false,
            },addjs: {
                type: 'open',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/index/addfile?exts=js',
                icon: 'layui-icon layui-icon-add-1',
                text: __('添加JS'),
                title: __('添加JS'),
                full: 1,
                btn:false,
            },list: {
                type: 'open',
                class: 'layui-btn-sm layui-btn-normal',
                url: 'curd/index/list'+location.search,
                icon: 'layui-icon layui-icon-list',
                text: __('文件列表'),
                title: __('文件列表'),
                full: 1,
                btn:false,
            },
        },
    };
    let Controller = {
        index: function () {
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                // lineStyle: 'height: 80px;', // 定义表格的多行样式
                toolbar: ['refresh','add_full','create','tablelist','list','delete'],
                cols: [[
                    {checkbox: true,},
                    {field: 'id', title: 'ID', width: 80, sort: true},
                    {field: 'admin.username', title: __('Admin'), width: 120,templet: Table.templet.tags},
                    {field: 'post_json', title: __('json'), width: 300},
                    {field: 'curd', title: __('curd'), width: 400,},
                    {field: 'create_time', title: __('createTime'), width: 180,search:'range'},
                    {
                        fixed:'right',
                        wdth: 200,
                        align: 'center',
                        title: __('Operat'),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ['execute','delall','delete']
                    }
                ]],
                limits: [10, 15, 20, 25, 50, 100],
                limit: 15,
                page: true
            });
            Table.api.bindEvent(Table.init.tableId);

        },
        add:function () {
            Controller.api.bindevent()
        },
        list:function () {
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.list.url),
                init: Table.init,
                tree: {
                    customName: {
                        'name':'name',
                    },
                    // data: {isSimpleData:false},
                },
                toolbar: ['refresh','addphp','addhtml','addjs','addcss',],
                cols: [[
                    {checkbox: true,},
                    {field: 'id', title: __('文件名'), hide:true,search: false},
                    {field: 'name', title: __('文件名'),align: 'left', },
                    {field: 'path', title: __('文件目录'), align: 'left',search: false},
                    {field: 'size', title: __('文件大小'), width: 100,search: false},
                    {field: 'mtime', title: __('更新时间'), width: 180,search:'range',align: 'left',search:false},
                    {field: 'exts', title: __('后缀'), width: 180,search: 'select',selectList:{'php':'php','js':'js','css':'css','html':'html','sql':'sql','txt':'txt','ini':'ini'}},
                    {
                        fixed:'right',
                        wdth: 250,
                        align: 'center',
                        title: __('Operat'),
                        init: Table.init,
                        templet: Table.templet.operat,
                        operat: ['editfile','delfile',]
                    }
                ]],
                limits: [10, 15, 20, 25, 50, 100],
                limit: 15,
                page: true
            });

        },
        addfile:function (){
            Controller.api.bindevent();
            Controller.api.ace();
        },
        editfile:function (){
            // var ace = ace.edit("content");
            Controller.api.bindevent();
            Controller.api.ace();

       },
        api: {
            bindevent: function () {
                Form.api.bindEvent($('form'))
                var relTable = [];
                function buildOptions(data,select,type=0){
                    if (type){
                        $('.'+select).attr('data-attr','id,title');
                        $('.'+select).attr('data-data',JSON.stringify(data));
                        window['selects-'+select].reload({
                            options: data,
                        });
                    }else{
                        var html = [];
                        for (var i = 0; i < data.length; i++) {
                            html.push("<option value='" + data[i] + "'>" + data[i] + "</option>");
                        }
                        select.html(html);
                        layui.form.render();Form.api.bindEvent();
                    }
                };
                //选驱动
                layui.form.on('select(driver)', function(data){
                    var driver = data.value;
                    Fun.ajax({url:Table.init.requests.add_url,method:'GET',data:{type:1,driver:driver}},function (res){
                        buildOptions(res.data.table,$('.table'));
                        var length = $('.jointable').length;
                        if(length > 0){
                            for (var index=0;index<length;index++){
                                var data = { //数据
                                    "index":index,
                                    'table':res.data.table,
                                }
                                buildOptions(res.data.table,$('.jointable'));

                            }
                        }
                    })
                })
                //选表
                layui.form.on('select(table)', function(data){
                    var tableName = data.value;
                    var driver = $('select[name="driver"]').val();
                    if(!tableName){
                        Fun.toastr.error(__('please choose main table'));
                        return false;
                    }
                    Fun.ajax({url:Table.init.requests.add_url,method:'GET',data:{type:2,'tablename':tableName,driver:driver}},function (res){
                        buildOptions(res.data.mfields_table,'fields',1);
                        buildOptions(res.data.mfields_table,'formFields',1);
                        buildOptions(res.data.fields_table,$('.joinforeignkey'));
                    })
                })
                layui.form.on('select(jointable)', function(data){
                    _that  = $(data.elem);
                    var jointablename = data.value;
                    var driver = $('select[name="driver"]').val();
                    var tableName = $('select[name="table"]').val();
                    if(!tableName){
                        Fun.toastr.error(__('please choose rel table'));
                        return false;
                    }
                    Fun.ajax({url:Table.init.requests.add_url,method:'GET',data:{type:3,tablename:tableName,jointablename:jointablename,driver:driver}},function (res){
                        buildOptions(res.data.fields_join,_that.parents('tr').find('.joinprimarykey'));
                        buildOptions(res.data.fields_join,_that.parents('tr').find('.selectfields'));
                        buildOptions(res.data.fields_table,_that.parents('tr').find('.joinforeignkey'));
                    })
                })
                $('.addRelation').click(function (){
                    let index = relTable.length
                    relTable.push({
                        title: '',
                        content: []
                    })
                    //第三步：渲染模版
                    var data = { //数据
                        "index":index
                    }
                    var tableName = $('select[name="table"]').val();
                    var driver = $('select[name="driver"]').val();
                    if(!tableName){
                        Fun.toastr.error(__('please choose main table'));
                        return false;
                    }
                    Fun.ajax({async:false,url:Table.init.requests.add_url,method:'GET',data:{type:4,'tablename':tableName,driver:driver}},function (res){
                        var index = $('#relTab').find('tr').length;
                        buildOptions(res.data.fields,$('.joinforeignkey'));
                        buildOptions(res.data.table,$('.jointable'));
                        data.table = res.data.table;
                        tableInit(data)
                    })
                    // 规格删除按钮事件
                    $(`#relTab-delete-${index}`).click(function (){
                        // 移除元素
                        $(`#relTab-${index}`).remove();
                        // 数组移除
                        relTable.splice(index,1);
                    })
                })
                function tableInit(data){
                    var getTpl = tpl.innerHTML
                        ,view = $('#relTab');
                    layui.laytpl(getTpl).render(data, function(html){
                        view.append(html) ;
                    });
                    layui.form.render()
                    Form.api.bindEvent();
                }
            },
            ace:function (){
                $mode  = FormArray?FormArray['mode']:'php';
                console.log($mode)
                var editor = ace.edit("content",{
                    theme: "ace/theme/monokai",
                    fontSize:'16px',
                    mode: "ace/mode/"+$mode,//,
                    selectionStyle: "text",
                    autoScrollEditorIntoView: true,
                    copyWithEmptySelection: true,
                    showPrintMargin:false,
                    enableMultiselect:true,
                });
                if(FormArray.content){
                    editor.setValue(FormArray.content); // 或 session.setValue
                }
                editor.getSession().setUseSoftTabs(true);
                // editor.getSession().setUseWrapMode(true);
                editor.getSession().setTabSize(4);
                editor.setHighlightActiveLine(true);
                editor.setShowPrintMargin(false);
                editor.setOptions({
                    enableBasicAutocompletion: true,
                    enableSnippets: true,
                    enableLiveAutocompletion: true

                });
                //动态改变
                editor.getSession().on('change', function(){
                    $('textarea[name="content"]').val(editor.getValue());
                });
                editor.commands.addCommand({
                    name: '到下一行',
                    bindKey: {win: 'Shift+Enter',  mac: ''},
                    exec: function(_editor) {
                        _editor.selection.clearSelection();
                        _editor.navigateLineEnd();
                        _editor.insert("\n");
                    }
                });
                editor.commands.addCommand({
                    name: "快捷键使用",
                    bindKey: {win: "Ctrl-Alt-h", mac: "Command-Alt-h"},
                    exec: function(editor) {
                        ace.config.loadModule("ace/ext/keybinding_menu", function(module) {
                            module.init(editor);
                            editor.showKeyboardShortcuts()
                        })
                    }
                })
                editor.commands.addCommand({
                    name: "当前选中全部",
                    bindKey: {win: "Alt+J", mac: ""},
                    exec: function(editor) {
                        editor.selectMore(1);
                    }
                })

                layui.util.on('lay-on', {
                    save: function(){
                        Fun.ajax({
                            url:'curd/index/editfile',
                            data:{
                            'content':editor.getValue(),
                            'id':$('#id').val(),
                            'exts':$('#exts').val(),
                            'type':$('#type').val()
                            },
                        }, function(res){
                            Fun.toastr.success(res.msg);
                            window.close(self);
                        },function (res){
                            Fun.toastr.error(res.msg)
                        })
                    },
                    refresh: function(){
                        window.location.reload();
                    },
                    find: function(){
                        editor.execCommand ('find')
                    },
                    replace: function(){
                        editor.execCommand ('replace')
                    }
                });
            }

        },

    };
    return Controller;
});
