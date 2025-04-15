define(['table','form'], function (Table,Form) {
    let Controller = {
        index: function () {
            Table.init = {
                table_elem: 'list',
                tableId: 'list',
                requests:{
                    index_url:'Region/index'+location.search,
                    add_url:'Region/add'+location.search,
                    edit_url:'Region/edit'+location.search,
                    destroy_url:'Region/destroy'+location.search,
                    delete_url:'Region/delete'+location.search,
                    recycle_url:'Region/recycle'+location.search,
                    import_url:'Region/import'+location.search,
                    export_url:'Region/export'+location.search,
                    modify_url:'Region/modify'+location.search,

                }
            }
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.index_url),
                init: Table.init,
                primaryKey:'id',
                toolbar: ['refresh','add','delete','import','export','recycle'],
                cols: [[
                    {checkbox: true,},
                     {field: 'id', title: __('ID'), sort:true,},
                    {field:'label', title: __('Label'),align: 'center'},
                    {field:'value', title: __('Value'),align: 'center'},
                    {field:'key', title: __('Key'),align: 'center'},
                    // {field:'old_key', title: __('OldKey'),align: 'center'},
                    {field:'status',search: 'select',title: __('Status'),filter: 'status',selectList:statusList,templet: Table.templet.select},
                    {field:'create_time',title: __('CreateTime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'timerange',templet: Table.templet.time,sort:true,searchOp:'daterange'},
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
                page: true,
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
                    delete_url:'Region/delete'+location.search,
                    recycle_url:'Region/recycle'+location.search,
                    restore_url:'Region/restore'+location.search,
                    
                },
            };
            Table.render({
                elem: '#' + Table.init.table_elem,
                id: Table.init.tableId,
                url: Fun.url(Table.init.requests.recycle_url),
                init: Table.init,
                primaryKey:'id',
                toolbar: ['refresh','delete','restore'],
                cols: [[
                    {checkbox: true,},
                     {field: 'id', title: __('ID'), sort:true,},
                    {field:'label', title: __('Label'),align: 'center'},
                    {field:'value', title: __('Value'),align: 'center'},
                    {field:'key', title: __('Key'),align: 'center'},
                    // {field:'old_key', title: __('OldKey'),align: 'center'},
                    {field:'status',search: 'select',title: __('Status'),filter: 'status',selectList:statusList,templet: Table.templet.select},
                    {field:'create_time',title: __('CreateTime'),align: 'center',timeType:'datetime',dateformat:'yyyy-MM-dd HH:mm:ss',searchdateformat:'yyyy-MM-dd HH:mm:ss',search:'timerange',templet: Table.templet.time,sort:true,searchOp:'daterange'},
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
                page: true,
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
